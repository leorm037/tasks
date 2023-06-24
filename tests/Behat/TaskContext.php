<?php

namespace App\Tests\Behat;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class TaskContext implements Context
{

    /** @var Task[] $tasks */
    private $tasks;
    private UserRepository $userRepository;
    private TaskRepository $taskRepository;
    private UserPasswordHasherInterface $userPasswordHasherInterface;

    public function __construct(
            UserRepository $userRepository,
            TaskRepository $taskRepository,
            UserPasswordHasherInterface $userPasswordHasherInterface
    )
    {
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    /**
     * @beforeScenario
     */
    public function beforeScenario()
    {
        foreach ($this->userRepository->findAll() as $user) {
            $this->userRepository->remove($user, true);
        }
    }

    /**
     * @Given que temos o usuário <username>, com nome <name> e e-mail <email>
     */
    public function queTemosOUsuarioUsernameComNomeNameEEMailEmail(TableNode $table)
    {
        foreach ($table as $row) {
            $user = new User();
            $user->setUsername($row['username'])
                    ->setName($row['name'])
                    ->setEmail($row['email'])
            ;

            $password = $this->userPasswordHasherInterface->hashPassword($user, $row['username']);
            $user->setPassword($password);

            $this->userRepository->save($user, true);

            Assert::assertNotNull($user->getId());
        }
    }

    /**
     * @Given que o usuário <username> tem a tarefa <task_name>
     */
    public function queOUsuarioUsernameTemATarefaTaskName(TableNode $table)
    {
        $user = null;

        foreach ($table as $row) {
            if (null == $user || $row['username'] == $user->getUsername()) {
                $user = $this->userRepository->findByUsername($row['username']);
            }

            $task = new Task();
            $task->setName($row['task_name'])
                    ->setOwner($user)
            ;

            $this->taskRepository->save($task, true);

            Assert::assertNotNull($task->getId());
        }
    }

    /**
     * @When consultar as tarefas do usuário :username
     */
    public function consultarAsTarefasDoUsuario($username)
    {
        $user = $this->userRepository->findByUsername($username);
        $this->tasks = $this->taskRepository->findByOwner($user);

        Assert::assertIsArray($this->tasks);
    }

    /**
     * @Then será apresentado as tarefas <task_name>:
     */
    public function seraApresentadoAsTarefasTaskName(TableNode $table)
    {
        $i = 0;

        foreach ($table as $row) {
            $expected = $row['task_name'];
            $actual = $this->tasks[$i]->getName();

            Assert::assertEquals($expected, $actual);

            $i++;
        }
    }

    /**
     * @Given que o usuário <username> com a tarefa <task_name> com status de feito <done>
     */
    public function queOUsuarioUsernameComATarefaTaskNameComStatusDeFeitoDone(TableNode $table)
    {
        $user = null;

        foreach ($table as $row) {
            if (null == $user || $row['username'] == $user->getUsername()) {
                $user = $this->userRepository->findByUsername($row['username']);
            }

            $task = new Task();
            $task->setName($row['task_name'])
                    ->setOwner($user)
                    ->setDone(("Sim" === $row['done']) ? true : false)
            ;

            $this->taskRepository->save($task, true);

            Assert::assertNotNull($task->getId());
        }
    }
}
