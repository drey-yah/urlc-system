<?php

namespace Tests\Feature;

use App\Models\ProposalDocument;
use App\Models\ResearchProposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProposalDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_researcher_can_delete_proposal_pending_coordinator_endorsement()
    {
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-001',
            'title' => 'Pending Proposal',
            'abstract' => 'Sample abstract',
            'status' => 'pending_coordinator_endorsement',
        ]);

        $response = $this->actingAs($researcher)
            ->delete(route('proposal.destroy', $proposal->id));

        $response->assertRedirect(route('proposal.index'));
        $response->assertSessionHas('success', 'Proposal deleted successfully!');
        $this->assertDatabaseMissing('research_proposals', ['id' => $proposal->id]);
    }

    public function test_researcher_can_delete_draft_proposal()
    {
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-002',
            'title' => 'Draft Proposal',
            'abstract' => 'Draft abstract',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($researcher)
            ->delete(route('proposal.destroy', $proposal->id));

        $response->assertRedirect(route('proposal.index'));
        $this->assertDatabaseMissing('research_proposals', ['id' => $proposal->id]);
    }

    public function test_researcher_can_delete_proposal_returned_for_revision()
    {
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-003',
            'title' => 'Returned Proposal',
            'abstract' => 'Sample abstract',
            'status' => 'returned_for_revision',
        ]);

        $response = $this->actingAs($researcher)
            ->delete(route('proposal.destroy', $proposal->id));

        $response->assertRedirect(route('proposal.index'));
        $this->assertDatabaseMissing('research_proposals', ['id' => $proposal->id]);
    }

    public function test_researcher_cannot_delete_proposal_once_endorsed_by_coordinator()
    {
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        
        // Proposal endorsed by coordinator moves to pending_dean_noting or subsequent statuses
        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-004',
            'title' => 'Endorsed Proposal',
            'abstract' => 'Sample abstract',
            'status' => 'pending_dean_noting',
        ]);

        $response = $this->actingAs($researcher)
            ->delete(route('proposal.destroy', $proposal->id));

        $response->assertSessionHas('error', 'Proposals can only be deleted before they are endorsed by the college coordinator.');
        $this->assertDatabaseHas('research_proposals', ['id' => $proposal->id]);
    }

    public function test_researcher_cannot_delete_proposal_accepted_for_in_house_review()
    {
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-005',
            'title' => 'In House Review Proposal',
            'abstract' => 'Sample abstract',
            'status' => 'accepted_for_in_house_review',
        ]);

        $response = $this->actingAs($researcher)
            ->delete(route('proposal.destroy', $proposal->id));

        $response->assertSessionHas('error', 'Proposals can only be deleted before they are endorsed by the college coordinator.');
        $this->assertDatabaseHas('research_proposals', ['id' => $proposal->id]);
    }

    public function test_researcher_cannot_delete_another_researchers_proposal()
    {
        $owner = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $otherResearcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);

        $proposal = ResearchProposal::create([
            'user_id' => $owner->id,
            'proposal_code' => 'RP-2026-006',
            'title' => 'Other Owner Proposal',
            'abstract' => 'Sample abstract',
            'status' => 'pending_coordinator_endorsement',
        ]);

        $response = $this->actingAs($otherResearcher)
            ->delete(route('proposal.destroy', $proposal->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('research_proposals', ['id' => $proposal->id]);
    }

    protected function createFakePdf(string $name = 'manuscript.pdf'): UploadedFile
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($tempPath, '%PDF-1.4 test dummy content');
        return new UploadedFile($tempPath, $name, 'application/pdf', null, true);
    }

    public function test_files_are_deleted_from_storage_when_proposal_is_deleted()
    {
        config(['filesystems.default' => 'public']);
        Storage::fake('public');

        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $path = 'documents/manuscript.pdf';
        Storage::disk('public')->put($path, '%PDF-1.4 dummy content');

        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-007',
            'title' => 'With File Proposal',
            'abstract' => 'Sample abstract',
            'document_path' => $path,
            'status' => 'pending_coordinator_endorsement',
        ]);

        $doc = $proposal->documents()->create([
            'document_tag' => 'RP-2026-007-PH1-MANUSCRIPT-V1',
            'document_type' => 'manuscript',
            'phase' => 1,
            'version' => 1,
            'file_path' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $this->actingAs($researcher)
            ->delete(route('proposal.destroy', $proposal->id))
            ->assertRedirect(route('proposal.index'));

        $this->assertDatabaseMissing('research_proposals', ['id' => $proposal->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_related_notifications_are_cleaned_up_on_proposal_deletion()
    {
        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'proposal_code' => 'RP-2026-008',
            'title' => 'With Notification Proposal',
            'abstract' => 'Sample abstract',
            'status' => 'pending_coordinator_endorsement',
        ]);

        $researcher->notify(new \App\Notifications\WorkflowStatusNotification(
            $proposal,
            'Proposal Update',
            'Your proposal has been updated.'
        ));

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $researcher->id,
        ]);

        $this->actingAs($researcher)
            ->delete(route('proposal.destroy', $proposal->id))
            ->assertRedirect(route('proposal.index'));

        $this->assertDatabaseMissing('research_proposals', ['id' => $proposal->id]);
        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $researcher->id,
        ]);
    }

    public function test_researcher_can_submit_proposal_with_long_title()
    {
        config(['filesystems.default' => 'public']);
        Storage::fake('public');

        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $longTitle = 'It is recommended that the researchers conduct an initial needs assessment among selected farmers in Antique to determine their existing agricultural monitoring practices, technological readiness, and accessibility to IoT-based solutions before deployment.';

        $file = $this->createFakePdf('manuscript.pdf');

        $response = $this->actingAs($researcher)->post(route('proposal.store'), [
            'title' => $longTitle,
            'abstract' => 'Comprehensive abstract for testing long titles in the URLC system.',
            'research_field' => 'Information Technology & Precision Agriculture',
            'document' => $file,
        ]);

        $response->assertRedirect('/proposal/my');
        $this->assertDatabaseHas('research_proposals', [
            'user_id' => $researcher->id,
            'title' => $longTitle,
        ]);
    }
}
