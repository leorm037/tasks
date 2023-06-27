<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{

    private LoggerInterface $logger;

    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Task;
    }

    /**
     * 
     * @param string $attribute
     * @param Task $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->taskNotDone($subject, $user) && $this->ownerTask($subject, $user);

            case self::DELETE:
                return $this->ownerTask($subject, $user);
        }

        return false;
    }

    /**
     * 
     * @param Task $task
     * @param User $user
     * @return bool
     */
    private function taskNotDone(Task $task, User $user): bool
    {
        if (false === $task->isDone()) {
            return true;
        }

        $this->logger->alert("Tarefa concluída não pode ser editada.", [
            'task_id' => $task->getId(),
            'task_name' => $task->getName(),
            'User' => $user->getUsername()
        ]);

        return false;
    }

    /**
     * 
     * @param Task $task
     * @param User $user
     * @return bool
     */
    private function ownerTask(Task $task, User $user): bool
    {
        if ($user !== $task->getOwner()) {
            $this->logger->alert("Tarefa concluída não pode ser editada.", [
                'task_id' => $task->getId(),
                'task_name' => $task->getName(),
                'User' => $user->getUsername()
            ]);

            return false;
        }

        return true;
    }
}
