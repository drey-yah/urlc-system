<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMonitoring extends Model
{
    use HasFactory;

    protected $table = 'project_monitorings';

    protected $fillable = [
        'research_proposal_id',
        'user_id',
        'period_covered',
        'progress_summary',
        'monitoring_form_path',
        'coordinator_verified',
        'coordinator_verified_at',
        'status',
    ];

    protected $casts = [
        'coordinator_verified' => 'boolean',
        'coordinator_verified_at' => 'datetime',
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
