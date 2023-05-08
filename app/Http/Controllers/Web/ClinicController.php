<?php

namespace App\Http\Controllers\Web;

use App\Dose;
use App\Item;
use DateTime;
use App\Doctor;
use App\Service;
use App\Category;
use App\Employee;
use App\Diagnosis;
use App\SurgenFee;
use App\Department;
use App\Appointment;
use App\OtRoomUsage;
use App\SubCategory;
use App\CountingUnit;
use App\ClinicPatient;
use App\ClinicVoucher;
use App\ProcedureItem;
use App\ProcedureGroup;
use Illuminate\Http\Request;
use App\AppointmentAttachment;
use App\Clinicappointmentinfo;
use App\Exports\PatientListExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;

class ClinicController extends Controller
{
    //

    private $excel;


    public function __construct(Excel $excel){
        $this->excel = $excel;
    }

    public function appointmentStore(Request $request)
	{

		$validator = Validator::make($request->all(), [
            'name' => 'required',
            'fathername' => 'required',
            'age' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'appointmentdoc' => 'required',
            'appointmentclinic' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);

        if ($validator->fails()) {

            alert()->error('Something Wrong!');

            return redirect()->back();
        }

		$date = date("Y-m-d", strtotime($request->date));
		$time = date("h:i", strtotime($request->time));

		$check_booking = Appointment::where('doctor_id', $request->appointmentdoc)
		->whereDate('date', $date)->where('from_clinic',$request->appointmentclinic)
		->get();

		if(!empty($check_booking)){
			$count = count($check_booking) +1;
			$token_number = "TKN-" . sprintf("%03s", $count);

		}else{
			$token_number = "TKN-" . sprintf("%03s", 1);
		}



		$app= ClinicPatient::all();
		if(!empty($app)){
			$app_count = count($app) +1;
			$patient_code = "PTN-" . sprintf("%04s", $app_count);

		}else{
			$patient_code = "PTN-" . sprintf("%04s", 1);
		}

		$patient = ClinicPatient::create([
			'name'=>$request->name,
			'father_name'=>$request->fathername,
			'age'=>$request->age,
			'phone'=>$request->phone,
			'address'=>$request->address,
			'code'=> $patient_code,
			'age_month'=> $request->age_month,
		]);



		$appointment = Appointment::create([
			'doctor_id'=>$request->appointmentdoc,
			'clinic_patient_id' =>$patient->id,
			'from_clinic'=> $request->appointmentclinic,
			'date' => $date,
			'time' => $time,
			'token'=> $token_number
		]);
		alert()->success(' Success !');

		return redirect()->route("appointments",$patient->id);
	}

    public function appointments($patient_id)
	{
		try {
			$patient = ClinicPatient::findOrFail($patient_id);
		} catch (\Exception $e) {

			alert()->error("Patient Not Found!")->persistent("Close!");

		}
		$date = new DateTime('Asia/Yangon');
        $today_date = $date->format('d');
         //voucher chan
         $appointments = Appointment::where('clinic_patient_id',$patient_id)->with('clinic_patient')->with('diagnosis')->with('clinic_voucher')->orderBy('id','desc')->get();
		// $appointments = Appointment::where('clinic_patient_id',$patient_id)->with('clinic_patient')->with('diagnosis')->orderBy('id','desc')->get();
		return view("Clinic.appointment", compact('appointments','patient','today_date'));
	}

	public function searchAppointments(Request $request)
	{
	   // dd($request->all());
		$patient_id= $request->patient_id;

		if($request->filterName== 'count'){
			$appointments = Appointment::where('clinic_patient_id',$patient_id)->latest()->take($request->count)->with('doctor')->with('diagnosis')->get();
		}
		else if($request->filterName== 'date'){
			$fromdate= $request->fromdate;
			$todate= $request->todate;
			$appointments = Appointment::where('clinic_patient_id',$patient_id)->whereBetween('date',[$fromdate,$todate])->with('doctor')->with('diagnosis')->get();

		}
		else{
			$appointments = Appointment::where('clinic_patient_id',$patient_id)->with('doctor')->with('diagnosis')->get();
		}
// 		dd($appointments);
		return response()->json($appointments);

	}

    public function patientProfile(Request $request , $patient_id)
	{
		$patient = ClinicPatient::findOrfail($patient_id);
		return view('Clinic.patient_profile_edit',compact('patient'));
	}

	public function patientDelete($id)
	{
			$patient = ClinicPatient::findOrfail($id);
			$patient->delete();
			$appointment = Appointment::where('clinic_patient_id',$id)->delete();
			alert()->success("Successfully Deleted !");
			return redirect()->route('todappointments');
	}

    public function patientProfileUpdate(Request $request)
	{
		$patient = ClinicPatient::find($request->patient_id)->update([
			'name' => $request->name,
			'father_name' => $request->fathername,
			'ownerphone' => $request->ownerphone,
			'age' => $request->age,
			'age_month' => $request->age_month,
			'phone' => $request->phone,
			'address' => $request->address,
		]);
		alert()->success("Successfully Update !");
		return redirect()->route('appointments',[$request->patient_id]);
	}

    public function appointmentRecord($appointment_id)
	{
		$userId = session()->get('user')->id;
        //chan ->with('voucher')->with('voucher.counting_unit')->with('voucher.services')->with('attachments')
		$appointment = Appointment::where('id',$appointment_id)->with('clinic_patient')->with('clinic_voucher')->with('diagnosis')->first();
		// $vouchers = ClinicVoucher::where('appointment_id',$appointment_id)->where('clinicvoucher_status',0)->with('counting_unit')->with('counting_unit.item')->with("services")->first();
	    $vouchers = ClinicVoucher::where('appointment_id',$appointment_id)->with('counting_unit')->with('counting_unit.item')->with("services")->first();
       $vouchers = ClinicVoucher::where('appointment_id',$appointment_id)->first();
		try {
			$appointment = Appointment::where('id',$appointment_id)->with('clinic_patient')->with('diagnosis')->first();
		} catch (\Exception $e) {

			alert()->error("Appointment Not Found!")->persistent("Close!");
			return back();

		}
		$diagnosis = Diagnosis::where('created_by',$userId)->get();
		$doses = Dose::all();
		$appointmentinfo= $appointment->appointmentinfo;
        $proceduregroup = ProcedureGroup::all();
        $procedureitem  = ProcedureItem::all();
        $services = Service::all();
        $roomusage = OtRoomUsage::all();
        $clinicinfo = Clinicappointmentinfo::where('appointment_id',$appointment_id)->first();
        $service_yes = DB::table('appointment_service')->where('appointment_id',$appointment_id)->get();
        $pro_yes = DB::table('appointment_procedure_item')->where('appointment_id',$appointment_id)->get();
        // dd(count($service_yes));
        $ot_yes = DB::table('appointment_ot_room_usage')->where('appointment_id',$appointment_id)->first();
        $surgen = SurgenFee::where('appointment_id',$appointment_id)->get();
        // dd($appointmentinfo);
		return view("Clinic.record", compact('pro_yes','surgen','appointment','appointmentinfo','diagnosis','vouchers','doses','proceduregroup','procedureitem','services','roomusage','clinicinfo','service_yes','ot_yes'));
	}

    public function getDiagnosis()
	{
		$userId = session()->get('user')->id;
		$diagnosis = Diagnosis::where('created_by',$userId)->get();
		return view('Clinic.diagnosis',compact('diagnosis'));
	}

	public function diagnosisStore(Request $request)
	{
		$userId = session()->get('user')->id;
		$diagnosis= Diagnosis::create([
			'name' => $request->name,
			'created_by'=> $userId
		]);
		alert()->success('Successfully Added!');

		return redirect()->route('getDiagnosis');
	}

    public function getprocuduregroup()
	{
		$proceduregroup = ProcedureGroup::all();
		return view('Clinic.proceduregroup',compact('proceduregroup'));
	}

	public function proceduregroupStore(Request $request)
	{
		$proceduregroup= ProcedureGroup::create([
			'name' => $request->name,
		]);
		alert()->success('Successfully Added!');

		return redirect()->route('getprocuduregroup');
	}

    public function getprocedureItem()
	{
        $proceduregroup =  ProcedureGroup::all();
		$procedureItem = ProcedureItem::all();
		return view('Clinic.procedureItem',compact('proceduregroup','procedureItem'));
	}

	public function procedureItemStore(Request $request)
	{
        // dd($request->all());
		$procedureItem= ProcedureItem::create([
			'name' => $request->name,
            'procedure_group_id' => $request->pg_id,
            'price' => $request->price,
		]);
		alert()->success('Successfully Added!');

		return redirect()->route('getprocedureitem');
	}
    public function procedureItemDelete($id)
	{
        // dd($request->all());
		$procedureItem= ProcedureItem::find($id);
        $procedureItem->delete();
		alert()->success('Successfully Deleted!');

		return redirect()->route('getprocedureitem');
	}
    public function procedureItemUpdate($id,Request $request)
	{
        // dd($request->all());
		$procedureItem= ProcedureItem::find($id);
        $procedureItem->name = $request->name;
        $procedureItem->procedure_group_id = $request->pg_id;
        $procedureItem->price = $request->price;
        $procedureItem->save();
		alert()->success('Successfully Updated!');

		return redirect()->route('getprocedureitem');
	}

    public function getroomusage()
	{
		$roomusage = OtRoomUsage::all();
		return view('Clinic.roomusage',compact('roomusage'));
	}

	public function roomusageStore(Request $request)
	{
        // dd($request->all());
		$roomusage= OtRoomUsage::create([
			'room_type' => $request->room_type,
            'charges' => $request->charges,
            'duration' => $request->duration,
		]);
		alert()->success('Successfully Added!');

		return redirect()->route('getroomusage');
	}

    // Department
    protected function DepartmentList()
	{

		$department_lists = Department::all();

		return view('Clinic/department_list', compact('department_lists'));
	}

	//To update with Modal Box
	protected function CreateDepartment()
	{
		return view('Clinic/create_department');
	}

	protected function StoreDepartment(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'description' => 'required',
			'image' => 'required|file'
		]);

		if ($validator->fails()) {

			alert()->error('Something Wrong');

			return redirect()->back();
		}

		if ($request->hasfile('image')) {

			$image = $request->file('image');
			$name = $image->getClientOriginalName();
			$image->move(public_path() . '/image/Department_Image/', $name);
			$image = $name;
		}
		$department = Department::create([
			'name' => $request->name,
			'description' => $request->description,
			'photo_path' => $image,
			'status' => $request->status,
		]);

		$department_id = $department->id;

		$department_code = "DEPT" . sprintf("%04s", $department_id);

		$department->department_code = $department_code;

		$department->save();

		alert()->success('Successfully Added!');

		return redirect()->route('department_list');
	}

	protected function EditDepartment($department, Request $request)
	{

		$department = Department::where('id', $department)->first();

		return view('Clinic/edit_department', compact('department'));
	}

	protected function UpdateDepartment($department, Request $request)
	{

		$department = Department::where('id', $department)->first();

		if ($request->dept_status == "on") {

			$department->status = 1;
		} else {

			$department->status = 2;
		}

		$department->name = $request->name;

		$department->description = $request->description;

		$department->save();

		alert()->success('ပြင်ဆင်တာ​အောင်မြင်ပါသည်');

		return redirect()->route('department_list');
	}

    // End Department

    public function patientHistory($appointment_id)
	{
		try {
			$appointment = Appointment::where('id',$appointment_id)->with('attachments')->with('clinic_patient')->with('clinic_voucher')->with('clinic_voucher.counting_unit')->with('clinic_voucher.services')->with('diagnosis')->with('services')->with('procedure_items')->first();
            // $appointment = Appointment::where('id',$appointment_id)->with('services')->with('diagnosis')->first();
            // dd($appointment->clinic_voucher);
		} catch (\Exception $e) {

			alert()->error("Appointment Not Found!")->persistent("Close!");
			return back();
		}
	    if($appointment->clinic_voucher == null){
	        	alert()->error("This patient has no history!")->persistent("Close!");
			 return back();
	    }


		$look_pro = 0;

		if($appointment->clinic_voucher){
			foreach($appointment->clinic_voucher->counting_unit as $counting){
				$look_pro+=$counting->pivot->look_procedure;
			}
		}

		$appointmentinfo= $appointment->appointmentinfo;
        $ot = DB::table('appointment_ot_room_usage')->where('appointment_id',$appointment_id)->first();
        if($ot != null){
            $ot_room = OtRoomUsage::find($ot->ot_room_usage_id);
        }else{
            $ot_room = null;
        }

        $relate_fee = 0;
        $other_related_fee = DB::table('service_related_fees')->where('appointment_id',$appointment_id)->get();
        if($other_related_fee != null){
            foreach($other_related_fee as $other){
                $relate_fee += $other->fee;
            }
        }

        // dd($appointment->clinic_voucher->ot_room_charges);
        $total_charges = $relate_fee + $appointment->clinic_voucher->medicine_charges + $appointment->clinic_voucher->procedure_item_charges+ $appointment->clinic_voucher->service_charges+ $appointment->clinic_voucher->ot_room_charges+ $appointment->clinic_voucher->surgen_charges + 6500;
        // dd($appointment->procedure_items);
		return view("Clinic.patienthistory", compact('ot_room','appointment','appointmentinfo','look_pro','total_charges','relate_fee','other_related_fee'));
	}

    public function todayAppointments()
	{
		$date = new DateTime('Asia/Yangon');
        $today_date = $date->format('y-m-d');

		$userId = session()->get('user')->id;
		$doctor =Doctor::where('user_id',$userId)->first();
		if($doctor){
			// $appointments = Appointment::where('date',$today_date)->where('doctor_id',$doctor->id)->with('clinic_patient')->with('voucher')->get();
            $appointments = Appointment::where('date',$today_date)->where('doctor_id',$doctor->id)->with('clinic_patient')->get();
		}
		else{
			// $appointments = Appointment::where('date',$today_date)->with('clinic_patient')->with('voucher')->get();
            $appointments = Appointment::where('date',$today_date)->with('clinic_patient')->get();
		}
		return view('Clinic.appointments',compact('appointments'));
	}

	public function todAppointments(){
	    	$date = new DateTime('Asia/Yangon');
        $today_date = $date->format('y-m-d');
         $appointments = Appointment::where('date',$today_date)->with('clinic_patient')->get();

		return view('Clinic.appointments',compact('appointments'));
	}

    public function oldpatientAppointment(Request $request)
	{

		$validator = Validator::make($request->all(), [
            'oldpatientid' => 'required',
            'oldappointmentdoc' => 'required',
            'oldappointmentclinic' => 'required',
            'olddate' => 'required',
            'oldtime' => 'required',
        ]);

        if ($validator->fails()) {

            alert()->error('Something Wrong!');

            return redirect()->back();
        }

		$date = date("Y-m-d", strtotime($request->olddate));
		$time = date("h:i", strtotime($request->oldtime));

		$check_booking = Appointment::where('doctor_id', $request->oldappointmentdoc)
		->whereDate('date', $date)->where('from_clinic',$request->oldappointmentclinic)
		->get();

		if(!empty($check_booking)){
			$count = count($check_booking) +1;
			$token_number = "TKN-" . sprintf("%03s", $count);

		}else{
			$token_number = "TKN-" . sprintf("%03s", 1);
		}

		$appointment = Appointment::create([
			'doctor_id'=>$request->oldappointmentdoc,
			'clinic_patient_id' =>$request->oldpatientid,
			'from_clinic'=> $request->oldappointmentclinic,
			'date' => $date,
			'time' => $time,
			'token'=> $token_number
		]);
		alert()->success(' Success !');

		return redirect()->route("appointments",$request->oldpatientid);

	}

    public function searchpatient(Request $request)
	{
		$patients = ClinicPatient::where('name', 'LIKE', "%{$request->name}%")
		->where('father_name', 'LIKE', "%{$request->fathername}%")->get();
		return response()->json($patients);
	}

    public function storeRecordInfo(Request $request)
	{

        //   dd($request->all());
		if(session()->get('user')->role == 'Nurse'){
			$validator = Validator::make($request->all(), [
				'temperature' => 'required',
				'oxygen' => 'required',
				'weight_kg' => 'required',
				'weight_lb' => 'required',
			]);
		}
		elseif(session()->get('user')->role == 'Doctor' || session()->get('user')->role == 'Nurse'){
			$validator = Validator::make($request->all(), [
				'procedure' => 'required',
			]);
		}



        if ($validator->fails()) {

            alert()->error('Something Wrong!');

            return redirect()->back();
        }

		if(session()->get('user')->role == 'Nurse'){
			$appointmentinfo = Clinicappointmentinfo::updateOrCreate([
				'appointment_id'=>$request->appointment_id,],
			[
				'body_temperature'=> $request->temperature,
				'bloodpressure_lower'=> $request->lowerpressure,
				'bloodpressure_higher'=>$request->upperpressure,
				'pr' =>$request->pr,
				'oxygen'=> $request->oxygen,
				'weight_kg' =>$request->weight_kg,
				'weight_lb' =>$request->weight_lb,

			]);
            $p = 0;
            $parr = [];
            $pitem_charge = 0;
            $q = 0;
            if($request->procedure_item != null){
                // dd('Heell');
                foreach($request->procedure_item_qty as $pqty){
                   if($pqty != null){
					array_push($parr,$pqty);
				   }
                }
                // dd($parr);
                $has_procedure_item = DB::table('appointment_procedure_item')->where('appointment_id',$request->appointment_id)->get();

                if(count($has_procedure_item) == 0){
                    // dd('heelo');
                    foreach($request->procedure_item as $pro){
                        DB::table('appointment_procedure_item')->insert([
                            'appointment_id' => $request->appointment_id,
                            'procedure_item_id'   => $pro,
                            'qty' => $parr[$p++],
                        ]);
                        $pitem = ProcedureItem::find($pro);
                        $pitem_charge += $pitem->price * $parr[$q++];
                    }
                }
                // dd($has_procedure_item);
				$pclinic_voucher = ClinicVoucher::where('appointment_id',$request->appointment_id)->first();
				$pclinic_voucher->procedure_item_charges = $pitem_charge;
				$pclinic_voucher->save();
            }
			$c = 0;
			$d = 0;

			$arr = [];
			$ser_charge = 0;
            if($request->service != null){
                foreach($request->service_qty as $qty){
                   if($qty != null){
					array_push($arr,$qty);
				   }
                }
                $has_service = DB::table('appointment_service')->where('appointment_id',$request->appointment_id)->get();
                if(count($has_service) == 0){
                    foreach($request->service as $serv){
                        DB::table('appointment_service')->insert([
                            'appointment_id' => $request->appointment_id,
                            'service_id'   => $serv,
                            'qty' => $arr[$c++],
                        ]);
                        $ser = Service::find($serv);
                        $ser_charge += $ser->charges * $arr[$d++];
                        if($request->title){
                            	$num = 0;
                            foreach($request->title as $title){
                                $tit  =   explode('-',$title);
                                $f = $request->fee[$num++];
                                $relate_fee = explode('-',$f);
                             if($ser->name == $tit[0]){
                             DB::table('service_related_fees')->insert([
                            'appointment_id' => $request->appointment_id,
                            'service_id'   => $serv,
                            'title' => $tit[1],
                            'fee'   => $relate_fee[1],
                        ]);
                                }

                            }
                        }
                    }
                    $clinic_voucher = ClinicVoucher::where('appointment_id',$request->appointment_id)->first();
				// dd($clinic_voucher->service_charges);
				$clinic_voucher->service_charges = $ser_charge;
				$clinic_voucher->save();
                }
                // dd($ser_charge);

				// dd($clinic_voucher);
            }
            if($request->ot_room != null){
                $ot = OtRoomUsage::find($request->ot_room);
                $has_ot = DB::table('appointment_ot_room_usage')->where('appointment_id',$request->appointment_id)->get();
                if(count($has_ot) == 0){
                    DB::table('appointment_ot_room_usage')->insert([
                        'appointment_id' => $request->appointment_id,
                        'ot_room_usage_id' => $request->ot_room
                    ]);
                }
                $clinic_voucher = ClinicVoucher::where('appointment_id',$request->appointment_id)->first();
				$clinic_voucher->ot_room_charges = $ot->charges;
				$clinic_voucher->save();
            }
            if($request->sur_name != null){
                // dd($request->sur_name);
                $su = 0;
                $sur_fees = 0;
                $has_surgen = SurgenFee::where('appointment_id',$request->appointment_id)->get();
                if(count($has_surgen) == 0){
                    foreach($request->sur_name as $sur){
                        SurgenFee::create([
                            'appointment_id'=>$request->appointment_id,
                            'surgen_name'=> $sur,
                            'charges'=>$request->sur_charges[$su++],
                        ]);
                       }
                 }
               foreach($request->sur_charges as $sur_ch){
                $sur_fees+=$sur_ch;
               }
               $clinic_voucher = ClinicVoucher::where('appointment_id',$request->appointment_id)->first();
				$clinic_voucher->surgen_charges = $sur_fees;
				$clinic_voucher->save();
            }

		}

		// elseif(session()->get('user')->role == 'Doctor' || session()->get('user')->role == 'Nurse'){
            // dd($request->all());
			$titles =count($request->titles);
			$titlesdata= [];
			$descriptionsdata= [];
			for($i=0; $i<$titles; $i++){
				if($request->titles[$i]){
					array_push($titlesdata,$request->titles[$i]);
					array_push($descriptionsdata,$request->descriptions[$i]);
				}
			}
            if($request->otroom){
                $appointmentinfo = Clinicappointmentinfo::updateOrCreate([
                    'appointment_id'=>$request->appointment_id,],
                [
                    'next_appointmentdate'=> $request->nextappointment_date,
                    "gc" => $request->gc,
                    "ht" => $request->ht,
                    "lgs" => $request->lgs,
                    "abd" => $request->abd,
                    "titles"=> json_encode($titlesdata),
                    "descriptions" => json_encode($descriptionsdata),
                    "complaint" => $request->complaint,
                    "procedure" => $request->procedure,
                    'bloodpressure_lower'=> $request->lowerpressure,
                    'bloodpressure_higher'=>$request->upperpressure,
                    'pr' =>$request->pr,
                    'ot_flag' => $request->otroom,
                ]);
            }else{
                $appointmentinfo = Clinicappointmentinfo::updateOrCreate([
                    'appointment_id'=>$request->appointment_id,],
                [
                    'next_appointmentdate'=> $request->nextappointment_date,
                    "gc" => $request->gc,
                    "ht" => $request->ht,
                    "lgs" => $request->lgs,
                    "abd" => $request->abd,
                    "titles"=> json_encode($titlesdata),
                    "descriptions" => json_encode($descriptionsdata),
                    "complaint" => $request->complaint,
                    "procedure" => $request->procedure,
                    'bloodpressure_lower'=> $request->lowerpressure,
                    'bloodpressure_higher'=>$request->upperpressure,
                    'pr' =>$request->pr,

                ]);
            }



			$appointment = Appointment::findOrfail($request->appointment_id);
			$appointment->diagnosis()->detach();
			foreach($request->diagnosis as $diag){
				$appointment->diagnosis()->attach($diag);
			}

		// }
		alert()->success(' Success !');
		return back();
	}

    public function attachmentsStore(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'appointment_id' => 'required',
			'descriptions' => 'required',
			'attachments'=>'required'
		]);


	if ($validator->fails()) {

		alert()->error('Please Fill the Fileds!');

		return redirect()->back();
	}

		if($request->hasfile('attachments'))
        {

            foreach($request->file('attachments') as $key=>$file)
            {
                $name1=$file->getClientOriginalName();
                $name = time().$name1;
				$path='/files/attachments/'.$name;
				$file->move(public_path() . '/files/attachments', $name);
				$attachmentfile= AppointmentAttachment::create([
					'attachment'=> $path,
					'description'=> $request->descriptions[$key],
					'appointment_id' => $request->appointment_id

				]);
            }
        }else{

			alert()->error('Something Wrong!');

			return redirect()->back();
		}
		alert()->success('Success!');

			return redirect()->back();
	}
    public function attachmentsDelete(Request $request)
	{
		try {
			$attachmentfile = AppointmentAttachment::findOrfail($request->document_id);
		} catch (\Exception $e) {

			return response()->json(0);

		}
		$attachmentfile->delete();

		$count = AppointmentAttachment::all();

		return response()->json($count);

	}

    protected function getClinicSalePage(Request $request){

        $items = Item::all();

        $categories = Category::all();

        $sub_categories = SubCategory::all();

        $employees = Employee::all();

        $date = new DateTime('Asia/Yangon');

        $otherServices= Service::otherservice()->get();

        $doctors = Doctor::with('services')->get();

        $today_date = strtotime($date->format('d-m-Y H:i'));

        // $state_lists= State::all();
    	return view('Clinic.clinic_sale_page',compact('items','categories','employees','today_date','sub_categories','otherServices','doctors'));
    }

    public function searchItem(Request $request)
    {
        $searchquery = $request->searchquery;
        $items = Item::where('item_name', 'LIKE', "%{$searchquery}%")->Orwhere('item_code', 'LIKE', "%{$searchquery}%")->get();
        return response()->json($items);

    }

    public function diagnosisStoreOntime(Request $request)

	{
		$userId = session()->get('user')->id;
		$alreadyDiagnosis = Diagnosis::where('name',$request->name)->where('created_by',$userId)->first();
		if(!$alreadyDiagnosis){
			$diagnosis= Diagnosis::create([
				'name' => $request->name,
				'created_by'=> $userId
			]);

		$diagnose = Diagnosis::where('created_by',$userId)->get();
		return response()->json([
			'status'=>1,
			'diagnose'=> $diagnose
		]);
		}else{
			return response()->json([
				'status'=>0,
				'diagnose'=> null
			]);
		}


	}

    public function storeRecord(Request $request)
	{
        //   dd($request->all());
			$validator = Validator::make($request->all(), [
				// 'doctorChargesbyHand' => 'required',
				'patient_id' => 'required',
				'appointment_id' => 'required',
			]);
			if($request->medicineTotalbyHand){
				$validator = Validator::make($request->all(), [
					'dose' => 'required',
					'duration' => 'required',
					'qtyDose' => 'required',
					'medicineTotalbyHand' => 'required'
				]);

			}

        if ($validator->fails()) {

            alert()->error('Something Wrong!');

            return redirect()->back();
        }
			$user = session()->get('user');

			$date = new DateTime('Asia/Yangon');

			$voucher_date = $date->format('Y-m-d');

			$items = json_decode($request->item);

			$grand = json_decode($request->grand);

			$pagServiceItem = json_decode($request->pagServiceItem);

			$pagServicegrandTotal = json_decode($request->pagServicegrandTotal);

			$total_amount = ($request->medicineTotalbyHand ?? 0)+$request->serviceTotalbyHand;

			// $total_quantity = $request->allQty;
			$alreadyVoucher = ClinicVoucher::where('appointment_id',$request->appointment_id)->first();

			if($alreadyVoucher){
				$voucher_code= $alreadyVoucher->voucher_code;
			}
			else
			{
				$last_voucher = ClinicVoucher::get()->last();
				if(empty($last_voucher)){
				$voucher_code =  "VOU-".date('dmY')."-".sprintf("%04s", 1);
				}else{
					$voucher_code =  "VOU-".date('dmY')."-".sprintf("%04s", ($last_voucher->id + 1));

				}
			}
			$voucher = ClinicVoucher::updateOrCreate([
				'appointment_id'=> $request->appointment_id
			],[
				'voucher_code' => $voucher_code,
				'sale_by' => $user->id,
				'total_price' =>  $total_amount,
				'total_quantity' => 0,
				'voucher_date' => $voucher_date,
				'type' => 1,
				'status' => 0,
				'clinicvoucher_status'=> 0,   //just record not voucher
				'medicine_charges'=> $request->medicineTotalbyHand,
				'service_charges'=> $request->serviceTotalbyHand,

			]);

			if(!empty($items)){
				$voucher->counting_unit()->detach();
				foreach ($items as $key=>$item) {
					if($request->look_procedure){
						for($i=0;$i<count($request->look_procedure);$i++){
							if($item->id == (int) $request->look_procedure[$i]){
								$lookProcedure = 1;
								break;
							}else{
								$lookProcedure=0;
							}
						}
					}else{
						$lookProcedure=0;
					}

					$dosename = Dose::find($request->dose[$key]);
					$doseNameQty =$dosename->name;
					$voucher->counting_unit()->attach($item->id, ['quantity' => $request->totlaqty[$key],'price' => 0,'dose'=> $doseNameQty,'duration'=> $request->duration[$key],'doseper_qty'=>$request->qtyDose[$key],'look_procedure'=>$lookProcedure]);
				}
			}

			if(!empty($pagServiceItem))
			{
				$voucher->services()->detach();

				foreach ($pagServiceItem as $pagServiceitem) {
					if($pagServiceitem->type=="service"){
					$voucher->services()->attach($pagServiceitem->id, ['quantity' => $pagServiceitem->qty]);
					}else{
					$voucher->packages()->attach($pagServiceitem->id, ['quantity' => $pagServiceitem->qty]);
					}
					// $voucher->services()->attach($pagServiceitem->id, ['quantity' => $pagServiceitem->qty]);

				}
			}


        if(session()->get('user')->role == 'Doctor' || session()->get('user')->role == 'Nurse'){
            alert()->success(' Success !');
            return back();
        }

		return redirect()->route('patienthist',$request->appointment_id);
	}

    protected function getClinicSaleHistoryPage(Request $request){
        if(session()->get('user')->role == 'EmployeeC' || session()->get('user')->role =='Doctor'){
            $voucher_lists =ClinicVoucher::where('type', 1)->where('clinicvoucher_status',1)->orderBy('id','desc')->get();

        }
        else{
            $voucher_lists =ClinicVoucher::where('type', 1)->orderBy('id','desc')->get();

        }

        $countunits=[];
        $arr_ki=[];
        $total_qty=[];

                    foreach($voucher_lists as $key=>$item){
                        $item_count=count($countunits);
                        for($i=0; $i<count($item->counting_unit);$i++){
                            if(!in_array($item->counting_unit[$i]->id,$arr_ki)){
                                array_push($arr_ki,$item->counting_unit[$i]->id);
                                array_push($total_qty,[
                                    'countingunit_id'=>$item->counting_unit[$i]->id,
                                    'qty'=>$item->counting_unit[$i]->pivot->quantity]
                                );
                                array_push($countunits,$item->counting_unit[$i]);
                            }

                        else{
                            foreach($total_qty as $key=>$t){


                               if($t['countingunit_id']==$item->counting_unit[$i]->id)
                                {
                                    $qty = $t['qty'] + $item->counting_unit[$i]->pivot->quantity;

                                    array_splice($total_qty, $key, 1);
                                    array_push($total_qty,[
                                        'countingunit_id'=>$item->counting_unit[$i]->id,
                                        'qty'=>$qty
                                    ]);
                                }
                            }

                        }

                        }

                    }

                    $total_sales  = 0;

        foreach ($voucher_lists as $voucher_list){

            $total_sales += $voucher_list->total_price;

        }
        $date = new DateTime('Asia/Yangon');

        $current_date = strtotime($date->format('Y-m-d'));

        $weekly = date('Y-m-d', strtotime('-1week', $current_date));
        $to = $date->format('Y-m-d');

        // $to = date('Y-m-d', strtotime('+1day', $current_date));use =>created_at

        if(session()->get('user')->role == 'Employee' || session()->get('user')->role == 'Doctor'){

            $weekly_data = ClinicVoucher::where('type', 1)->where('clinicvoucher_status',1)->whereBetween('voucher_date',[$weekly,$to])->get();
        }
        else{
            $weekly_data = ClinicVoucher::where('type', 1)->whereBetween('voucher_date', [$weekly,$to])->get();

        }
        $weekly_sales = 0;

        foreach($weekly_data as $weekly){

            $weekly_sales += $weekly->total_price;
        }

        $current_month = $date->format('m');
        $current_month_year = $date->format('Y');
        $today_date = $date->format('Y-m-d');
        if(session()->get('user')->role =='Employee' || session()->get('user')->role == 'Doctor'){
            $daily = ClinicVoucher::where('type', 1)->where('clinicvoucher_status',1)->whereDate('created_at', $today_date)->get();
        }
        else{
            $daily = ClinicVoucher::where('type', 1)->where('created_at', $today_date)->get();

        }

        $daily_sales = 0;

        foreach($daily as $day){

            $daily_sales += $day->total_price;
        }
        if(session()->get('user')->role == 'Employee' || session()->get('user')->role == 'Doctor'){

            $monthly = ClinicVoucher::where('type', 1)->where('clinicvoucher_status',1)->whereMonth('created_at',$current_month)->whereYear('created_at',$current_month_year)->get();

        }
        else{
            $monthly = ClinicVoucher::where('type', 1)->whereMonth('created_at',$current_month)->get();

        }
        $monthly_sales = 0;

        foreach ($monthly as $month){

            $monthly_sales += $month->total_price;
        }

        $counting_units= CountingUnit::all();


        return view('Clinic.clinic_sale_history_page',compact('counting_units','voucher_lists','total_sales','daily_sales','monthly_sales','weekly_sales','total_qty','countunits'));
    }

    protected function searchClinicSaleHistory(Request $request){


        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required',
        ]);
        if ($validator->fails()) {

            alert()->error('Something Wrong!');

            return redirect()->back();
        }
        if(session()->get('user')->role == "Employee" || session()->get('user')->role == 'Doctor'){

            $voucher_lists = ClinicVoucher::where('type', 1)->where('clinicvoucher_status',1)->whereBetween('voucher_date', [$request->from, $request->to])->get();
            // $voucher_lists = ClinicVoucher::where('type', 1)->where('clinicvoucher_status',1)->where('created_at', '>=', $request->from)
            // ->where('created_at', '<=', $request->to)->get();

        }else{
            $voucher_lists = ClinicVoucher::where('type', 1)->whereBetween('created_at', [$request->from, $request->to])->get();

        }

        if(session()->get('user')->role == "Employee" || session()->get('user')->role == 'Doctor'){
            $voucher_lists_all = ClinicVoucher::where('type', 1)->where('clinicvoucher_status',1)->get();

        }
        else{
            $voucher_lists_all = ClinicVoucher::where('type', 1)->get();

        }

        $total_sales  = 0;

        foreach ($voucher_lists_all as $voucher_list){

            $total_sales += $voucher_list->total_price;

        }


        $countunits=[];
        $arr_ki=[];
        $total_qty=[];

                    foreach($voucher_lists as $key=>$item){
                        $item_count=count($countunits);
                        // if($item_count ==0)
                        // {
                        //     array_push($countunits,$item->counting_unit);
                        //     array_push($arr_ki,$item->counting_unit[0]->id);
                        //     array_push($total_qty,[$item->counting_unit[0]->id=>$item->total_quantity]);
                        // }
                        // else{
                        for($i=0; $i<count($item->counting_unit);$i++){
                            if(!in_array($item->counting_unit[$i]->id,$arr_ki)){
                                array_push($arr_ki,$item->counting_unit[$i]->id);
                                array_push($total_qty,[
                                    'countingunit_id'=>$item->counting_unit[$i]->id,
                                    'qty'=>$item->counting_unit[$i]->pivot->quantity]
                                );
                                array_push($countunits,$item->counting_unit[$i]);
                            }

                        else{
                            foreach($total_qty as $key=>$t){


                               if($t['countingunit_id']==$item->counting_unit[$i]->id)
                                {
                                    $qty = $t['qty'] + $item->counting_unit[$i]->pivot->quantity;

                                    array_splice($total_qty, $key, 1);
                                    array_push($total_qty,[
                                        'countingunit_id'=>$item->counting_unit[$i]->id,
                                        'qty'=>$qty
                                    ]);
                                }
                            }

                        }

                        }

                    }


                    $date = new DateTime('Asia/Yangon');

                    $current_date = strtotime($date->format('Y-m-d'));

                    $weekly = date('Y-m-d', strtotime('-1week', $current_date));
                    $to = date('Y-m-d', strtotime('+1day', $current_date));

                    if(session()->get('user')->role == "Employee" || session()->get('user')->role == 'Doctor'){

                        $weekly_data = ClinicVoucher::where('type', 1)->where('clinicvoucher_status',1)->whereBetween('created_at',[$weekly,$to])->get();

                    }
                    else{
                        $weekly_data = ClinicVoucher::where('type', 1)->whereBetween('voucher_date', [$weekly,$to])->get();

                    }
                    $weekly_sales = 0;

                    foreach($weekly_data as $weekly){

                        $weekly_sales += $weekly->total_price;
                    }

                    $current_month = $date->format('m');
                    $current_month_year = $date->format('Y');
                    $today_date = $date->format('Y-m-d');
                    if(session()->get('user')->role == "Employee" || session()->get('user')->role == 'Doctor'){
                        $daily = ClinicVoucher::where('type', 1)->where('clinicvoucher_status',1)->whereDate('created_at', $today_date)->get();
                    }
                    else{
                        $daily = ClinicVoucher::where('type', 1)->where('created_at', $today_date)->get();

                    }

                    $daily_sales = 0;

                    foreach($daily as $day){

                        $daily_sales += $day->total_price;
                    }
                    if(session()->get('user')->role == "Employee" || session()->get('user')->role == 'Doctor'){

                        $monthly = ClinicVoucher::where('type', 1)->where('clinicvoucher_status',1)->whereMonth('created_at',$current_month)->whereYear('created_at',$current_month_year)->get();

                    }
                    else{
                        $monthly = ClinicVoucher::where('type', 1)->whereMonth('created_at',$current_month)->get();

                    }
                    $monthly_sales = 0;

                    foreach ($monthly as $month){

                        $monthly_sales += $month->total_price;
                    }
        $counting_units= CountingUnit::all();

        return view('Clinic.clinic_sale_history_page',compact('counting_units','voucher_lists','total_sales','daily_sales','monthly_sales','weekly_sales','countunits','total_qty'));

    }

    protected function getClinicVoucherDetails(request $request, $id){
        //  dd('hello');
        $unit = ClinicVoucher::find($id);
        // dd($unit);
        $appointment = Appointment::where('id',$unit->appointment->id)->with('attachments')->with('clinic_patient')->with('clinic_voucher')->with('clinic_voucher.counting_unit')->with('clinic_voucher.services')->with('diagnosis')->with('services')->with('procedure_items')->first();
        $packages= $unit->packages;
        $payed = $unit->total_price;
        $docAndservices= $unit->services;
        $total_charges = $appointment->clinic_voucher->medicine_charges + $appointment->clinic_voucher->procedure_item_charges+ $appointment->clinic_voucher->service_charges+ $appointment->clinic_voucher->ot_room_charges+ $appointment->clinic_voucher->surgen_charges + 6500;
        $ot = DB::table('appointment_ot_room_usage')->where('appointment_id',$unit->appointment->id)->first();
        $ot_room = OtRoomUsage::find($ot->ot_room_usage_id);
         $relate_fee = 0;
        $other_related_fee = DB::table('service_related_fees')->where('appointment_id',$unit->appointment->id)->get();
        if($other_related_fee != null){
            foreach($other_related_fee as $other){
                $relate_fee += $other->fee;
                $total_charges += $other->fee;
            }
        }
        // dd($other_related_fee);
        return view('Clinic.clinic_voucher_detail', compact('appointment','total_charges','unit','payed','packages','docAndservices','ot_room','other_related_fee','relate_fee'));
    }

    public function history()
	{
		$diagnosis = Diagnosis::all();
		return view('Clinic.history',compact('diagnosis'));
	}
    protected function patientlistexport(Request $request,$from,$to){


        return $this->excel->download(new PatientListExport($from,$to),'PatientListExport.xlsx');
    }

    public function searchpatientToday(Request $request)
	{
		$todayorall= $request->todayorall;
		$date = new DateTime('Asia/Yangon');
        $today_date = $date->format('d');
		$name= $request->name;
		$fathername= $request->fathername;
		if($todayorall=='today')
		{
			$appointments = Appointment::whereDay('date',$today_date)->with('clinic_patient')->with('doctor')->with('clinic_voucher')->whereHas('clinic_patient', function($q) use($name, $fathername){
				$q->where('name', 'LIKE', "%{$name}%")->where('father_name', 'LIKE', "%{$fathername}%");
			})->get();
		}
		else{
			$dias= $request->diagnosis;
			if($request->filterName=='name'){
				if($dias){
					$appointments= ClinicPatient::where('name', 'LIKE', "%{$name}%")->where('father_name', 'LIKE', "%{$fathername}%")->with('appointments.diagnosis')
					->whereHas('appointments.diagnosis', function($q) use($dias){
						// foreach($dias as $dia){
						// 	$q= $q->where('diagnosis_id', $dia);
						// 	if($q){
						// 		continue;
						// 	}else{
						// 		break;
						// 	}
						// }
						$q->whereIn('diagnosis_id',$dias);
					})
					->withCount('appointments')->get();
				}
				else{
					$appointments= ClinicPatient::where('name', 'LIKE', "%{$name}%")->where('father_name', 'LIKE', "%{$fathername}%")->withCount('appointments')->get();
				}
			}
			else{
				$fromdate= $request->fromdate;
				$todate= $request->todate;
				$appointments= ClinicPatient::with('appointments')
				->whereHas('appointments', function($q) use($fromdate,$todate){
					$q->whereBetween('date', [$fromdate, $todate]);
				})
				->withCount('appointments')->get();
				// $appointments = Appointment::whereBetween('date', [$request->fromdate, $request->todate])->with('clinic_patient')->with('doctor')->with('voucher')->get();
			}


		}
		return response()->json($appointments);

	}

    protected function getClinicVucherPage(Request $request){
        dd($request->all());
        $validator = Validator::make($request->all(), [
            'delivery' => 'required'
        ]);
        if($request->delivery==1){
            $validator = Validator::make($request->all(), [
                'pickupname' => 'required',
                'pickupphone' => 'required',
                'pickupdate' => 'required'
            ]);
        }
        if($request->delivery==2){
            $validator = Validator::make($request->all(), [
                'receivername' => 'required',
                'receiverphno' => 'required',
                'deliverydate' => 'required',
                'state_id' => 'required',
                'town_id' => 'required',
                'address' => 'required',
                'charges' => 'required',
            ]);
        }

        if ($validator->fails()) {

            alert()->error('Something Wrong!');

            return redirect()->back();
        }
        $deliveryinfo= ['delivery'=>$request->delivery,'pickupname'=>$request->pickupname,'pickupphone'=>$request->pickupphone,'pickupdate'=>$request->pickupdate,'receivername'=>$request->receivername,'receiverphno'=>$request->receiverphno,'deliverydate'=>$request->deliverydate,'state_id'=>$request->state_id,'town_id'=>$request->town_id,'address'=>$request->address,'charges'=>$request->charges];

        $date = new DateTime('Asia/Yangon');

        $today_date = $date->format('d-m-Y h:i:s');

        $check_date = $date->format('Y-m-d');

        $items = json_decode($request->item);

        $grand = json_decode($request->grand);

        $docServiceItem = json_decode($request->docServiceItem);
        $docServiceGrandTotal = json_decode($request->docServiceGrandTotal);
        $pagServiceItem = json_decode($request->pagServiceItem);
        $pagServicegrandTotal = json_decode($request->pagServicegrandTotal);

        $last_voucher = ClinicVoucher::get()->last();

        $right_now_customer= $request->right_now_customer;
        if(empty($last_voucher)){
        $voucher_code =  "VOU-".date('dmY')."-".sprintf("%04s", 1);
        }else{
            $voucher_code =  "VOU-".date('dmY')."-".sprintf("%04s", ($last_voucher->id + 1));

        }

        return view('Clinic.clinic_voucher', compact('items','today_date','grand','voucher_code','right_now_customer','docServiceItem','docServiceGrandTotal','pagServiceItem','pagServicegrandTotal','deliveryinfo'));
    }

    // CAnvas
    public function canvas(){
        return view('canvas');
    }

    //Medical REcord
    public function medicalrecord(Request $request)
	{
		if($request->filter_name=="date"){
			$validator = Validator::make($request->all(), [
				'patient_id' => 'required',
				'from_date' => 'required',
				'to_date' => 'required',
			]);
		}
		else if($request->filter_name=="count"){
			$validator = Validator::make($request->all(), [
				'patient_id' => 'required',
				'count_app' => 'required',
			]);
		}else {
			$validator = Validator::make($request->all(), [
				'patient_id' => 'required',
				'filter_name' => 'required',
			]);
		}


	if ($validator->fails()) {

		alert()->error('Fill the fileds!');

		return redirect()->back();
	}


		try {
			$patient = ClinicPatient::findOrFail($request->patient_id);
		} catch (\Exception $e) {

			alert()->error("Patient Not Found!")->persistent("Close!");

		}

		$patient_id = $request->patient_id;

		if($request->filter_name== 'count'){

			$appointments = Appointment::where('clinic_patient_id',$request->patient_id)->with('clinic_patient')->with('diagnosis')->with('clinic_voucher')->with('clinic_voucher.counting_unit')->with('clinic_voucher.counting_unit.item')->orderBy('id','desc')->take($request->count_app)->get();
		}

		else if($request->filter_name== 'date'){
			$fromdate= $request->from_date;
			$todate= $request->to_date;

			$appointments = Appointment::where('clinic_patient_id',$request->patient_id)->whereBetween('date',[$request->from_date,$request->to_date])->with('clinic_patient')->with('diagnosis')->with('clinic_voucher')->with('clinic_voucher.counting_unit')->with('clinic_voucher.counting_unit.item')->orderBy('id','desc')->get();
		}
		else{
		$appointments = Appointment::where('clinic_patient_id',$request->patient_id)->with('clinic_patient')->with('diagnosis')->with('clinic_voucher')->with('clinic_voucher.counting_unit')->with('clinic_voucher.counting_unit.item')->orderBy('id','desc')->get();
		}
		if(count($appointments)<=0){
			alert()->error(' Not Medical  Record Found !');
			return redirect()->back();
		}
		return view("Clinic.medical_record", compact('appointments','patient'));
	}
    public function attachimg(Request $request)
	{
		if($request->filter_name=="date"){
			$validator = Validator::make($request->all(), [
				'patient_id' => 'required',
				'from_date' => 'required',
				'to_date' => 'required',
			]);
		}
		else if($request->filter_name=="count"){
			$validator = Validator::make($request->all(), [
				'patient_id' => 'required',
				'count_app' => 'required',
			]);
		}else {
			$validator = Validator::make($request->all(), [
				'patient_id' => 'required',
				'filter_name' => 'required',
			]);
		}


	if ($validator->fails()) {

		alert()->error('Fill the fileds!');

		return redirect()->back();
	}

		// $appointments = ClinicPatient::findOrfail($request->patient_id)->appointments;

		$patient_id = $request->patient_id;

		if($request->filter_name== 'count'){

			$appointments = Appointment::where('clinic_patient_id',$patient_id)->latest()->take($request->count_app)->get();

		}
		else if($request->filter_name== 'date'){
			$fromdate= $request->from_date;
			$todate= $request->to_date;
			$appointments = Appointment::where('clinic_patient_id',$patient_id)->whereBetween('date',[$request->from_date,$request->to_date])->get();

		}
		else{
			$appointments = Appointment::where('clinic_patient_id',$patient_id)->get();
		}

		$allimgs= [];
		foreach($appointments as $appo){
			foreach($appo->attachments as $attach){
				$img= array_push($allimgs,$attach);
			}
		}

		if(count($allimgs)<=0){
			alert()->error(' Not Attachment Images !');
			return redirect()->back();
		}
		return view(('Clinic.attachimg'),compact('allimgs','patient_id'));
	}
    public function storeVoucher(Request $request)
	{
		$voucher = ClinicVoucher::findOrfail($request->voucher_id);
		$voucher->clinicvoucher_status = 1;    //get voucher
		$voucher->save();

		return response()->json('success');

	}
}
