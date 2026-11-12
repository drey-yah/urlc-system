<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\PdfTextExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProposalLibAutoFillTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_extracts_line_item_budget_from_sample_text()
    {
        $sampleText = "
Line-Item Budget:
I. Maintenance and Other Operating Expenses (MOOE)
A. Supplies and Materials
A4 Paper: 5 reams Proposed: RESU 1,500.00 1,500.00
Printer Ink Cyan Proposed: RESU 750.00 750.00
C. Travelling Expenses (Local)
Fieldwork Transportation Proposed: RESU 5,000.00 5,000.00
E. Other Professional Services
Technical Consultant Proposed: RESU 3,500.00 3,500.00
F. Other MOOE
Snacks for enumerators Proposed: RESU 1,200.00 1,200.00
Sub-total for MOOE 11,950.00 11,950.00
II. Capital Outlay
Laptop Core i7 16GB RAM RESU 45,000.00 45,000.00
Sub-total for CO 45,000.00 45,000.00
GRAND TOTAL 56,950.00 56,950.00
Work Plan
Month 1: Preparation
";

        $items = PdfTextExtractor::parseBudgetItems($sampleText);

        $this->assertNotEmpty($items);
        $this->assertCount(6, $items);

        // Verify categories
        $supplies = array_values(array_filter($items, fn($i) => $i['category_group'] === 'supplies'));
        $this->assertCount(2, $supplies);
        $this->assertEquals(1500.00, $supplies[0]['amount']);
        $this->assertEquals(750.00, $supplies[1]['amount']);

        $travel = array_values(array_filter($items, fn($i) => $i['category_group'] === 'travel'));
        $this->assertCount(1, $travel);
        $this->assertEquals(5000.00, $travel[0]['amount']);

        $prof = array_values(array_filter($items, fn($i) => $i['category_group'] === 'professional_services'));
        $this->assertCount(1, $prof);
        $this->assertEquals(3500.00, $prof[0]['amount']);

        $otherMooe = array_values(array_filter($items, fn($i) => $i['category_group'] === 'other_mooe'));
        $this->assertCount(1, $otherMooe);
        $this->assertEquals(1200.00, $otherMooe[0]['amount']);

        $co = array_values(array_filter($items, fn($i) => $i['category_group'] === 'capital_outlay'));
        $this->assertCount(1, $co);
        $this->assertEquals('co', $co[0]['category_type']);
        $this->assertEquals(45000.00, $co[0]['amount']);
    }

    /** @test */
    public function researcher_receives_budget_items_in_extract_pdf_ajax_response()
    {
        $user = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $pdfPath = storage_path('app/documents/VsnZ0GAL9yQMFIH8KLBVjnyEwVl5TMGSJCyFnkha.pdf');
        if (!file_exists($pdfPath)) {
            $this->markTestSkipped('Sample PDF file not found.');
        }

        $file = new UploadedFile($pdfPath, 'proposal.pdf', 'application/pdf', null, true);

        $response = $this->actingAs($user)->postJson(route('proposal.extractPdf'), [
            'document' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'has_text',
            'title',
            'sections',
            'budget_items' => [
                '*' => [
                    'category_type',
                    'category_group',
                    'item_name',
                    'existing_resources',
                    'amount',
                ],
            ],
            'budget_total',
        ]);

        $this->assertTrue($response->json('budget_total') > 0);
        $this->assertNotEmpty($response->json('budget_items'));
        $this->assertNotEmpty($response->json('sections'));
        $this->assertArrayHasKey('background_of_study', $response->json('sections'));
    }

    /** @test */
    public function researcher_can_view_create_proposal_page_with_dual_modes()
    {
        $user = User::factory()->create([
            'role' => 'researcher',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('proposal.create'));

        $response->assertOk();
        $response->assertSee('Option A: Upload &amp; Auto-Fill from PDF', false);
        $response->assertSee('Option B: Draft Directly Online', false);
        $response->assertSee('smartProposalDoc');
    }
}
