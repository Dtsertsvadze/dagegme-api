<?php

namespace App\Models;

use App\Models\Concerns\HasProfilePhotoUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Videographer extends Model
{
    use HasFactory, HasProfilePhotoUrl;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'profile_photo',
        'description',
        'links',
        'city',
        'vip',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'city' => 'array',
            'links' => 'array',
            'vip' => 'boolean',
        ];
    }
}
