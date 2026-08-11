<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesMediaUploads;
use App\Models\Hall;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HallController extends Controller
{
    use HandlesMediaUploads;

    public function index(): JsonResponse
    {
        return response()->json(
            Hall::query()->latest()->get()
        );
    }

    public function show(Hall $hall): JsonResponse
    {
        return response()->json($hall);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'array:en,ka'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ka' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array:en,ka'],
            'description.en' => ['required_with:description', 'string'],
            'description.ka' => ['required_with:description', 'string'],
            'city' => ['required', 'array:en,ka'],
            'city.en' => ['required', 'string', 'max:255'],
            'city.ka' => ['required', 'string', 'max:255'],
            'vip' => ['nullable', 'boolean'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'halls/profile-photos'
        );

        $hall = Hall::query()->create($data);

        return response()->json($hall, 201);
    }

    public function update(Request $request, Hall $hall): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'array:en,ka'],
            'name.en' => ['required_with:name', 'string', 'max:255'],
            'name.ka' => ['required_with:name', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'array:en,ka'],
            'description.en' => ['required_with:description', 'string'],
            'description.ka' => ['required_with:description', 'string'],
            'city' => ['sometimes', 'required', 'array:en,ka'],
            'city.en' => ['required_with:city', 'string', 'max:255'],
            'city.ka' => ['required_with:city', 'string', 'max:255'],
            'vip' => ['nullable', 'boolean'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'halls/profile-photos',
            $hall->profile_photo
        );

        $hall->update($data);

        return response()->json($hall);
    }

    public function destroy(Hall $hall): JsonResponse
    {
        $hall->delete();

        return response()->json([
            'message' => 'Hall deleted successfully.',
        ]);
    }
}
