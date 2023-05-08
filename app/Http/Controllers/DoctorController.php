<?php

namespace App\Http\Controllers;

use App\Day;
use App\User;
use DateTime;
use App\Doctor;
use App\Service;
use App\Employee;
use App\Department;
use App\DoctorInfo;
use Illuminate\Http\Request;
use App\EducationInformation;
use App\ExperienceInformation;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    //
    protected function DoctorList(){

		$doctors = Doctor::all();

		$employee = Employee::get();

		return view('Doctor.doctor_lists', compact('doctors','employee'));
	}

    protected function CreateDoctor(){

		$departments = Department::all();

		$days = Day::all();

		$doctorServices = Service::where('status',0)->get();

		return view('Doctor.create_doctor', compact('departments','days','doctorServices'));
	}

    protected function StoreDoctor(Request $request){
    //   dd($request->consultation);
		$image = "user.jpg";

		$degree = $request->degree;
        // dd($degree);
        $degree_arr = explode("-", $degree, -1);
        // dd($degree_arr);
        $university = $request->university;

        $university_arr = explode("-", $university, -1);

        $subject = $request->subject;

        $subject_arr = explode("-", $subject, -1);

        $position = $request->position;

        $position_arr = explode("-", $position, -1);

        $place = $request->place;

        $place_arr = explode("-", $place, -1);
//user create
		// $user= User::create([
		// 	'name' => $request->name,
		// 	'email' => $request->email,
		// 	'password' => bcrypt($request->password),
        //     'role' => 'Doctor',
        //     'prohibition_flag' => 1,
        //     'photo_path' => $image,
        //     'from_id' => 2
		// ]);
//role create
		// $user->assignRole(5);
//

		$doctors = Doctor::create([
			'name' => $request->name,
			'gender' => $request->gender,
			'photo' => $image,
			'doctor_code' => "",
			'position' => $request->position_now,
			'address' => $request->address,
			'about_doc' => $request->about_doc,
			'phone' => $request->phone,
			'status' => 1,
			// 'user_id' => $user->id,
			'department_id' => $request->department,
			'consultation_fee' => $request->consultation,
			// 'online_early_payment' => $request->online_early_payment,
		]);

		$doctor_id = $doctors->id;

		$doctor_code = "DOC-" . sprintf("%04s", $doctor_id);

		$doctors->doctor_code = $doctor_code;

		$doctors->save();

		$booking_range = request('range');

		$range = request('weekormonth');

		$book_range = $booking_range . "-" . $range;

		// $doc_info = DoctorInfo::create([
		// 	'reserved_token' => $request->vip_token,
		// 	'maximum_token' => $request->max_token_no,
		// 	'status' => $request->status,
		// 	'advance_time' => 0,
		// 	'time_per_patient' => 0,
		// 	'doctor_id' => $doctors->id,
		// 	'booking_range' => $book_range,
		// ]);

		// $services= $request->services;
		// if($services){
		// 	foreach($services as $service){
		// 		$doctors->services()->attach($service);
		// 	}
		// }


		$now = $now = new DateTime('Asia/Yangon');

        $today = $now->format('Y-m-d H:i:s');

        for($count = 0; $count < count($degree_arr); $count++){

            $data_two = array(
                'university'  => $university_arr[$count],
                'subject'  => $subject_arr[$count],
                'degree'  => $degree_arr[$count],
                'doctor_id'  => $doctors->id,
                'created_at'  => $today,
                'updated_at'  => $today,
            );

            $insert_data_edu[] = $data_two;
        }

        $edu = EducationInformation::insert($insert_data_edu);


        if (count($position_arr) != 0) {

        	for($count = 0; $count < count($position_arr); $count++){

	            $data_one = array(
	                'position'  => $position_arr[$count],
	                'place'  => $place_arr[$count],
	                'doctor_id'  => $doctors->id,
	                'created_at'  => $today,
	                'updated_at'  => $today,
	            );

	            $insert_data_exp[] = $data_one;
        	}

        	$exp = ExperienceInformation::insert($insert_data_exp);
        }

		return response()->json($doctors);
   	}

    public function ScheduleList(){

    	$doctors = Doctor::with('day')->orderBy('department_id', 'desc')->get();

    	return view('Doctor.schedule_list', compact('doctors'));
    }

    public function CreateScheduleDay(Request $request){

    	$departments = Department::all();

    	$days = Day::all();

    	return view('Doctor.create_schedule', compact('departments','days'));
    }

    public function AjaxDepartment(Request $request){

    	$department = $request->department;

    	$doctors = Doctor::where('department_id', $department)->get();

    	return response()->json($doctors);
    }

    public function StoreScheduleDay(Request $request){

    	$validator = Validator::make($request->all(), [
			'doctor' => 'required',
			'days' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
		]);

		if ($validator->fails()) {

			alert()->error('Validation Error');

			return redirect()->back();
		}

		$doctor = Doctor::find($request->doctor);

		$array_days = $request->days;

        $start_time = date('H:i', strtotime($request->start_time));

        $end_time = date('H:i', strtotime($request->end_time));

		foreach ($array_days as $days) {

			$doctor->day()->attach($days, ['start_time' => $start_time,'end_time' => $end_time]);
		}

        alert()->success("Successfully Added!");

        return redirect()->back();
    }

    protected function CheckDoctorProfile($doctor, Request $request){

		try {

			$doctor = Doctor::findOrFail($doctor);

			return view ('Doctor.profile', compact('doctor'));

   		} catch (\Exception $e) {

        	alert()->error("Doctor Not Found!")->persistent("Close!");

            return redirect()->back();

    	}
	}

    protected function editDoctor($id){

		// $user= User::findOrfail($id);
		$doctor = Doctor::where('id',$id)->with('department')->with('doc_info')->with('doc_exp')->with('doc_edu')->with('services')->with('user')->first();
		$doctorServices = Service::where('status',0)->get();
		$departments = Department::all();
		return view('Doctor.edit_doctor', compact('doctor','departments','doctorServices'));
	}

    protected function editStoreDoctor(Request $request){

		$image = "user.jpg";

		$degree = $request->degree;

        $degree_arr = explode("-", $degree, -1);

        $university = $request->university;

        $university_arr = explode("-", $university, -1);

        $subject = $request->subject;

        $subject_arr = explode("-", $subject, -1);

        $position = $request->position;

        $position_arr = explode("-", $position, -1);

        $place = $request->place;

        $place_arr = explode("-", $place, -1);
//user create
$doc= Doctor::find($request->doctor_id);
$doctor = $doc->update([
	'name' => $request->name,
	'gender' => $request->gender,
	'photo' => $image,
	'doctor_code' => "",
	'position' => $request->position_now,
	'address' => $request->address,
	'about_doc' => $request->about_doc,
	'phone' => $request->phone,
	'status' => 1,
	'user_id' => $doc->user->id,
	'department_id' => $request->department,
	'online_early_payment' => $request->online_early_payment,
]);
		$user= $doc->user->update([
			'name' => $request->name,
			'email' => $request->email
		]);
		if($request->password){
			$doc->user->update([
				'password' => bcrypt($request->password)
			]);
		}
//
		$booking_range = request('range');

		$range = request('weekormonth');

		$book_range = $booking_range . "-" . $range;
		$doc_info = $doc->doc_info->update([
			'reserved_token' => $request->vip_token,
			'maximum_token' => $request->max_token_no,
			'status' => $request->status,
			'advance_time' => 0,
			'time_per_patient' => 0,
			'doctor_id' => $request->doctor_id,
			'booking_range' => $book_range,
		]);

		$services= $request->services;
			$doc->services()->sync($services);

        $edu = EducationInformation::where('doctor_id',$request->doctor_id)->delete();
        for($count = 0; $count < count($degree_arr); $count++){

            $data_two = array(
                'university'  => $university_arr[$count],
                'subject'  => $subject_arr[$count],
                'degree'  => $degree_arr[$count],
                'doctor_id'  => $request->doctor_id
            );

        $edu = EducationInformation::updateOrCreate($data_two);

        }
		$exp = ExperienceInformation::where('doctor_id',$request->doctor_id)->delete();

        if (count($position_arr) != 0) {

        	for($count = 0; $count < count($position_arr); $count++){

	            $data_one = array(
	                'position'  => $position_arr[$count],
	                'place'  => $place_arr[$count],
	                'doctor_id'  => $request->doctor_id,
	            );

	            $insert_data_exp[] = $data_one;
        	}
        	$exp = ExperienceInformation::insert($insert_data_exp);
        }



		$docto = Doctor::where('id',$request->doctor_id)->with('department')->with('doc_info')->with('doc_exp')->with('doc_edu')->with('services')->with('user')->first();


		return response()->json($docto);
}

public function CheckScheduleTime($day, $doctor ,Request $request){

    $doctors = Doctor::where('id', $doctor)->first();

    $day = $doctors->day()->where('day_id', $day)->first();

    $new_start_time = date('h:i: a', strtotime($day->pivot->start_time));

    $new_end_time = date('h:i: a', strtotime($day->pivot->end_time));

    alert()->info("$new_start_time - $new_end_time \n \n Doctor - ". $doctors->name)->persistent("Close");

    return redirect()->back();

}

public function doctorProfile()
{
    $userid = session()->get('user')->id;

    try {

        $doctor = Doctor::where('user_id',$userid)->first();

        return view('Doctor.profile', compact('doctor'));
    } catch (\Exception $e) {

        alert()->error("Doctor Not Found!")->persistent("Close!");

        return redirect()->back();
    }
}

public function DoctorScheduleList()
{
    $userID = session()->get('user')->id;
    $doc = Doctor::where('user_id',$userID)->with('day')->first();
    return view('Doctor.doctor_schedule', compact('doc'));
}

}
