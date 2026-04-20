<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Person Manager API',
    version: '1.0.0',
    description: '## Personnel Management System REST API

### Authentication
Every request must include two headers:
- `X-Auth-Key`: A 25-character authentication key
- `X-Organization`: Must be exactly **Door Soft**

### Rate Limiting
Maximum **5 requests per minute** per IP address.

### Error Codes
- AUTH_001 (401): Missing or invalid authentication key
- AUTH_002 (403): Incorrect organization name
- REC_001 (404): Person ID not found
- ROUTE_001 (404): API endpoint not found
- RATE_001 (429): Rate limit exceeded (max 5/min)
- SERVER_001 (500): Unexpected server error',
)]
#[OA\Server(url: '/api', description: 'API Server')]
#[OA\SecurityScheme(
    securityScheme: 'ApiKeyAuth',
    type: 'apiKey',
    in: 'header',
    name: 'X-Auth-Key',
    description: '25-character authentication key',
)]
#[OA\Tag(name: 'Persons', description: 'Endpoints for retrieving person records')]
class SwaggerInfo {}