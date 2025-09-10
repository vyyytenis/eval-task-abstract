<?php

namespace App\NotificationPublisher\Infrastructure\Provider\Email;

use App\Dto\Notification\NotificationMessageDto;
use App\NotificationPublisher\Infrastructure\Provider\ProviderInterface;
use App\NotificationPublisher\Infrastructure\Provider\ProviderException;
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;


class AwsSesEmailProvider implements ProviderInterface
{
    private SesClient $sesClient;
    public function __construct(
        private string $apiKey,
        private string $apiSecret,
        private string $fromEmail,
        private string $region
    ) {}

    public function send(NotificationMessageDto $messageDto): bool
    {
        try {
            $this->sesClient = new SesClient([
                'version' => 'latest',
                'region'  => $this->region,
                'credentials' => [
                    'key'    => $this->apiKey,
                    'secret' => $this->apiSecret,
                ],
            ]);

            $this->sesClient->sendEmail([
                'Destination' => [
                    'ToAddresses' => [$messageDto->getReceiver()],
                ],
                'Message' => [
                    'Body' => [
                        'Text' => [
                            'Charset' => 'UTF-8',
                            'Data' => $messageDto->getContent(),
                        ],
                    ],
                    'Subject' => [
                        'Charset' => 'UTF-8',
                        'Data' => $messageDto->getContent() ?? 'Notification',
                    ],
                ],
                'Source' => $this->fromEmail,
            ]);

            return true;
        } catch (AwsException  $e) {
            throw new ProviderException("AWS SES sending failed: " . $e->getAwsErrorMessage(), 0, $e);
        }
    }

    public function getChannel(): string
    {
        return 'email';
    }
}
