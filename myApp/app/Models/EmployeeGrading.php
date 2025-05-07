<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeGrading extends Model
{
    protected $table = 'EmployeeGrading';
    protected $primaryKey = 'EmployeeID';

    protected $fillable = [
        'EmployeeID', 'Grade'
    ];
}
