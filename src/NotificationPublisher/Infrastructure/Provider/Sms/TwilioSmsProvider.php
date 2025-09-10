<?php

namespace App\NotificationPublisher\Infrastructure\Provider\Sms;

use App\Dto\Notification\NotificationMessageDto;
use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use App\NotificationPublisher\Infrastructure\Provider\ProviderException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioSmsProvider implements ProviderInterface
{
    public function __construct(
        private string $accSid,
        private string $authToken,
        private string $fromNumber
    ) {
    }

    public function send(NotificationMessageDto $messageDto): bool
    {
        try {
            $twilio = new Client($this->accSid, $this->authToken);

            $response = $twilio->messages->create(
                $messageDto->getReceiver(),
                [
                    "body" =>$messageDto->getContent(),
                    "from" => $this->fromNumber,
                ]
            );

            return true;
        } catch (TwilioException $e) {
            throw new ProviderException("Twilio SMS sending failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function getChannel(): string
    {
        return 'sms';
    }
}
