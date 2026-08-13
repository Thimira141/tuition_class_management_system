<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

class Classroom extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * @var string $table table name
     */
    protected $table = 'classes';

    protected $fillable = [
        // 'class_code',auto generated
        'name',
        'grade',
        'remarks',
        'payment_method',
        'price',
        'start_date',
        'end_date',
    ];

    /**
     * Auto-generate class_code after creation.
     * @return void
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($class) {
            // Only generate if not already set
            if (empty($class->class_code)) {
                $year = now()->year;
                $class->class_code = $year . str_pad($class->id, 6, '0', STR_PAD_LEFT);
                $class->saveQuietly();
            }
        });
    }

    /**
     * Students belonging to this classroom
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Student, Classroom, \Illuminate\Database\Eloquent\Relations\Pivot>
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id');
    }

    /**
     * Use class_code instead of id for route model binding.
     * @return string
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function getRouteKeyName()
    {
        return 'class_code';
    }
}
