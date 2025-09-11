<?php

namespace App\NotificationPublisher\Domain\Service\Notification;

use App\Constant\Channels;
use App\Constant\Status;
use App\Dto\Notification\NotificationMessageDto;
use App\NotificationPublisher\Infrastructure\Persistence\DoctrineNotificationRepository;
use Psr\Log\LoggerInterface;

class NotificationService
{
    public function __construct(
        public DoctrineNotificationRepository $repository,
        public EmailNotificationService $emailNotificationService,
        public SmsNotificationService $smsNotificationService,
        public LoggerInterface $logger,
        private bool $enableEmail,
        private bool $enableSms,
    ) {
    }

    public function publish(int $id): void
    {
        $entity = $this->repository->find($id);
        if ($entity === null) {
            throw new \RuntimeException("Notification with id {$id} not found");
        }

        $dto = NotificationMessageDto::createFromEntity($entity);

        try {
            $result = $this->send($dto);
        } catch (\Throwable $e) {
            $this->logger->error('error:',['message' => $e->getMessage()]);

            $entity->setStatus(Status::STATUS_FAILED);
            $entity->setErrorMessage($e->getMessage());

            $this->repository->save($entity);

            return;
        }

        if ($result) {
            $entity->setStatus(Status::STATUS_SENT);
            $this->repository->save($entity);

            return;
        }

        $entity->setStatus(Status::STATUS_FAILED);
        $this->repository->save($entity);
    }

    private function send(NotificationMessageDto $dto): bool
    {
        return match ($dto->getChannel()) {
            Channels::SMS => $this->sendSms($dto),
            Channels::EMAIL => $this->sendEmail($dto),
            default => throw new \Exception('Not a valid notification channel'),
        };
    }

    private function sendSms($dto): bool
    {
        if (!$this->enableSms) {
            throw new \RuntimeException('SMS notifications are disabled via configuration.');
        }

        return $this->smsNotificationService->send($dto);
    }

    private function sendEmail($dto): bool
    {
        if (!$this->enableEmail) {
            throw new \RuntimeException('Email notifications are disabled via configuration.');
        }

        return $this->emailNotificationService->send($dto);
    }
}
