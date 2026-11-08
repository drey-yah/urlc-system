<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\ResearchPresentation;
use App\Models\PresentedResearchList;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class PresentedResearchListTest extends TestCase
{
    use RefreshDatabase;

    protected $staff;
    protected $admin;
    protected $researcher;
    protected $proposal1;
    protected $proposal2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::factory()->create([
            'name' => 'Receiving Staff User',
            'email' => 'staff@antiquespride.edu.ph',
            'role' => 'staff',
            'is_approved' => true,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Dr. Director UA',
            'email' => 'director@antiquespride.edu.ph',
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $this->researcher = User::factory()->create([
            'name' => 'Prof. Ernesto Cruz',
            'email' => 'ernesto@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
            'department' => 'CTE',
            'campus' => 'Main Campus (Sibalom)',
        ]);

        $this->proposal1 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Digital Pedagogies in Rural Schools of Antique',
            'proposal_code' => 'PROP-2026-401',
            'abstract' => 'Educational technology study',
            'research_field' => 'Education',
            'status' => 'completed',
            'current_phase' => 3,
        ]);

        ResearchPresentation::create([
            'research_proposal_id' => $this->proposal1->id,
            'user_id' => $this->researcher->id,
            'presentation_title' => 'Digital Pedagogies in Rural Schools of Antique',
            'sponsoring_agency' => 'CHED / PASUC',
            'conference_name' => 'National Education Research Congress 2026',
            'venue' => 'Iloilo Convention Center, Iloilo City',
            'presentation_type' => 'oral',
            'event_date' => '2026-10-15',
            'status' => 'approved',
        ]);

        $this->proposal2 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Community-Based Literacy Programs in Highland Communities',
            'proposal_code' => 'PROP-2026-402',
            'abstract' => 'Highland literacy outreach study',
            'research_field' => 'Education',
            'status' => 'ongoing',
            'current_phase' => 3,
        ]);
    }

    /** @test */
    public function receiving_staff_can_view_index_and_create_views()
    {
        $response = $this->actingAs($this->staff)->get(route('presented_researches.index'));
        $response->assertStatus(200);
        $response->assertSee('List of Presented Researches');

        $responseCreate = $this->actingAs($this->staff)->get(route('presented_researches.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Digital Pedagogies in Rural Schools of Antique');
        $responseCreate->assertSee('Transmit to Director for Noting');
    }

    /** @test */
    public function receiving_staff_can_prepare_presented_research_list_and_notify_director()
    {
        Notification::fake();

        $response = $this->actingAs($this->staff)->post(route('presented_researches.store'), [
            'title' => 'LIST OF PRESENTED RESEARCHES - FY 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'proposal_ids' => [$this->proposal1->id, $this->proposal2->id],
            'items' => [
                $this->proposal1->id => [
                    'date_presented' => 'October 15, 2026',
                    'conference_title' => 'National Education Research Congress 2026',
                    'venue' => 'Iloilo Convention Center',
                    'conference_type' => 'National',
                ],
                $this->proposal2->id => [
                    'date_presented' => 'November 05, 2026',
                    'conference_title' => 'Western Visayas Regional Research Colloquium',
                    'venue' => 'University of Antique Main Campus',
                    'conference_type' => 'Regional',
                ],
            ],
            'remarks' => 'Duly verified presentation certificates and conference acceptance letters.',
        ]);

        $list = PresentedResearchList::first();
        $this->assertNotNull($list);
        $this->assertEquals('LIST OF PRESENTED RESEARCHES - FY 2026', $list->title);
        $this->assertEquals('pending_noting', $list->status);
        $this->assertEquals($this->staff->id, $list->prepared_by);
        $this->assertCount(2, $list->proposal_ids);
        $this->assertCount(2, $list->items);
        $this->assertEquals('National Education Research Congress 2026', $list->items[0]['conference_title']);

        $response->assertRedirect(route('presented_researches.show', $list->id));

        Notification::assertSentTo(
            [$this->admin],
            WorkflowStatusNotification::class,
            function ($notification) {
                return $notification->title === 'List of Presented Researches Prepared';
            }
        );
    }

    /** @test */
    public function research_director_can_officially_note_the_presented_research_list()
    {
        Notification::fake();

        $list = PresentedResearchList::create([
            'title' => 'LIST OF PRESENTED RESEARCHES - Academic Year 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->staff->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'proposal_id' => $this->proposal1->id,
                    'title' => $this->proposal1->title,
                    'authors' => 'Prof. Ernesto Cruz',
                    'date_presented' => 'October 15, 2026',
                    'conference_title' => 'National Education Research Congress 2026',
                    'venue' => 'Iloilo Convention Center',
                    'conference_type' => 'National',
                ]
            ],
        ]);

        $response = $this->actingAs($this->admin)->post(route('presented_researches.note', $list->id));

        $response->assertRedirect();

        $list->refresh();
        $this->assertEquals('noted', $list->status);
        $this->assertEquals($this->admin->id, $list->noted_by);
        $this->assertNotNull($list->noted_at);

        Notification::assertSentTo(
            [$this->staff],
            WorkflowStatusNotification::class,
            function ($notification) {
                return $notification->title === 'List of Presented Researches Noted';
            }
        );
    }

    /** @test */
    public function users_can_view_document_preview_and_download_official_landscape_pdf()
    {
        $list = PresentedResearchList::create([
            'title' => 'LIST OF PRESENTED RESEARCHES - Final Dossier',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->staff->id,
            'noted_by' => $this->admin->id,
            'noted_at' => now(),
            'status' => 'noted',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'proposal_id' => $this->proposal1->id,
                    'title' => $this->proposal1->title,
                    'authors' => 'Prof. Ernesto Cruz',
                    'date_presented' => 'October 15, 2026',
                    'conference_title' => 'National Education Research Congress 2026',
                    'venue' => 'Iloilo Convention Center',
                    'conference_type' => 'National',
                ]
            ],
        ]);

        // Web view
        $responseShow = $this->actingAs($this->admin)->get(route('presented_researches.show', $list->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('UNIVERSITY OF ANTIQUE');
        $responseShow->assertSee('LIST OF PRESENTED RESEARCHES');
        $responseShow->assertSee('National Education Research Congress 2026');
        $responseShow->assertSee('Officially Noted by Director');

        // PDF download
        $responsePdf = $this->actingAs($this->staff)->get(route('presented_researches.pdf', $list->id));
        $responsePdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responsePdf->headers->get('content-type'));
    }

    /** @test */
    public function researcher_cannot_prepare_or_note_presented_research_list()
    {
        $response = $this->actingAs($this->researcher)->get(route('presented_researches.index'));
        $response->assertStatus(403);

        $responseCreate = $this->actingAs($this->researcher)->get(route('presented_researches.create'));
        $responseCreate->assertStatus(403);

        $list = PresentedResearchList::create([
            'title' => 'Test Presented List',
            'report_date' => '2026-11-08',
            'prepared_by' => $this->staff->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
        ]);

        $responseNote = $this->actingAs($this->researcher)->post(route('presented_researches.note', $list->id));
        $responseNote->assertStatus(403);
    }
}
