<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalForumSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'local_research_forum_id',
        'research_proposal_id',
        'user_id',
        'coordinator_id',
        'paper_title',
        'abstract',
        'extended_abstract_path',
        'presentation_file_path',
        'coordinator_endorsed',
        'coordinator_endorsed_at',
        'is_accepted',
        'accepted_at',
        'notice_of_acceptance_path',
        'certificate_path',
        'status',
    ];

    protected $casts = [
        'coordinator_endorsed' => 'boolean',
        'coordinator_endorsed_at' => 'datetime',
        'is_accepted' => 'boolean',
        'accepted_at' => 'datetime',
    ];

    public function forum()
    {
        return $this->belongsTo(LocalResearchForum::class, 'local_research_forum_id');
    }

    public function proposal()
    {
        return $this->belongsTo(ResearchProposal::class, 'research_proposal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }
}
