<?php
namespace App\NotificationPublisher\Domain\Service\Notification;
//
use App\Dto\Notification\NotificationMessageDto;
//use Symfony\Component\Mailer\MailerInterface;
//use Symfony\Component\Mime\Email;
use App\NotificationPublisher\Infrastructure\Provider\Email\AwsSesEmailProvider;
use App\NotificationPublisher\Infrastructure\Provider\Sms\TwilioSmsProvider;
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
