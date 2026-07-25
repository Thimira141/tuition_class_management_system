<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_code',
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
                $student->save();
            }
        });
    }
}
