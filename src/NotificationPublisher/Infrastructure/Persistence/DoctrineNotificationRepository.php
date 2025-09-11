<?php

namespace App\NotificationPublisher\Infrastructure\Persistence;

use App\Constant\Status;
use App\Entity\Notification;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineNotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function find(int $id): ?Notification
    {
        return $this->em->getRepository(Notification::class)->find($id);
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Notification::class)->findAll();
    }

    public function save(Notification $notification): void
    {
        $this->em->persist($notification);
        $this->em->flush();
    }

    public function findPending(): array
    {
        return $this->em->getRepository(Notification::class)
            ->findBy(['status' => 'pending']);
    }

    public function findFailedNotifications(): array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('n')
            ->from(Notification::class, 'n')
            ->where('n.status IN (:statuses)')
            ->andWhere('n.retryAt IS NOT NULL AND n.retryAt <= :now')
            ->setParameter('statuses', [Status::STATUS_FAILED, Status::STATUS_PENDING])
            ->setParameter('now', new \DateTimeImmutable());

        return $qb->getQuery()->getResult();
    }
}
