<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_date',
        'start_time',
        'end_time',
        'is_full_day',
        'leave_type',
        'reason',
        'remark',
        'resubmission_count'
    ];

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function getStatusAttribute()
    {
        $approvals = $this->approvals;

        if ($approvals->contains('status', 'rejected')) {
            return 'rejected';
        }

        if ($approvals->contains('status', 'approved')) {
            return 'approved';
        }

        return 'pending';
    }
}
