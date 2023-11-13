<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes\Info;
use OpenApi\Attributes\PathItem;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\SecurityScheme;

#[
    Info(
        version: '1.0.0',
        title: 'Home budget',
    ),
    PathItem(
        path: '/api',
    ),
    Schema(
        format: 'https',
    ),
    SecurityScheme(
        securityScheme: 'bearerAuth',
        type: 'http',
        name: 'bearerAuth',
        in: 'header',
        bearerFormat: 'JWT',
        scheme: 'bearer',
    )
]
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
}
