<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchPresentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_proposal_id',
        'user_id',
        'sponsoring_agency',
        'conference_name',
        'presentation_type',
        'presentation_title',
        'event_date',
        'venue',
        'acceptance_letter_path',
        'presentation_file_path',
        'certificate_path',
        'director_recommended',
        'director_recommended_at',
        'president_approved',
        'president_approved_at',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'director_recommended' => 'boolean',
        'director_recommended_at' => 'datetime',
        'president_approved' => 'boolean',
        'president_approved_at' => 'datetime',
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
