<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesMediaUploads;
use App\Models\Band;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BandController extends Controller
{
    use HandlesMediaUploads;

    public function index(): JsonResponse
    {
        return response()->json(
            Band::query()->latest()->get()
        );
    }

    public function show(Band $band): JsonResponse
    {
        return response()->json($band);
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
            'bands/profile-photos'
        );

        $band = Band::query()->create($data);

        return response()->json($band, 201);
    }

    public function update(Request $request, Band $band): JsonResponse
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
            'bands/profile-photos',
            $band->profile_photo
        );

        $band->update($data);

        return response()->json($band);
    }

    public function destroy(Band $band): JsonResponse
    {
        $band->delete();

        return response()->json([
            'message' => 'Band deleted successfully.',
        ]);
    }
}
