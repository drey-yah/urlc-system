<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\ResearchPublication;
use App\Models\PublishedResearchList;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class PublishedResearchListTest extends TestCase
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
            'name' => 'Dr. Clarissa Reyes',
            'email' => 'clarissa@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
            'department' => 'CAS',
            'campus' => 'Main Campus (Sibalom)',
        ]);

        $this->proposal1 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Ethnobotanical Survey of Medicinal Plants in Central Panay',
            'proposal_code' => 'PROP-2026-301',
            'abstract' => 'Ethnobotany study in Antique',
            'research_field' => 'Biology',
            'status' => 'completed',
            'current_phase' => 5,
        ]);

        ResearchPublication::create([
            'research_proposal_id' => $this->proposal1->id,
            'user_id' => $this->researcher->id,
            'journal_title' => 'Asian Journal of Biodiversity',
            'indexing_agency' => 'Scopus / Clarivate',
            'status' => 'published',
        ]);

        $this->proposal2 = ResearchProposal::create([
            'user_id' => $this->researcher->id,
            'title' => 'Microplastic Concentrations in Coastal Waters of Antique',
            'proposal_code' => 'PROP-2026-302',
            'abstract' => 'Microplastics marine investigation',
            'research_field' => 'Marine Science',
            'status' => 'final_approved',
            'current_phase' => 4,
        ]);
    }

    /** @test */
    public function research_director_can_view_index_and_create_views()
    {
        $response = $this->actingAs($this->admin)->get(route('published_researches_dossiers.index'));
        $response->assertStatus(200);
        $response->assertSee('List of Published Researches');

        $responseCreate = $this->actingAs($this->admin)->get(route('published_researches_dossiers.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Ethnobotanical Survey of Medicinal Plants in Central Panay');
        $responseCreate->assertSee('Transmit to VPREI for Noting');
    }

    /** @test */
    public function research_director_can_prepare_published_research_list_and_notify_vprei()
    {
        Notification::fake();

        $response = $this->actingAs($this->admin)->post(route('published_researches_dossiers.store'), [
            'title' => 'LIST OF PUBLISHED RESEARCHES - FY 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'proposal_ids' => [$this->proposal1->id, $this->proposal2->id],
            'items' => [
                $this->proposal1->id => [
                    'date_published' => 'October 2026',
                    'type_of_publication' => 'Scopus Indexed',
                    'name_of_publication' => 'Asian Journal of Biodiversity',
                    'volume_no' => 'Vol. 14 No. 2',
                ],
                $this->proposal2->id => [
                    'date_published' => 'November 2026',
                    'type_of_publication' => 'CHED Recognized',
                    'name_of_publication' => 'Philippine Marine Science Journal',
                    'volume_no' => 'Vol. 8 No. 1',
                ],
            ],
            'remarks' => 'Duly peer-reviewed and published in international indexed journals.',
        ]);

        $list = PublishedResearchList::first();
        $this->assertNotNull($list);
        $this->assertEquals('LIST OF PUBLISHED RESEARCHES - FY 2026', $list->title);
        $this->assertEquals('pending_noting', $list->status);
        $this->assertEquals($this->admin->id, $list->prepared_by);
        $this->assertCount(2, $list->proposal_ids);
        $this->assertCount(2, $list->items);
        $this->assertEquals('Asian Journal of Biodiversity', $list->items[0]['name_of_publication']);
        $this->assertEquals('Vol. 14 No. 2', $list->items[0]['volume_no']);

        $response->assertRedirect(route('published_researches_dossiers.show', $list->id));

        Notification::assertSentTo(
            [$this->vprei],
            WorkflowStatusNotification::class,
            function ($notification) {
                return $notification->title === 'List of Published Researches Prepared';
            }
        );
    }

    /** @test */
    public function vprei_can_officially_note_the_published_research_list()
    {
        Notification::fake();

        $list = PublishedResearchList::create([
            'title' => 'LIST OF PUBLISHED RESEARCHES - Academic Year 2026',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'proposal_id' => $this->proposal1->id,
                    'title' => $this->proposal1->title,
                    'authors' => 'Dr. Clarissa Reyes',
                    'date_published' => 'October 2026',
                    'type_of_publication' => 'Scopus Indexed',
                    'name_of_publication' => 'Asian Journal of Biodiversity',
                    'volume_no' => 'Vol. 14 No. 2',
                ]
            ],
        ]);

        $response = $this->actingAs($this->vprei)->post(route('published_researches_dossiers.note', $list->id));

        $response->assertRedirect();

        $list->refresh();
        $this->assertEquals('noted', $list->status);
        $this->assertEquals($this->vprei->id, $list->noted_by);
        $this->assertNotNull($list->noted_at);

        Notification::assertSentTo(
            [$this->admin],
            WorkflowStatusNotification::class,
            function ($notification) {
                return $notification->title === 'List of Published Researches Noted';
            }
        );
    }

    /** @test */
    public function users_can_view_document_preview_and_download_official_landscape_pdf()
    {
        $list = PublishedResearchList::create([
            'title' => 'LIST OF PUBLISHED RESEARCHES - Final Dossier',
            'report_date' => '2026-11-08',
            'fiscal_year' => 2026,
            'prepared_by' => $this->admin->id,
            'noted_by' => $this->vprei->id,
            'noted_at' => now(),
            'status' => 'noted',
            'proposal_ids' => [$this->proposal1->id],
            'items' => [
                [
                    'proposal_id' => $this->proposal1->id,
                    'title' => $this->proposal1->title,
                    'authors' => 'Dr. Clarissa Reyes',
                    'date_published' => 'October 2026',
                    'type_of_publication' => 'Scopus Indexed',
                    'name_of_publication' => 'Asian Journal of Biodiversity',
                    'volume_no' => 'Vol. 14 No. 2',
                ]
            ],
        ]);

        // Web view
        $responseShow = $this->actingAs($this->vprei)->get(route('published_researches_dossiers.show', $list->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('UNIVERSITY OF ANTIQUE');
        $responseShow->assertSee('LIST OF PUBLISHED RESEARCHES');
        $responseShow->assertSee('Asian Journal of Biodiversity');
        $responseShow->assertSee('Officially Noted by VPREI');

        // PDF download
        $responsePdf = $this->actingAs($this->admin)->get(route('published_researches_dossiers.pdf', $list->id));
        $responsePdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $responsePdf->headers->get('content-type'));
    }

    /** @test */
    public function researcher_cannot_prepare_or_note_published_research_list()
    {
        $response = $this->actingAs($this->researcher)->get(route('published_researches_dossiers.index'));
        $response->assertStatus(403);

        $responseCreate = $this->actingAs($this->researcher)->get(route('published_researches_dossiers.create'));
        $responseCreate->assertStatus(403);

        $list = PublishedResearchList::create([
            'title' => 'Test Published List',
            'report_date' => '2026-11-08',
            'prepared_by' => $this->admin->id,
            'status' => 'pending_noting',
            'proposal_ids' => [$this->proposal1->id],
        ]);

        $responseNote = $this->actingAs($this->researcher)->post(route('published_researches_dossiers.note', $list->id));
        $responseNote->assertStatus(403);
    }
}
