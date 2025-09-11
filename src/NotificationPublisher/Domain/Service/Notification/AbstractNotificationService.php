<?php
namespace App\NotificationPublisher\Domain\Service\Notification;

use App\Dto\Notification\NotificationMessageDto;
use App\NotificationPublisher\Infrastructure\Provider\ProviderException;
use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

abstract class AbstractNotificationService
{
    /**
     * @param ProviderInterface[] $providers
     */
    public function __construct(
        protected iterable $providers,
        private RateLimiterFactory $notificationUserLimiter
    ) {
    }

    public function send(NotificationMessageDto $messageDto): bool
    {
        if (iterator_count($this->providers) === 0) {
            throw new \RuntimeException(sprintf(
                'No providers enabled for channel: %s',
                $messageDto->getChannel()
            ));
        }

        $this->validateRecipient($messageDto->getReceiver());

        $limiter = $this->notificationUserLimiter->create($messageDto->getUserId());
        $limit = $limiter->consume(1);

        if (!$limit->isAccepted()) {
            throw new \RuntimeException(sprintf(
                'User %s exceeded rate limit for channel %s',
                $messageDto->getUserId(),
                $messageDto->getChannel()
            ));
        }

        foreach ($this->providers as $provider) {
            try {
                if ($provider->send($messageDto)) {
                    return true;
                }
            } catch (ProviderException $e) {
                continue;
            }
        }

        return false;
    }

    protected function validateRecipient(string $receiver): void
    {
        if (empty($receiver)) {
            throw new \InvalidArgumentException('Recipient cannot be empty.');
        }
    }
}
