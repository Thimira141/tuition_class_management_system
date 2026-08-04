<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use SoftDeletes;

    /**
     * @var string $table table name
     */
    protected $table = 'classes';

    protected $fillable = [
        'class_code',
        'name',
        'grade',
        'remarks',
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
    public function students() {
        return $this->belongsToMany(Student::class, 'class_student');
    }
}
