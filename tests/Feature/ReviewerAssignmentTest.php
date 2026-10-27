<?php

namespace Tests\Feature;

use App\Models\ResearchProposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewerAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_reviewer_assignment_section_on_proposal_show_page()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $reviewer = User::factory()->create(['role' => 'reviewer', 'name' => 'Prof. Reviewer Alpha', 'is_approved' => true]);

        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-101',
            'title' => 'AI System for Precision Agriculture',
            'abstract' => 'Sample abstract for evaluation.',
            'status' => 'pending_in_house_review',
        ]);

        $response = $this->actingAs($admin)->get(route('proposal.show', $proposal->id));

        $response->assertStatus(200);
        $response->assertSee('Assign Peer Reviewer');
        $response->assertSee('Prof. Reviewer Alpha');
        $response->assertSee(route('admin.proposals.assign', $proposal->id));
    }

    public function test_researcher_cannot_see_reviewer_assignment_section()
    {
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-102',
            'title' => 'Marine Biodiversity in Antique',
            'abstract' => 'Sample abstract.',
            'status' => 'pending_in_house_review',
        ]);

        $response = $this->actingAs($researcher)->get(route('proposal.show', $proposal->id));

        $response->assertStatus(200);
        $response->assertDontSee('Assign Peer Reviewer');
    }

    public function test_admin_can_assign_reviewer_to_proposal()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $reviewer = User::factory()->create(['role' => 'reviewer', 'is_approved' => true]);

        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-103',
            'title' => 'Biomass Energy Conversion',
            'abstract' => 'Sample abstract.',
            'status' => 'pending_in_house_review',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.proposals.assign', $proposal->id), [
            'reviewer_id' => $reviewer->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('proposal_assignments', [
            'research_proposal_id' => $proposal->id,
            'user_id' => $reviewer->id,
        ]);

        // Reviewer receives notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $reviewer->id,
        ]);
    }

    public function test_admin_can_unassign_reviewer_from_proposal()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $reviewer = User::factory()->create(['role' => 'reviewer', 'is_approved' => true]);

        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-104',
            'title' => 'Solar Powered Microgrid',
            'abstract' => 'Sample abstract.',
            'status' => 'pending_in_house_review',
        ]);

        $proposal->assignments()->attach($reviewer->id);

        $this->assertDatabaseHas('proposal_assignments', [
            'research_proposal_id' => $proposal->id,
            'user_id' => $reviewer->id,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.proposals.unassign', [
            'id' => $proposal->id,
            'reviewer_id' => $reviewer->id,
        ]));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('proposal_assignments', [
            'research_proposal_id' => $proposal->id,
            'user_id' => $reviewer->id,
        ]);
    }

    public function test_non_admin_cannot_assign_reviewer()
    {
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $reviewer = User::factory()->create(['role' => 'reviewer', 'is_approved' => true]);

        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-105',
            'title' => 'Unauthorized Assign Attempt',
            'abstract' => 'Sample abstract.',
            'status' => 'pending_in_house_review',
        ]);

        $response = $this->actingAs($researcher)->post(route('admin.proposals.assign', $proposal->id), [
            'reviewer_id' => $reviewer->id,
        ]);

        $response->assertStatus(403);
    }
}
