<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'purchase_vou',
    	'supplier_name',
		'total_quantity',
		'total_price',
		'purchase_date',
		'purchase_by',
        'credit_amount',
		'supplier_id',
	];

	public function counting_unit() {
		return $this->belongsToMany('App\CountingUnit')->withPivot('id','quantity','price');
	}
 
	public function user(){
        return $this->belongsTo('App\User','purchase_by');
    }
	public function supplier(){
        return $this->belongsTo('App\Supplier','purchase_by');
    }

    public function supplier_credit_list(){
        return $this->hasOne('App\SupplierCreditList');
    }
}
