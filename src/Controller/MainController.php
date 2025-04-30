<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'spa_index')]
    #[Route('/{vueRouting}', name: 'spa', requirements: ['vueRouting' => '.+'])]
    public function index()
    {
        return $this->render('base.html.twig');
    }
}