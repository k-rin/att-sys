<?php

namespace App\Models;

use App\Enums\UserType;
use App\Models\Traits\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubHolidayReport extends Model
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
        'sub_attendance_report_id',
        'user_id',
        'date',
        'status',
    ];

    public function subAttendanceReport()
    {
        return $this->belongsTo(SubAttendanceReport::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}