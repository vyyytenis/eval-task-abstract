<?php
namespace App\NotificationPublisher\Domain\Service\Notification;

use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class SmsNotificationService extends AbstractNotificationService
{
    /**
     * @param ProviderInterface[] $providers
     */
    public function __construct(
        iterable $providers,
        private RateLimiterFactory $notificationUserLimiter
    ) {
        parent::__construct($providers, $notificationUserLimiter);
    }
}
