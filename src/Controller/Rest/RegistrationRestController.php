<?php

namespace App\Controller\Rest;


use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationRestController extends AbstractController
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher,
                                private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/registration', name: 'registration', methods: ['POST'])]
    public function postRegistration(Request $request): JsonResponse
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user, ['method' => 'POST']);
        $form->submit(json_decode($request->getContent(),true));

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
            $user->addRole('ROLE_USER');

            if (count($this->entityManager->getRepository(User::class)->findAll()) === 0) {
                $user->addRole('ROLE_ADMIN');
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Registration successful!'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['message' => 'Bad Request!'], Response::HTTP_BAD_REQUEST);
    }
}