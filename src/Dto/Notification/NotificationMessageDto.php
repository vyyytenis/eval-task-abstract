<?php

namespace App\Dto\Notification;

use App\Entity\Notification;

class NotificationMessageDto
{
    private int $userId;
    private string $channel;
    private string $content;
    private string $receiver;

    public static function createFromEntity(Notification $entity): self
    {
        return (new self())
            ->setUserId($entity->getUserId())
            ->setChannel($entity->getChannel())
            ->setContent($entity->getContent())
            ->setReceiver($entity->getReceiver())
            ;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getReceiver(): string
    {
        return $this->receiver;
    }

    public function setReceiver(string $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }
}
