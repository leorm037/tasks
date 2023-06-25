# features/task/task_incluir.feature
# language: pt

Funcionalidade:
    Eu, como usuário
    Quero incluir minhas tarefas
    Para saber mminhas atividades pendentes

    Regras:
        - É obrigatório informar o nome da tarefa
        - A situação de pronto no cadastro da tarefa deve ser não

    Contexto:
        Dado que temos o usuário <username>, com nome <name> e e-mail <email>
            | username | name                       | email           |
            | leorm    | Leonardo Rodrigues Marques | leorm@teste.com |
            | jose     | José Antonio Guedes        | jose@teste.com  |

    Cenário: tarefas do usuário com nome
        Dado que o usuário <username> tem a tarefa <task_name>
            | username | task_name               |
            | leorm    | Pagar conta de energia  |
            | leorm    | Pagar conta de Internet |
            | leorm    | Pagar conta de água     |
        Quando consultar as tarefas do usuário "leorm"
        Então será apresentado as tarefas <task_name>:
            | task_name               |
            | Pagar conta de água     |
            | Pagar conta de energia  |
            | Pagar conta de Internet |

    Cenário: tarefas do usuário sem nome
        Dado que o usuário "leorm" com a tarefa sem nome
        Quando salvar
        Então será apresentado mensagem de erro
        E a tarefa não será salva

    Cenário: nova tarefa cadastrada
        Dado que a tarefa "Pagar conta de energia" é nova
        Quando salvar
        Então a tarefa "Pagar conta de energia" deve ter a situação pronto "não"