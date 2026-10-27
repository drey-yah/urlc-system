<?php

namespace Tests\Feature;

use App\Models\ResearchProposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_multi_role_methods()
    {
        $user = User::factory()->create([
            'role' => 'researcher',
            'additional_roles' => ['reviewer'],
            'is_approved' => true,
        ]);

        $this->assertTrue($user->isMultiRole());
        $this->assertTrue($user->hasRole('researcher'));
        $this->assertTrue($user->hasRole('reviewer'));
        $this->assertFalse($user->hasRole('dean'));
        $this->assertEquals(['researcher', 'reviewer'], $user->allRoles());
        $this->assertEquals('researcher', $user->activeRole());
    }

    public function test_with_role_query_scope_finds_primary_and_additional_roles()
    {
        $pureReviewer = User::factory()->create(['role' => 'reviewer', 'is_approved' => true]);
        $dualUser = User::factory()->create(['role' => 'researcher', 'additional_roles' => ['reviewer'], 'is_approved' => true]);
        $pureResearcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);

        $reviewers = User::withRole('reviewer')->pluck('id')->toArray();

        $this->assertContains($pureReviewer->id, $reviewers);
        $this->assertContains($dualUser->id, $reviewers);
        $this->assertNotContains($pureResearcher->id, $reviewers);
    }

    public function test_superadmin_can_assign_multi_roles_to_user()
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_approved' => true]);
        $user = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);

        $response = $this->actingAs($superAdmin)->post(route('superadmin.users.updateRoles', $user->id), [
            'primary_role' => 'researcher',
            'additional_roles' => ['reviewer', 'coordinator'],
        ]);

        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('researcher', $user->role);
        $this->assertEquals(['reviewer', 'coordinator'], $user->additional_roles);
        $this->assertTrue($user->hasRole('reviewer'));
        $this->assertTrue($user->hasRole('coordinator'));
    }

    public function test_user_can_switch_active_role_if_authorized()
    {
        $dualUser = User::factory()->create([
            'role' => 'researcher',
            'additional_roles' => ['reviewer'],
            'is_approved' => true,
        ]);

        $response = $this->actingAs($dualUser)->post(route('role.switch'), [
            'role' => 'reviewer',
        ]);

        $response->assertRedirect('/reviewer');
        $this->assertEquals('reviewer', session('active_role'));
        $this->assertEquals('reviewer', $dualUser->activeRole());
    }

    public function test_user_cannot_switch_to_unauthorized_role()
    {
        $researcher = User::factory()->create([
            'role' => 'researcher',
            'additional_roles' => null,
            'is_approved' => true,
        ]);

        $response = $this->actingAs($researcher)->post(route('role.switch'), [
            'role' => 'dean',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('researcher', $researcher->activeRole());
    }

    public function test_conflict_of_interest_prevents_assigning_author_as_reviewer()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);
        $dualUser = User::factory()->create([
            'role' => 'researcher',
            'additional_roles' => ['reviewer'],
            'is_approved' => true,
        ]);

        $proposal = ResearchProposal::create([
            'user_id' => $dualUser->id,
            'proposal_code' => 'RP-2026-COI-01',
            'title' => 'Sample Author Proposal',
            'abstract' => 'Sample abstract.',
            'status' => 'pending_in_house_review',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.proposals.assign', $proposal->id), [
            'reviewer_id' => $dualUser->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('proposal_assignments', [
            'research_proposal_id' => $proposal->id,
            'user_id' => $dualUser->id,
        ]);
    }
}
