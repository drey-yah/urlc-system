<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ExternalFundingProject extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['funding_agency', 'grant_program', 'requested_amount', 'awarded_amount', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $fillable = [
        'research_proposal_id', 'funding_agency_user_id', 'funding_agency', 'grant_program',
        'agency_reference_number', 'requested_amount', 'awarded_amount',
        'status', 'agency_feedback', 'moa_mou_path', 'grant_start_date',
        'grant_end_date', 'terminal_report_path', 'university_endorsed_at',
        'president_endorsed_at', 'agency_decided_at', 'agreement_executed_at', 'closed_at',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'awarded_amount' => 'decimal:2',
        'grant_start_date' => 'date',
        'grant_end_date' => 'date',
        'university_endorsed_at' => 'datetime',
        'president_endorsed_at' => 'datetime',
        'agency_decided_at' => 'datetime',
        'agreement_executed_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function proposal()
    {
        return $this->belongsTo(ResearchProposal::class, 'research_proposal_id');
    }

    public function fundingAgencyUser()
    {
        return $this->belongsTo(User::class, 'funding_agency_user_id');
    }
}
