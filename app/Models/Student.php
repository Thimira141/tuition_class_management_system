<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // 'student_code', auto generated
        'cover_img',
        'name',
        'nic',
        'dob',
        'joined_at',
        'email',
        'tel',
        'address',
        'remarks',
        'guardian_id',
    ];
    protected $appends = ['student__cover_img_url'];

    /**
     * Each student belongs to one guardian.
     */
    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }

    /**
     * Auto-generate student_code after creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($student) {
            // Only generate if not already set
            if (empty($student->student_code)) {
                $year = now()->year;
                $student->student_code = $year . str_pad($student->id, 6, '0', STR_PAD_LEFT);
                $student->saveQuietly();
            }
        });
    }

    /**
     * This creates a virtual attribute: student__cover_img_url, for url
     * @return string
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function getStudentCoverImgUrlAttribute() {
        return $this->cover_img ? url(Storage::url($this->cover_img)) : asset('images/placeholder-image-member.svg');
    }

    /**
     * Classes this student belongs to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Classroom, Student, \Illuminate\Database\Eloquent\Relations\Pivot>
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function classrooms() {
        return $this->belongsToMany(Classroom::class, 'class_student', 'student_id', 'class_id');
    }

    /**
     * StudentAttendance this student belongs to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<StudentAttendance, Student>
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function attendance()
    {
        return $this->belongsTo(StudentAttendance::class, 'student_id');
    }

    /**
     * Use student_code instead of id for route model binding.
     * @return string
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function getRouteKeyName()
    {
        return 'student_code';
    }
}
