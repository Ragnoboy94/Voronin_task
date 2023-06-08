<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Patient extends Model
{
    protected $fillable = ['first_name', 'last_name', 'birthdate', 'age', 'age_type'];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function setBirthdateAttribute($value)
    {
        $birthdate = Carbon::parse($value);
        $now = Carbon::now();

        $age = $birthdate->diffInDays($now);
        $age_type = 'день';

        if ($age > 30) {
            $age = $birthdate->diffInMonths($now);
            $age_type = 'месяц';
        }

        if ($age > 12) {
            $age = $birthdate->diffInYears($now);
            $age_type = 'год';
        }

        $this->attributes['birthdate'] = $value;
        $this->attributes['age'] = $age;
        $this->attributes['age_type'] = $age_type;
    }
}
