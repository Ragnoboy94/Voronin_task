<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['first_name', 'last_name', 'birthdate', 'age', 'age_type'];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
