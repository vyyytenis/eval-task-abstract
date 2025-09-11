<?php

namespace App\Command;

use App\NotificationPublisher\Domain\Service\Notification\NotificationService;
use App\NotificationPublisher\Infrastructure\Persistence\DoctrineNotificationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResendNotificationsCommand extends Command
{
    protected static $defaultName = 'app:notifications:resend';

    public function __construct(
        private DoctrineNotificationRepository $repository,
        private NotificationService            $notificationService
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            $notifications = $this->repository->findFailedNotifications();

            if (count($notifications) === 0) {
                $output->writeln("No notifications to process, sleeping");
            }

            foreach ($notifications as $notification) {
                $output->writeln("Retrying");
                try {
                    $this->notificationService->publish($notification->getId());
                    $output->writeln("Finished processing notification ID {$notification->getId()}");
                } catch (\Throwable $e) {
                    // Catch unexpected exceptions to keep worker running
                    $output->writeln("Eerror for ID {$notification->getId()}: {$e->getMessage()}");
                }
            }

            sleep(30);
        }
    }
}
