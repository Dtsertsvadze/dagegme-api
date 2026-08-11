<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesMediaUploads;
use App\Models\Dj;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DjController extends Controller
{
    use HandlesMediaUploads;

    public function index(): JsonResponse
    {
        return response()->json(
            Dj::query()->latest()->get()
        );
    }

    public function show(Dj $dj): JsonResponse
    {
        return response()->json($dj);
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
            'links' => ['nullable', 'array'],
            'links.*' => ['nullable', 'string', 'max:2048'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'djs/profile-photos'
        );

        $dj = Dj::query()->create($data);

        return response()->json($dj, 201);
    }

    public function update(Request $request, Dj $dj): JsonResponse
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
            'links' => ['nullable', 'array'],
            'links.*' => ['nullable', 'string', 'max:2048'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'djs/profile-photos',
            $dj->profile_photo
        );

        $dj->update($data);

        return response()->json($dj);
    }

    public function destroy(Dj $dj): JsonResponse
    {
        $dj->delete();

        return response()->json([
            'message' => 'DJ deleted successfully.',
        ]);
    }
}
