<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_name'
    ];

    public function jobListings(){
        return $this->hasMany(JobListings::class);
    }
}
