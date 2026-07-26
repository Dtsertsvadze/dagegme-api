<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasProfilePhotoUrl
{
    public function initializeHasProfilePhotoUrl(): void
    {
        $this->append('profile_photo_url');
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $path = $this->profile_photo;

            if (! is_string($path) || $path === '') {
                return null;
            }

            if (Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }

            return Storage::disk((string) config('media.disk'))->url($path);
        });
    }
}
