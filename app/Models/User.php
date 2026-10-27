<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'additional_roles',
        'is_approved',
        'campus',
        'department',
        'organization',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_approved' => 'boolean',
        'additional_roles' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['role', 'additional_roles', 'is_approved', 'campus', 'department'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get all assigned roles for this user (primary + additional).
     *
     * @return array<string>
     */
    public function allRoles(): array
    {
        $additional = is_array($this->additional_roles) ? $this->additional_roles : [];
        return array_values(array_unique(array_filter(array_merge([$this->role], $additional))));
    }

    /**
     * Check if user has a specific role (primary or additional).
     */
    public function hasRole(string $role): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return in_array($role, $this->allRoles(), true);
    }

    /**
     * Get the active role for the current session, fallback to primary role.
     */
    public function activeRole(): string
    {
        $sessionRole = session('active_role');
        if ($sessionRole && ($this->hasRole($sessionRole) || $this->isSuperAdmin())) {
            return $sessionRole;
        }
        return $this->role ?? 'researcher';
    }

    /**
     * Check if user has multiple roles enabled.
     */
    public function isMultiRole(): bool
    {
        return count($this->allRoles()) > 1;
    }

    /**
     * Query scope to find users who have a specific role either as primary or additional.
     */
    public function scopeWithRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('role', $role);

            $driver = $q->getConnection()->getDriverName();
            if ($driver === 'sqlite') {
                $q->orWhere('additional_roles', 'like', '%"' . $role . '"%');
            } else {
                $q->orWhereJsonContains('additional_roles', $role);
            }
        });
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    public function isReviewer()
    {
        return $this->role === 'reviewer';
    }

    public function isResearcher()
    {
        return $this->role === 'researcher';
    }

    public function isCoordinator()
    {
        return $this->role === 'coordinator';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isRecordingStaff()
    {
        return $this->role === 'recording_staff';
    }

    public function isDean()
    {
        return $this->role === 'dean';
    }

    public function isVprei()
    {
        return $this->role === 'vprei';
    }

    public function isBudgetOfficer()
    {
        return $this->role === 'budget_officer';
    }

    public function isSaoFinance()
    {
        return $this->role === 'sao_finance';
    }

    public function isPresident()
    {
        return $this->role === 'president';
    }

    public function isFundingAgency()
    {
        return $this->role === 'funding_agency';
    }

    public function leadProposals()
    {
        return $this->hasMany(\App\Models\ResearchProposal::class);
    }

    public function collaboratedProposals()
    {
        return $this->belongsToMany(\App\Models\ResearchProposal::class, 'proposal_collaborators');
    }
}
