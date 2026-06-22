<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Foundation\Auth\Access\Authorizable as AuthorizableTrait;
use Spatie\Permission\Traits\HasRoles;

class Staff extends Model implements Authenticatable, Authorizable
{
    use HasFactory, HasRoles, AuthenticatableTrait, AuthorizableTrait;

    protected $table = 'staff';

    protected $fillable = [
        'staff_id',
        'first_name',
        'last_name',
        'email',
        'gender',
        'phone',
        'position_id',
        'microsoft_id',
        'calendar_feed_token',
        'profile_picture',
        'status',
        'is_admin',
        'annual_leave_balance',
        'hire_date',
        'last_login',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'last_login' => 'datetime',
        'is_admin' => 'boolean',
    ];

    protected $hidden = [
        'microsoft_id',
        'calendar_feed_token',
    ];

    // Override authentication methods for SSO
    public function getAuthPassword()
    {
        return null; // No password for SSO users
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

    /**
     * The staff table has a legacy JSON "permissions" column that conflicts with
     * Spatie's permissions() relationship. Always resolve "permissions" to the relation.
     */
    public function getAttribute($key)
    {
        if ($key === 'permissions') {
            if (! $this->relationLoaded('permissions')) {
                $this->load('permissions');
            }

            return $this->getRelation('permissions');
        }

        return parent::getAttribute($key);
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

    public function activityReports(): HasMany
    {
        return $this->hasMany(ActivityReport::class);
    }

    public function position(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Position::class);
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

    public function getPositionTitleAttribute(): string
    {
        if (!$this->position) {
            return 'Not Assigned';
        }
        
        // If position is loaded as a relationship, return the title
        if (is_object($this->position) && isset($this->position->title)) {
            return $this->position->title;
        }
        
        // Fallback for any other cases
        return 'Position Not Found';
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
        if ($this->isSuperAdmin() || $this->hasRole('Super Admin')) {
            return true;
        }

        return $this->can($permission);
    }

    public function syncSpatieRoleFromAdminFlag(): void
    {
        if (! class_exists(\Spatie\Permission\Models\Role::class)) {
            return;
        }

        if ($this->isSuperAdmin()) {
            $this->syncRoles(['Super Admin']);

            return;
        }

        if ($this->is_admin) {
            $this->syncRoles(['Administrator']);

            return;
        }

        $this->syncRoles(['Staff']);
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
        return $this->position_title;
    }

    public function isSuperAdmin(): bool
    {
        return $this->email === 'admin@africacdc.org';
    }

    /**
     * SSO users must set position and gender before accessing the app.
     * Phone is optional (see profile completion form).
     */
    public function needsProfileCompletion(): bool
    {
        foreach (['position_id', 'gender'] as $field) {
            if ($this->{$field} === null || $this->{$field} === '') {
                return true;
            }
        }

        return false;
    }

    public function ensureCalendarFeedToken(): string
    {
        if ($this->calendar_feed_token) {
            return $this->calendar_feed_token;
        }

        return $this->regenerateCalendarFeedToken();
    }

    public function regenerateCalendarFeedToken(): string
    {
        $token = bin2hex(random_bytes(32));

        $this->forceFill(['calendar_feed_token' => $token])->save();

        return $token;
    }

    public function getCalendarFeedUrlAttribute(): ?string
    {
        if (! $this->calendar_feed_token) {
            return null;
        }

        return route('calendar.feed', ['token' => $this->calendar_feed_token]);
    }

    public function getCalendarWebcalUrlAttribute(): ?string
    {
        if (! $this->calendar_feed_url) {
            return null;
        }

        return preg_replace('/^https?:\/\//', 'webcal://', $this->calendar_feed_url);
    }
}
