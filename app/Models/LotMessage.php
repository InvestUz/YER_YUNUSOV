<?php

// =============================================================================
// File: app/Models/LotMessage.php
// =============================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotMessage extends Model
{
    protected $fillable = [
        'lot_id',
        'user_id',
        'name',
        'email',
        'phone',
        'message',
        'ip_address',
        'status',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_READ = 'read';
    const STATUS_REPLIED = 'replied';

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->update([
            'status' => self::STATUS_READ,
            'read_at' => now()
        ]);
    }

    /**
     * Mark message as replied
     */
    public function markAsReplied()
    {
        $this->update([
            'status' => self::STATUS_REPLIED,
            'read_at' => $this->read_at ?? now()
        ]);
    }

    /**
     * Check if message is unread
     */
    public function isUnread()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if message is read
     */
    public function isRead()
    {
        return $this->status === self::STATUS_READ;
    }

    /**
     * Check if message is replied
     */
    public function isReplied()
    {
        return $this->status === self::STATUS_REPLIED;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_READ => 'blue',
            self::STATUS_REPLIED => 'green',
        ][$this->status] ?? 'gray';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_PENDING => 'Кутилмоқда',
            self::STATUS_READ => 'Ўқилган',
            self::STATUS_REPLIED => 'Жавоб берилган',
        ][$this->status] ?? 'Номаълум';
    }

    /**
     * Scope for pending messages
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for read messages
     */
    public function scopeRead($query)
    {
        return $query->where('status', self::STATUS_READ);
    }

    /**
     * Scope for replied messages
     */
    public function scopeReplied($query)
    {
        return $query->where('status', self::STATUS_REPLIED);
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}