<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Role Constants
    const ROLE_ADMIN = 'admin';
    const ROLE_QA_OFFICER = 'qa_officer';
    const ROLE_CALL_CENTER = 'call_center';
    const ROLE_QA_HOD = 'qa_hod';
    const ROLE_COO = 'coo';
    const ROLE_LINE_MANAGER = 'line_manager';

    const APPROVABLE_ROLES = [
        self::ROLE_QA_OFFICER,
        self::ROLE_CALL_CENTER,
        self::ROLE_QA_HOD,
        self::ROLE_COO,
        self::ROLE_LINE_MANAGER,
    ];

    const FEEDBACK_MANAGEMENT_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_QA_OFFICER,
        self::ROLE_CALL_CENTER,
        self::ROLE_QA_HOD,
        self::ROLE_COO,
        self::ROLE_LINE_MANAGER,
    ];

    const REPORT_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_COO,
        self::ROLE_LINE_MANAGER,
    ];

    // Role Labels
    public static function getRoleLabels(): array
    {
        return [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_QA_OFFICER => 'Quality Assurance Officer',
            self::ROLE_CALL_CENTER => 'Call Center',
            self::ROLE_QA_HOD => 'Quality Assurance Head of Department',
            self::ROLE_COO => 'Chief Operating Officer',
            self::ROLE_LINE_MANAGER => 'Line Manager',
        ];
    }

    public function getRoleLabel(): string
    {
        return self::getRoleLabels()[$this->role] ?? $this->role;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'fname',
        'mname',
        'lname',
        'email',
        'dob',
        'password',
        'role',
        'is_active',
        'is_first_user',
        'approved_by',
        'approved_at',
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
        'password' => 'hashed',
        'dob' => 'date',
        'is_active' => 'boolean',
        'is_first_user' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Role Check Methods
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isQAOfficer(): bool
    {
        return $this->role === self::ROLE_QA_OFFICER;
    }

    public function isCallCenter(): bool
    {
        return $this->role === self::ROLE_CALL_CENTER;
    }

    public function isQAHod(): bool
    {
        return $this->role === self::ROLE_QA_HOD;
    }

    public function isCOO(): bool
    {
        return $this->role === self::ROLE_COO;
    }

    public function isLineManager(): bool
    {
        return $this->role === self::ROLE_LINE_MANAGER;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get full name
     */
    public function getFullName(): string
    {
        $parts = array_filter([$this->fname, $this->mname, $this->lname]);
        return implode(' ', $parts) ?: $this->name;
    }

    /**
     * Permission Check Methods
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin() || $this->isQAHod();
    }

    public function canManageComplaints(): bool
    {
        return in_array($this->role, self::FEEDBACK_MANAGEMENT_ROLES, true);
    }

    public function canEscalateToCOO(): bool
    {
        return $this->isAdmin() || $this->isQAHod();
    }

    public function canViewReports(): bool
    {
        return in_array($this->role, self::REPORT_ROLES, true);
    }

    public function canViewWeeklyReport(): bool
    {
        return in_array($this->role, self::FEEDBACK_MANAGEMENT_ROLES, true);
    }

    public function canApproveUsers(): bool
    {
        return $this->isAdmin() || $this->isQAHod();
    }

    public function canActivateUsers(): bool
    {
        return $this->isAdmin() || $this->isQAHod();
    }

    public function canEscalateToHelpdesk(): bool
    {
        return $this->isCallCenter() || $this->isAdmin();
    }

    /**
     * Check if this is the first user (admin)
     */
    public static function hasUsers(): bool
    {
        return self::count() > 0;
    }

    public static function getFirstUser(): ?self
    {
        return self::where('is_first_user', true)->first();
    }
}
