<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class ApiPersonController extends Controller
{
    #[OA\Get(
        path: '/persons',
        operationId: 'getPersons',
        summary: 'Retrieve persons',
        description: 'Returns all persons or a specific person by their unique 6-digit ID. Photo name and storage path are excluded from the response.',
        tags: ['Persons'],
        parameters: [
            new OA\Parameter(
                name: 'X-Auth-Key',
                in: 'header',
                required: true,
                description: '25-character authentication key',
                schema: new OA\Schema(type: 'string', minLength: 25, maxLength: 25, example: 'ABCDE12345FGHIJ67890KLMNO')
            ),
            new OA\Parameter(
                name: 'X-Organization',
                in: 'header',
                required: true,
                description: 'Organization name — must be exactly: Door Soft',
                schema: new OA\Schema(type: 'string', example: 'Door Soft')
            ),
            new OA\Parameter(
                name: 'id',
                in: 'query',
                required: false,
                description: 'Optional 6-digit unique person ID. Omit to retrieve all persons.',
                schema: new OA\Schema(type: 'string', minLength: 6, maxLength: 6, example: '145289')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Request successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'total', type: 'integer', example: 2),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'string', example: '145289'),
                                    new OA\Property(property: 'full_name', type: 'string', example: 'Denzel Washington'),
                                    new OA\Property(property: 'full_address', type: 'string', example: '221b Baker St, London NW1 6XE, United Kingdom'),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'AUTH_001 — Missing or invalid authentication key',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'error_code', type: 'string', example: 'AUTH_001'),
                        new OA\Property(property: 'message', type: 'string', example: 'Invalid or missing authentication key.'),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'AUTH_002 — Incorrect organization name',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'error_code', type: 'string', example: 'AUTH_002'),
                        new OA\Property(property: 'message', type: 'string', example: 'Organization name is incorrect.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'REC_001 — Person not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'error_code', type: 'string', example: 'REC_001'),
                        new OA\Property(property: 'message', type: 'string', example: 'No record found with the given ID.'),
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: 'RATE_001 — Too many requests',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'error_code', type: 'string', example: 'RATE_001'),
                        new OA\Property(property: 'message', type: 'string', example: 'Too many requests. Maximum 5 requests per minute allowed.'),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'SERVER_001 — Unexpected server error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'error_code', type: 'string', example: 'SERVER_001'),
                        new OA\Property(property: 'message', type: 'string', example: 'An unexpected server error occurred.'),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request)
    {
        $path    = storage_path('app/persons.json');
        $persons = file_exists($path)
            ? json_decode(file_get_contents($path), true) ?? []
            : [];

        // ── Specific person by ID ─────────────────────────────────────
        if ($request->filled('id')) {
            $person = collect($persons)->firstWhere('id', $request->input('id'));

            if (! $person) {
                return response()->json([
                    'success'    => false,
                    'error_code' => 'REC_001',
                    'message'    => 'No record found with the given ID.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'total'   => 1,
                'data'    => [$this->sanitizeForApi($person)],
            ]);
        }

        // ── All persons ───────────────────────────────────────────────
        $data = collect($persons)
            ->map(fn($p) => $this->sanitizeForApi($p))
            ->values();

        return response()->json([
            'success' => true,
            'total'   => $data->count(),
            'data'    => $data,
        ]);
    }

    // ── Remove photo fields from API response ─────────────────────────
    private function sanitizeForApi(array $person): array
    {
        return [
            'id'           => $person['id'],
            'full_name'    => $person['full_name'],
            'full_address' => $person['full_address'],
        ];
    }
}