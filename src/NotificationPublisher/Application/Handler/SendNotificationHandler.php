<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Handler;

use App\Dto\Notification\NotificationMessageDto;
use App\NotificationPublisher\Application\Command\SendNotificationMessage;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use App\Entity\Notification;
use App\NotificationPublisher\Domain\Service\Notification\NotificationService;
use App\NotificationPublisher\Infrastructure\Provider\Email\AwsSesEmailProvider;
use App\NotificationPublisher\Infrastructure\Provider\Email\DummyEmailProvider;
use App\NotificationPublisher\Infrastructure\Provider\Sms\DummySmsProvider;
use App\NotificationPublisher\Infrastructure\Provider\Sms\TwilioSmsProvider;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\RateLimiter\RateLimiterFactory;

#[AsMessageHandler]
class SendNotificationHandler
{
    public function __construct(
        private NotificationRepositoryInterface $repository,
        private RateLimiterFactory $notificationUserLimiter,
        private NotificationService $notificationService,
    ) {
    }

    public function __invoke(SendNotificationMessage $sendNotificationMessage): void
    {
        try {
            $this->notificationService->publish($sendNotificationMessage->getNotificationId());
//            $success= $notificationService->send($message);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
//            $entity
//                ->setStatus(Notification::STATUS_FAILED)
//                ->setErrorMessage($e->getMessage())
//            ;
//            $this->repository->save($entity);
//
//            return;
        }
        ;

//        if ($success) {
//            $entity->setStatus(Notification::STATUS_SENT);
//            $entity->setSentAt(new \DateTimeImmutable());
//        } else {
//            $entity->setStatus(Notification::STATUS_FAILED);
//            $entity->setErrorMessage('Failed to send notification');
//        }

//        $this->repository->save($entity);
    }
}
