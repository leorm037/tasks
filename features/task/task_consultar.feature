# features/task/task_consultar.feature
# language: pt

Funcionalidade:
    Eu, como usuário
    Quero consultar minhas tarefas
    Para saber mminhas atividades pendentes

    Regras:
        - Um usuário só deve consultar suas tarefas
        - Não deve ser apresentado as tarefas concluídas

    Contexto:
        Dado que temos o usuário <username>, com nome <name> e e-mail <email>
            | username | name                       | email           |
            | leorm    | Leonardo Rodrigues Marques | leorm@teste.com |
            | jose     | José Antonio Guedes        | jose@teste.com  |

    Cenário: tarefas do usuário
        Dado que o usuário <username> tem a tarefa <task_name>
            | username | task_name              |
            | leorm    | Pagar conta de energia |
            | leorm    | Pagar conta de água    |
            | jose     | Dever de história      |
            | jose     | Dever de matemática    |
            | jose     | Deve de geografia      |
        Quando consultar as tarefas do usuário "leorm"
        Então será apresentado para o <username> a tarefa <task_name>:
            | username | task_name              |
            | leorm    | Pagar conta de energia |
            | leorm    | Pagar conta de água    |

    Cenário: tarefas concluídas
        Dado que o usuário <username> com a tarefa <task_name> na situação <status>
            | username | task_name              | status  |
            | leorm    | Pagar conta de energia | Fazendo |
            | leorm    | Pagar conta de água    | Feito   |
            | leorm    | Dever de história      | Fazendo |
            | jose     | Dever de matemática    | Fazendo |
            | jose     | Deve de geografia      | Feito   |
        Quando consultar as tarefas em andamento do usuário "leorm"
        Então será apresentado para o <username> a tarefa <task_name>:
            | username | task_name              |
            | leorm    | Pagar conta de energia |
            | leorm    | Dever de história      |
