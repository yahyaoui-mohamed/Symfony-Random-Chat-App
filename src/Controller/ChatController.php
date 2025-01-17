<?php

namespace App\Controller;

use Predis\Client;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatController extends AbstractController
{
    private Client $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    #[Route('/', name: 'chat')]
    public function index(SessionInterface $session): Response
    {
        if ($session->has('user_id')) {
            $userid = $session->get("user_id");
        } else {
            $userid = uniqid('user_', true);
            $session->set('user_id', $userid);
        }
        return $this->render('chat/index.html.twig', [
            'mercure_url' => $this->getParameter('mercure.default_hub'),
            'user_id' => $userid,
        ]);
    }

    #[Route('/pair', name: 'pair')]
    public function pair(SessionInterface $session): JsonResponse
    {
        if (!$session->has('user_id')) {
            $userId = uniqid('user_', true);
            $session->set('user_id', $userId);
        } else {
            $userId = $session->get('user_id');
        }
        $peerId = $this->addToQueue($userId);

        if ($peerId) {
            $chatId = $this->generateChatId($userId, $peerId);
            if ($chatId) {
                return new JsonResponse(['peerId' => $peerId, 'chatId' => $chatId]);
            } else {
                return new JsonResponse(['peerId' => null, 'chatId' => null]);
            }
        }

        $pairData = $this->addToQueue($userId);

        if ($pairData) {
            return new JsonResponse($pairData);
        }

        return new JsonResponse(['peerId' => null, 'chatId' => null]);
    }

    public function addToQueue(string $userId): ?string
    {
        if ($this->redis->llen("waiting_queue") > 0) {
            $peerId = $this->redis->lpop('waiting_queue');
            return $peerId;
        } else {
            $this->redis->rpush('waiting_queue', $userId);
            return null;
        }
    }

    private function generateChatId(string $userId1, string $userId2): ?string
    {
        if ($userId1 === $userId2) {
            return null;
        }
        $ids = [$userId1, $userId2];
        sort($ids);
        return implode('-', $ids);
    }

    #[Route('/queue-length', name: 'queue_length')]
    public function queueLength(): JsonResponse
    {
        $length = $this->redis->llen('waiting_queue');
        $content = $this->redis->lrange('waiting_queue', 0, $length);
        dd($content);
        return new JsonResponse(['length' => $length]);
    }

    #[Route('/send-message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, HubInterface $hub, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'];
        $chatId = $data['chatId'];

        $update = new Update(
            "chat-topic-{$chatId}",
            json_encode(['message' => $message, 'from' => $session->get("user_id")])
        );
        $hub->publish($update);

        return new JsonResponse(['status' => 'Message sent']);
    }
}
