<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Course;

class UserCourseProgress extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'completed_sections',
        'completed_section_ids',
        'pending_sections',
        'completed_modules',
        'completed_module_ids',
        'pending_modules',
        'awarded',
    ];

      protected $casts = [

        'completed_section_ids' => 'array',
        'completed_module_ids' => 'array',

      ];
 public function user()
    {
        return $this->belongsTo(User::class,'user_id');

    }

    public function course()
    {
        return $this->belongsTo(Course::class,'course_id');

    }

}
