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
            | username | task_name               |
            | leorm    | Pagar conta de água     |
            | leorm    | Pagar conta de energia  |
            | leorm    | Pagar conta de Internet |
            | jose     | Deve de geografia       |
            | jose     | Dever de história       |
            | jose     | Dever de matemática     |
        Quando consultar as tarefas do usuário "leorm"
        Então será apresentado as tarefas <task_name>:
            | task_name               |
            | Pagar conta de energia  |
            | Pagar conta de Internet |
            | Pagar conta de água     |

    Cenário: tarefas concluídas
        Dado que o usuário <username> com a tarefa <task_name> com status de feito <done>
            | username | task_name              | done |
            | leorm    | Pagar conta de energia | Não  |
            | leorm    | Pagar conta de água    | Sim  |
            | leorm    | Dever de história      | Não  |
            | jose     | Deve de geografia      | Sim  |
            | jose     | Dever de matemática II | Não  |
            | jose     | Dever de português     | Não  |
        Quando consultar as tarefas do usuário "jose"
        Então será apresentado as tarefas <task_name>:
            | task_name              |
            | Dever de matemática II |
            | Dever de português     |
