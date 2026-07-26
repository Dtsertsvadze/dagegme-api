<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotographerPhoto extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $appends = [
        'photo_url',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'photographer_id',
        'photo_path',
    ];

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class);
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->photo_path === '') {
                return null;
            }

            if (Str::startsWith($this->photo_path, ['http://', 'https://'])) {
                return $this->photo_path;
            }

            return Storage::disk((string) config('media.disk'))->url($this->photo_path);
        });
    }
}
