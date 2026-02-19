<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_forms_management_page_is_accessible_to_authenticated_users()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('google-forms.manage'));

        $response->assertStatus(200);
        $response->assertSee('Google Forms');
    }

    public function test_google_forms_management_page_redirects_unauthenticated_users_to_login()
    {
        $response = $this->get(route('google-forms.manage'));

        $response->assertRedirect(route('login'));
    }
}
