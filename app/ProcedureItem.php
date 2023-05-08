<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcedureItem extends Model
{
    //
    protected $fillable = ['name','qty','procedure_group_id','price'];
    public function procedure_group() {
        return $this->belongsTo(ProcedureGroup::class);
    }
}
