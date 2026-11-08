<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\ApprovedResearchList;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class ApprovedResearchListTest extends TestCase
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
            'name' => 'Dr. Research Director',
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
            'name' => 'Prof. Juan Dela Cruz',
            'email' => 'juan@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
            'department' => 'CCIS',
            'campus' => 'Main Campus (Sibalom)',
        ]);

        $this->proposal1 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'AI Driven Coastal Resource Monitoring in Antique',
            'proposal_code' => 'PROP-2026-001',
            'abstract' => 'Sample abstract for coastal resource study',
            'research_field' => 'Information Technology',
            'status' => 'approved',
            'total_budget' => 150000.00,
        ]);

        $this->proposal2 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Sustainable Post-Harvest Marine Processing',
            'proposal_code' => 'PROP-2026-002',
            'abstract' => 'Sample abstract for post-harvest marine processing',
            'research_field' => 'Fisheries',
            'status' => 'endorsed_to_vprei',
            'total_budget' => 220000.00,
        ]);
    }

    /** @test */
    public function research_director_can_view_index_and_create_views()
    {
        $response = $this->actingAs($this->admin)->get(route('approved_researches.index'));
        $response->assertStatus(200);
        $response->assertSee('List of Approved Researches');

        $responseCreate = $this->actingAs($this->admin)->get(route('approved_researches.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('AI Driven Coastal Resource Monitoring in Antique');
        $responseCreate->assertSee('Transmit to VPREI for Noting');
    }

    /** @test */
    public function research_director_can_prepare_approved_research_list_and_notify_vprei()
    {
        Notification::fake();

        $response = $this->actingAs($this->admin)->post(route('approved_researches.store'), [
            'title' => 'List of Approved Researches - 1st Semester 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'proposal_ids' => [$this->proposal1->id, $this->proposal2->id],
            'remarks' => 'Duly reviewed and endorsed by college deans and research unit.',
        ]);

        $list = ApprovedResearchList::first();
        $this->assertNotNull($list);
        $this->assertEquals('List of Approved Researches - 1st Semester 2026', $list->title);
        $this->assertEquals('pending_noting', $list->status);
        $this->assertEquals($this->admin->id, $list->prepared_by);
        $this->assertCount(2, $list->proposal_ids);

        $response->assertRedirect(route('approved_researches.show', $list->id));

        Notification::assertSentTo(
            [$this->vprei],
            WorkflowStatusNotification::class,
            function ($notification) {
                return $notification->title === 'List of Approved Researches Prepared';
            }
        );
    }

    /** @test */
    public function vprei_can_officially_note_the_prepared_list()
    {
        Notification::fake();

        $list = ApprovedResearchList::create([
            'title' => 'List of Approved Researches - Academic Year 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
        ]);

        $response = $this->actingAs($this->vprei)->post(route('approved_researches.note', $list->id));

        $response->assertRedirect();

        $list->refresh();
        $this->assertEquals('noted', $list->status);
        $this->assertEquals($this->vprei->id, $list->noted_by);
        $this->assertNotNull($list->noted_at);

        Notification::assertSentTo(
            [$this->admin],
            WorkflowStatusNotification::class,
            function ($notification) {
                return $notification->title === 'List of Approved Researches Noted';
            }
        );
    }

    /** @test */
    public function users_can_view_document_preview_and_download_official_pdf()
    {
        $list = ApprovedResearchList::create([
            'title' => 'List of Approved Researches - Official Dossier',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'noted_by' => $this->vprei->id,
            'noted_at' => now(),
            'status' => 'noted',
            'proposal_ids' => [$this->proposal1->id, $this->proposal2->id],
        ]);

        // Web view
        $responseShow = $this->actingAs($this->vprei)->get(route('approved_researches.show', $list->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('UNIVERSITY OF ANTIQUE');
        $responseShow->assertSee('List of Approved Researches');
        $responseShow->assertSee('AI Driven Coastal Resource Monitoring in Antique');
        $responseShow->assertSee('Officially Noted by VPREI');

        // PDF download
        $responsePdf = $this->actingAs($this->admin)->get(route('approved_researches.pdf', $list->id));
        $responsePdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responsePdf->headers->get('content-type'));
    }

    /** @test */
    public function researcher_cannot_prepare_or_note_the_approved_researches_list()
    {
        $response = $this->actingAs($this->researcher)->get(route('approved_researches.index'));
        $response->assertStatus(403);

        $responseCreate = $this->actingAs($this->researcher)->get(route('approved_researches.create'));
        $responseCreate->assertStatus(403);

        $list = ApprovedResearchList::create([
            'title' => 'Test List',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
        ]);

        $responseNote = $this->actingAs($this->researcher)->post(route('approved_researches.note', $list->id));
        $responseNote->assertStatus(403);
    }
}
