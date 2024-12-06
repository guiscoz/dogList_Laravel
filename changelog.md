# Changelog

Neste arquivo será registrado todas as atualizações neste projeto

## 21/11/2022

Neste dia foram feitos os primeiros commits do projeto. No momento foi possível fazer autenticação por JWT, fazer cadastro de cachorros, upload de imagens, editar e remover cães cadastrados. Tendo um Controller para autenticação e outro para os cachorros.

## 22/11/2022

Foi criado uma foreign key para a tabela de cachorros chamado 'user_id' para atrelar ao usuário autenticado. Além disso, tem uma função a mais no AuthController para pegar os dados do mesmo usuário.

## 23/11/2022

Houve um problema para fazer upload de arquivos e foi necessários instalar o laravel-cors para resolver.

## 24/11/2022

Na função get_user que retorna os dados do usuário autenticado é possível receber os dados de seus cachorros também. Ao remover um cão no banco de dados, a imagem atrelada a ele também é removida. 

## 29/11/2022

Adição da paginação na rota de perfil para os cachorros atrelados ao usuário.

## 02/12/2022

A partir desse dia, só é permitido acessar as rotas relacionadas aos cães quem estiver autenticado.

## 09/12/2022

Foi adicionado funções de relacionamentos nos modelos usados no projeto.

## 12/12/2022

Foi criado uma função separada somente para excluir imagens.

## 20/12/2022

As funções de relacionamento foram atualizadas.

## 03/04/2024

Foram criados testes automatizados para as funções do AuthController e DogsControllers.

## 06/05/2024

O arquivo readme foi atualizado para explicar o que é o projeto e dar instruções de como usar.

## 02/12/2024

Foram criados separados para cada tipo de teste. Um de feature para autenticação e outro para o controller de cães, um de unidade para autenicação e outro para os cachorros. 

## 03/12/2024

Foi adicionado um arquivo de changelog para registrar as atualizações do projeto e os testes de features foram finalizados. Falta criar os testes unitários.