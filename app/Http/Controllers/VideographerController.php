<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesMediaUploads;
use App\Models\Videographer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideographerController extends Controller
{
    use HandlesMediaUploads;

    public function index(): JsonResponse
    {
        return response()->json(
            Videographer::query()->latest()->get()
        );
    }

    public function show(Videographer $videographer): JsonResponse
    {
        return response()->json($videographer);
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
            'links' => ['nullable', 'array'],
            'links.*' => ['nullable', 'string', 'max:2048'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'videographers/profile-photos'
        );

        $videographer = Videographer::query()->create($data);

        return response()->json($videographer, 201);
    }

    public function update(Request $request, Videographer $videographer): JsonResponse
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
            'links' => ['nullable', 'array'],
            'links.*' => ['nullable', 'string', 'max:2048'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'videographers/profile-photos',
            $videographer->profile_photo
        );

        $videographer->update($data);

        return response()->json($videographer);
    }

    public function destroy(Videographer $videographer): JsonResponse
    {
        $videographer->delete();

        return response()->json([
            'message' => 'Videographer deleted successfully.',
        ]);
    }
}
