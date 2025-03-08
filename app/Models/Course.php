<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Course extends Model
{
    protected $fillable = [
        'title',
        'descrition',
        'intro_video_url',
        'intro_image_url',
        'status',
        'nr_of_files',
        'duration',
        'created_by',
        'updated_by',
    ];


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->selectUserName();
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_By')->selectUserName();
    }
    public function modules()
    {
        return $this->hasMany( CourseModule::class, 'course_id')->selectUserName();
    }
}
