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
    ];

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
