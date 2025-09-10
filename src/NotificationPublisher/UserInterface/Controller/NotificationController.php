<?php

namespace App\NotificationPublisher\UserInterface\Controller;

use App\Dto\Notification\NotificationMessageDto;
use App\NotificationPublisher\Domain\Repository\NotificationRepositoryInterface;
use App\NotificationPublisher\Domain\Service\Notification\NotificationMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationRepositoryInterface $repository,
        private NotificationMessageService $messageService,
    ) {
    }

    #[Route('/api/send-notifications', name: 'send_notifications', methods: ['POST'])]
    public function send(
         Request $request,
         SerializerInterface $serializer,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $notificationsData = $data['notifications'] ?? [];

        $notifications = [];
        foreach ($notificationsData as $item) {
            $notifications[] = $serializer->denormalize($item, NotificationMessageDto::class);
        }

        $this->messageService->createAndSave($notifications);

        return $this->json([
            'status' => 'ok',
            'count' => count($notifications),
        ]);
    }

    #[Route('/api/list-notifications', name: 'list_notifications', methods: ['GET'])]
    public function list(): JsonResponse
    {

        $notifications = $this->repository->findAll();

        $data = array_map(function ($notification) {
            return [
                'id' => $notification->getId(),
                'userId' => $notification->getUserId(),
                'channel' => $notification->getChannel(),
                'content' => $notification->getContent(),
                'status' => $notification->getStatus(),
                'receiver' => $notification->getReceiver(),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                'sentAt' => $notification->getSentAt()?->format('Y-m-d H:i:s'),
                'errorMessage' => $notification->getErrorMessage(),
            ];
        }, $notifications);

        return new JsonResponse($data, 200);
    }
}
