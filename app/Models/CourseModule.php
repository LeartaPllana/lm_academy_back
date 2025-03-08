<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\CourseSection;

class CourseModule extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'nr_of_files',
        'duration'
    ];

    public function course(){
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function sections(){
        return $this->hasMany(CourseSection::class,'module_id');
    }


}
