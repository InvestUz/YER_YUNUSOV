<?php

// =============================================================================
// File: app/Models/LoginHistory.php
// =============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'platform',
        'login_at',
        'logout_at',
        'session_id',
        'status'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_LOGGED_OUT = 'logged_out';
    const STATUS_EXPIRED = 'expired';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get session duration in minutes
     */
    public function getSessionDurationAttribute()
    {
        if (!$this->logout_at) {
            return null;
        }

        return $this->login_at->diffInMinutes($this->logout_at);
    }

    /**
     * Get human readable session duration
     */
    public function getSessionDurationHumanAttribute()
    {
        if (!$this->logout_at) {
            return 'Актив';
        }

        $minutes = $this->session_duration;

        if ($minutes < 60) {
            return $minutes . ' дақиқа';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes > 0) {
            return $hours . ' соат ' . $remainingMinutes . ' дақиқа';
        }

        return $hours . ' соат';
    }

    /**
     * Check if session is active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user logged out
     */
    public function isLoggedOut()
    {
        return $this->status === self::STATUS_LOGGED_OUT;
    }

    /**
     * Check if session expired
     */
    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_ACTIVE => 'green',
            self::STATUS_LOGGED_OUT => 'gray',
            self::STATUS_EXPIRED => 'red',
        ][$this->status] ?? 'gray';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_ACTIVE => 'Актив',
            self::STATUS_LOGGED_OUT => 'Чиққан',
            self::STATUS_EXPIRED => 'Эскирган',
        ][$this->status] ?? 'Номаълум';
    }

    /**
     * Mark as logged out
     */
    public function markAsLoggedOut()
    {
        $this->update([
            'logout_at' => now(),
            'status' => self::STATUS_LOGGED_OUT
        ]);
    }

    /**
     * Mark as expired
     */
    public function markAsExpired()
    {
        $this->update([
            'logout_at' => now(),
            'status' => self::STATUS_EXPIRED
        ]);
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for logged out sessions
     */
    public function scopeLoggedOut($query)
    {
        return $query->where('status', self::STATUS_LOGGED_OUT);
    }

    /**
     * Scope for expired sessions
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Scope for today's logins
     */
    public function scopeToday($query)
    {
        return $query->whereDate('login_at', today());
    }

    /**
     * Scope for recent logins (last N days)
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('login_at', '>=', now()->subDays($days));
    }

    /**
     * Parse user agent to get device info
     */
    public static function parseUserAgent($userAgent)
    {
        return LotView::parseUserAgent($userAgent);
    }
}