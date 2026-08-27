<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityDesign extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_proposal_id',
        'user_id',
        'activity_title',
        'venue',
        'target_date',
        'objectives',
        'target_participants',
        'proposed_budget',
        'activity_design_file',
        'budget_requirement_file',
        'director_noted',
        'director_noted_at',
        'budget_officer_noted',
        'budget_officer_noted_at',
        'vprei_approved',
        'vprei_approved_at',
        'status',
    ];

    protected $casts = [
        'director_noted' => 'boolean',
        'budget_officer_noted' => 'boolean',
        'vprei_approved' => 'boolean',
        'target_date' => 'date',
        'director_noted_at' => 'datetime',
        'budget_officer_noted_at' => 'datetime',
        'vprei_approved_at' => 'datetime',
    ];

    public function proposal()
    {
        return $this->belongsTo(ResearchProposal::class, 'research_proposal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
