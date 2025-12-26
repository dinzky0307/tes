<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'payroll_number',
        'lastname',
        'firstname',
        'middle_initial',
        'grant_type_batch',
        'validation_status',
    ];
}
