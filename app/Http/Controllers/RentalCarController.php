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
            RentalCar::query()->latest()->get()
        );
    }

    public function show(RentalCar $rentalCar): JsonResponse
    {
        return response()->json($rentalCar);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'model' => ['required', 'string', 'max:255'],
            'mark' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'city' => ['required', 'array:en,ka'],
            'city.en' => ['required', 'string', 'max:255'],
            'city.ka' => ['required', 'string', 'max:255'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'rental-cars/profile-photos'
        );

        $rentalCar = RentalCar::query()->create($data);

        return response()->json($rentalCar, 201);
    }

    public function update(Request $request, RentalCar $rentalCar): JsonResponse
    {
        $data = $request->validate([
            'model' => ['sometimes', 'required', 'string', 'max:255'],
            'mark' => ['sometimes', 'required', 'string', 'max:255'],
            'year' => ['sometimes', 'required', 'integer', 'min:1900', 'max:2100'],
            'city' => ['sometimes', 'required', 'array:en,ka'],
            'city.en' => ['required_with:city', 'string', 'max:255'],
            'city.ka' => ['required_with:city', 'string', 'max:255'],
        ]);

        $data['profile_photo'] = $this->resolveSingleMediaPath(
            $request,
            'profile_photo',
            'rental-cars/profile-photos',
            $rentalCar->profile_photo
        );

        $rentalCar->update($data);

        return response()->json($rentalCar);
    }

    public function destroy(RentalCar $rentalCar): JsonResponse
    {
        $rentalCar->delete();

        return response()->json([
            'message' => 'Rental car deleted successfully.',
        ]);
    }
}
