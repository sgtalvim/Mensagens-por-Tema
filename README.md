# Mensagens-por-Tema
# Messages-by-topic

A small project using PHP and SQLite for registering, editing, and searching for messages categorized by topic. | Pequeno projeto em PHP + SQLite para cadastrar, editar e pesquisar mensagens
classificadas por tema.

## Requirements | Requisitos

- PHP 8.1 or higher with the `pdo_sqlite` and `sqlite3` extensions | PHP 8.1 ou superior com as extensões `pdo_sqlite` e `sqlite3`
- A web browser with internet access (Bootstrap is loaded via CDN) | Navegador com acesso à internet (Bootstrap é carregado via CDN)

## How to run | Como executar

At the root of the project: | Na raiz do projeto:

```bash
php -S localhost:8000
```

Access | Acesse <http://localhost:8000>.

The database is automatically created at `database/app.sqlite` upon the first request, already containing the `temas` and `mensagens` tables. | O banco é criado automaticamente em `database/app.sqlite` na primeira requisição,
já com as tabelas `temas` e `mensagens`.

## Structure

```
config.php            PDO/SQLite connection, migration, and utilities (CSRF, flash messages, escaping)
index.php             Home page with counters and latest messages
mensagens.php         List of messages (with filtering by topic)
mensagem-form.php     Message creation and editing (text + topic)
temas.php             List of topics
tema-form.php         Topic creation and editing
pesquisa.php          Search by topic: all messages or a random one
excluir.php           Deletion via POST (message or topic)
includes/             Header, footer, and menu
assets/               Custom CSS and JavaScript
database/             SQLite database generated at runtime (ignored by Git)
```

## Estrutura

```
config.php            Conexão PDO/SQLite, migração e utilitários (CSRF, flash, escape)
index.php             Página inicial com contadores e últimas mensagens
mensagens.php         Listagem de mensagens (com filtro por tema)
mensagem-form.php     Cadastro e edição de mensagem (texto + tema)
temas.php             Listagem de temas
tema-form.php         Cadastro e edição de tema
pesquisa.php          Pesquisa por tema: todas as mensagens ou uma aleatória
excluir.php           Exclusão via POST (mensagem ou tema)
includes/             Cabeçalho, rodapé e menu
assets/               CSS e JavaScript próprios
database/             Banco SQLite gerado em tempo de execução (ignorado no git)
```

## Features

- Message creation with text and topic selection.
- Topic creation, editing, and deletion (deleting a topic also removes its associated messages).
- Message editing and deletion.
- Search by topic with two modes: all messages for a given topic or a
random message.
- CSRF protection for forms and server-side validation.

## Funcionalidades

- Cadastro de mensagens com texto e seleção de tema.
- Cadastro, edição e exclusão de temas (excluir um tema remove suas mensagens).
- Edição e exclusão de mensagens.
- Pesquisa por tema com dois modos: todas as mensagens do tema ou uma
  mensagem aleatória.
- Proteção CSRF nos formulários e validação no servidor.
