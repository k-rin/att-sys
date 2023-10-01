<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'alias',
        'sex',
        'birthday',
        'hire_date',
        'paid_leaves',
        'department_id',
        'locked',
    ];

    public function leaveReports()
    {
        return $this->hasMany(LeaveReport::class);
    }

    public function overtimeReports()
    {
        return $this->hasMany(OvertimeReport::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendanceTimes()
    {
        return $this->hasMany(AttendanceTime::class);
    }

    public function getIsManagerAttribute()
    {
        return $this->id == $this->department->manager_id;
    }

    public function getAttendanceTime(string $date)
    {
        return $this->attendanceTimes->sortByDesc('date')->first(function ($value) use ($date) {
            return $value->date <= $date;
        });
    }
}