<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ResearchProposal extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'reviewer_id',
        'proposal_code',
        'title',
        'abstract',
        'rationale',
        'research_field',
        'status',
        'total_budget',
        'current_phase',
        'phase_updated_at',
        'review_comments',
        'review_suggestions',
        'document_path',
        'terminal_report_path',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'current_phase', 'reviewer_id', 'title'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    //Relationship: Proposal belongs to a User (Researcher)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Relationship: Proposal was last reviewed by a User (Reviewer)
    public function reviewer()
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewer_id');
    }

    //Relationship: Proposal can be assigned to multiple Reviewers
    public function assignments()
    {
        return $this->belongsToMany(\App\Models\User::class, 'proposal_assignments');
    }

    public function collaborators()
    {
        return $this->belongsToMany(\App\Models\User::class, 'proposal_collaborators');
    }

    // Relationship: Proposal has many milestones
    public function milestones()
    {
        return $this->hasMany(ResearchMilestone::class);
    }

    // Relationship: Proposal has many documents
    public function documents()
    {
        return $this->hasMany(ProposalDocument::class);
    }

    // Relationship: Proposal has many budget items (Line Item Budget)
    public function budgetItems()
    {
        return $this->hasMany(ProposalBudgetItem::class);
    }
}