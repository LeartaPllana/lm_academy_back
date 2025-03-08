<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseSection;
use App\Models\User;

class CourseMaterial extends Model
{
    protected $fillable = [
        'course_section_id',
        'title',
        'type',
        'content',
        'material_url',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    public function section(){
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }
    public function creator(){
        return $this->belongsTo(User::class, 'created_by')->selectUserName();
    }
    public function updator(){
        return $this->belongsTo(User::class, 'updated_by')->selectUserName();
    }
    
}
