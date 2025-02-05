# Changelog

Neste arquivo será registrado todas as atualizações no backend do projeto Dog List.

## 2022

Este é o ano em que o projeto foi criado com o objeto de enriquecer meu portfólio.

### 21/11

Neste dia foram feitos os primeiros commits do projeto. No momento foi possível fazer autenticação por JWT, fazer cadastro de cachorros, upload de imagens, editar e remover cães cadastrados. Tendo um Controller para autenticação e outro para os cachorros.

### 22/11

Foi criado uma foreign key para a tabela de cachorros chamado 'user_id' para atrelar ao usuário autenticado. Além disso, tem uma função a mais no AuthController para pegar os dados do mesmo usuário.

### 23/11

Houve um problema para fazer upload de arquivos e foi necessários instalar o laravel-cors para resolver.

### 24/11

Na função get_user que retorna os dados do usuário autenticado é possível receber os dados de seus cachorros também. Ao remover um cão no banco de dados, a imagem atrelada a ele também é removida. 

### 29/11

Adição da paginação na rota de perfil para os cachorros atrelados ao usuário.

### 02/12

A partir desse dia, só é permitido acessar as rotas relacionadas aos cães quem estiver autenticado.

### 09/12

Foi adicionado funções de relacionamentos nos modelos usados no projeto.

### 12/12

Foi criado uma função separada somente para excluir imagens.

### 20/12

As funções de relacionamento foram atualizadas.

## 2024

Dois anos após sua criação, foi notado a necessidade de fazer alguns ajustes no projeto para melhora-lo.

### 03/04

Foram criados testes automatizados para as funções do AuthController e DogsControllers.

### 06/05

O arquivo readme foi atualizado para explicar o que é o projeto e dar instruções de como usar.

### 02/12

Foram criados separados para cada tipo de teste. Um de feature para autenticação e outro para o controller de cães, um de unidade para autenicação e outro para os cachorros. 

### 03/12

Foi adicionado um arquivo de changelog para registrar as atualizações do projeto e os testes de features foram finalizados. Falta criar os testes unitários.

### 09/12

Foi instalado o pacote l5-swagger no projeto para documentar a API e testar cada rota através do Swagger. Para utilizar este recurso foi necessário criar mais uma variável de ambiente chamada 'L5_SWAGGER_CONST_HOST' no arquivo '.env'. Depois houve alterações na url de algumas rotas por uma questão estética. Essas atividades foram registradas em uma nova branch chamada 'swagger'. No momento só faltar fazer as rotas com JWT funcionarem neste pacote.

### 10/12

Agora é possível testar os métodos que requerem um JWT e também da para enviar arquivos na função de cadastrar um novo cachorro. O único problema no momento é que o mesmo não está acontecendo com o método de editar os dados do cachorro.

### 11/12

Todos os métodos dos controllers estão funcionando. Depois foi criado o arquivo SwaggerConfig para armazenar as anotações @OA\Info e @OA\SecurityScheme porque elas são globais, ou seja, serão usadas em ambos os controllers. Por fim, houve alterações na descrição dos métodos para orientar melhor o usuário.

### 12/12

Merge da branch swagger com a main.

### 17/12

Houve alterações no arquivo de Cors para permitir que somente requisições vindo do domínio de desenvolvimento fossem permitidas. Este arquivo também sofreu mudanças por questão de organização, deixando claro em qual ano foi iniciado e qual passou a ter ajustes. Além disso, foi criado um middleware chamado 'VerifyDogOwner' que servirá para comparar o valor de user_id do cachorro com o id do usuário autenticado, tirando a necessidade de fazer esta verificação diretamente no DogController. Isso será útil nas rotas com o parametro 'id' que evitará o acesso à cachorros de outros usuários. Por fim, teve a adição no arquivo 'README.md' para informar sobre o domínio permitido pelo Cors.

## 2025

Neste ano também foi dedicado para fazer mais ajustes e além disso, adicionar novas features.

### 05/02

Devido a vulnerabilidades encontradas no pacote nesbot/carbon e league/commonmark, eles tiveram que ser atualizados. O pacote fruitcake/laravel-cors foi removido do projeto por não estar sendo utilizado. Por fim, foi criado um arquivo de docke-compose com o Sail para gerar um ambiente Docker.