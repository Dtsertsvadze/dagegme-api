<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesMediaUploads;
use App\Models\RentalCar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalCarController extends Controller
{
    use HandlesMediaUploads;

    public function index(): JsonResponse
    {
        return response()->json(
            RentalCar::query()->with('photos')->latest()->get()
        );
    }

    public function show(RentalCar $rentalCar): JsonResponse
    {
        $rentalCar->load('photos');

        return response()->json($rentalCar);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'model' => ['required', 'string', 'max:255'],
            'mark' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'description' => ['nullable', 'array:en,ka'],
            'description.en' => ['required_with:description', 'string'],
            'description.ka' => ['required_with:description', 'string'],
            'city' => ['required', 'array:en,ka'],
            'city.en' => ['required', 'string', 'max:255'],
            'city.ka' => ['required', 'string', 'max:255'],
            'photos' => ['nullable'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'rental-cars/profile-photos'
        );

        $rentalCar = RentalCar::query()->create($data);

        $photoPaths = $this->resolveMultipleMediaPaths(
            $request,
            'photos',
            'rental-cars/photos'
        );

        if ($photoPaths !== []) {
            $rentalCar->photos()->createMany(
                collect($photoPaths)
                    ->map(fn (string $path): array => ['photo_path' => $path])
                    ->all()
            );
        }

        return response()->json($rentalCar->load('photos'), 201);
    }

    public function update(Request $request, RentalCar $rentalCar): JsonResponse
    {
        $data = $request->validate([
            'model' => ['sometimes', 'required', 'string', 'max:255'],
            'mark' => ['sometimes', 'required', 'string', 'max:255'],
            'year' => ['sometimes', 'required', 'integer', 'min:1900', 'max:2100'],
            'description' => ['sometimes', 'nullable', 'array:en,ka'],
            'description.en' => ['required_with:description', 'string'],
            'description.ka' => ['required_with:description', 'string'],
            'city' => ['sometimes', 'required', 'array:en,ka'],
            'city.en' => ['required_with:city', 'string', 'max:255'],
            'city.ka' => ['required_with:city', 'string', 'max:255'],
            'photos' => ['nullable'],
            'replace_photos' => ['nullable', 'boolean'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'rental-cars/profile-photos',
            $rentalCar->profile_photo
        );

        $replacePhotos = (bool) ($data['replace_photos'] ?? false);
        unset($data['replace_photos']);

        $rentalCar->update($data);

        $photoPaths = $this->resolveMultipleMediaPaths(
            $request,
            'photos',
            'rental-cars/photos'
        );

        if ($replacePhotos) {
            $rentalCar->photos()->delete();
        }

        if ($photoPaths !== []) {
            $rentalCar->photos()->createMany(
                collect($photoPaths)
                    ->map(fn (string $path): array => ['photo_path' => $path])
                    ->all()
            );
        }

        return response()->json($rentalCar->load('photos'));
    }

    public function destroy(RentalCar $rentalCar): JsonResponse
    {
        $rentalCar->delete();

        return response()->json([
            'message' => 'Rental car deleted successfully.',
        ]);
    }
}
