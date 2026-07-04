<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobListings extends Model
{
    use HasFactory;
    protected $filable=[
        'tittle',
        'description',
        'location',
        'salary',
        'company_id',
        'type',
    ];
    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function applications(){
        return $this->hasMany(Application::class);

    }

}
