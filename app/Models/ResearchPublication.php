<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchPublication extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_proposal_id',
        'user_id',
        'intent_letter_path',
        'journal_title',
        'issn_number',
        'indexing_agency',
        'has_ip_potential',
        'ip_notes',
        'ip_registration_file_path',
        'ip_cleared',
        'ip_cleared_at',
        'vprei_approved',
        'vprei_approved_at',
        'submission_proof_path',
        'published_copy_path',
        'doi_link',
        'status',
    ];

    protected $casts = [
        'has_ip_potential' => 'boolean',
        'ip_cleared' => 'boolean',
        'ip_cleared_at' => 'datetime',
        'vprei_approved' => 'boolean',
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
