<?php

namespace App\Controller\Rest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class LoginRestController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login()
    {
        return new JsonResponse(['message' => 'Authentication successful'], 200);
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout()
    {
        return new JsonResponse(['message' => 'Authentication successful'], 200);
    }

    #[Route('/user', name: 'user', methods: ['GET'])]
    public function user()
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ];

        return new JsonResponse($data, 200);
    }
}