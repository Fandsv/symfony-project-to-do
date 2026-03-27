<?php

namespace App\Controller;

use App\Repository\TwigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/twigs')]
class TwigController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(TwigRepository $twigRepository): Response
    {
        //$twigs = $twigRepository->findAll();
        $twigs = [
            ['title' => 'Сделать урок', 'description' => 'Выучить Symfony', 'status' => 1],
            ['title' => 'Проверить Twig', 'description' => 'Создать шаблон', 'status' => 3],
        ];
        return $this->render('test.html.twig', [
            'title' => 'Todo App',
            'tasks' => $twigs,
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $task = [
            'title' => $data['title'] ?? 'Без названия',
            'description' => $data['description'] ?? '',
            'status' => $data['status'] ?? 1,
        ];

        return new JsonResponse($task);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function update(Request $request, int $id): Response
    {
        $tasks = [
            1 => ['title' => 'Сделать урок', 'description' => 'Выучить Symfony', 'status' => 1],
            2 => ['title' => 'Проверить Twig', 'description' => 'Создать шаблон', 'status' => 3],
        ];

        if (!isset ($tasks[$id])) {
            return new Response('Task not found', 404);
        }
        $data = $request->request->all();

        if (isset($data['title'])) {
            $tasks[$id]['title'] = $data['title'];
        }
        if (isset($data['description'])) {
            $tasks[$id]['description'] = $data['description'];
        }
        if (isset($data['status'])) {
            $tasks[$id]['status'] = $data['status'];
        }

        return $this->json($tasks[$id]); }

    #[Route('/{id}', methods: ['DELETE'])]
    function delete(Request $request, int $id): Response {
        $tasks = [
            1 => ['title' => 'Сделать урок', 'description' => 'Выучить Symfony', 'status' => 1],
            2 => ['title' => 'Проверить Twig', 'description' => 'Создать шаблон', 'status' => 3],
        ];
        if (!isset ($tasks[$id])) {
            return new Response('Task not found', 404);
        }
        unset($tasks[$id]);

        return new JsonResponse($tasks[$id]);}
}
