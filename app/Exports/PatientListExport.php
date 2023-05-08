<?php

namespace App\Exports;

use App\Appointment;
use App\Diagnosis;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PatientListExport implements FromArray,ShouldAutoSize,WithHeadings
{
    use Exportable;

    protected $from_date;
    protected $to_date;

    public function __construct($from,$to){

        $this->from_date =$from;
        $this->to_date =$to;
    }

   public function array() :array
    {
        $patient_list=[];
        $patients= Appointment::whereBetween('date',[$this->from_date,$this->to_date])->with('clinic_patient')->with('doctor')->get();
        foreach($patients as $patient)
        {
            foreach($patient->diagnosis as $diag)
            $combined =array(
                'patient_name'=>$patient->clinic_patient->name,
                'father_name'=>$patient->clinic_patient->father_name,
                'age'=>$patient->clinic_patient->age,
                'booking_date'=>$patient->date,
                'phone'=>$patient->clinic_patient->phone,
                'address'=>$patient->clinic_patient->address,
                'doctor'=>$patient->doctor->name,
                'diagnosis'=>$diag->name,

            );
            array_push($patient_list,$combined);
        }

        return $patient_list;
    }


    public function headings():array{

            return [
                'patient_name',
                'fathername',
                'age',
                'date',
                'phone',
                'address',
                'doctor',
                'diagnosis'
        ];

    }


}
