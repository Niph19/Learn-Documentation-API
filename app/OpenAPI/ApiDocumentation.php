<?php
    namespace App\OpenAPI;

    use OpenApi\Attributes as OA;

    #[OA\info(
        title: 'Dokumentasi API',
        version: '1.0.0',
        description: 'This is the API documentation for the application.',
    )]

    #[OA\server(
        url: 'http://localhost:8000',
        description: 'Local server'
    )]

    class ApiDocumentation
    {
        // This class is used to generate OpenAPI documentation using attributes.
    }
?>