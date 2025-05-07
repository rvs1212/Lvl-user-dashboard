<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CEODecision extends Model
{
    protected $table = 'CEODecision';
    protected $primaryKey = 'EmployeeID';

    protected $fillable = [
        'EmployeeID', 'decision'
    ];
}
