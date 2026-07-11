<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_delete_a_user()
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_approved' => true,
        ]);

        $userToDelete = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($superAdmin)
            ->delete(route('superadmin.users.destroy', $userToDelete->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_super_admin_cannot_delete_another_super_admin()
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_approved' => true,
        ]);

        $otherSuperAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($superAdmin)
            ->delete(route('superadmin.users.destroy', $otherSuperAdmin->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $otherSuperAdmin->id]);
    }

    public function test_admin_can_delete_a_user()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $userToDelete = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $userToDelete->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_admin_cannot_delete_themselves()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_delete_a_super_admin()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $superAdmin->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    public function test_non_admin_cannot_delete_users()
    {
        $researcher = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
        ]);

        $userToDelete = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
        ]);

        // Attempt as researcher on admin route
        $response = $this->actingAs($researcher)
            ->delete(route('admin.users.destroy', $userToDelete->id));
        $response->assertStatus(403);

        // Attempt as researcher on superadmin route
        $response = $this->actingAs($researcher)
            ->delete(route('superadmin.users.destroy', $userToDelete->id));
        $response->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $userToDelete->id]);
    }
}
