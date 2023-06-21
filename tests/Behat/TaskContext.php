<?php

namespace App\Tests\Behat;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\SituationRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Exception;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class TaskContext implements Context
{

    private KernelInterface $kernel;
    private UserRepository $userRepository;
    private TaskRepository $taskRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private $tasks;

    public function __construct(
            KernelInterface $kernel,
            UserRepository $userRepository,
            TaskRepository $taskRepository,
            UserPasswordHasherInterface $userPasswordHasher
    )
    {
        $this->kernel = $kernel;
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario($event)
    {
        foreach ($this->taskRepository->findAll() as $task) {
            $this->taskRepository->remove($task, true);
        }

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
                    ->setEmail($row['email']);

            $password = $this->userPasswordHasher->hashPassword($user, $user->getName());

            $user->setPassword($password);

            try {
                Assert::assertTrue(true);
            } catch (Exception $e) {
                Assert::assertTrue(false);
            }

            $this->userRepository->save($user, true);
        }
    }

    /**
     * @Given que o usuário <username> tem a tarefa <task_name>
     */
    public function queOUsuarioUsernameTemATarefaTaskName(TableNode $table)
    {
        $user = null;

        foreach ($table as $row) {
            if (null === $user || $user->getUsername() !== $row['username']) {
                $user = $this->userRepository->findByUsername($row['username']);
            }

            $task = new Task();
            $task->setName($row['task_name'])
                    ->setOwner($user)
                    ->setSituation($situation)
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
    }

    /**
     * @Then será apresentado para o <username> a tarefa <task_name>:
     */
    public function seraApresentadoParaOUsernameATarefaTaskName(TableNode $table)
    {
        foreach ($table as $row) {
            $condition = false;

            /** @var Task $task */
            foreach ($this->tasks as $task) {
                if ($task->getName() === $row['task_name'] && $task->getOwner()->getUsername() === $row['username']) {
                    $condition = true;
                    return;
                }
            }

            Assert::assertTrue($condition);
        }

        Assert::assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * @Given que o usuário <username> com a tarefa <task_name> na situação <status>
     */
    public function queOUsuarioUsernameComATarefaTaskNameNaSituacaoStatus(TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @When consultar as tarefas em andamento do usuário :arg1
     */
    public function consultarAsTarefasEmAndamentoDoUsuario($arg1)
    {
        throw new PendingException();
    }
}
