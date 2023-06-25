<?php

namespace App\Tests\Behat;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Exception;
use PHPUnit\Framework\Assert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class TaskContext implements Context
{

    /** @var Task[] $tasks */
    private $tasks;
    private $errorMessage = null;
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

        $this->tasks = null;
        $this->errorMessage = null;
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
            if (null == $user || $row['username'] != $user->getUsername()) {
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

        Assert::assertTrue(count($this->tasks) > 0);
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
            if (null == $user || $row['username'] != $user->getUsername()) {
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

    /**
     * @Given que o usuário :username com a tarefa sem nome
     */
    public function queOUsuarioComATarefaSemNome($username)
    {
        $task = new Task();
        try {
            $this->taskRepository->save($task, true);
        } catch (NotNullConstraintViolationException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    /**
     * @When salvar
     */
    public function salvar()
    {
        
    }

    /**
     * @Then será apresentado mensagem de erro
     */
    public function seraApresentadoAMensagemDeErro()
    {
        Assert::assertNotNull($this->errorMessage);
    }

    /**
     * @Then a tarefa não será salva
     */
    public function aTarefaNaoSeraSalva()
    {
        $expected = 0;
        $actual = $this->taskRepository->count(['done' => false]);
        Assert::assertEquals($expected, $actual);
    }

    /**
     * @Given que a tarefa :task_name é nova
     */
    public function queATarefaENova($task_name)
    {
        $task = new Task();
        $task->setName($task_name);
        $task->setOwner($this->userRepository->findByUsername("leorm"));

        $this->taskRepository->save($task, true);
    }

    /**
     * @Then a tarefa :task_name deve ter a situação pronto :done
     */
    public function aTarefaDeveTerASituacaoPronto($task_name, $done)
    {
        $task = $this->taskRepository->findByName($task_name, $this->userRepository->findByUsername("leorm"));

        $expected = ("Sim" == $done) ? true : false;
        $actual = $task[0]->isDone();

        Assert::assertEquals($expected, $actual);
    }

    /**
     * @When excluir a tarefa :task_name do usuário :username
     */
    public function excluirATarefa($task_name, $username)
    {
        try {
            $user = $this->userRepository->findByUsername($username);
            $tasks = $this->taskRepository->findByName($task_name, $user);

            if (count($tasks) == 0) {
                $this->errorMessage = "Nenhuma tarefa será excluida";
            }

            foreach ($tasks as $task) {
                $this->taskRepository->remove($task, true);
            }

            $this->tasks = $this->taskRepository->findByOwner($user);
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }
    
    /**
     * @Then a tarefa :task_name do usuário :username não deve ser excluída
     */
    public function aTarefaDoUsuarioNaoDeveSerExcluida($task_name, $username)
    {
        $user = $this->userRepository->findByUsername($username);
        $tasks = $this->taskRepository->findByName($task_name, $user);
        
        /** @var Task $task */
        foreach($tasks as $task) {
            $expected = $task_name;
            $actual = $task->getName();
            
            Assert::assertEquals($expected, $actual);
        }
    }
}
