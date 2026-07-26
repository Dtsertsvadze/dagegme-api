<?php

namespace Tests\Feature;

use Tests\TestCase;

class CorsTest extends TestCase
{
    public function test_user_frontend_origin_is_allowed(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'https://dagegme.com',
            'Access-Control-Request-Method' => 'GET',
        ])->options('/api/photographers');

        $response
            ->assertNoContent()
            ->assertHeader('Access-Control-Allow-Origin', 'https://dagegme.com');
    }

    public function test_admin_frontend_origin_is_allowed(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'https://admin.dagegme.com',
            'Access-Control-Request-Method' => 'POST',
        ])->options('/api/admin/login');

        $response
            ->assertNoContent()
            ->assertHeader('Access-Control-Allow-Origin', 'https://admin.dagegme.com');
    }

    public function test_unknown_origin_is_not_allowed(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'https://example.com',
            'Access-Control-Request-Method' => 'GET',
        ])->options('/api/photographers');

        $response
            ->assertNoContent()
            ->assertHeaderMissing('Access-Control-Allow-Origin');
    }
}
