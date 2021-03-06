<?php

namespace App\Http\Controllers\Nora;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\Nora\NoraSchedule;
use App\Model\Nora\noraPatient;
use Illuminate\Support\Facades\Auth;
use DB;

class NoraSchedulerController extends Controller
{
    public function index(Request $request)
    {	//dd($request);
		
		$enccode = $request->enccode;
		
		$patientDetails = noraPatient::where('enccode', $enccode)->get()->first();	
		$patientNoraHpercode = $patientDetails['hpercode'];
		$patientLastName = $patientDetails['patlast'];
		$patientFirstName = $patientDetails['patfirst'];
		$patientMiddleName = $patientDetails['patmiddle'];
		$patientName = $patientLastName.", ".$patientFirstName." ".$patientMiddleName;
		$patientRoom= $patientDetails['patward'];
		$patientAge = $patientDetails['patage'];
		$patientSex = $patientDetails['patsex'];

		$events = array();
    	if($request->ajax())
    	{ 
				
			
    		$data = NoraSchedule::whereDate('start', '>=', $request->start)
                       ->whereDate('end',   '<=', $request->end)
                       ->get(['id', 'title', 'start', 'end']);

			// $data = DB::SELECT("SELECT * FROM nora.paul.nora_events WHERE CONVERT(date, start)  ");
			
			foreach($data as $sched){
				$color = null;
				if(substr( $sched->title, 0, 3 )  === 'GI-'){
					$color = '#04AA6D';
				}else if (substr( $sched->title, 0, 11 ) == 'RADIO/ONCO-'){
					$color = '#2196F3';
				}else if (substr( $sched->title, 0, 7 )  == 'BRACHY-'){
					$color = '#ff9800';
				}

				$events[] = [
					'id' => $sched->id,
					'title' => $sched->title,
					'start' => $sched->start,
					'end' => $sched->end,
					'color' => $color,
					

				];
			}
			
            return response()->json($events);
    	}
		
    	return view('nora.scheduler.noraScheduler',compact('patientName','enccode','patientRoom','patientAge','patientSex' , 'patientNoraHpercode'));
    }

	public static function anestheologistList()
    {
        return DB::SELECT("SELECT hpersonal.employeeid, hpersonal.lastname, hpersonal.firstname, hpersonal.middlename, hprovider.empdegree, htypser.tsdesc, hprovider.licno from hpersonal 
        INNER JOIN hprovider 
        ON hpersonal.employeeid = hprovider.employeeid
        INNER JOIN htypser
        ON hpersonal.deptcode = htypser.tscode
        WHERE hprovider.empstat = 'A' AND htypser.tsdesc = 'Pain Clinic/Anesthesia'
        ORDER BY hpersonal.lastname");
    }

    public function action(Request $request)
    {	
		$enccode = $request->enccode;
		
		$employeeid = Auth::user()->employeeid;

		$patientDetailsAdd = noraPatient::where('enccode', $enccode)->get()->first();	
		
		$patientLastNameAdd = $patientDetailsAdd['patlast'];
		$patientFirstNameAdd = $patientDetailsAdd['patfirst'];
		$patientMiddleNameAdd = $patientDetailsAdd['patmiddle'];
		$patientRoomAdd = $patientDetailsAdd['patward'];
		$patientAgeAdd = $patientDetailsAdd['patage'];
		$patientSexAdd = $patientDetailsAdd['patsex'];
		
		
		//$patientName = $patientLastName.", ".$patientFirstName." ".$patientMiddleName;
			// dd($request);

			
    	if($request->ajax())
    	{	
    		if($request->type == 'add')
    		{	
    			$event = NoraSchedule::create([
    				'title'		=>	$request->title,
    				'start'		=>	$request->start,
    				'end'		=>	$request->end,
					'service_type' => $request->serviceTypeAdd,
					'enccode'   =>  $enccode,
					'patientNoraHpercode' => $request->patientNoraHpercodeAdd,
					'patient_lastname' => $patientLastNameAdd,
					'patient_firstname' => $patientFirstNameAdd,
					'patient_middlename' => $patientMiddleNameAdd,
					'patient_procedure' => $request->patientProcedureAdd,
					'induction_time' => $request->inductionTimeAdd,
					'referring_physician' => $request->referringPhysicianAdd,
					'anesthesiologist' => $request->anesthesiologistAdd,
					'duration_time' =>  $request->durationTimeAdd,
					'patient_room' => $patientRoomAdd,
					'svc_pvt' => $request->svcPvtAdd,
					'patient_age' => $patientAgeAdd,
					'patient_sex' => $patientSexAdd,
					
    			]);

				$latestId = DB::table('nora.paul.nora_events')->orderBy('id','desc')->first();
				
				
				DB::table('nora.paul.nora_patients')
				->where ('enccode', $enccode)->increment('scheduled', 1);
				
									
					$lastGeneratedEventid = (int)$latestId->id;
					$eventId = $lastGeneratedEventid ;
					DB::table('nora.paul.nora_actlog')
					->insert([
						'act_details' => 'Add New Schedule',
						'events_id' => $eventId,					
						'employeeid' => $employeeid,
						'patient_id' => $request->patientNoraHpercodeAdd
						
					]);
				
				
    			//return response()->json($event);
				return view('nora.layouts.master');
    		}

    		if($request->type == 'update')
    		{
    			$event = NoraSchedule::find($request->id)->update([
    				'title'		=>	$request->title,
    				'start'		=>	$request->start,
    				'end'		=>	$request->end
    			]);

    			return response()->json($event);
    		}
			if($request->type == 'editUpdate')
    		{
    			$event = NoraSchedule::find($request->id)->update([
    				'title'		=>	$request->title,
    				'start'		=>	$request->start,
    				'end'		=>	$request->end,
					'service_type' => $request->serviceTypeAdd,
					'enccode'   =>  $enccode,
					'patient_lastname' => $patientLastNameAdd,
					'patient_firstname' => $patientFirstNameAdd,
					'patient_middlename' => $patientMiddleNameAdd,
					'patient_procedure' => $request->patientProcedureAdd,
					'induction_time' => $request->inductionTimeAdd,
					'referring_physician' => $request->referringPhysicianAdd,
					'anesthesiologist' => $request->anesthesiologistAdd,
					'duration_time' =>  $request->durationTimeAdd,
					'patient_room' => $patientRoomAdd,
					'svc_pvt' => $request->svcPvtAdd,
					'patient_age' => $patientAgeAdd,
					'patient_sex' => $patientSexAdd
    			]);

    			return response()->json($event);
    		}
			if($request->type == 'edit')
    		{	
    			//dd($request->id);
				$event = NoraSchedule::where('id', $request->id)->get();
				
    			return response()->json($event);
    		}

    		if($request->type == 'delete')
    		{
				
    			// $event = NoraSchedule::find($request->id);
    			$event = NoraSchedule::where('id', $request->id)->delete();
				// dd($event);?
    			return response()->json($event);
    		}
    	}
    }
}
?>
