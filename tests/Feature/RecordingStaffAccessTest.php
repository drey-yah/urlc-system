<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecordingStaffAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $recordingStaff;
    protected $researcher;
    protected $proposal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recordingStaff = User::factory()->create([
            'name' => 'Maria Recording Staff',
            'email' => 'recording@antiquespride.edu.ph',
            'role' => 'recording_staff',
            'is_approved' => true,
        ]);

        $this->researcher = User::factory()->create([
            'name' => 'Prof. Ernesto Cruz',
            'email' => 'ernesto@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
            'department' => 'College of Teacher Education',
            'campus' => 'Main Campus (Sibalom)',
        ]);

        $this->proposal = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Innovative Agriculture and Climate Resilience',
            'abstract' => 'Sample abstract for tracking proposal flow.',
            'keywords' => 'climate, agriculture',
            'target_timeline' => '12 months',
            'status' => 'pending_coordinator_endorsement',
            'current_phase' => 1,
            'proposal_code' => 'RP-2026-099',
        ]);
    }

    public function test_recording_staff_dashboard_loads_clean_navigation_without_unauthorized_features()
    {
        $response = $this->actingAs($this->recordingStaff)->get(route('recording_staff.dashboard'));

        $response->assertStatus(200);

        // Authorized features should be present
        $response->assertSee('Tracking Dashboard');
        $response->assertSee('Budget Allocations');
        $response->assertSee('Inception Lists');
        $response->assertSee('Service Credits');
        $response->assertSee('Completed Summaries');
        $response->assertSee('Accomplishment Reports');

        // Unauthorized / duplicate features should be removed from the sidebar
        $response->assertDontSee('Approved Researches');
        $response->assertDontSee('In-house Reviews');
        $response->assertDontSee('On-going Researches');
        $response->assertDontSee('Published Lists');
        $response->assertDontSee('Presented Lists');
        $response->assertDontSee('Funded Endorsements');
    }

    public function test_recording_staff_can_view_proposals_from_tracking_dashboard()
    {
        $response = $this->actingAs($this->recordingStaff)->get(route('proposal.show', $this->proposal->id));

        $response->assertStatus(200);
        $response->assertSee('Innovative Agriculture and Climate Resilience');
    }

    public function test_recording_staff_can_view_proposal_review_workspace()
    {
        $response = $this->actingAs($this->recordingStaff)->get(route('proposal.reviewWorkspace', $this->proposal->id));

        $response->assertStatus(200);
    }

    public function test_recording_staff_can_view_endorsement_form()
    {
        $response = $this->actingAs($this->recordingStaff)->get(route('proposal.endorsement_form', $this->proposal->id));

        $response->assertStatus(200);
    }
}
