<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCreditList extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'fiscal_year',
        'report_date',
        'prepared_by',
        'approved_by',
        'approved_at',
        'status',
        'proposal_ids',
        'items',
        'remarks',
    ];

    protected $casts = [
        'report_date' => 'date',
        'approved_at' => 'datetime',
        'proposal_ids' => 'array',
        'items' => 'array',
        'fiscal_year' => 'integer',
    ];

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the ResearchProposal models referenced in this list.
     */
    public function getProposalsAttribute()
    {
        if (empty($this->proposal_ids)) {
            return collect();
        }

        return ResearchProposal::with(['user', 'collaborators', 'terminalReport'])
            ->whereIn('id', $this->proposal_ids)
            ->get()
            ->sortBy(function ($proposal) {
                return array_search($proposal->id, $this->proposal_ids);
            });
    }
}
