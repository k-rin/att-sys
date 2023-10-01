<?php

namespace App\Models;

use App\Enums\UserType;
use App\Models\Traits\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRecordReport extends Model
{
    use HasFactory, ApprovalStatus;

    const Layer = [
        UserType::Employee => 2,
        UserType::Manager  => 2,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'date',
        'start_at',
        'end_at',
        'reason',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}