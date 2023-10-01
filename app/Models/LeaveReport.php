<?php

namespace App\Models;

use App\Enums\UserType;
use App\Models\Traits\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveReport extends Model
{
    use HasFactory, ApprovalStatus;

    const Layer = [
        UserType::Employee => 2,
        UserType::Manager  => 4,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'start_at',
        'end_at',
        'days',
        'type',
        'reason',
        'note',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}