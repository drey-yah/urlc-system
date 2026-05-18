<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewer_id',
        'title',
        'abstract',
        'rationale',
        'research_field',
        'status',
        'current_phase',
        'phase_updated_at',
        'review_comments',
        'review_suggestions',
        'document_path',
        'terminal_report_path',
    ];

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
}