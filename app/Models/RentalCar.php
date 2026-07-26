<?php

namespace App\Models;

use App\Models\Concerns\HasProfilePhotoUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalCar extends Model
{
    use HasFactory, HasProfilePhotoUrl;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'model',
        'mark',
        'year',
        'profile_photo',
        'city',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'city' => 'array',
        ];
    }
}
