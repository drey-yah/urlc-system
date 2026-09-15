<?php

namespace Tests\Feature;

use App\Models\ExternalFundingProject;
use App\Models\ResearchProposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExternalFundingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_funding_project_completes_the_full_workflow()
    {
        config(['filesystems.default' => 'local']);
        Storage::fake('local');

        $researcher = User::factory()->create(['role' => 'researcher', 'is_approved' => true]);
        $vprei = User::factory()->create(['role' => 'vprei', 'is_approved' => true]);
        $president = User::factory()->create(['role' => 'president', 'is_approved' => true]);
        $fundingAgency = User::factory()->create([
            'role' => 'funding_agency',
            'organization' => 'National Research Agency',
            'is_approved' => true,
        ]);
        $proposal = ResearchProposal::create([
            'user_id' => $researcher->id,
            'title' => 'External Grant Proposal',
            'abstract' => 'Research abstract',
            'research_field' => 'Science',
            'status' => 'final_approved',
        ]);

        $this->actingAs($researcher)->post(route('external-funding.store'), [
            'research_proposal_id' => $proposal->id,
            'funding_agency_user_id' => $fundingAgency->id,
            'grant_program' => 'Innovation Grant',
            'requested_amount' => 250000,
        ])->assertRedirect(route('external-funding.index'));

        $project = ExternalFundingProject::firstOrFail();
        $this->assertSame('pending_university_endorsement', $project->status);
        $this->assertDatabaseHas('research_proposals', ['id' => $proposal->id, 'funding_type' => 'external']);

        $this->actingAs($vprei)->post(route('external-funding.endorse-university', $project))
            ->assertRedirect();
        $this->assertDatabaseHas('external_funding_projects', ['id' => $project->id, 'status' => 'pending_president_endorsement']);

        $this->actingAs($president)->post(route('external-funding.endorse-agency', $project))
            ->assertRedirect();
        $this->assertDatabaseHas('external_funding_projects', ['id' => $project->id, 'status' => 'under_funder_review']);

        $this->actingAs($fundingAgency)->post(route('external-funding.agency-decision', $project), [
            'decision' => 'accepted',
            'awarded_amount' => 200000,
        ])->assertRedirect();
        $this->assertDatabaseHas('external_funding_projects', ['id' => $project->id, 'status' => 'pending_moa_mou']);

        $this->actingAs($president)->post(route('external-funding.agreement', $project), [
            'moa_mou_file' => UploadedFile::fake()->create('agreement.pdf', 100, 'application/pdf'),
            'grant_start_date' => '2026-10-01',
            'grant_end_date' => '2027-09-30',
        ])->assertRedirect();
        $this->assertDatabaseHas('external_funding_projects', ['id' => $project->id, 'status' => 'grant_active']);

        $this->actingAs($vprei)->post(route('external-funding.monitor', $project))->assertRedirect();
        $this->assertDatabaseHas('external_funding_projects', ['id' => $project->id, 'status' => 'under_grant_monitoring']);

        $this->actingAs($researcher)->post(route('external-funding.terminal-report', $project), [
            'terminal_report' => UploadedFile::fake()->create('terminal-report.pdf', 100, 'application/pdf'),
        ])->assertRedirect();
        $this->assertDatabaseHas('external_funding_projects', ['id' => $project->id, 'status' => 'pending_grant_closure']);

        $this->actingAs($vprei)->post(route('external-funding.close', $project))->assertRedirect();
        $this->assertDatabaseHas('external_funding_projects', ['id' => $project->id, 'status' => 'grant_closed']);
        $this->assertDatabaseHas('research_proposals', ['id' => $proposal->id, 'status' => 'completed']);
    }
}
