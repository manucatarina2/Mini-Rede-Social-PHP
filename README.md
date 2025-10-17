# XoXo - Mini Rede Social em PHP

Este projeto é uma aplicação web de rede social simplificada, desenvolvida em PHP, que permite que usuários se cadastrem, publiquem mensagens com ou sem imagem, curtam e comentem postagens, visualizem notificações e conversem via chat privado.

## Funcionalidades

### 1. Autenticação de Usuários
- Cadastro com nome, e-mail, senha e foto de perfil (opcional)
- Login seguro com verificação de sessão
- Logout e proteção de páginas por sessão ativa

### 2. Perfil de Usuário
- Página de perfil com dados pessoais e foto
- Edição de informações do perfil
- Visualização de publicações feitas pelo usuário

### 3. Feed de Postagens
- Criação de posts com texto e imagem (opcional)
- Exibição das postagens em ordem cronológica decrescente
- Exibição do nome, foto e data de cada postagem
- Curtidas com contador e sistema de "curtir/descurtir"
- Comentários visíveis logo abaixo de cada post

### 4. Comentários
- Cada postagem pode receber comentários ilimitados
- Nome e horário do autor de cada comentário são exibidos
- Os comentários são exibidos em tempo real via AJAX

### 5. Chat Privado entre Usuários
- Envio de mensagens privadas entre usuários
- Atualização em tempo real usando polling com AJAX
- Exibição de mensagens não lidas na barra lateral
- Contagem de mensagens não lidas por usuário

### 6. Notificações
- Sistema de notificações para ações importantes (ex: novos seguidores, mensagens, etc.)
- Contador de notificações não lidas
- Atualização periódica via AJAX

### 7. Pesquisa em Tempo Real
- Pesquisa por usuários ou publicações
- Resultados exibidos dinamicamente enquanto o usuário digita
- Popup com informações do perfil do usuário ao clicar em um resultado

## Tecnologias Utilizadas

- **Backend**: PHP (procedural) com MySQL
- **Frontend**: HTML5, CSS3 (com foco em responsividade), JavaScript
- **Banco de Dados**: MySQL/MariaDB
- **Comunicação Assíncrona**: AJAX (via Fetch API)
- **Sessões**: PHP Sessions para autenticação e controle de login


## Requisitos para Rodar o Projeto

- PHP 7.4 ou superior
- Servidor Apache (ex: XAMPP, WAMP ou LAMP)
- MySQL ou MariaDB
- Navegador moderno (compatível com JavaScript ES6)

## Instruções de Instalação

1. Clone ou baixe o repositório
2. Importe o banco de dados `rede_social.sql` (caso disponível)
3. Configure o arquivo `conexao.php` com os dados do seu banco:
   ```php
   $conn = new mysqli("localhost", "usuario", "senha", "nome_do_banco");

http://localhost/xoxo/index.php


