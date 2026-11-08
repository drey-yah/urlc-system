<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Notifications\CollaborationInvitationNotification;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class CollaboratorInvitationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_toggle_collaboration_privacy_between_public_and_private()
    {
        $user = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
            'allow_collaboration' => true,
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'campus' => 'Main Campus (Sibalom)',
            'department' => 'CCIS',
            'allow_collaboration' => 0,
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'allow_collaboration' => false,
        ]);

        // Toggle back to public
        $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'campus' => 'Main Campus (Sibalom)',
            'department' => 'CCIS',
            'allow_collaboration' => 1,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'allow_collaboration' => true,
        ]);
    }

    /** @test */
    public function private_researchers_do_not_appear_in_create_proposal_collaborator_list()
    {
        $lead = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        
        $publicResearcher = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
            'name' => 'Visible Collab',
            'allow_collaboration' => true,
        ]);

        $privateResearcher = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
            'name' => 'Hidden Collab',
            'allow_collaboration' => false,
        ]);

        $response = $this->actingAs($lead)->get(route('proposal.create'));
        $response->assertStatus(200);
        $response->assertSee('Visible Collab');
        $response->assertDontSee('Hidden Collab');
    }

    /** @test */
    public function collaborator_receives_invitation_with_pending_status_upon_submission()
    {
        Notification::fake();

        $lead = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $collaborator = User::factory()->create(['role' => 'researcher', 'is_approved' => true, 'allow_collaboration' => true]);

        $proposal = ResearchProposal::create([
            'user_id' => $lead->id,
            'proposal_code' => 'RP-2026-COLLAB',
            'title' => 'Innovative Multi-Campus AI Research',
            'abstract' => 'Comprehensive research proposal test',
            'status' => 'draft',
        ]);

        // Add collaborator with pending status and send notification
        $proposal->collaborators()->sync([
            $collaborator->id => ['status' => 'pending', 'invited_at' => now()],
        ]);
        $collaborator->notify(new CollaborationInvitationNotification($proposal, $lead));

        Notification::assertSentTo($collaborator, CollaborationInvitationNotification::class);

        $this->assertDatabaseHas('proposal_collaborators', [
            'research_proposal_id' => $proposal->id,
            'user_id' => $collaborator->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function invited_collaborator_can_accept_invitation()
    {
        Notification::fake();

        $lead = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $collaborator = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);

        $proposal = ResearchProposal::create([
            'user_id' => $lead->id,
            'proposal_code' => 'RP-2026-ACC',
            'title' => 'Proposal for Acceptance Test',
            'abstract' => 'Abstract content',
            'status' => 'draft',
        ]);

        $proposal->collaborators()->attach($collaborator->id, ['status' => 'pending', 'invited_at' => now()]);

        // Collaborator visits show page and sees invitation banner
        $response = $this->actingAs($collaborator)->get(route('proposal.show', $proposal->id));
        $response->assertStatus(200);
        $response->assertSee('Collaboration Invitation');
        $response->assertSee('Accept Invitation');
        $response->assertSee('Decline');

        // Collaborator clicks Accept
        $acceptResponse = $this->actingAs($collaborator)->post(route('collaboration.respond', [$proposal->id, 'accept']));
        $acceptResponse->assertSessionHas('success');

        $this->assertDatabaseHas('proposal_collaborators', [
            'research_proposal_id' => $proposal->id,
            'user_id' => $collaborator->id,
            'status' => 'accepted',
        ]);

        Notification::assertSentTo($lead, WorkflowStatusNotification::class);
    }

    /** @test */
    public function invited_collaborator_can_decline_invitation()
    {
        Notification::fake();

        $lead = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $collaborator = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);

        $proposal = ResearchProposal::create([
            'user_id' => $lead->id,
            'proposal_code' => 'RP-2026-DEC',
            'title' => 'Proposal for Decline Test',
            'abstract' => 'Abstract content',
            'status' => 'draft',
        ]);

        $proposal->collaborators()->attach($collaborator->id, ['status' => 'pending', 'invited_at' => now()]);

        // Collaborator clicks Decline
        $declineResponse = $this->actingAs($collaborator)->post(route('collaboration.respond', [$proposal->id, 'decline']));
        $declineResponse->assertSessionHas('info');

        $this->assertDatabaseHas('proposal_collaborators', [
            'research_proposal_id' => $proposal->id,
            'user_id' => $collaborator->id,
            'status' => 'declined',
        ]);

        Notification::assertSentTo($lead, WorkflowStatusNotification::class);
    }
}
