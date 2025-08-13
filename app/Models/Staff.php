<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Staff extends Model implements Authenticatable
{
    use HasFactory, HasRoles;

    protected $table = 'staff';

    protected $fillable = [
        'staff_id',
        'first_name',
        'last_name',
        'email',
        'gender',
        'phone',
        'position',
        'department',
        'microsoft_id',
        'profile_picture',
        'status',
        'is_admin',
        'annual_leave_balance',
        'hire_date',
        'permissions',
        'last_login',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'last_login' => 'datetime',
        'permissions' => 'array',
        'is_admin' => 'boolean',
    ];

    protected $hidden = [
        'microsoft_id',
    ];

    // Authentication Interface Methods
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return null; // No password for SSO users
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function getRememberToken()
    {
        return null; // Not using remember tokens
    }

    public function setRememberToken($value)
    {
        // Not using remember tokens
    }

    public function getRememberTokenName()
    {
        return null;
    }

    // Relationships
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function weeklyTrackers(): HasMany
    {
        return $this->hasMany(WeeklyTracker::class);
    }

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function uploadedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function createdActivities(): HasMany
    {
        return $this->hasMany(ActivityCalendar::class, 'created_by');
    }

    public function updatedActivities(): HasMany
    {
        return $this->hasMany(ActivityCalendar::class, 'updated_by');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getProfilePictureUrlAttribute(): string
    {
        return $this->profile_picture
            ? asset('images/uploads/' . $this->profile_picture)
            : asset('images/default-avatar.png');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    // Helper methods
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }

    public function getTodayAttendance()
    {
        return $this->attendances()->whereDate('date', today())->first();
    }

    public function getCurrentWeekTracker()
    {
        $startOfWeek = now()->startOfWeek();
        return $this->weeklyTrackers()
            ->whereDate('week_start_date', $startOfWeek)
            ->first();
    }

    // AdminLTE Integration Methods
    public function adminlte_profile_url()
    {
        return url('staff/profile');
    }

    public function adminlte_image()
    {
        return $this->profile_picture_url;
    }

    public function adminlte_desc()
    {
        return $this->position . ' - ' . $this->department;
    }
}
