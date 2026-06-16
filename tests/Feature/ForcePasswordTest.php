<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForcePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_to_force_password()
    {
        $user = User::factory()->create([
            'email' => 'kuiner6@gmail.com',
            'role' => \App\Enums\UserRole::EMPRESA,
            'must_change_password' => true,
        ]);

        $this->assertNotNull($user);
        $this->assertTrue($user->must_change_password, 'User must change password is not true');

        $response = $this->actingAs($user)->get(route('company.dashboard'));

        $response->assertRedirect(route('company.password.force-change'));
        
        echo "Test passed! Redirected to force-change\n";
    }
}
