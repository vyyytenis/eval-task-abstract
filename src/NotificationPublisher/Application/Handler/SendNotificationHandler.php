<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Handler;

use App\NotificationPublisher\Application\Command\SendNotificationMessage;
use App\NotificationPublisher\Domain\Service\Notification\NotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendNotificationHandler
{
    public function __construct(
        private NotificationService $notificationService,
    ) {
    }

    public function __invoke(SendNotificationMessage $sendNotificationMessage): void
    {
        try {
            $this->notificationService->publish($sendNotificationMessage->getNotificationId());
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}
