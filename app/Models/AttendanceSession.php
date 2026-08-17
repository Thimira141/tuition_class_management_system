<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceSession extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * @var string $table table name
     */
    protected $table = 'attendance_sessions';

    protected $fillable = [
        // 'attendance_session_code', // auto generate
        'title',
        'class_id',
        'session_year',
        'session_month',
        'start_datetime',
        'end_datetime',
        'closed_at',
    ];

    /**
     * Auto-generate attendance_session_code after creation.
     * @return void
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($attendance_session) {
            // Only generate if not already set
            if (empty($attendance_session->attendance_session_code)) {
                $year = now()->year;
                $attendance_session->attendance_session_code = $year . str_pad($attendance_session->id, 6, '0', STR_PAD_LEFT);
                $attendance_session->saveQuietly();
            }
        });
    }

    /**
     * classroom belonging to this AttendanceSession
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Classroom, AttendanceSession>
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function classroom ()
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    /**
     * StudentAttendance belonging to this AttendanceSession
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<StudentAttendance, AttendanceSession>
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function studentsAttendance()
    {
        return $this->hasMany(StudentAttendance::class, 'attendance_session_id');
    }

    /**
     * Use attendance_session_code instead of id for route model binding.
     * @return string
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function getRouteKeyName()
    {
        return 'attendance_session_code';
    }
}
