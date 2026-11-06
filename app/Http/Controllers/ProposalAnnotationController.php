<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResearchProposal;
use App\Models\ProposalAnnotation;
use App\Models\User;
use App\Notifications\ProposalAnnotationNotification;
use Illuminate\Support\Facades\Auth;

class ProposalAnnotationController extends Controller
{
    /**
     * Display the Google Docs-style Split-Screen Review & Annotation Workspace.
     */
    public function workspace($id)
    {
        $proposal = ResearchProposal::with([
            'user',
            'collaborators',
            'assignments',
            'documents',
            'milestones',
            'budgetItems',
            'annotations.user',
            'annotations.taggedUser',
            'annotations.replies.user'
        ])->findOrFail($id);

        $currentUser = Auth::user();
        $activeRole = $currentUser->activeRole();
        $isAuthorOrCollaborator = $proposal->user_id === $currentUser->id || $proposal->collaborators->contains('id', $currentUser->id);
        $isAssignedReviewer = $proposal->assignments->contains('id', $currentUser->id);
        $isAdminOrStaff = in_array($activeRole, ['admin', 'super_admin', 'staff', 'vprei']) || $currentUser->isSuperAdmin();

        // Authorization check
        if (!$isAdminOrStaff && !$isAuthorOrCollaborator && !$isAssignedReviewer && $activeRole !== 'reviewer') {
            abort(403, 'Unauthorized access to review workspace.');
        }

        // Taggable candidates: Lead Author + Collaborators
        $taggableUsers = collect([$proposal->user])->merge($proposal->collaborators)->unique('id');

        // Available chapters/sections for structured feedback (aligned with Form RESU-FM-015)
        $chapters = [
            'General / Whole Document' => 'General / Whole Document',
            'Title & Proponent Credentials' => 'Title & Proponent Credentials',
            'Abstract' => 'Executive Abstract',
            'Background of the Study & Problem' => 'Background of the Study & Problem Statement',
            'Significance of the Study' => 'Significance of the Study',
            'Definition of Terms' => 'Definition of Terms',
            'Review of Related Literature' => 'Review of Related Literature (RRL)',
            'Methodology & Research Design' => 'Methodology & Research Design',
            'Work Plan & Gantt Matrix' => 'Work Plan & Gantt Matrix',
            'Line-Item Budget (LIB)' => 'Line-Item Budget (LIB)',
            'References & Citations' => 'References & Citations',
        ];

        return view('proposals.review_workspace', compact('proposal', 'taggableUsers', 'chapters', 'isAuthorOrCollaborator', 'isAssignedReviewer', 'isAdminOrStaff'));
    }

    /**
     * Store a new annotation or thread reply.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:5000',
            'highlighted_text' => 'nullable|string|max:5000',
            'highlight_anchor' => 'nullable|string|max:255',
            'chapter_section' => 'nullable|string|max:255',
            'tagged_user_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:proposal_annotations,id',
        ]);

        $proposal = ResearchProposal::with(['user', 'collaborators'])->findOrFail($id);
        $currentUser = Auth::user();

        $taggedUser = $request->tagged_user_id ? User::find($request->tagged_user_id) : null;

        $annotation = ProposalAnnotation::create([
            'research_proposal_id' => $proposal->id,
            'user_id' => $currentUser->id,
            'tagged_user_id' => $taggedUser ? $taggedUser->id : null,
            'tagged_email' => $taggedUser ? $taggedUser->email : null,
            'parent_id' => $request->parent_id,
            'chapter_section' => $request->chapter_section ?: 'General / Whole Document',
            'highlighted_text' => $request->highlighted_text,
            'highlight_anchor' => $request->highlight_anchor,
            'comment' => $request->comment,
            'is_resolved' => false,
        ]);

        $annotation->load(['user', 'taggedUser', 'replies.user']);

        // Dispatch notification
        try {
            if ($taggedUser && $taggedUser->id !== $currentUser->id) {
                $taggedUser->notify(new ProposalAnnotationNotification($annotation, $currentUser->name));
            } elseif ($proposal->user_id !== $currentUser->id) {
                // If not tagging a specific user, notify lead author
                $proposal->user->notify(new ProposalAnnotationNotification($annotation, $currentUser->name));
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed sending annotation notification: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review comment posted successfully!',
                'annotation' => $annotation,
                'user' => [
                    'id' => $currentUser->id,
                    'name' => $currentUser->name,
                    'avatar_initial' => strtoupper(substr($currentUser->name, 0, 1)),
                    'role' => ucfirst($currentUser->role ?? 'Reviewer'),
                ],
                'created_at_human' => $annotation->created_at->diffForHumans(),
            ]);
        }

        return redirect()->back()->with('success', 'Review comment added successfully!');
    }

    /**
     * Toggle the resolved status of an annotation.
     */
    public function toggleResolve(Request $request, $id)
    {
        $annotation = ProposalAnnotation::findOrFail($id);
        $currentUser = Auth::user();

        $annotation->is_resolved = !$annotation->is_resolved;
        $annotation->resolved_at = $annotation->is_resolved ? now() : null;
        $annotation->resolved_by = $annotation->is_resolved ? $currentUser->id : null;
        $annotation->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_resolved' => $annotation->is_resolved,
                'resolved_by_name' => $currentUser->name,
                'resolved_at_human' => $annotation->resolved_at ? $annotation->resolved_at->diffForHumans() : null,
                'message' => $annotation->is_resolved ? 'Comment marked as resolved!' : 'Comment reopened.',
            ]);
        }

        return redirect()->back()->with('success', 'Annotation status updated.');
    }

    /**
     * Delete an annotation.
     */
    public function destroy(Request $request, $id)
    {
        $annotation = ProposalAnnotation::findOrFail($id);
        $currentUser = Auth::user();

        if ($annotation->user_id !== $currentUser->id && !in_array($currentUser->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized to delete this comment.');
        }

        $annotation->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Comment deleted.');
    }

    /**
     * Export / Print a formal In-House Review Summary Sheet of all annotations.
     */
    public function exportSummary($id)
    {
        $proposal = ResearchProposal::with([
            'user',
            'collaborators',
            'assignments',
            'annotations.user',
            'annotations.taggedUser',
            'annotations.replies.user'
        ])->findOrFail($id);

        $annotations = $proposal->annotations;

        return view('reports.proposal_review_summary', compact('proposal', 'annotations'));
    }
}
