<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    protected $fillable=[
        'user_id',
        'job_listing_id',
        'cover_letter',
        'cv_path',
        'status',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function jobListing(){
        return $this->belongsTo(JobListing::class);
    }
}
