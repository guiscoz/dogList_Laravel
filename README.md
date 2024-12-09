# Sobre o projeto


DogList é um projeto de cadastro de cachorros. Primeiramente é necessário criar uma conta para depois cadastrar seus cães. Suas funções podem ser testadas no Postman ou em um projeto contendo o frontend do projeto. Ele foi criado no Vue Js e está disponível no link abaixo:
```
https://github.com/guiscoz/dogList_Vue
```


# Para rodar o projeto após clonar


Primeiramente use o comando "composer install" no terminal do VScode na pasta do projeto.

Em seguida crie um arquivo '.env' e configure com base no arquivo '.env.example'.

Então crie um banco de dados para o projeto. Não precisa criar as tabelas porque elas já vão ser geradas com o comando: 
```
php artisan migrate
```


Caso queira usar os seeders do projeto, verifique o que está comentado no arquivo 'DatabaseSeeder.php' e decide o que vai deixar assim ou não.

Por fim, para rodar o projeto, utilize este comando: php artisan serve


# Rotas disponíveis


Aqui será listado todas as rotas contendo as funções de requisição de api do projeto, contendo sua url e os campos a serem preenchidos. Nota que a base da url é "http://localhost:8080/", mas isso é porque o projeto está sendo executado com o comando "php artisan serve". Caso você for testar o projeto com o Laragon ou de alguma outra a forma, a base da url pode ser diferente.


## Autenticação:


[POST - retorna um JWT] Cadastro de usuário (http://localhost:8080/api/register):
```
name:
email:
password:
```


[POST - retorna um JWT] Login (http://127.0.0.1:8000/api/login):
```
name:
password:
```


Essas rotas não precisam de formulário, mas requerem um JWT:
```
[GET] http://127.0.0.1:8000/api/get_user
[GET] http://127.0.0.1:8000/api/logout
```


## Cachorros


[POST - requer um JWT] Cadastro de cachorro (http://127.0.0.1:8000/api/dog_list/store):
```
name: 
breed: 
gender: [F ou M]
is_public: [1 (true) ou 0 (false)]
img_path: [upload de image]
```


[POST - requer um JWT] Edição de cachorro (http://127.0.0.1:8000/api/dog_list/update/{id}):
```
name: 
breed: 
gender: [F ou M]
is_public: [1 (true) ou 0 (false)]
img_path: [upload de image]
```


Essas rotas não precisam de formulário:
```
[GET - requer JWT] http://127.0.0.1:8000/api/current_dog/{id}
[DELETE - requer JWT] http://127.0.0.1:8000/api/dog_list/delete/{id}
[PUT - requer JWT] http://127.0.0.1:8000/api/dog_list/delete_image/{id}
[GET - não precisa de JWT] http://127.0.0.1:8000/api/dog_list
```


# Testes automatizados


Há dois arquivos contendo as funções de testes unitários. Um deles é para testar a autenticação de usuários e outro para testar as função de cadastro de cachorro. No momento, somente os testes de feature estão prontos. É possível executa-los com os seguintes comandos:
```
Todas as funções:   php artisan test
Autenticação:       php artisan test tests/Feature/AuthControllerFeatureTest.php
Cadastro de cães:   php artisan test tests/Feature/DogsControllerFeatureTest.php
```



# Swagger

O Swagger é um recurso que documenta e auxilia nos testes da API, possibilitando testar manuamente cada rota em uma única página. Sua URL será definida pela variável L5_SWAGGER_CONST_HOST do arquivo '.env':
```
# Laragon
# L5_SWAGGER_CONST_HOST=http://doglist_laravel.test:8080
# Artisan Server
# L5_SWAGGER_CONST_HOST=http://127.0.0.1:8000
``` 
Depois basta digitar 'api/documentation' no final da URL para acessar o Swagger.