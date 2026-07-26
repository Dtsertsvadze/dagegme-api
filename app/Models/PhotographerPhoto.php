<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotographerPhoto extends Model
{
    use HasFactory;

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
}
