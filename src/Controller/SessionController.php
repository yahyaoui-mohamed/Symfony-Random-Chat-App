<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/get-id', name: 'get_id')]
    public function getId(Request $request): JsonResponse
    {
        $session = $request->getSession();
        if (!$session->has('user_id')) {
            $session->set('user_id', uniqid('user_', true));
        }

        return new JsonResponse(['id' => $session->get('user_id')]);
    }
}
