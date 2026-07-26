<?php

namespace App\Http\Controllers;

use App\Models\Photographer;
use App\Models\PhotographerPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PhotographerPhotoController extends Controller
{
    public function destroy(
        Photographer $photographer,
        PhotographerPhoto $photo
    ): JsonResponse {
        $photoId = $photo->id;
        $photoPath = $photo->photo_path;

        $photo->delete();

        if (
            str_starts_with($photoPath, 'photographers/photos/')
            && ! PhotographerPhoto::query()->where('photo_path', $photoPath)->exists()
        ) {
            Storage::disk('public')->delete($photoPath);
        }

        return response()->json([
            'message' => 'Photo deleted successfully.',
            'deleted_photo_id' => $photoId,
        ]);
    }
}
