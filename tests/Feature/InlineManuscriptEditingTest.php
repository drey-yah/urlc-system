<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResearchProposal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InlineManuscriptEditingTest extends TestCase
{
    use RefreshDatabase;

    protected $author;
    protected $stranger;
    protected $proposal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->create([
            'name' => 'Dr. Lead Researcher',
            'email' => 'author@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
        ]);

        $this->stranger = User::factory()->create([
            'name' => 'Dr. Unrelated Researcher',
            'email' => 'stranger@antiquespride.edu.ph',
            'role' => 'researcher',
            'is_approved' => true,
        ]);

        $this->proposal = ResearchProposal::create([
            'user_id' => $this->author->id,
            'title' => 'Initial Proposal Title',
            'abstract' => 'Initial abstract text.',
            'background_of_study' => 'Initial background of the study.',
            'methodology' => 'Initial methodology content.',
            'extracted_text' => 'Continuous manuscript extracted text here.',
            'status' => 'revision_required',
            'current_phase' => 1,
            'proposal_code' => 'RP-2026-TEST',
        ]);
    }

    public function test_author_can_update_manuscript_section_inline()
    {
        $response = $this->actingAs($this->author)->patchJson(route('proposal.updateSection', $this->proposal->id), [
            'section' => 'abstract',
            'content' => 'Revised executive abstract with improved details.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'section' => 'abstract',
        ]);

        $this->proposal->refresh();
        $this->assertEquals('Revised executive abstract with improved details.', $this->proposal->abstract);
    }

    public function test_author_can_update_extracted_text_inline()
    {
        $response = $this->actingAs($this->author)->patchJson(route('proposal.updateSection', $this->proposal->id), [
            'section' => 'extracted_text',
            'content' => "Background of the Study\n\nThis is updated full continuous text.",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'section' => 'extracted_text',
        ]);

        $this->proposal->refresh();
        $this->assertEquals("Background of the Study\n\nThis is updated full continuous text.", $this->proposal->extracted_text);
    }

    public function test_unauthorized_user_cannot_update_section()
    {
        $response = $this->actingAs($this->stranger)->patchJson(route('proposal.updateSection', $this->proposal->id), [
            'section' => 'abstract',
            'content' => 'Malicious update.',
        ]);

        $response->assertStatus(403);
    }

    public function test_author_can_resubmit_revisions_from_workspace()
    {
        $response = $this->actingAs($this->author)->postJson(route('proposal.resubmitRevisions', $this->proposal->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'new_status' => 'accepted_for_in_house_review',
        ]);

        $this->proposal->refresh();
        $this->assertEquals('accepted_for_in_house_review', $this->proposal->status);
    }

    public function test_author_can_update_individual_parsed_manuscript_section()
    {
        $this->proposal->update([
            'extracted_text' => "Background of the Study\n\nInitial background context.\n\nSignificance of the Study\n\nInitial significance of the study.\n\nMethodology\n\nInitial methodology framework."
        ]);

        // Section index 1 corresponds to "Significance of the Study"
        $response = $this->actingAs($this->author)->patchJson(route('proposal.updateSection', $this->proposal->id), [
            'section' => 'manuscript_section',
            'section_index' => 1,
            'content' => 'Revised significance of the study with institutional impact.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'section' => 'manuscript_section',
            'section_index' => 1,
        ]);

        $this->proposal->refresh();
        $this->assertStringContainsString('Revised significance of the study with institutional impact.', $this->proposal->extracted_text);
        $this->assertEquals('Revised significance of the study with institutional impact.', $this->proposal->significance_of_study);
    }
}
