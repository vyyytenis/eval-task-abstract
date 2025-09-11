<?php
namespace App\NotificationPublisher\Domain\Service\Notification;

use App\NotificationPublisher\Infrastructure\Provider\Email\AwsSesEmailProvider;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class EmailNotificationService extends AbstractNotificationService
{
    public function __construct(
        private AwsSesEmailProvider $emailProvider,
        private RateLimiterFactory $notificationUserLimiter
    ) {
        parent::__construct($emailProvider, $notificationUserLimiter);
    }
}
