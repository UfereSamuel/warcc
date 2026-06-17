<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PublicEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'summary',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'venue_address',
        'category',
        'status',
        'featured_image',
        'registration_link',
        'contact_email',
        'contact_phone',
        'max_participants',
        'current_registrations',
        'is_featured',
        'registration_required',
        'registration_deadline',
        'fee',
        'tags',
        'additional_info',
        'created_by',
        'updated_by',
        'published_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'registration_deadline' => 'date',
        'published_at' => 'datetime',
        'tags' => 'array',
        'fee' => 'decimal:2',
        'is_featured' => 'boolean',
        'registration_required' => 'boolean',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }

    // Accessors
    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'conference' => 'Conference',
            'workshop' => 'Workshop',
            'training' => 'Training',
            'seminar' => 'Seminar',
            'meeting' => 'Meeting',
            'announcement' => 'Announcement',
            'celebration' => 'Celebration',
            default => 'Event'
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'conference' => 'primary',
            'workshop' => 'info',
            'training' => 'success',
            'seminar' => 'warning',
            'meeting' => 'secondary',
            'announcement' => 'danger',
            'celebration' => 'purple',
            default => 'secondary'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'warning',
            'published' => 'success',
            'archived' => 'secondary',
            default => 'secondary'
        };
    }

    public function getFormattedFeeAttribute(): string
    {
        if (!$this->fee) {
            return 'Free';
        }
        return 'GHS ' . number_format($this->fee, 2);
    }

    public function getRegistrationStatusAttribute(): string
    {
        if (!$this->registration_required) {
            return 'No registration required';
        }

        if ($this->registration_deadline && $this->registration_deadline->isPast()) {
            return 'Registration closed';
        }

        if ($this->max_participants && $this->current_registrations >= $this->max_participants) {
            return 'Fully booked';
        }

        return 'Registration open';
    }

    public function getCanRegisterAttribute(): bool
    {
        if (!$this->registration_required) {
            return false;
        }

        if ($this->registration_deadline && $this->registration_deadline->isPast()) {
            return false;
        }

        if ($this->max_participants && $this->current_registrations >= $this->max_participants) {
            return false;
        }

        return true;
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date->isFuture();
    }

    public function getIsOngoingAttribute(): bool
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->end_date->isPast();
    }

    public function getEventStatusAttribute(): string
    {
        if ($this->is_upcoming) {
            return 'upcoming';
        } elseif ($this->is_ongoing) {
            return 'ongoing';
        } else {
            return 'past';
        }
    }

    public function getEventStatusLabelAttribute(): string
    {
        return match($this->event_status) {
            'upcoming' => 'Upcoming',
            'ongoing' => 'Ongoing',
            'past' => 'Past',
            default => 'Unknown'
        };
    }

    public function getEventStatusColorAttribute(): string
    {
        return match($this->event_status) {
            'upcoming' => 'info',
            'ongoing' => 'success',
            'past' => 'secondary',
            default => 'secondary'
        };
    }

    public function getFeaturedImageUrlAttribute(): string
    {
        if ($this->featured_image) {
            return asset('images/events/' . $this->featured_image);
        }
        return asset('images/default-event.jpg');
    }

    public function getFormattedDateRangeAttribute(): string
    {
        if ($this->start_date->eq($this->end_date)) {
            return $this->start_date->format('M d, Y');
        }

        if ($this->start_date->year === $this->end_date->year && $this->start_date->month === $this->end_date->month) {
            return $this->start_date->format('M d') . ' - ' . $this->end_date->format('d, Y');
        }

        return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
    }

    public function getFormattedTimeRangeAttribute(): ?string
    {
        if (!$this->start_time && !$this->end_time) {
            return null;
        }

        $startTime = $this->start_time ? Carbon::parse($this->start_time)->format('g:i A') : '';
        $endTime = $this->end_time ? Carbon::parse($this->end_time)->format('g:i A') : '';

        if ($startTime && $endTime) {
            return $startTime . ' - ' . $endTime;
        } elseif ($startTime) {
            return 'From ' . $startTime;
        } elseif ($endTime) {
            return 'Until ' . $endTime;
        }

        return null;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', today());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', today())
                    ->where('end_date', '>=', today());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', today());
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrderByDate($query, $direction = 'asc')
    {
        return $query->orderBy('start_date', $direction);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeWithRegistration($query)
    {
        return $query->where('registration_required', true);
    }

    public function scopeRegistrationOpen($query)
    {
        return $query->where('registration_required', true)
                    ->where(function($q) {
                        $q->whereNull('registration_deadline')
                          ->orWhere('registration_deadline', '>=', today());
                    })
                    ->whereRaw('(max_participants IS NULL OR current_registrations < max_participants)');
    }
}
