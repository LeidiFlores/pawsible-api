<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_and_demo_users_exist_after_seeding(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['admin', 'staff', 'adopter'] as $role) {
            $this->assertDatabaseHas('roles', [
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $this->assertTrue(User::where('email', 'admin@pawsible.dev')->first()->hasRole('admin'));
        $this->assertTrue(User::where('email', 'staff@pawsible.dev')->first()->hasRole('staff'));
        $this->assertTrue(User::where('email', 'user@pawsible.dev')->first()->hasRole('adopter'));
    }

    public function test_registration_assigns_adopter_role_by_default(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated();

        $this->assertTrue(User::where('email', 'jane@example.com')->first()->hasRole('adopter'));
    }

    public function test_pet_write_routes_reject_adopter_role(): void
    {
        $adopter = $this->userWithRole('adopter');

        Sanctum::actingAs($adopter);

        $this->postJson('/api/v1/pets', [
            'name' => 'Milo',
            'species' => 'cat',
        ])->assertForbidden();
    }

    public function test_pet_delete_route_rejects_staff_role(): void
    {
        $staff = $this->userWithRole('staff');
        $pet = Pet::create([
            'name' => 'Milo',
            'species' => 'cat',
        ]);

        Sanctum::actingAs($staff);

        $this->deleteJson("/api/v1/pets/{$pet->id}")->assertForbidden();
    }

    public function test_adoption_submit_route_rejects_admin_and_staff_roles(): void
    {
        foreach (['admin', 'staff'] as $role) {
            Sanctum::actingAs($this->userWithRole($role));

            $this->postJson('/api/v1/adoptions', [
                'pet_id' => 1,
            ])->assertForbidden();
        }
    }

    public function test_adoption_review_routes_reject_adopter_role(): void
    {
        $adopter = $this->userWithRole('adopter');

        Sanctum::actingAs($adopter);

        $this->getJson('/api/v1/adoptions')->assertForbidden();
    }

    public function test_pet_listing_and_details_are_public(): void
    {
        $pet = Pet::create([
            'name' => 'Milo',
            'species' => 'cat',
        ]);

        $this->getJson('/api/v1/pets')->assertOk();
        $this->getJson("/api/v1/pets/{$pet->id}")->assertOk();
    }

    private function userWithRole(string $role): User
    {
        Role::findOrCreate($role, 'web');

        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
