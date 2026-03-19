<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class ForcePasswordTest extends TestCase
{
    public function test_login_redirects_to_force_password()
    {
        $user = User::where('email', 'kuiner6@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->must_change_password, 'User must change password is not true');

        $response = $this->actingAs($user)->get(route('company.dashboard'));

        $response->assertRedirect(route('company.password.force-change'));
        
        echo "Test passed! Redirected to force-change\n";
    }
}
