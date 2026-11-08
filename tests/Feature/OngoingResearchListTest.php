<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\OngoingResearchList;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class OngoingResearchListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $vprei;
    protected $researcher;
    protected $proposal1;
    protected $proposal2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Dr. Director UA',
            'email' => 'director@antiquespride.edu.ph',
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $this->vprei = User::factory()->create([
            'name' => 'Dr. VPREI Executive',
            'email' => 'vprei@antiquespride.edu.ph',
            'role' => 'vprei',
            'is_approved' => true,
        ]);

        $this->researcher = User::factory()->create([
            'name' => 'Prof. Pedro Penduko',
            'email' => 'pedro@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
            'department' => 'CEA',
            'campus' => 'Main Campus (Sibalom)',
        ]);

        $this->proposal1 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Geothermal Energy Feasibility in Northern Antique',
            'proposal_code' => 'PROP-2026-201',
            'abstract' => 'Geothermal assessment project',
            'research_field' => 'Engineering',
            'status' => 'ongoing',
            'current_phase' => 2,
            'total_budget' => 350000.00,
        ]);

        $this->proposal2 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Smart Microgrid for Island Communities',
            'proposal_code' => 'PROP-2026-202',
            'abstract' => 'Solar-wind hybrid microgrid',
            'research_field' => 'Electrical Engineering',
            'status' => 'final_approved',
            'current_phase' => 2,
            'total_budget' => 420000.00,
        ]);
    }

    /** @test */
    public function research_director_can_view_index_and_create_views()
    {
        $response = $this->actingAs($this->admin)->get(route('ongoing_researches.index'));
        $response->assertStatus(200);
        $response->assertSee('List of On-going Researches');

        $responseCreate = $this->actingAs($this->admin)->get(route('ongoing_researches.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Geothermal Energy Feasibility in Northern Antique');
        $responseCreate->assertSee('Transmit to VPREI for Noting');
    }

    /** @test */
    public function research_director_can_prepare_ongoing_research_list_and_notify_vprei()
    {
        Notification::fake();

        $response = $this->actingAs($this->admin)->post(route('ongoing_researches.store'), [
            'title' => 'List of On-going Researches - FY 2026',
            'fiscal_year' => 2026,
            'report_date' => '2026-11-08',
            'proposal_ids' => [$this->proposal1->id, $this->proposal2->id],
            'remarks' => 'Duly monitored and actively executing under Phase 2.',
        ]);

        $list = OngoingResearchList::first();
        $this->assertNotNull($list);
        $this->assertEquals('List of On-going Researches - FY 2026', $list->title);
        $this->assertEquals(2026, $list->fiscal_year);
        $this->assertEquals('pending_noting', $list->status);
        $this->assertEquals($this->admin->id, $list->prepared_by);
        $this->assertCount(2, $list->proposal_ids);
        $this->assertCount(2, $list->items);
        $this->assertEquals('CEA', $list->items[0]['college']);

        $response->assertRedirect(route('ongoing_researches.show', $list->id));

        Notification::assertSentTo(
            [$this->vprei],
            WorkflowStatusNotification::class,
            function ($notification) {
                return $notification->title === 'List of On-going Researches Prepared';
            }
        );
    }

    /** @test */
    public function vprei_can_officially_note_the_ongoing_research_list()
    {
        Notification::fake();

        $list = OngoingResearchList::create([
            'title' => 'List of On-going Researches - Midyear 2026',
            'fiscal_year' => 2026,
            'report_date' => '2026-11-08',
            'prepared_by' => $this->admin->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'proposal_id' => $this->proposal1->id,
                    'title' => $this->proposal1->title,
                    'proponents' => 'Prof. Pedro Penduko',
                    'college' => 'CEA',
                    'time_frame' => '12 Months',
                ]
            ],
        ]);

        $response = $this->actingAs($this->vprei)->post(route('ongoing_researches.note', $list->id));

        $response->assertRedirect();

        $list->refresh();
        $this->assertEquals('noted', $list->status);
        $this->assertEquals($this->vprei->id, $list->noted_by);
        $this->assertNotNull($list->noted_at);

        Notification::assertSentTo(
            [$this->admin],
            WorkflowStatusNotification::class,
            function ($notification) {
                return $notification->title === 'List of On-going Researches Noted';
            }
        );
    }

    /** @test */
    public function users_can_view_document_preview_and_download_official_pdf()
    {
        $list = OngoingResearchList::create([
            'title' => 'List of On-going Researches - Official Dossier',
            'fiscal_year' => 2026,
            'report_date' => '2026-11-08',
            'prepared_by' => $this->admin->id,
            'noted_by' => $this->vprei->id,
            'noted_at' => now(),
            'status' => 'noted',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'proposal_id' => $this->proposal1->id,
                    'title' => $this->proposal1->title,
                    'proponents' => 'Prof. Pedro Penduko',
                    'college' => 'CEA',
                    'time_frame' => '12 Months',
                ]
            ],
        ]);

        // Web view
        $responseShow = $this->actingAs($this->vprei)->get(route('ongoing_researches.show', $list->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('UNIVERSITY OF ANTIQUE');
        $responseShow->assertSee('List of On-going Researches');
        $responseShow->assertSee('Fiscal Year');
        $responseShow->assertSee('Geothermal Energy Feasibility in Northern Antique');
        $responseShow->assertSee('Officially Noted by VPREI');

        // PDF download
        $responsePdf = $this->actingAs($this->admin)->get(route('ongoing_researches.pdf', $list->id));
        $responsePdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responsePdf->headers->get('content-type'));
    }

    /** @test */
    public function researcher_cannot_prepare_or_note_ongoing_research_list()
    {
        $response = $this->actingAs($this->researcher)->get(route('ongoing_researches.index'));
        $response->assertStatus(403);

        $responseCreate = $this->actingAs($this->researcher)->get(route('ongoing_researches.create'));
        $responseCreate->assertStatus(403);

        $list = OngoingResearchList::create([
            'title' => 'Test Ongoing List',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
        ]);

        $responseNote = $this->actingAs($this->researcher)->post(route('ongoing_researches.note', $list->id));
        $responseNote->assertStatus(403);
    }
}
