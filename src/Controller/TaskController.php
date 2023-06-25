<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    public function index(TaskRepository $taskRepository): Response
    {
        $user = $this->getUser();

        return $this->render('task/index.html.twig', [
                    'tasks' => $taskRepository->findByOwner($user),
        ]);
    }

    public function new(Request $request, TaskRepository $taskRepository): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/new.html.twig', [
                    'task' => $task,
                    'form' => $form,
        ]);
    }

    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $this->isGranted(TaskVoter::EDIT, $task);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
                    'task' => $task,
                    'form' => $form,
        ]);
    }

    public function delete(Task $task, TaskRepository $taskRepository): Response
    {
        $this->isGranted(TaskVoter::DELETE, $task);

        $taskRepository->remove($task, true);

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }
}
