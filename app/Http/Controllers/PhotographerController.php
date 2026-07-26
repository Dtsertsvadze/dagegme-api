<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesMediaUploads;
use App\Models\Photographer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhotographerController extends Controller
{
    use HandlesMediaUploads;

    public function index(): JsonResponse
    {
        return response()->json(
            Photographer::query()->with('photos')->latest()->get()
        );
    }

    public function show(Photographer $photographer): JsonResponse
    {
        $photographer->load('photos');

        return response()->json($photographer);
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
            'photos' => ['nullable'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'photographers/profile-photos'
        );

        $photographer = Photographer::query()->create($data);

        $photoPaths = $this->resolveMultipleMediaPaths(
            $request,
            'photos',
            'photographers/photos'
        );

        if ($photoPaths !== []) {
            $photographer->photos()->createMany(
                collect($photoPaths)
                    ->map(fn (string $path): array => ['photo_path' => $path])
                    ->all()
            );
        }

        return response()->json(
            $photographer->load('photos'),
            201
        );
    }

    public function update(Request $request, Photographer $photographer): JsonResponse
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
            'photos' => ['nullable'],
            'replace_photos' => ['nullable', 'boolean'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'photographers/profile-photos',
            $photographer->profile_photo
        );

        $replacePhotos = (bool) ($data['replace_photos'] ?? false);
        unset($data['replace_photos']);

        $photographer->update($data);

        $photoPaths = $this->resolveMultipleMediaPaths(
            $request,
            'photos',
            'photographers/photos'
        );

        if ($replacePhotos) {
            $photographer->photos()->delete();
        }

        if ($photoPaths !== []) {
            $photographer->photos()->createMany(
                collect($photoPaths)
                    ->map(fn (string $path): array => ['photo_path' => $path])
                    ->all()
            );
        }

        return response()->json($photographer->load('photos'));
    }

    public function destroy(Photographer $photographer): JsonResponse
    {
        $photographer->delete();

        return response()->json([
            'message' => 'Photographer deleted successfully.',
        ]);
    }
}
