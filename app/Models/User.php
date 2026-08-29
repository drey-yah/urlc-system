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
        'is_approved',
        'campus',
        'department',
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
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['role', 'is_approved', 'campus', 'department'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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

    public function leadProposals()
    {
        return $this->hasMany(\App\Models\ResearchProposal::class);
    }

    public function collaboratedProposals()
    {
        return $this->belongsToMany(\App\Models\ResearchProposal::class, 'proposal_collaborators');
    }
}
