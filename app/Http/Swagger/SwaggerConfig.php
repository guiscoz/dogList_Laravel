<?php

namespace App\Http\Swagger;

/**
 * @OA\Info(
 *     title="Dog List API",
 *     version="1.0.0",
 *     description="API para gerenciamento de usuários e cachorros"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearer",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Insira o token JWT no formato: Bearer {seu_token}"
 * )
 */
class SwaggerConfig
{
    /*
    Esta classe não precisa de conteúdo ou métodos porque foi criada para centralizar as anotações globais, 
    como @OA\Info e @OA\SecurityScheme.
    */ 
}
