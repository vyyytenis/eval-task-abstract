<?php

namespace App\NotificationPublisher\Infrastructure\Provider;

use App\Dto\Notification\NotificationMessageDto;

interface ProviderInterface
{
    /**
     * Send a notification message.
     *
     * @throws ProviderException
     */
    public function send(NotificationMessageDto $messageDto): bool;

    /**
     * Return the channel this provider supports (e.g., sms, email, push).
     */
    public function getChannel(): string;
}
