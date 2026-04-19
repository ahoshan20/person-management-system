<?php
namespace App\Http\Controllers;

use App\Services\PersonService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonController extends Controller
{
    public function __construct(
        private PersonService $personService,
        private ImageService $imageService
    ) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);

        return response()->json(
            $this->personService->paginate($perPage, $page)
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name'    => 'required|string|max:150',
            'full_address' => 'required|string|max:500',
            'photo'        => 'required|file',
        ], [
            'full_name.required'    => 'Full Name is required.',
            'full_name.max'         => 'Full Name must not exceed 150 characters.',
            'full_address.required' => 'Full Address is required.',
            'full_address.max'      => 'Full Address must not exceed 500 characters.',
            'photo.required'        => 'Photo is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $imageData = $this->imageService->validateAndStore($request->file('photo'));
        } catch (\Exception $e) {
            return response()->json(['errors' => ['photo' => [$e->getMessage()]]], 422);
        }

        $person = $this->personService->add([
            'full_name'    => strip_tags(trim($request->full_name)),
            'full_address' => strip_tags(trim($request->full_address)),
            'photo'        => $imageData['photo'],
            'thumb'        => $imageData['thumb'],
        ]);

        return response()->json(['success' => true, 'person' => $person], 201);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'full_name'    => 'required|string|max:150',
            'full_address' => 'required|string|max:500',
            'photo'        => 'nullable|file',
        ], [
            'full_name.required'    => 'Full Name is required.',
            'full_name.max'         => 'Full Name must not exceed 150 characters.',
            'full_address.required' => 'Full Address is required.',
            'full_address.max'      => 'Full Address must not exceed 500 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $existing = $this->personService->find($id);
        if (!$existing) {
            return response()->json(['error' => 'Record not found.'], 404);
        }

        $updates = [
            'full_name'    => strip_tags(trim($request->full_name)),
            'full_address' => strip_tags(trim($request->full_address)),
        ];

        if ($request->hasFile('photo')) {
            try {
                $imageData = $this->imageService->validateAndStore($request->file('photo'));
                $this->imageService->delete($existing['photo']);
                $updates['photo'] = $imageData['photo'];
                $updates['thumb'] = $imageData['thumb'];
            } catch (\Exception $e) {
                return response()->json(['errors' => ['photo' => [$e->getMessage()]]], 422);
            }
        }

        $person = $this->personService->update($id, $updates);
        return response()->json(['success' => true, 'person' => $person]);
    }

    public function destroy(string $id)
    {
        $existing = $this->personService->find($id);
        if (!$existing) {
            return response()->json(['error' => 'Record not found.'], 404);
        }
        $this->imageService->delete($existing['photo']);
        $this->personService->delete($id);
        return response()->json(['success' => true]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        foreach ($ids as $id) {
            $existing = $this->personService->find($id);
            if ($existing) {
                $this->imageService->delete($existing['photo']);
            }
        }
        $this->personService->bulkDelete($ids);
        return response()->json(['success' => true]);
    }
}