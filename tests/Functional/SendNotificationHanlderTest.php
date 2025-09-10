<?php

namespace App\Tests\Functional;

use App\NotificationPublisher\Application\Command\SendNotificationMessage;
use App\NotificationPublisher\Application\Handler\SendNotificationHandler;
use App\NotificationPublisher\Infrastructure\Persistence\DoctrineNotificationRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SendNotificationHanlderTest extends KernelTestCase
{
    private SendNotificationHandler $sendNotificationHandler;

    private DoctrineNotificationRepository&MockObject $repository;

    public function testSomething(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->willReturn(null)
        ;

        $message = new SendNotificationMessage(1);
        $this->sendNotificationHandler->__invoke($message);
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        self::bootKernel();
        $container = static::getContainer();

        $repository = $this->createMock(DoctrineNotificationRepository::class);
        $this->repository = $repository;

        $this->sendNotificationHandler = $container->get(SendNotificationHandler::class);
    }
}
