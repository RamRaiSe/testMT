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
}