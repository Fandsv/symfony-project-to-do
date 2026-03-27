<?php

namespace App\Controller\Api;

use App\DTO\CreateTaskDto;
use App\DTO\UpdateTaskDto;
use App\Entity\Task;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/tasks')]
class MainController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): JsonResponse
    {
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $tasks = $taskRepository->findAll();
        } else {
            $tasks = $taskRepository->findBy([
                'owner' => $user
            ]);
        }
        return $this->json($tasks);
    }

    #[Route('', methods: ['POST'])]
    public function store(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $dto = new CreateTaskDto();
        $dto->title = $data['title'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->isDone = $data['isDone'] ?? false;

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $property = $error->getPropertyPath();
                $messages[$property] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 422);
        }

        $task = new Task();
        $task->setTitle($dto->title);
        $task->setDescription($dto->description);
        $task->setIsDone($dto->isDone);
        $task->setCreatedAt(new DateTimeImmutable());
        $task->setOwner($this->getUser());

        $em->persist($task);
        $em->flush();

        return $this->json($task, 201);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    #[IsGranted('EDIT', subject: 'task')]
    public function update(
        Task $task,
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }

        $dto = new UpdateTaskDto();

        if (array_key_exists('title', $data)) {
            $dto->title = $data['title'];
        }

        if (array_key_exists('description', $data)) {
            $dto->description = $data['description'];
        }

        if (array_key_exists('isDone', $data)) {
            $dto->isDone = $data['isDone'];
        }

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $messages], 422);
        }

        if (array_key_exists('title', $data)) {
            $task->setTitle($dto->title);
        }

        if (array_key_exists('description', $data)) {
            $task->setDescription($dto->description);
        }

        if (array_key_exists('isDone', $data)) {
            $task->setIsDone((bool)$dto->isDone);
        }

        $task->setUpdatedAt(new DateTimeImmutable());

        $em->flush();

        return $this->json($task);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('DELETE', subject: 'task')]
    public function remove(
        Task $task,
        EntityManagerInterface $em
    ): JsonResponse {

        $em->remove($task);
        $em->flush();

        return $this->json(null, 204);
    }
}
