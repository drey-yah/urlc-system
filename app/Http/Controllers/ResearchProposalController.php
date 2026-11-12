<?php

namespace App\Http\Controllers;

use App\Models\ResearchProposal;
use App\Models\User;
use App\Notifications\WorkflowStatusNotification;
use Illuminate\Http\Request;

class ResearchProposalController extends Controller
{
    // Researcher: View own proposals
    public function index()
    {
        $leadProposals = auth()->user()->leadProposals()->with(['collaborators', 'milestones'])->latest()->get();
        $collaboratedProposals = auth()->user()->collaboratedProposals()->with(['user', 'collaborators', 'milestones'])->latest()->get();
        
        return view('researcher.my_proposals', compact('leadProposals', 'collaboratedProposals'));
    }

    // Researcher: Show submission form
    public function create()
    {
        $researchers = \App\Models\User::where('role', 'researcher')
            ->where('id', '!=', auth()->id())
            ->where(function ($q) {
                $q->where('allow_collaboration', true)
                  ->orWhereNull('allow_collaboration');
            })
            ->orderBy('name')
            ->get();
        return view('researcher.create_proposal', compact('researchers'));
    }

    /**
     * AJAX: Extract text, title, abstract, and keywords from uploaded proposal document.
     */
    public function extractPdfMetadata(Request $request)
    {
        $request->validate([
            'document' => 'required|file|max:20480',
        ]);

        $file = $request->file('document');
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext !== 'pdf') {
            return response()->json([
                'success' => false,
                'message' => 'The uploaded file must be a PDF document.',
            ], 422);
        }

        try {
            $tempPath = $file->getRealPath();

            $text = \App\Services\PdfTextExtractor::extractText($tempPath);
            $meta = \App\Services\PdfTextExtractor::parseMetadata($text, $file->getClientOriginalName());
            $budgetItems = \App\Services\PdfTextExtractor::parseBudgetItems($text);
            $wordCount = !empty($text) ? str_word_count($text) : 0;
            $totalBudget = array_sum(array_column($budgetItems, 'amount'));

            // Extract structured narrative sections (Form RESU-FM-015 chapters)
            $rawSections = \App\Services\PdfTextExtractor::parseManuscriptSections($text);
            $sections = [];

            $findContent = function($keys) use ($rawSections) {
                $keys = is_array($keys) ? $keys : [$keys];
                $out = [];
                foreach ($rawSections as $s) {
                    if (in_array($s['key'], $keys) && !empty(trim($s['content'] ?? ''))) {
                        $out[] = trim($s['content']);
                    }
                }
                return implode("\n\n", $out);
            };

            // Background of the Study (includes Statement of the Problem & Framework if distinct)
            $bg = $findContent(['background_of_study']);
            $sop = $findContent(['statement_of_problem']);
            $tf = $findContent(['theoretical_framework']);
            $combinedBg = $bg;
            if (!empty($sop) && stripos($combinedBg, 'Statement of the Problem') === false) {
                $combinedBg .= ($combinedBg ? "\n\n" : "") . "Statement of the Problem:\n" . $sop;
            }
            if (!empty($tf) && stripos($combinedBg, 'Theoretical') === false) {
                $combinedBg .= ($combinedBg ? "\n\n" : "") . "Theoretical & Conceptual Framework:\n" . $tf;
            }
            if (!empty($combinedBg)) {
                $sections['background_of_study'] = $combinedBg;
            }

            // Significance of the Study
            $sig = $findContent(['significance_of_study']);
            if (!empty($sig)) $sections['significance_of_study'] = $sig;

            // Definition of Terms
            $dot = $findContent(['definition_of_terms']);
            if (!empty($dot)) $sections['definition_of_terms'] = $dot;

            // Review of Related Literature (RRL)
            $rrl = $findContent(['literature_review']);
            if (!empty($rrl)) $sections['literature_review'] = $rrl;

            // Methodology & Research Design (includes Sampling, Instrumentation, and Analysis if distinct)
            $meth = $findContent(['methodology']);
            $pop = $findContent(['population_sampling']);
            $inst = $findContent(['instrumentation']);
            $dg = $findContent(['data_gathering']);
            $da = $findContent(['data_analysis']);
            $combinedMeth = $meth;
            if (!empty($pop) && stripos($combinedMeth, 'Sampling') === false && stripos($combinedMeth, 'Participants') === false) {
                $combinedMeth .= ($combinedMeth ? "\n\n" : "") . "Population and Sampling:\n" . $pop;
            }
            if (!empty($inst) && stripos($combinedMeth, 'Instrument') === false) {
                $combinedMeth .= ($combinedMeth ? "\n\n" : "") . "Research Instrument:\n" . $inst;
            }
            if (!empty($dg) && stripos($combinedMeth, 'Data Gathering') === false) {
                $combinedMeth .= ($combinedMeth ? "\n\n" : "") . "Data Gathering Procedure:\n" . $dg;
            }
            if (!empty($da) && stripos($combinedMeth, 'Data Analysis') === false) {
                $combinedMeth .= ($combinedMeth ? "\n\n" : "") . "Data Analysis:\n" . $da;
            }
            if (!empty($combinedMeth)) {
                $sections['methodology'] = $combinedMeth;
            }

            // References
            $ref = $findContent(['references']);
            if (!empty($ref)) $sections['references'] = $ref;

            // Proponent Credentials
            $cred = $findContent(['credentials']);
            if (!empty($cred)) $sections['credentials'] = $cred;

            return response()->json([
                'success' => true,
                'has_text' => !empty($text),
                'word_count' => $wordCount,
                'title' => $meta['title'],
                'title_suggestions' => $meta['title_suggestions'] ?? [],
                'abstract' => $meta['abstract'],
                'research_field' => $meta['research_field'],
                'sections' => $sections,
                'budget_items' => $budgetItems,
                'budget_total' => $totalBudget,
                'extracted_text' => $text,
                'message' => !empty($text) 
                    ? "Document analyzed successfully! Extracted {$wordCount} words." 
                    : "No digital text stream detected in PDF (may be a scanned image).",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error analyzing document: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Researcher: Store proposal
    public function store(Request $request)
    {
        $isDraft = $request->action === 'draft';

        $request->validate([
            'title' => 'required',
            'abstract' => 'nullable|string',
            'rationale' => 'nullable|string',
            'research_field' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf|max:20480',
            'collaborators' => 'nullable|array',
            'collaborators.*' => 'exists:users,id',
            // Template fields validation
            'credentials' => 'nullable|string',
            'college_affiliation' => 'nullable|string',
            'sdgs_addressed' => 'nullable|string',
            'background_of_study' => 'nullable|string',
            'significance_of_study' => 'nullable|string',
            'definition_of_terms' => 'nullable|string',
            'literature_review' => 'nullable|string',
            'methodology' => 'nullable|string',
            'references' => 'nullable|string',
        ]);

        $filePath = null;

        if ($request->hasFile('document')) {
            $filePath = $request->file('document')->store('documents', config('filesystems.default', 'public'));
        }

        $extractedText = $request->extracted_text;
        if (empty($extractedText) && $filePath) {
            try {
                $disk = config('filesystems.default', 'public');
                $fullPath = \Storage::disk($disk)->path($filePath);
                if (file_exists($fullPath)) {
                    $extractedText = \App\Services\PdfTextExtractor::extractText($fullPath);
                }
            } catch (\Throwable $e) {
                \Log::warning('PDF extraction failed: ' . $e->getMessage());
            }
        }

        // If template sections were filled and extracted text is empty, synthesize structured manuscript text
        if (empty($extractedText)) {
            $sections = [];
            if ($request->title) $sections[] = "RESEARCH TITLE:\n" . $request->title;
            if ($request->credentials) $sections[] = "KEY PROPONENTS & CREDENTIALS:\n" . $request->credentials;
            if ($request->background_of_study) $sections[] = "BACKGROUND OF THE STUDY & THEORETICAL FRAMEWORK:\n" . $request->background_of_study;
            if ($request->significance_of_study) $sections[] = "SIGNIFICANCE OF THE STUDY:\n" . $request->significance_of_study;
            if ($request->definition_of_terms) $sections[] = "DEFINITION OF TERMS:\n" . $request->definition_of_terms;
            if ($request->literature_review) $sections[] = "REVIEW OF RELATED LITERATURE:\n" . $request->literature_review;
            if ($request->methodology) $sections[] = "METHODOLOGY & RESEARCH DESIGN:\n" . $request->methodology;
            if ($request->references) $sections[] = "REFERENCES:\n" . $request->references;
            $extractedText = implode("\n\n", $sections);
        }

        $abstractText = $request->abstract ?: ($request->background_of_study ? \Illuminate\Support\Str::limit($request->background_of_study, 500) : $request->title);
        $rationaleText = $request->rationale ?: $request->background_of_study;

        $year = date('Y');
        $latestProposal = ResearchProposal::latest('id')->first();
        $nextId = $latestProposal ? $latestProposal->id + 1 : 1;
        $sequence = str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $proposalCode = "RP-{$year}-{$sequence}";

        $proposal = null;
        \DB::transaction(function () use ($request, $isDraft, $filePath, $proposalCode, $extractedText, $abstractText, $rationaleText, &$proposal) {
            $proposal = ResearchProposal::create([
                'user_id' => auth()->id(),
                'proposal_code' => $proposalCode,
                'title' => $request->title,
                'abstract' => $abstractText,
                'rationale' => $rationaleText,
                'research_field' => $request->research_field,
                'credentials' => $request->credentials,
                'college_affiliation' => $request->college_affiliation ?: auth()->user()->department,
                'sdgs_addressed' => $request->sdgs_addressed,
                'background_of_study' => $request->background_of_study,
                'significance_of_study' => $request->significance_of_study,
                'definition_of_terms' => $request->definition_of_terms,
                'literature_review' => $request->literature_review,
                'methodology' => $request->methodology,
                'references' => $request->references,
                'work_plan_matrix' => $request->work_plan_matrix,
                'document_path' => $filePath, // Kept for backwards compatibility
                'extracted_text' => $extractedText,
                'status' => $isDraft ? 'draft' : 'pending_coordinator_endorsement',
            ]);

            // Save line-item budget if provided
            if ($request->has('budget_items') && is_array($request->budget_items)) {
                $totalBudget = 0;
                foreach ($request->budget_items as $bItem) {
                    if (!empty($bItem['item_name']) && isset($bItem['amount']) && $bItem['amount'] !== '') {
                        $cost = floatval(str_replace(',', '', $bItem['amount']));
                        $catGroup = $bItem['category_group'] ?? ($bItem['category'] ?? 'supplies');
                        $catType = $bItem['category_type'] ?? (in_array($catGroup, ['capital_outlay', 'co']) ? 'co' : 'mooe');
                        $proposal->budgetItems()->create([
                            'category_type' => $catType,
                            'category_group' => $catGroup,
                            'item_name' => $bItem['item_name'],
                            'funding_agency' => $bItem['funding_agency'] ?? 'University of Antique',
                            'existing_resources' => $bItem['existing_resources'] ?? null,
                            'amount' => $cost,
                        ]);
                        $totalBudget += $cost;
                    }
                }
                if ($totalBudget > 0) {
                    $proposal->update(['total_budget' => $totalBudget]);
                }
            }

            // Save work plan milestones if provided
            if ($request->has('milestones') && is_array($request->milestones)) {
                foreach ($request->milestones as $ms) {
                    if (!empty($ms['title'])) {
                        $proposal->milestones()->create([
                            'title' => $ms['title'],
                            'start_date' => !empty($ms['start_date']) ? $ms['start_date'] : null,
                            'target_date' => !empty($ms['target_date']) ? $ms['target_date'] : null,
                            'description' => $ms['description'] ?? 'Work plan activity',
                            'status' => 'pending',
                        ]);
                    }
                }
            }

            if ($filePath) {
                $documentTag = "{$proposalCode}-PH1-MANUSCRIPT-V1";
                $proposal->documents()->create([
                    'document_tag' => $documentTag,
                    'document_type' => 'manuscript',
                    'phase' => 1,
                    'version' => 1,
                    'file_path' => $filePath,
                ]);
            }

            if ($request->has('collaborators') && is_array($request->collaborators)) {
                $syncData = [];
                foreach ($request->collaborators as $collabId) {
                    $syncData[$collabId] = [
                        'status' => 'pending',
                        'invited_at' => now(),
                    ];
                }
                $proposal->collaborators()->sync($syncData);

                // Notify invited collaborators with Accept/Decline action
                $invitedUsers = \App\Models\User::whereIn('id', $request->collaborators)->get();
                foreach ($invitedUsers as $invitedUser) {
                    $invitedUser->notify(new \App\Notifications\CollaborationInvitationNotification($proposal, auth()->user()));
                }
            }
        });

        if (!$isDraft && $proposal) {
            // Notify Lead Researcher
            $proposal->user->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Submitted for Endorsement',
                "Your proposal '{$proposal->title}' ({$proposal->proposal_code}) was submitted successfully and is awaiting College Coordinator endorsement.",
                'bi-send-check',
                'text-primary'
            ));

            // Notify College Coordinator(s)
            $coordinators = User::where('role', 'coordinator')
                ->where('department', auth()->user()->department)
                ->get();
            foreach ($coordinators as $coord) {
                $coord->notify(new WorkflowStatusNotification(
                    $proposal,
                    'New Proposal for Endorsement',
                    "Researcher " . auth()->user()->name . " submitted proposal '{$proposal->title}' for college endorsement.",
                    'bi-file-earmark-plus-fill',
                    'text-info'
                ));
            }
        }

        $msg = $isDraft ? 'Proposal saved as draft!' : 'Proposal submitted successfully!';
        return redirect('/proposal/my')->with('success', $msg);
    }

    // Reviewer: View ALL proposals
    public function reviewerIndex()
    {
        // Reviewers should only see proposals assigned to them, excluding their own (COI safeguard)
        $assignedProposalIds = \DB::table('proposal_assignments')
            ->where('user_id', auth()->id())
            ->pluck('research_proposal_id');

        $proposals = ResearchProposal::with([
            'user',
            'collaborators',
            'evaluations' => function ($q) {
                $q->where('user_id', auth()->id());
            }
        ])
            ->whereIn('id', $assignedProposalIds)
            ->where('user_id', '!=', auth()->id())
            ->get();

        return view('reviewer.proposals', compact('proposals'));
    }

    // Admin: Assign a reviewer to a proposal
    public function assignReviewer(Request $request, $id)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
        ]);

        $reviewer = User::findOrFail($request->reviewer_id);
        if (!$reviewer->hasRole('reviewer')) {
            return redirect()->back()->with('error', 'The selected user is not authorized as an Evaluator/Reviewer.');
        }

        $proposal = ResearchProposal::with(['collaborators'])->findOrFail($id);

        // Conflict of Interest (COI) Protection
        if ($proposal->user_id == $reviewer->id) {
            return redirect()->back()->with('error', 'Conflict of Interest: The lead researcher cannot be assigned to review their own proposal.');
        }

        if ($proposal->collaborators->contains('id', $reviewer->id)) {
            return redirect()->back()->with('error', 'Conflict of Interest: A co-researcher / collaborator cannot be assigned to review this proposal.');
        }

        // Create assignment if not exists
        \DB::table('proposal_assignments')->updateOrInsert(
            ['research_proposal_id' => $id, 'user_id' => $request->reviewer_id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $reviewer->notify(new WorkflowStatusNotification(
            $proposal,
            'Assigned as Peer Reviewer',
            "You have been assigned to evaluate proposal: {$proposal->proposal_code} - {$proposal->title}",
            'bi-person-check-fill',
            'text-info'
        ));

        return redirect()->back()->with('success', 'Reviewer assigned successfully!');
    }

    // Admin: Unassign a reviewer from a proposal
    public function unassignReviewer($id, $reviewer_id)
    {
        if (!in_array(auth()->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized.');
        }

        \DB::table('proposal_assignments')
            ->where('research_proposal_id', $id)
            ->where('user_id', $reviewer_id)
            ->delete();

        return redirect()->back()->with('success', 'Reviewer removed successfully!');
    }

    // Reviewer: Update proposal status, criteria ratings (RESU-FM-001), comments, and suggestions
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,revision_required,approved_with_revisions',
            'score_a' => 'nullable|numeric|min:0|max:15',
            'score_b' => 'nullable|numeric|min:0|max:5',
            'score_c' => 'nullable|numeric|min:0|max:25',
            'score_d' => 'nullable|numeric|min:0|max:25',
            'score_e' => 'nullable|numeric|min:0|max:30',
            'review_comments' => 'nullable|string',
            'review_suggestions' => 'nullable|string',
            'evaluation_document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $proposal = ResearchProposal::with(['collaborators', 'user'])->findOrFail($id);

        // Conflict of Interest (COI) Protection: Cannot evaluate own proposal
        if ($proposal->user_id === auth()->id() || $proposal->collaborators->contains('id', auth()->id())) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Conflict of Interest: You cannot evaluate your own proposal.'], 403);
            }
            abort(403, 'Conflict of Interest: You cannot evaluate or submit feedback on your own research proposal.');
        }

        if (in_array($proposal->status, ['final_approved', 'final_rejected'])) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This proposal has already received a final decision and cannot be updated.'], 422);
            }
            return redirect()->back()->with('error', 'This proposal has already received a final decision and cannot be updated.');
        }

        $proposal->update([
            'status' => $request->status,
            'reviewer_id' => auth()->id(),
            'review_comments' => $request->review_comments,
            'review_suggestions' => $request->review_suggestions,
        ]);

        $docPath = null;
        if ($request->hasFile('evaluation_document')) {
            $docPath = $request->file('evaluation_document')->store('evaluations', config('filesystems.default', 'public'));
            
            $proposal->documents()->create([
                'document_tag' => "{$proposal->proposal_code}-PH{$proposal->current_phase}-EVAL-" . auth()->id() . '-' . time(),
                'document_type' => 'evaluation',
                'phase' => $proposal->current_phase ?? 3,
                'version' => 1,
                'file_path' => $docPath,
            ]);
        }

        // Save structured evaluation (RESU-FM-001)
        $hasScores = $request->filled('score_a') || $request->filled('score_b') || $request->filled('score_c') || $request->filled('score_d') || $request->filled('score_e');
        $scoreA = floatval($request->score_a ?? 0);
        $scoreB = floatval($request->score_b ?? 0);
        $scoreC = floatval($request->score_c ?? 0);
        $scoreD = floatval($request->score_d ?? 0);
        $scoreE = floatval($request->score_e ?? 0);
        $totalScore = $scoreA + $scoreB + $scoreC + $scoreD + $scoreE;
        $meetsThreshold = $totalScore >= 80.0;

        $evaluation = null;
        if ($hasScores || $request->filled('review_comments') || $request->filled('review_suggestions')) {
            $evaluation = \App\Models\ProposalEvaluation::updateOrCreate(
                [
                    'research_proposal_id' => $proposal->id,
                    'user_id' => auth()->id(),
                ],
                [
                    'score_a' => $scoreA,
                    'score_b' => $scoreB,
                    'score_c' => $scoreC,
                    'score_d' => $scoreD,
                    'score_e' => $scoreE,
                    'total_score' => $totalScore,
                    'meets_threshold' => $meetsThreshold,
                    'decision' => $request->status,
                    'comments' => $request->review_comments,
                    'suggestions' => $request->review_suggestions,
                    'document_path' => $docPath,
                    'evaluated_at' => now(),
                ]
            );
        }

        // Notify Researcher
        $proposal->user->notify(new \App\Notifications\ProposalFeedbackNotification($proposal));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proposal evaluation and ratings saved successfully!',
                'total_score' => $totalScore,
                'meets_threshold' => $meetsThreshold,
                'status' => $request->status,
                'pdf_url' => route('proposal.evaluationPdf', $proposal->id),
            ]);
        }

        return redirect()->back()->with('success', 'Proposal evaluation saved and researcher notified successfully!');
    }

    /**
     * Generate & Download Official Form RESU-FM-001 (Criteria on Approval of Research Proposals) PDF
     */
    public function downloadEvaluationPdf($id, $evaluationId = null)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators', 'assignments'])->findOrFail($id);
        $currentUser = auth()->user();

        // Check permission: author, collaborator, assigned reviewer, admin/staff
        $isAuthorOrCollab = $proposal->user_id === $currentUser->id || $proposal->collaborators->contains('id', $currentUser->id);
        $isAssignedReviewer = $proposal->assignments->contains('id', $currentUser->id);
        $isAdminOrStaff = in_array($currentUser->role, ['admin', 'super_admin', 'staff', 'recording_staff', 'vprei']) || $currentUser->isSuperAdmin();

        if (!$isAuthorOrCollab && !$isAssignedReviewer && !$isAdminOrStaff && $currentUser->role !== 'reviewer') {
            abort(403, 'Unauthorized access to evaluation sheet.');
        }

        if ($evaluationId) {
            $evaluation = \App\Models\ProposalEvaluation::with('reviewer')
                ->where('research_proposal_id', $proposal->id)
                ->findOrFail($evaluationId);
        } else {
            // Find current user's evaluation if reviewer, or latest evaluation
            $evaluation = \App\Models\ProposalEvaluation::with('reviewer')
                ->where('research_proposal_id', $proposal->id)
                ->where('user_id', $currentUser->id)
                ->first();

            if (!$evaluation) {
                $evaluation = \App\Models\ProposalEvaluation::with('reviewer')
                    ->where('research_proposal_id', $proposal->id)
                    ->latest()
                    ->first();
            }

            // Fallback for printing blank form
            if (!$evaluation) {
                $evaluation = new \App\Models\ProposalEvaluation([
                    'research_proposal_id' => $proposal->id,
                    'score_a' => 0,
                    'score_b' => 0,
                    'score_c' => 0,
                    'score_d' => 0,
                    'score_e' => 0,
                    'total_score' => 0,
                    'meets_threshold' => false,
                    'decision' => 'pending',
                ]);
            }
        }

        $logoBase64 = null;
        if (file_exists(public_path('logo.png'))) {
            $logoBase64 = base64_encode(file_get_contents(public_path('logo.png')));
        }

        $pdf = \PDF::loadView('pdfs.resu_fm001', compact('proposal', 'evaluation', 'logoBase64'));
        $filename = 'RESU-FM-001_Evaluation_' . ($proposal->proposal_code ?? 'PR-' . $proposal->id) . '.pdf';
        
        return $pdf->stream($filename);
    }

    // Admin: View ALL proposals
    public function adminIndex()
    {
        $proposals = ResearchProposal::with(['user', 'assignments'])->latest()->paginate(15, ['*'], 'proposals_page');
        $reviewers = User::withRole('reviewer')->orderBy('name')->get();
        $announcements = \App\Models\Announcement::with('user')->latest()->paginate(15, ['*'], 'announcements_page');
        
        $stats = [
            'total' => ResearchProposal::count(),
            'pending' => ResearchProposal::where('status', 'pending')->count(),
            'approved' => ResearchProposal::whereIn('status', ['approved', 'final_approved'])->count(),
            'announcements' => \App\Models\Announcement::count()
        ];

        return view('admin.proposals', compact('proposals', 'reviewers', 'announcements', 'stats'));
    }

    // Admin: Final decision
    public function updatePhase(Request $request, $id)
    {
        $request->validate([
            'phase' => 'required|integer|min:1|max:5',
        ]);

        $proposal = ResearchProposal::findOrFail($id);
        
        if ($request->phase <= $proposal->current_phase && $request->phase != 1) {
             // Allow resetting if needed, but usually it moves forward
        }

        $proposal->update([
            'current_phase' => $request->phase,
            'phase_updated_at' => now(),
        ]);

        return redirect()->back()->with('success', "Proposal moved to Phase {$request->phase} successfully.");
    }

    public function adminFinalDecision(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:final_approved,final_rejected',
        ]);

        $proposal = ResearchProposal::findOrFail($id);

        if (in_array($proposal->status, ['final_approved', 'final_rejected']) && $request->status == $proposal->status) {
            return redirect()->back()->with('error', 'This proposal already has this final status.');
        }

        $proposal->update([
            'status' => $request->status,
        ]);

        if ($request->status === 'final_approved') {
            $proposal->user->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Final Approved',
                "Your proposal '{$proposal->title}' has received final approval from the Research Director.",
                'bi-check-circle-fill',
                'text-success'
            ));
        } else {
            $proposal->user->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Decision: Final Rejected',
                "Your proposal '{$proposal->title}' was rejected during administrative final decision.",
                'bi-x-circle-fill',
                'text-danger'
            ));
        }

        return redirect()->back()->with('success', 'Final decision applied successfully!');
    }

    public function show($id)
    {
        $proposal = ResearchProposal::with([
            'user', 'collaborators', 'assignments', 'milestones', 'budgetItems', 'purchaseRequests',
            'researchPresentations', 'researchPublications', 'localForumSubmissions.forum',
            'activities.causer'
        ])->findOrFail($id);

        $openForums = \App\Models\LocalResearchForum::where('status', 'open')->latest()->get();
        $activeRole = auth()->user()->activeRole();
        $isAuthorOrCollaborator = $proposal->user_id === auth()->id() || $proposal->collaborators->contains('id', auth()->id());
        $isAssignedReviewer = $proposal->assignments->contains('id', auth()->id());

        // Reviewers dropdown excludes lead author and collaborators to prevent conflict of interest
        $reviewers = (in_array($activeRole, ['admin', 'super_admin']) || auth()->user()->isSuperAdmin())
            ? User::withRole('reviewer')
                ->where('id', '!=', $proposal->user_id)
                ->whereNotIn('id', $proposal->collaborators->pluck('id'))
                ->orderBy('name')
                ->get()
            : collect();
        
        // Authorization check
        if (!auth()->user()->isSuperAdmin() && !in_array($activeRole, ['admin', 'staff', 'recording_staff', 'vprei', 'president', 'budget_officer', 'sao_finance'])) {
            if ($activeRole == 'researcher' && !$isAuthorOrCollaborator) {
                abort(403, 'Unauthorized access.');
            } elseif ($activeRole == 'reviewer' && !$isAssignedReviewer && !$isAuthorOrCollaborator) {
                abort(403, 'Unauthorized access. You are not assigned to review this proposal.');
            } elseif ($activeRole == 'dean') {
                if ($proposal->user->department !== auth()->user()->department) {
                    abort(403, 'Unauthorized access. You can only view proposals from your college/department.');
                }
            } elseif ($activeRole == 'coordinator') {
                if ($proposal->user->department !== auth()->user()->department) {
                    abort(403, 'Unauthorized access. You can only view proposals from your college/department.');
                }
            }
        }

        return view('proposals.show', compact('proposal', 'openForums', 'reviewers'));
    }

    // Researcher: Show edit form for revision
    public function edit($id)
    {
        $proposal = ResearchProposal::with(['collaborators', 'budgetItems', 'milestones'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!in_array($proposal->status, ['revision_required', 'draft', 'returned_for_revision'])) {
            abort(403, 'You cannot edit this proposal.');
        }

        $researchers = User::where('role', 'researcher')
            ->where('id', '!=', auth()->id())
            ->where(function ($q) use ($proposal) {
                $q->where('allow_collaboration', true)
                  ->orWhereNull('allow_collaboration')
                  ->orWhereIn('id', $proposal->collaborators->pluck('id'));
            })
            ->orderBy('name')
            ->get();

        return view('researcher.edit_proposal', compact('proposal', 'researchers'));
    }

    // Researcher: Update and resubmit proposal
    public function update(Request $request, $id)
    {
        $proposal = ResearchProposal::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!in_array($proposal->status, ['revision_required', 'draft', 'returned_for_revision'])) {
            abort(403, 'You cannot update this proposal.');
        }

        $isDraft = $request->action === 'draft';

        $request->validate([
            'title' => 'required',
            'abstract' => 'nullable|string',
            'rationale' => 'nullable|string',
            'research_field' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf|max:20480',
            'collaborators' => 'nullable|array',
            'collaborators.*' => 'exists:users,id',
            // Template fields
            'credentials' => 'nullable|string',
            'college_affiliation' => 'nullable|string',
            'sdgs_addressed' => 'nullable|string',
            'background_of_study' => 'nullable|string',
            'significance_of_study' => 'nullable|string',
            'definition_of_terms' => 'nullable|string',
            'literature_review' => 'nullable|string',
            'methodology' => 'nullable|string',
            'references' => 'nullable|string',
        ]);

        $filePath = $proposal->document_path;

        if ($request->hasFile('document')) {
            // BUG-08 FIXED: Previous file is no longer deleted to maintain version history
            $filePath = $request->file('document')->store('documents', config('filesystems.default', 'public'));
            
            $latestDoc = $proposal->documents()->where('document_type', 'manuscript')->latest('version')->first();
            $nextVersion = $latestDoc ? $latestDoc->version + 1 : 2;
            $phase = $proposal->current_phase ?? 1;
            $documentTag = "{$proposal->proposal_code}-PH{$phase}-MANUSCRIPT-V{$nextVersion}";

            $proposal->documents()->create([
                'document_tag' => $documentTag,
                'document_type' => 'manuscript',
                'phase' => $phase,
                'version' => $nextVersion,
                'file_path' => $filePath,
            ]);

            $extractedText = $request->extracted_text;
            if (empty($extractedText)) {
                try {
                    $disk = config('filesystems.default', 'public');
                    $fullPath = \Storage::disk($disk)->path($filePath);
                    if (file_exists($fullPath)) {
                        $extractedText = \App\Services\PdfTextExtractor::extractText($fullPath);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('PDF extraction failed on update: ' . $e->getMessage());
                }
            }
            if (!empty($extractedText)) {
                $updateData['extracted_text'] = $extractedText;
            }
        }

        $newStatus = 'pending_coordinator_endorsement';
        if ($proposal->status === 'revision_required' || $proposal->status === 'approved_with_revisions') {
            $newStatus = 'accepted_for_in_house_review';
        } elseif ($proposal->status === 'returned_for_revision' && $proposal->returned_by_role === 'staff') {
            // Option A: Fast-track directly back to Receiving Staff
            $newStatus = 'submitted_to_research_unit';
        }

        // Re-synthesize extracted text if template sections were updated
        $updatedExtractedText = $proposal->extracted_text;
        if ($request->background_of_study || $request->methodology || $request->literature_review) {
            $sections = [];
            if ($request->title) $sections[] = "RESEARCH TITLE:\n" . $request->title;
            if ($request->credentials) $sections[] = "KEY PROPONENTS & CREDENTIALS:\n" . $request->credentials;
            if ($request->background_of_study) $sections[] = "BACKGROUND OF THE STUDY & THEORETICAL FRAMEWORK:\n" . $request->background_of_study;
            if ($request->significance_of_study) $sections[] = "SIGNIFICANCE OF THE STUDY:\n" . $request->significance_of_study;
            if ($request->definition_of_terms) $sections[] = "DEFINITION OF TERMS:\n" . $request->definition_of_terms;
            if ($request->literature_review) $sections[] = "REVIEW OF RELATED LITERATURE:\n" . $request->literature_review;
            if ($request->methodology) $sections[] = "METHODOLOGY & RESEARCH DESIGN:\n" . $request->methodology;
            if ($request->references) $sections[] = "REFERENCES:\n" . $request->references;
            $updatedExtractedText = implode("\n\n", $sections);
        }

        $abstractText = $request->abstract ?: ($request->background_of_study ? \Illuminate\Support\Str::limit($request->background_of_study, 500) : $proposal->abstract);
        $rationaleText = $request->rationale ?: ($request->background_of_study ?: $proposal->rationale);

        $updateData = [
            'title' => $request->title,
            'abstract' => $abstractText,
            'rationale' => $rationaleText,
            'research_field' => $request->research_field,
            'credentials' => $request->credentials ?? $proposal->credentials,
            'college_affiliation' => $request->college_affiliation ?: ($proposal->college_affiliation ?: auth()->user()->department),
            'sdgs_addressed' => $request->sdgs_addressed ?? $proposal->sdgs_addressed,
            'background_of_study' => $request->background_of_study ?? $proposal->background_of_study,
            'significance_of_study' => $request->significance_of_study ?? $proposal->significance_of_study,
            'definition_of_terms' => $request->definition_of_terms ?? $proposal->definition_of_terms,
            'literature_review' => $request->literature_review ?? $proposal->literature_review,
            'methodology' => $request->methodology ?? $proposal->methodology,
            'references' => $request->references ?? $proposal->references,
            'work_plan_matrix' => $request->work_plan_matrix ?? $proposal->work_plan_matrix,
            'extracted_text' => $updatedExtractedText,
            'document_path' => $filePath, // Update main path for backwards compatibility
            'status' => $isDraft ? 'draft' : $newStatus,
        ];

        // Update line-item budget if provided
        if ($request->has('budget_items') && is_array($request->budget_items)) {
            $proposal->budgetItems()->delete();
            $totalBudget = 0;
            foreach ($request->budget_items as $bItem) {
                if (!empty($bItem['item_name']) && isset($bItem['amount']) && $bItem['amount'] !== '') {
                    $cost = floatval(str_replace(',', '', $bItem['amount']));
                    $catGroup = $bItem['category_group'] ?? ($bItem['category'] ?? 'supplies');
                    $catType = $bItem['category_type'] ?? (in_array($catGroup, ['capital_outlay', 'co']) ? 'co' : 'mooe');
                    $proposal->budgetItems()->create([
                        'category_type' => $catType,
                        'category_group' => $catGroup,
                        'item_name' => $bItem['item_name'],
                        'funding_agency' => $bItem['funding_agency'] ?? 'University of Antique',
                        'existing_resources' => $bItem['existing_resources'] ?? null,
                        'amount' => $cost,
                    ]);
                    $totalBudget += $cost;
                }
            }
            if ($totalBudget > 0) {
                $updateData['total_budget'] = $totalBudget;
            }
        }

        // Update work plan milestones if provided
        if ($request->has('milestones') && is_array($request->milestones)) {
            $proposal->milestones()->delete();
            foreach ($request->milestones as $ms) {
                if (!empty($ms['title'])) {
                    $proposal->milestones()->create([
                        'title' => $ms['title'],
                        'start_date' => !empty($ms['start_date']) ? $ms['start_date'] : null,
                        'target_date' => !empty($ms['target_date']) ? $ms['target_date'] : null,
                        'description' => $ms['description'] ?? 'Work plan activity',
                        'status' => 'pending',
                    ]);
                }
            }
        }

        if (!$isDraft && $proposal->status === 'returned_for_revision') {
            $updateData['return_reason'] = null;
            $updateData['returned_by_role'] = null;
            $updateData['returned_at'] = null;
        }

        $proposal->update($updateData);

        if ($request->has('collaborators') && is_array($request->collaborators)) {
            $existing = $proposal->collaborators()->get()->keyBy('id');
            $syncData = [];
            $newlyInvitedIds = [];

            foreach ($request->collaborators as $collabId) {
                if ($existing->has($collabId)) {
                    $item = $existing->get($collabId);
                    $syncData[$collabId] = [
                        'status' => $item->pivot->status ?? 'pending',
                        'invited_at' => $item->pivot->invited_at ?? now(),
                        'responded_at' => $item->pivot->responded_at ?? null,
                    ];
                } else {
                    $syncData[$collabId] = [
                        'status' => 'pending',
                        'invited_at' => now(),
                        'responded_at' => null,
                    ];
                    $newlyInvitedIds[] = $collabId;
                }
            }
            $proposal->collaborators()->sync($syncData);

            if (!empty($newlyInvitedIds)) {
                $newlyInvited = User::whereIn('id', $newlyInvitedIds)->get();
                foreach ($newlyInvited as $collabUser) {
                    $collabUser->notify(new \App\Notifications\CollaborationInvitationNotification($proposal, auth()->user()));
                }
            }
        } else {
            $proposal->collaborators()->detach();
        }

        if (!$isDraft && $newStatus === 'submitted_to_research_unit') {
            $staffUsers = User::where('role', 'staff')->get();
            foreach ($staffUsers as $staff) {
                $staff->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Revised Proposal Resubmitted',
                    "Researcher {$proposal->user->name} resubmitted revised manuscript for '{$proposal->title}' addressing administrative completeness.",
                    'bi-arrow-repeat',
                    'text-info'
                ));
            }
        } elseif (!$isDraft && $newStatus === 'pending_coordinator_endorsement') {
            $coords = User::where('role', 'coordinator')
                ->where('department', $proposal->user->department)
                ->get();
            foreach ($coords as $coord) {
                $coord->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Proposal Resubmitted for Endorsement',
                    "Researcher {$proposal->user->name} resubmitted revised proposal '{$proposal->title}' for your endorsement.",
                    'bi-arrow-repeat',
                    'text-primary'
                ));
            }
        } elseif (!$isDraft && $newStatus === 'accepted_for_in_house_review') {
            $assignments = \DB::table('proposal_assignments')->where('research_proposal_id', $proposal->id)->pluck('user_id');
            $reviewers = User::whereIn('id', $assignments)->get();
            foreach ($reviewers as $rev) {
                $rev->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Revised Manuscript Resubmitted',
                    "Researcher {$proposal->user->name} resubmitted revised manuscript for '{$proposal->title}' addressing reviewer comments.",
                    'bi-arrow-repeat',
                    'text-info'
                ));
            }
        }

        if ($isDraft) {
            $msg = 'Draft updated successfully!';
        } elseif ($newStatus === 'submitted_to_research_unit') {
            $msg = 'Proposal updated and resubmitted directly to the Research Unit receiving queue!';
        } else {
            $msg = 'Proposal submitted successfully!';
        }
        return redirect('/proposal/my')->with('success', $msg);
    }

    public function destroy($id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->user_id !== auth()->id()) {
            abort(403, 'Unauthorized. Only the lead researcher can delete this proposal.');
        }

        // Allowed only as long as the college coordinator has not endorsed it yet
        $deletableStatuses = ['draft', 'pending_coordinator_endorsement', 'returned_for_revision', 'pending'];

        if (!in_array($proposal->status, $deletableStatuses)) {
            return redirect()->back()->with('error', 'Proposals can only be deleted before they are endorsed by the college coordinator.');
        }

        \DB::transaction(function () use ($proposal) {
            // Delete the main manuscript file from storage
            if ($proposal->document_path && \Storage::disk(config('filesystems.default', 'public'))->exists($proposal->document_path)) {
                \Storage::disk(config('filesystems.default', 'public'))->delete($proposal->document_path);
            }

            // Delete terminal report file if any
            if ($proposal->terminal_report_path && \Storage::disk(config('filesystems.default', 'public'))->exists($proposal->terminal_report_path)) {
                \Storage::disk(config('filesystems.default', 'public'))->delete($proposal->terminal_report_path);
            }

            // Delete all versioned document files
            foreach ($proposal->documents as $doc) {
                if ($doc->file_path && \Storage::disk(config('filesystems.default', 'public'))->exists($doc->file_path)) {
                    \Storage::disk(config('filesystems.default', 'public'))->delete($doc->file_path);
                }
            }

            // Detach relations
            $proposal->collaborators()->detach();
            $proposal->assignments()->detach();

            // Clean up related notifications (PostgreSQL safe)
            \Illuminate\Notifications\DatabaseNotification::where('data', 'like', '%"proposal_id":' . $proposal->id . '%')
                ->orWhere('data', 'like', '%"proposal_id":"' . $proposal->id . '"%')
                ->get()
                ->filter(function ($n) use ($proposal) {
                    return isset($n->data['proposal_id']) && (int)$n->data['proposal_id'] === (int)$proposal->id;
                })
                ->each->delete();

            // Delete the proposal record
            $proposal->delete();
        });

        return redirect()->route('proposal.index')->with('success', 'Proposal deleted successfully!');
    }

    public function downloadNoticeOfAcceptance($id)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators', 'assignments'])->findOrFail($id);

        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id() && !$proposal->collaborators->contains(auth()->id())) {
            abort(403, 'Unauthorized access.');
        } elseif (auth()->user()->role == 'reviewer' && !$proposal->assignments->contains('id', auth()->id())) {
            abort(403, 'Unauthorized access.');
        }

        $allowedStatuses = [
            'accepted_for_in_house_review',
            'under_review',
            'revision_required',
            'returned_for_revision',
            'approved_with_revisions',
            'approved',
            'final_copy_submitted',
            'final_copy_noted_by_dean',
            'pending_budget_certification',
            'funds_certified',
            'endorsed_to_vprei',
            'final_approved',
            'ongoing',
            'completed',
            'archived'
        ];

        if (!in_array($proposal->status, $allowedStatuses)) {
            abort(403, 'This proposal has not been accepted for in-house review yet.');
        }

        $pdf = \PDF::loadView('pdfs.notice', compact('proposal'));
        return $pdf->download('Notice_of_Acceptance_' . $proposal->id . '.pdf');
    }

    public function downloadNoticeToProceed($id)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators', 'assignments'])->findOrFail($id);

        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id() && !$proposal->collaborators->contains(auth()->id())) {
            abort(403, 'Unauthorized access.');
        } elseif (auth()->user()->role == 'reviewer' && !$proposal->assignments->contains('id', auth()->id())) {
            abort(403, 'Unauthorized access.');
        }

        if (!in_array($proposal->status, ['final_approved', 'ongoing', 'completed', 'archived'])) {
            abort(403, 'This proposal has not received final approval yet.');
        }

        $pdf = \PDF::loadView('pdfs.ntp', compact('proposal'));
        return $pdf->download('Notice_to_Proceed_' . $proposal->id . '.pdf');
    }

    public function downloadCertificate($id)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators', 'assignments'])->findOrFail($id);

        if (auth()->user()->role == 'researcher' && $proposal->user_id !== auth()->id() && !$proposal->collaborators->contains(auth()->id())) {
            abort(403, 'Unauthorized access.');
        } elseif (auth()->user()->role == 'reviewer' && !$proposal->assignments->contains('id', auth()->id())) {
            abort(403, 'Unauthorized access.');
        }

        if ($proposal->current_phase != 5) {
            abort(403, 'This research has not reached completion phase yet.');
        }

        $pdf = \PDF::loadView('pdfs.certificate', compact('proposal'));
        return $pdf->download('Certificate_of_Completion_' . $proposal->id . '.pdf');
    }

    public function submitFinalManuscript(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'final_manuscript' => 'required|file|mimes:pdf|max:20480',
        ]);

        $filePath = $request->file('final_manuscript')->store('terminal_reports', config('filesystems.default', 'public'));

        $proposal->documents()->create([
            'document_tag' => "{$proposal->proposal_code}-PH6-FINALMANUSCRIPT",
            'document_type' => 'terminal_report',
            'phase' => 6,
            'version' => 1,
            'file_path' => $filePath,
        ]);

        $proposal->update([
            'current_phase' => 6,
            'terminal_report_path' => $filePath,
            'status' => 'completed'
        ]);

        return redirect()->back()->with('success', 'Final Manuscript submitted successfully.');
    }

    public function acceptForInHouseReview($id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        if ($proposal->status !== 'pending_director_review') {
            return redirect()->back()->with('error', 'Only proposals pending Director review can be accepted for In-House review.');
        }

        $proposal->update([
            'status' => 'accepted_for_in_house_review',
            'current_phase' => 2, // Technical/Ethics Review
            'phase_updated_at' => now(),
        ]);

        // Notify Lead Researcher
        $proposal->user->notify(new WorkflowStatusNotification(
            $proposal,
            'Accepted for In-House Review',
            "Notice of Acceptance issued: Your proposal '{$proposal->title}' was accepted for In-House Review and is moving to Phase 2 (Technical & Ethics Review).",
            'bi-award-fill',
            'text-success'
        ));

        // Notify College Coordinator
        $coordinators = User::where('role', 'coordinator')
            ->where('department', $proposal->user->department)
            ->get();
        foreach ($coordinators as $coord) {
            $coord->notify(new WorkflowStatusNotification(
                $proposal,
                'Proposal Accepted for In-House Review',
                "Proposal '{$proposal->title}' ({$proposal->user->name}) was officially accepted by Research Director for In-House Review.",
                'bi-award-fill',
                'text-primary'
            ));
        }

        return redirect()->back()->with('success', 'Proposal accepted for In-House review. "IN-HOUSE REVIEW ACCEPTANCE FORM" issued.');
    }

    public function endorseToVprei($id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        if ($proposal->status !== 'funds_certified') {
            return redirect()->back()->with('error', 'Only proposals certified by the Budget Officer for availability of funds can be endorsed to the VPREI.');
        }

        $proposal->update([
            'status' => 'endorsed_to_vprei'
        ]);

        // Notify VPREI
        $vpreiUsers = User::where('role', 'vprei')->get();
        foreach ($vpreiUsers as $vprei) {
            $vprei->notify(new WorkflowStatusNotification(
                $proposal,
                'New Proposal Endorsed for Final Approval',
                "Proposal '{$proposal->title}' has been endorsed by Research Director for your final approval and Notice to Proceed (NTP).",
                'bi-send-check-fill',
                'text-primary'
            ));
        }

        // Notify Lead Researcher
        $proposal->user->notify(new WorkflowStatusNotification(
            $proposal,
            'Endorsed to VPREI for Final Approval',
            "Your proposal '{$proposal->title}' has been endorsed by the Research Director to the Office of the VPREI for final approval.",
            'bi-send-check-fill',
            'text-info'
        ));

        return redirect()->back()->with('success', 'Proposal successfully endorsed to the VPREI.');
    }

    public function submitFinalCopy(Request $request, $id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if ($proposal->user_id !== auth()->id()) {
            abort(403);
        }

        if ($proposal->status !== 'approved') {
            return redirect()->back()->with('error', 'You can only submit the final copy after the proposal is approved in the In-House review.');
        }

        $request->validate([
            'final_copy' => 'required|file|mimes:pdf|max:20480',
        ]);

        $filePath = $request->file('final_copy')->store('documents', config('filesystems.default', 'public'));

        // Save versioned document
        $latestDoc = $proposal->documents()->where('document_type', 'manuscript')->latest('version')->first();
        $nextVersion = $latestDoc ? $latestDoc->version + 1 : 2;
        $documentTag = "{$proposal->proposal_code}-PH2-FINALCOPY-V{$nextVersion}";

        $proposal->documents()->create([
            'document_tag' => $documentTag,
            'document_type' => 'manuscript',
            'phase' => 2,
            'version' => $nextVersion,
            'file_path' => $filePath,
        ]);

        $proposal->update([
            'document_path' => $filePath,
            'status' => 'final_copy_submitted'
        ]);

        // Notify College Dean
        $deans = User::where('role', 'dean')
            ->where('department', $proposal->user->department)
            ->get();
        foreach ($deans as $dean) {
            $dean->notify(new WorkflowStatusNotification(
                $proposal,
                'Final Copy Submitted for Executive Noting',
                "Researcher {$proposal->user->name} submitted final revised copy of '{$proposal->title}' awaiting your noting.",
                'bi-file-earmark-check-fill',
                'text-primary'
            ));
        }

        // Notify Lead Researcher
        $proposal->user->notify(new WorkflowStatusNotification(
            $proposal,
            'Final Copy Submitted',
            "Your final copy for '{$proposal->title}' was submitted successfully and is awaiting noting by your College Dean.",
            'bi-check2-circle',
            'text-success'
        ));

        return redirect()->back()->with('success', 'Final copy of research proposal submitted successfully.');
    }

    public function archiveProposal($id)
    {
        $proposal = ResearchProposal::findOrFail($id);

        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'super_admin') {
            abort(403);
        }

        $proposal->update([
            'status' => 'archived'
        ]);

        return redirect()->back()->with('success', 'Proposal successfully archived to the repository.');
    }

    public function exportResuFm015($id)
    {
        $proposal = ResearchProposal::with([
            'user', 'collaborators', 'milestones', 'budgetItems'
        ])->findOrFail($id);

        return view('reports.proposal_resu_fm015', compact('proposal'));
    }

    /**
     * Collaborator: Accept or decline a collaboration invitation.
     */
    public function respondToCollaboration($id, $action)
    {
        $proposal = ResearchProposal::with(['user', 'collaborators'])->findOrFail($id);
        $user = auth()->user();

        $collaborator = $proposal->collaborators()->where('users.id', $user->id)->first();
        if (!$collaborator) {
            abort(403, 'You are not invited as a collaborator for this proposal.');
        }

        if ($action === 'accept') {
            $proposal->collaborators()->updateExistingPivot($user->id, [
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // Notify Lead Researcher
            $proposal->user->notify(new WorkflowStatusNotification(
                $proposal,
                'Collaboration Accepted',
                $user->name . ' accepted your invitation to collaborate on "' . \Illuminate\Support\Str::limit($proposal->title, 60) . '".',
                'bi-check-circle-fill',
                'text-success'
            ));

            // Mark invitation notifications for this proposal as read
            $user->unreadNotifications
                ->where('data.proposal_id', $proposal->id)
                ->where('data.type', 'collaboration_invitation')
                ->markAsRead();

            return redirect()->back()->with('success', 'You have accepted the collaboration invitation. You are now officially a co-proponent on this research.');
        } elseif ($action === 'decline') {
            $proposal->collaborators()->updateExistingPivot($user->id, [
                'status' => 'declined',
                'responded_at' => now(),
            ]);

            // Notify Lead Researcher
            $proposal->user->notify(new WorkflowStatusNotification(
                $proposal,
                'Collaboration Declined',
                $user->name . ' declined the invitation to collaborate on "' . \Illuminate\Support\Str::limit($proposal->title, 60) . '".',
                'bi-x-circle-fill',
                'text-danger'
            ));

            // Mark invitation notifications for this proposal as read
            $user->unreadNotifications
                ->where('data.proposal_id', $proposal->id)
                ->where('data.type', 'collaboration_invitation')
                ->markAsRead();

            return redirect()->back()->with('info', 'You have declined the collaboration invitation.');
        }

        return redirect()->back();
    }

    /**
     * In-Place Update a specific manuscript section from the Review Workspace.
     */
    public function updateSection(Request $request, $id)
    {
        $proposal = ResearchProposal::with(['collaborators'])->findOrFail($id);

        $currentUser = auth()->user();
        $activeRole = $currentUser->activeRole();
        $isAuthorOrCollaborator = $proposal->user_id === $currentUser->id || $proposal->collaborators->contains('id', $currentUser->id);
        $isAdminOrStaff = in_array($activeRole, ['admin', 'super_admin', 'staff', 'recording_staff', 'vprei']) || $currentUser->isSuperAdmin();

        if (!$isAuthorOrCollaborator && !$isAdminOrStaff) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only proposal authors or administrators can edit the manuscript.'
            ], 403);
        }

        if (in_array($proposal->status, ['final_approved', 'rejected', 'archived', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'This proposal is archived or completed and can no longer be edited.'
            ], 422);
        }

        $request->validate([
            'section' => 'required|string|in:title,abstract,background_of_study,significance_of_study,definition_of_terms,literature_review,methodology,references,credentials,sdgs_addressed,extracted_text,manuscript_section',
            'section_index' => 'nullable|integer|min:0',
            'content' => 'nullable|string',
        ]);

        $section = $request->section;
        $content = $request->content ?? '';

        // Handle parsed manuscript section update
        if ($section === 'manuscript_section') {
            $sectionIndex = $request->input('section_index');
            if ($sectionIndex === null) {
                return response()->json(['success' => false, 'message' => 'Section index is required.'], 422);
            }

            $sections = \App\Services\PdfTextExtractor::parseManuscriptSections($proposal->extracted_text ?? '');
            if (!isset($sections[$sectionIndex])) {
                return response()->json(['success' => false, 'message' => 'Specified section was not found.'], 404);
            }

            // Update this section's content
            $sections[$sectionIndex]['content'] = $content;

            // If mapped to a database field, synchronize column
            if (!empty($sections[$sectionIndex]['field'])) {
                $fieldName = $sections[$sectionIndex]['field'];
                if (in_array($fieldName, ['background_of_study', 'significance_of_study', 'definition_of_terms', 'literature_review', 'methodology', 'references', 'credentials', 'title'])) {
                    $proposal->{$fieldName} = $content;
                    if ($fieldName === 'background_of_study') {
                        $proposal->rationale = $content;
                    }
                }
            }

            // Recombine into continuous extracted text
            $proposal->extracted_text = \App\Services\PdfTextExtractor::recombineSections($sections);
            $proposal->save();

            $renderedHtml = \App\Services\PdfTextExtractor::formatSectionContentHtml($content);

            return response()->json([
                'success' => true,
                'message' => ($sections[$sectionIndex]['title'] ?? 'Section') . ' updated successfully.',
                'section' => 'manuscript_section',
                'section_index' => $sectionIndex,
                'raw_text' => $content,
                'rendered_html' => $renderedHtml,
            ]);
        }

        $updatePayload = [];

        switch ($section) {
            case 'title':
                if (empty(trim($content))) {
                    return response()->json(['success' => false, 'message' => 'Title cannot be empty.'], 422);
                }
                $updatePayload['title'] = trim($content);
                break;

            case 'abstract':
                $updatePayload['abstract'] = $content;
                break;

            case 'background_of_study':
                $updatePayload['background_of_study'] = $content;
                $updatePayload['rationale'] = $content;
                break;

            case 'significance_of_study':
                $updatePayload['significance_of_study'] = $content;
                break;

            case 'definition_of_terms':
                $updatePayload['definition_of_terms'] = $content;
                break;

            case 'literature_review':
                $updatePayload['literature_review'] = $content;
                break;

            case 'methodology':
                $updatePayload['methodology'] = $content;
                break;

            case 'references':
                $updatePayload['references'] = $content;
                break;

            case 'credentials':
                $updatePayload['credentials'] = $content;
                break;

            case 'sdgs_addressed':
                $updatePayload['sdgs_addressed'] = $content;
                break;

            case 'extracted_text':
                $updatePayload['extracted_text'] = $content;
                break;
        }

        $proposal->update($updatePayload);

        // Re-synthesize extracted_text if structured fields were updated
        if (in_array($section, ['title', 'credentials', 'background_of_study', 'significance_of_study', 'definition_of_terms', 'literature_review', 'methodology', 'references'])) {
            if ($proposal->background_of_study || $proposal->methodology || $proposal->literature_review) {
                $parts = [];
                if ($proposal->title) $parts[] = "RESEARCH TITLE:\n" . $proposal->title;
                if ($proposal->credentials) $parts[] = "KEY PROPONENTS & CREDENTIALS:\n" . $proposal->credentials;
                if ($proposal->background_of_study) $parts[] = "BACKGROUND OF THE STUDY & THEORETICAL FRAMEWORK:\n" . $proposal->background_of_study;
                if ($proposal->significance_of_study) $parts[] = "SIGNIFICANCE OF THE STUDY:\n" . $proposal->significance_of_study;
                if ($proposal->definition_of_terms) $parts[] = "DEFINITION OF TERMS:\n" . $proposal->definition_of_terms;
                if ($proposal->literature_review) $parts[] = "REVIEW OF RELATED LITERATURE:\n" . $proposal->literature_review;
                if ($proposal->methodology) $parts[] = "METHODOLOGY & RESEARCH DESIGN:\n" . $proposal->methodology;
                if ($proposal->references) $parts[] = "REFERENCES:\n" . $proposal->references;
                $proposal->update(['extracted_text' => implode("\n\n", $parts)]);
            }
        }

        // Render response HTML for seamless frontend injection
        $renderedHtml = '';
        if ($section === 'extracted_text') {
            $renderedHtml = \App\Services\PdfTextExtractor::formatTextToHtml($content);
        } elseif ($section === 'title') {
            $renderedHtml = e($content);
        } else {
            $renderedHtml = nl2br(e($content));
        }

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully.',
            'section' => $section,
            'raw_text' => $content,
            'rendered_html' => $renderedHtml,
        ]);
    }

    /**
     * Resubmit revisions from the Review Workspace.
     */
    public function resubmitRevisions(Request $request, $id)
    {
        $proposal = ResearchProposal::with(['collaborators', 'user'])->findOrFail($id);

        $currentUser = auth()->user();
        $activeRole = $currentUser->activeRole();
        $isAuthorOrCollaborator = $proposal->user_id === $currentUser->id || $proposal->collaborators->contains('id', $currentUser->id);
        $isAdminOrStaff = in_array($activeRole, ['admin', 'super_admin', 'staff', 'recording_staff', 'vprei']) || $currentUser->isSuperAdmin();

        if (!$isAuthorOrCollaborator && !$isAdminOrStaff) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only proposal authors or administrators can resubmit revisions.'
            ], 403);
        }

        $newStatus = 'accepted_for_in_house_review';
        if ($proposal->status === 'returned_for_revision' && $proposal->returned_by_role === 'staff') {
            $newStatus = 'submitted_to_research_unit';
        } elseif ($proposal->status === 'returned_for_revision' && $proposal->returned_by_role === 'coordinator') {
            $newStatus = 'pending_coordinator_endorsement';
        }

        $proposal->update([
            'status' => $newStatus,
            'return_reason' => null,
            'returned_by_role' => null,
            'returned_at' => null,
        ]);

        // Send notifications to stakeholders
        if ($newStatus === 'accepted_for_in_house_review') {
            $assignments = \DB::table('proposal_assignments')->where('research_proposal_id', $proposal->id)->pluck('user_id');
            $reviewers = User::whereIn('id', $assignments)->get();
            foreach ($reviewers as $rev) {
                $rev->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Revised Proposal Resubmitted',
                    "Researcher {$proposal->user->name} resubmitted revised manuscript for '{$proposal->title}'.",
                    'bi-arrow-repeat',
                    'text-success'
                ));
            }
        } elseif ($newStatus === 'submitted_to_research_unit') {
            $staffUsers = User::where('role', 'staff')->get();
            foreach ($staffUsers as $staff) {
                $staff->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Revised Proposal Resubmitted',
                    "Researcher {$proposal->user->name} resubmitted revised manuscript for '{$proposal->title}' addressing administrative completeness.",
                    'bi-arrow-repeat',
                    'text-info'
                ));
            }
        } elseif ($newStatus === 'pending_coordinator_endorsement') {
            $coords = User::where('role', 'coordinator')
                ->where('department', $proposal->user->department)
                ->get();
            foreach ($coords as $coord) {
                $coord->notify(new WorkflowStatusNotification(
                    $proposal,
                    'Proposal Resubmitted for Endorsement',
                    "Researcher {$proposal->user->name} resubmitted revised proposal '{$proposal->title}' for your endorsement.",
                    'bi-arrow-repeat',
                    'text-primary'
                ));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Proposal revisions have been resubmitted successfully.',
            'new_status' => $newStatus,
        ]);
    }
}