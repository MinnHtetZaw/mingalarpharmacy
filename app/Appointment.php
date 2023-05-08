<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'doctor_id','clinic_patient_id','date','time','from_clinic','token'
    ];
    public function clinic_patient() {
		return $this->belongsTo('App\ClinicPatient');
	}
    public function doctor() {
		return $this->belongsTo('App\Doctor');
	}
  public function appointmentinfo() {
		return $this->hasOne(Clinicappointmentinfo::class);
	}
  public function clinic_voucher() {
		return $this->hasOne(ClinicVoucher::class);
	}
	public function attachments() {
		return $this->hasMany(AppointmentAttachment::class);
	}
    public function services() {
		return $this->belongsToMany(Service::class)->withPivot('service_id','qty');
	}
    public function procedure_items() {
		return $this->belongsToMany(ProcedureItem::class)->withPivot('procedure_item_id','qty');
	}
    public function ot_room_usage() {
		return $this->belongsTo(OtRoomUsage::class)->withPivot('ot_room_usage_id');
	}
	public function diagnosis() {
		return $this->belongsToMany(Diagnosis::class);
	}

}
