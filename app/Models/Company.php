<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'website',
        'adress',
        'user_id',
    ];

    public function jobListings(){
        return $this->hasMany(JobListings::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
