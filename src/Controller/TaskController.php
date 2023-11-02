<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/task', name: 'app_task')]
class TaskController extends AbstractController
{
    private $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->createTask($task);
            return $this->redirectToRoute('app_taskcreate');
        }

        return $this->render('task/update.html.twig', [
            'task_form' => $form->createView(),
        ]);
    }
    #[Route('/list', name: 'list')]
    public function list(TaskRepository $taskRepository, Request $request): Response
    {
        $tasks = $taskRepository->findAll();

        return $this->render('task/list.html.twig', [        
            'tasks' => $tasks,

        ]);
    }
    #[Route('/view/{id}', name: 'view')]
        public function view(int $id, TaskRepository $taskRepository): Response
        {
            $task = $taskRepository->find($id);
        
            if (!$task) {
                throw $this->createNotFoundException('Task not found');
            }
        
            return $this->render('task/view.html.twig', [
                'task' => $task,
            ]);
        }
        
    #[Route('/update/{id}', name: 'update')]
    public function update(int $id, Request $request, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->updateTask($task);
            return $this->redirectToRoute('app_task_view', ['id' => $task->getId()]);
        }

        return $this->render('task/update.html.twig', [
            'task' => $task,
            'task_form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'task_delete')]
    public function delete(int $id): Response
    {
        $this->taskService->deleteTask($id);

        return $this->redirectToRoute('task_delete');
    }
}
