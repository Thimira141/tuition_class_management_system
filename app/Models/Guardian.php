<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Guardian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // 'guardian_code', auto generated
        'cover_img',
        'name',
        'nic',
        'email',
        'tel',
        'address',
        'remarks',
    ];
    protected $appends = ['cover_img_url'];

    /**
     * A guardian can have many students.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Auto-generate guardian_code after creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($guardian) {
            // Only generate if not already set
            if (empty($guardian->guardian_code)) {
                $year = now()->year;
                $guardian->guardian_code = $year . str_pad($guardian->id, 6, '0', STR_PAD_LEFT);
                $guardian->saveQuietly();
            }
        });
    }

    /**
     * This creates a virtual attribute: cover_img_url, for url
     * @return string
     * @author Thimira Dilshan <thimirad865@gmail.com>
     */
    public function getCoverImgUrlAttribute() {
        return $this->cover_img ? url(Storage::url($this->cover_img)) : asset('images/placeholder-image-member.svg');
    }
}
