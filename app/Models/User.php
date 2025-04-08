<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username', // Changed from email to username
        'password',
        'division_id',
        'role',
        'photo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'username_verified_at' => 'datetime', // Changed from email_verified_at
            'password' => 'hashed',
        ];
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'user_id');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'admin_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}