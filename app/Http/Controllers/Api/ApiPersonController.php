<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PersonService;
use Illuminate\Http\Request;

class ApiPersonController extends Controller
{
    public function __construct(private PersonService $personService) {}

    public function index(Request $request)
    {
        // Fetch all, strip photo fields
        $persons = array_map(function ($person) {
            unset($person['photo'], $person['thumb']);
            return $person;
        }, $this->personService->all());

        // Filter by ID if provided
        if ($request->has('id')) {
            $id = trim($request->input('id'));

            if (!preg_match('/^\d{6}$/', $id)) {
                return response()->json([
                    'success'    => false,
                    'error_code' => 'PARAM_001',
                    'message'    => 'Invalid ID format. ID must be a 6-digit number.',
                ], 400);
            }

            $found = array_values(array_filter($persons, fn($p) => $p['id'] === $id));

            if (empty($found)) {
                return response()->json([
                    'success'    => false,
                    'error_code' => 'DATA_001',
                    'message'    => 'No person found with the provided ID.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data'    => $found[0],
            ]);
        }

        return response()->json([
            'success' => true,
            'total'   => count($persons),
            'data'    => $persons,
        ]);
    }
}