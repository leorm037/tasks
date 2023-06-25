# features/task/task_excluir.feature
# language: pt

Funcionalidade:
    Eu, como usuário
    Quero excluir minhas tarefas
    Para gerenciar minhas atividades

    Regras:
        - Somente o dono da tarefa pode exclui-la

    Contexto:
        Dado que temos o usuário <username>, com nome <name> e e-mail <email>
            | username | name                       | email           |
            | leorm    | Leonardo Rodrigues Marques | leorm@teste.com |
            | jose     | José Antonio Guedes        | jose@teste.com  |

    Cenário: excluir tarefa do usuário
        Dado que o usuário <username> tem a tarefa <task_name>
            | username | task_name               |
            | leorm    | Pagar conta de energia  |
            | leorm    | Pagar conta de Internet |
            | leorm    | Pagar conta de água     |
        Quando excluir a tarefa "Pagar conta de energia" do usuário "leorm"
        Então será apresentado as tarefas <task_name>:
            | task_name               |
            | Pagar conta de água     |
            | Pagar conta de Internet |

    Cenário: excluir tarefa de outro usuário
        Dado que o usuário <username> tem a tarefa <task_name>
            | username | task_name              |
            | leorm    | Pagar conta de energia |
            | jose     | Prova de matemática    |
        Quando excluir a tarefa "Prova de matemática" do usuário "leorm"
        Então será apresentado mensagem de erro
        E a tarefa "Prova de matemática" do usuário "jose" não deve ser excluída
