<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurgenFee extends Model
{
    //
    protected $fillable = ['appointment_id','surgen_name','charges'];
}
