<?php

namespace App\Controller\Api;

use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route; // <- атрибуты
use App\DTO\CreateTaskDto;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tests')]
class TestController extends AbstractController
{
    public function index(TestRepository $testRepository): JsonResponse
    {
        $tests = $testRepository->findAll();
        return $this->json($tests);
    }
}
