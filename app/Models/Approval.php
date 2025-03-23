<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_request_id',
        'admin_id',
        'status',
        'remark',
        'approved_at'
    ];
    
    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
