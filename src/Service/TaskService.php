<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private $entityManager;
    private $taskRepository;

    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
    }

    public function createTask(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function updateTask(Task $task): void
    {
        $this->entityManager->flush();
    }

    public function deleteTask(int $taskId): void
    {
        $task = $this->taskRepository->find($taskId);

        if (!$task) {
            throw new \InvalidArgumentException('Task not found');
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function listTasks()
    {
        return $this->taskRepository->findAll();
    }

    public function getTaskById(int $id): ?Task
    {
        return $this->taskRepository->find($id);
    }
}
