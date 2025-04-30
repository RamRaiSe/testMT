<?php

namespace App\Controller\Rest;

use App\Entity\Message;
use App\Entity\Ticket;
use App\Form\TicketFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class TicketRestController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly HubInterface $hub)
    {
    }

    #[Route('/ticket', name: 'ticket', methods: ['POST'])]
    public function postTicket(Request $request): JsonResponse
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketFormType::class, $ticket, ['method' => 'POST']);

        $form->submit(json_decode($request->getContent(),true));

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setCreator($this->getUser());

            $this->entityManager->persist($ticket);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Created!'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['message' => 'Bad Request!'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/tickets', name: 'tickets', methods: ['GET'])]
    public function getTickets(Request $request): JsonResponse
    {
        $tickets = $this->entityManager->getRepository(Ticket::class)->findBy(['creator' => $this->getUser()]);

        $data = [];

        foreach ($tickets as $ticket) {
            $data[] = [
                'id' => $ticket->getId(),
                'title' => $ticket->getTitle(),
                'description' => $ticket->getDescription(),
                'status' => $ticket->getStatus(),
                'creator' => ['email' => $ticket->getCreator()?->getEmail()],
                'createdAt' => $ticket->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/ticket/{id}', name: 'get_ticket', methods: ['GET'])]
    public function getTicket(Request $request, int $id): JsonResponse
    {
        $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(['id' => $id]);

        if (!$ticket) {
            throw $this->createNotFoundException();
        }

        $data = [
            'id' => $ticket->getId(),
            'title' => $ticket->getTitle(),
            'description' => $ticket->getDescription(),
            'status' => $ticket->getStatus(),
            'creator' => ['email' => $ticket->getCreator()?->getEmail()],
            'createdAt' => $ticket->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];

        return new JsonResponse($data);
    }

    #[Route('/ticket/{id}/messages', name: 'post_ticket_messages', methods: ['POST'])]
    public function postTicketMessages(Request $request, int $id): JsonResponse
    {
        $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(['id' => $id]);

        if (!$ticket) {
            throw $this->createNotFoundException();
        }

        $content = json_decode($request->getContent(),true)['content'];

        $message = new Message();
        $message->setContent($content);
        $message->setTicket($ticket);
        $message->setCreator($this->getUser());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $data = [
            'id' => $message->getId(),
            'content' => $message->getContent(),
            'creator' => ['email' => $message->getCreator()?->getEmail()],
            'createdAt' => $message->getCreatedAt()?->format('Y-m-d H:i:s'),
        ];

        $update = new Update(
            "ticket-$id",
            json_encode($data)
        );

        $this->hub->publish($update);

        return new JsonResponse($data);
    }

    #[Route('/ticket/{id}/messages', name: 'get_ticket_messages', methods: ['GET'])]
    public function getTicketMessages(Request $request, int $id): JsonResponse
    {
        $messages = $this->entityManager->getRepository(Message::class)->findBy(['ticket' => $id]);
        $data = [];

        if (!$messages) {
            return new JsonResponse($data);
        }

        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'creator' => ['email' => $message->getCreator()?->getEmail()],
                'createdAt' => $message->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }
}