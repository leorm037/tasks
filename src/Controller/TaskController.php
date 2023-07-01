<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskController extends AbstractController
{

    private TaskRepository $taskRepository;
    private TranslatorInterface $translator;

    public function __construct(
            TaskRepository $taskRepository,
            TranslatorInterface $translator
    )
    {
        $this->taskRepository = $taskRepository;
        $this->translator = $translator;
    }

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
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $owner = $this->getUser();
            $task->setOwner($owner);

            $taskRepository->save($task, true);

            $this->addFlash('success', $this->translator->trans('task.new.sucess', ['name' => $task->getName()]));

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
                    'task' => $task,
                    'form' => $form->createView(),
        ]);
    }

    public function edit(Request $request, Task $task): Response
    {
        $this->isGranted(TaskVoter::EDIT, $task);

        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskRepository->save($task, true);

            $this->addFlash('success', $this->translator->trans('task.edit.sucess', ['name' => $task->getName()]));

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
                    'task' => $task,
                    'form' => $form->createView(),
        ]);
    }

    public function search(Request $request): Response
    {
        $name = $request->request->get('name');
        $description = $request->request->get('description');
        
        $user = $this->getUser();
            
        $tasks = $this->taskRepository->search($user, $name, $description);
        
        return $this->render('task/search.html.twig', compact('name', 'description', 'tasks'));
    }

    public function delete(Task $task, TaskRepository $taskRepository): Response
    {
        $this->isGranted(TaskVoter::DELETE, $task);

        $taskRepository->remove($task, true);

        $this->addFlash('success', $this->translator->trans('task.delete.sucess', ['name' => $task->getName()]));

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }
}
