<?php
namespace App\NotificationPublisher\Domain\Service\Notification;

use App\NotificationPublisher\Infrastructure\Provider\Sms\TwilioSmsProvider;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class SmsNotificationService extends AbstractNotificationService
{
    public function __construct(
        private TwilioSmsProvider $twilioSmsProvider,
        private RateLimiterFactory $notificationUserLimiter
    ) {
        parent::__construct($twilioSmsProvider, $notificationUserLimiter);
    }
}
