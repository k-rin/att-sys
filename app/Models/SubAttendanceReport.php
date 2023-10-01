<?php

namespace App\Models;

use App\Enums\UserType;
use App\Models\Traits\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAttendanceReport extends Model
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
        'date',
        'reason',
        'compensation',
        'note',
        'status',
    ];

    public function subHolidayReport()
    {
        return $this->hasOne(SubHolidayReport::class, 'sub_attendance_report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}