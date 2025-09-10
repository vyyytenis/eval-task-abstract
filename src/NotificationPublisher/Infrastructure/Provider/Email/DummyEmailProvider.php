<?php

namespace App\NotificationPublisher\Infrastructure\Provider\Email;

use App\Dto\Notification\NotificationMessageDto;
use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use App\NotificationPublisher\Infrastructure\Provider\ProviderException;

class DummyEmailProvider implements ProviderInterface
{
    public function send(NotificationMessageDto $messageDto): bool
    {
        // Simulate sending email
        dump(sprintf(
            "DummyEmailProvider: Simulating sending email to %s with content: %s",
            $messageDto->getReceiver(),
            $messageDto->getContent()
        ));

        // Simulate a random failure for failover testing
        if (rand(0, 1) === 0) {
            throw new ProviderException("DummyEmailProvider failed randomly!");
        }

        return true;
    }

    public function getChannel(): string
    {
        return 'email';
    }
}
