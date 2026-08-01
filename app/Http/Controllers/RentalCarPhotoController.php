<?php

namespace App\Http\Controllers;

use App\Models\RentalCar;
use App\Models\RentalCarPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class RentalCarPhotoController extends Controller
{
    public function destroy(
        RentalCar $rentalCar,
        RentalCarPhoto $photo
    ): JsonResponse {
        $photoId = $photo->id;
        $photoPath = $photo->photo_path;

        $photo->delete();

        if (
            str_starts_with($photoPath, 'rental-cars/photos/')
            && ! RentalCarPhoto::query()->where('photo_path', $photoPath)->exists()
        ) {
            Storage::disk((string) config('media.disk'))->delete($photoPath);
        }

        return response()->json([
            'message' => 'Photo deleted successfully.',
            'deleted_photo_id' => $photoId,
        ]);
    }
}
