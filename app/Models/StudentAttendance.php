<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    /**
     * @var string $table table name
     */
    protected $table = 'students_attendance';

    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'present',
    ];

    /**
     * attendanceSession this StudentAttendance belongs to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<AttendanceSession, StudentAttendance>
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function attendanceSession()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    /**
     * students this StudentAttendance belongs to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Student, StudentAttendance>
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function students()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
