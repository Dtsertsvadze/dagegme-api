<?php

namespace Database\Seeders;

use App\Models\Band;
use App\Models\Dj;
use App\Models\Hall;
use App\Models\Photographer;
use App\Models\PhotographerPhoto;
use App\Models\Presenter;
use App\Models\RentalCar;
use App\Models\Videographer;
use Illuminate\Database\Seeder;

class ServiceProviderSeeder extends Seeder
{
    private const PLACEHOLDER_PHOTO = 'placeholders/test.jpg';

    /**
     * Seed the application's database with sample service provider data.
     */
    public function run(): void
    {
        $cities = [
            ['en' => 'Tbilisi', 'ka' => 'თბილისი', 'location_ka' => 'თბილისში'],
            ['en' => 'Batumi', 'ka' => 'ბათუმი', 'location_ka' => 'ბათუმში'],
            ['en' => 'Kutaisi', 'ka' => 'ქუთაისი', 'location_ka' => 'ქუთაისში'],
            ['en' => 'Rustavi', 'ka' => 'რუსთავი', 'location_ka' => 'რუსთავში'],
            ['en' => 'Gori', 'ka' => 'გორი', 'location_ka' => 'გორში'],
            ['en' => 'Zugdidi', 'ka' => 'ზუგდიდი', 'location_ka' => 'ზუგდიდში'],
            ['en' => 'Telavi', 'ka' => 'თელავი', 'location_ka' => 'თელავში'],
            ['en' => 'Poti', 'ka' => 'ფოთი', 'location_ka' => 'ფოთში'],
            ['en' => 'Mtskheta', 'ka' => 'მცხეთა', 'location_ka' => 'მცხეთაში'],
            ['en' => 'Akhaltsikhe', 'ka' => 'ახალციხე', 'location_ka' => 'ახალციხეში'],
        ];

        foreach (range(1, 10) as $index) {
            $city = $cities[$index - 1];

            Band::query()->updateOrCreate(
                ['name' => ['en' => "Band {$index}", 'ka' => "ბენდი {$index}"]],
                [
                    'profile_photo' => self::PLACEHOLDER_PHOTO,
                    'description' => [
                        'en' => "Live band for weddings, private parties, and corporate events in {$city['en']}.",
                        'ka' => "ცოცხალი ბენდი ქორწილებისთვის, კერძო წვეულებებისა და კორპორატიული ღონისძიებებისთვის {$city['location_ka']}.",
                    ],
                    'links' => [
                        'instagram' => "https://instagram.com/band{$index}",
                        'youtube' => "https://youtube.com/@band{$index}",
                    ],
                    'city' => ['en' => $city['en'], 'ka' => $city['ka']],
                ],
            );

            Dj::query()->updateOrCreate(
                ['name' => ['en' => "DJ {$index}", 'ka' => "დიჯეი {$index}"]],
                [
                    'profile_photo' => self::PLACEHOLDER_PHOTO,
                    'city' => ['en' => $city['en'], 'ka' => $city['ka']],
                    'links' => [
                        'instagram' => "https://instagram.com/dj{$index}",
                        'soundcloud' => "https://soundcloud.com/dj{$index}",
                    ],
                ],
            );

            Hall::query()->updateOrCreate(
                ['name' => ['en' => "Hall {$index}", 'ka' => "დარბაზი {$index}"]],
                [
                    'profile_photo' => self::PLACEHOLDER_PHOTO,
                    'city' => ['en' => $city['en'], 'ka' => $city['ka']],
                    'description' => [
                        'en' => "Spacious event hall in {$city['en']} suitable for weddings and celebrations.",
                        'ka' => "ფართო ღონისძიებების დარბაზი {$city['location_ka']}, რომელიც შესაფერისია ქორწილებისა და სხვა დღესასწაულებისთვის.",
                    ],
                ],
            );

            $photographer = Photographer::query()->updateOrCreate(
                ['name' => ['en' => "Photographer {$index}", 'ka' => "ფოტოგრაფი {$index}"]],
                [
                    'description' => [
                        'en' => "Event photographer covering weddings, engagements, and portraits in {$city['en']}.",
                        'ka' => "ღონისძიებების ფოტოგრაფი ქორწილების, ნიშნობისა და პორტრეტების გადასაღებად {$city['location_ka']}.",
                    ],
                    'profile_photo' => self::PLACEHOLDER_PHOTO,
                    'links' => [
                        'instagram' => "https://instagram.com/photographer{$index}",
                        'website' => "https://photographer{$index}.example.com",
                    ],
                    'city' => ['en' => $city['en'], 'ka' => $city['ka']],
                ],
            );

            PhotographerPhoto::query()->updateOrCreate(
                [
                    'photographer_id' => $photographer->id,
                    'photo_path' => self::PLACEHOLDER_PHOTO,
                ],
                [],
            );

            Presenter::query()->updateOrCreate(
                ['name' => ['en' => "Presenter {$index}", 'ka' => "წამყვანი {$index}"]],
                [
                    'profile_photo' => self::PLACEHOLDER_PHOTO,
                    'description' => [
                        'en' => "Professional presenter and host available for events in {$city['en']}.",
                        'ka' => "პროფესიონალი წამყვანი ღონისძიებებისთვის {$city['location_ka']}.",
                    ],
                    'city' => ['en' => $city['en'], 'ka' => $city['ka']],
                ],
            );

            RentalCar::query()->updateOrCreate(
                [
                    'mark' => "Brand {$index}",
                    'model' => "Model {$index}",
                ],
                [
                    'year' => 2015 + $index,
                    'profile_photo' => self::PLACEHOLDER_PHOTO,
                    'city' => ['en' => $city['en'], 'ka' => $city['ka']],
                ],
            );

            Videographer::query()->updateOrCreate(
                ['name' => ['en' => "Videographer {$index}", 'ka' => "ვიდეოგრაფი {$index}"]],
                [
                    'profile_photo' => self::PLACEHOLDER_PHOTO,
                    'description' => [
                        'en' => "Videographer for weddings, reels, and full event coverage in {$city['en']}.",
                        'ka' => "ვიდეოგრაფი ქორწილების, რილსებისა და ღონისძიებების სრული გადაღებისთვის {$city['location_ka']}.",
                    ],
                    'links' => [
                        'instagram' => "https://instagram.com/videographer{$index}",
                        'youtube' => "https://youtube.com/@videographer{$index}",
                    ],
                    'city' => ['en' => $city['en'], 'ka' => $city['ka']],
                ],
            );
        }
    }
}
