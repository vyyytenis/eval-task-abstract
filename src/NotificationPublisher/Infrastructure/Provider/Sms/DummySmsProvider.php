<?php

namespace App\NotificationPublisher\Infrastructure\Provider\Sms;

use App\Dto\Notification\NotificationMessageDto;
use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use App\NotificationPublisher\Infrastructure\Provider\ProviderException;

class DummySmsProvider implements ProviderInterface
{
    public function send(NotificationMessageDto $messageDto): bool
    {
        dump(sprintf(
            "DummySmsProvider: Simulating sending SMS to %s with content: %s",
            $messageDto->getReceiver(),
            $messageDto->getContent()
        ));

        // Simulate a random failure
        if (rand(0, 1) === 0) {
            throw new ProviderException("DummySmsProvider failed randomly!");
        }

        return true;
    }

    public function getChannel(): string
    {
        return 'sms';
    }
}
