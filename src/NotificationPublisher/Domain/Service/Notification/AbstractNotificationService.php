<?php
namespace App\NotificationPublisher\Domain\Service\Notification;

use App\Dto\Notification\NotificationMessageDto;
use App\NotificationPublisher\Infrastructure\Provider\ProviderException;
use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

abstract class AbstractNotificationService
{
    public function __construct(
        protected ProviderInterface $provider,
        private RateLimiterFactory $notificationUserLimiter
    ) {
    }

    public function send(NotificationMessageDto $messageDto): bool
    {
        if ($messageDto->getChannel() !== $this->provider->getChannel()) {
            throw new \RuntimeException('Invalid channel');
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

        $result = $this->provider->send($messageDto);

        return $result;
    }

    protected function validateRecipient(string $receiver): void
    {
        if (empty($receiver)) {
            throw new \InvalidArgumentException('Recipient cannot be empty.');
        }
    }
}
