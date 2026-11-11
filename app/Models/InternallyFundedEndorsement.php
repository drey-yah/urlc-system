<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternallyFundedEndorsement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'fiscal_year',
        'report_date',
        'status',
        'proposal_ids',
        'items',
        'total_budget',
        'remarks',
        'prepared_by',
        'prepared_at',
        'vprei_by',
        'vprei_status',
        'vprei_signed_at',
        'vprei_remarks',
        'vpaf_by',
        'vpaf_status',
        'vpaf_signed_at',
        'vpaf_remarks',
        'president_by',
        'president_status',
        'president_signed_at',
        'president_remarks',
    ];

    protected $casts = [
        'report_date' => 'date',
        'prepared_at' => 'datetime',
        'vprei_signed_at' => 'datetime',
        'vpaf_signed_at' => 'datetime',
        'president_signed_at' => 'datetime',
        'proposal_ids' => 'array',
        'items' => 'array',
        'fiscal_year' => 'integer',
        'total_budget' => 'decimal:2',
    ];

    public function preparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function vpreiNoter()
    {
        return $this->belongsTo(User::class, 'vprei_by');
    }

    public function vpafNoter()
    {
        return $this->belongsTo(User::class, 'vpaf_by');
    }

    public function presidentApprover()
    {
        return $this->belongsTo(User::class, 'president_by');
    }

    /**
     * Get the ResearchProposal models referenced in this endorsement.
     */
    public function getProposalsAttribute()
    {
        if (empty($this->proposal_ids)) {
            return collect();
        }

        return ResearchProposal::with(['user', 'collaborators', 'budgetItems', 'milestones'])
            ->whereIn('id', $this->proposal_ids)
            ->get()
            ->sortBy(function ($proposal) {
                return array_search($proposal->id, $this->proposal_ids);
            });
    }

    public function isVpreiRecommended(): bool
    {
        return $this->vprei_status === 'recommended';
    }

    public function isVpafRecommended(): bool
    {
        return $this->vpaf_status === 'recommended';
    }

    public function isBothRecommended(): bool
    {
        return $this->isVpreiRecommended() && $this->isVpafRecommended();
    }

    public function isPresidentApproved(): bool
    {
        return $this->president_status === 'approved' || $this->status === 'approved';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'pending_recommending' => 'Pending Recommending Approval (VPREI & VPAF)',
            'pending_president_approval' => 'Pending President Approval',
            'approved' => 'Officially Approved by President',
            'returned' => 'Returned with Remarks',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'draft' => 'bg-secondary text-white',
            'pending_recommending' => 'bg-warning text-dark',
            'pending_president_approval' => 'bg-info text-dark',
            'approved' => 'bg-success text-white',
            'returned' => 'bg-danger text-white',
            default => 'bg-light text-dark border',
        };
    }
}
