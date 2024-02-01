<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\UserProvided;
use App\Models\CovidVaccine;
use App\Models\UserDetail;
use App\Models\Slot;
use App\Models\TimeSheet;
use App\Models\Position;
use App\Models\Facility;
use App\Models\Device;
use App\Models\UserVerify;
use App\Models\Booking;
use App\Models\Job;
use App\Services\FirebaseServices;
use Exception;
use File;
use Hash;

class UserController extends Controller
{
    public function __construct(FirebaseServices $firebaseServices)
    {
        $this->firebaseServices = $firebaseServices;
    }

    public function index(Request $request)
    {
        if ($request->position) {
            $data['users'] = User::select('users.*', 'user_details.*', 'users.id as uid')->leftJoin('user_details', 'user_details.user_id', 'users.id')->orderBy('users.id', 'desc')->where('user_details.position', $request->position)->paginate(15);
        } else if ($request->facility) {
            $data['users'] = User::select('users.*', 'user_details.*', 'users.id as uid')->leftJoin('user_details', 'user_details.user_id', 'users.id')->orderBy('users.id', 'desc')->where('user_details.facility', $request->facility)->paginate(15);
        } else {
            $data['users'] = User::select('users.*', 'users.id as uid')->orderBy('id', 'desc')->paginate(15);
        }

        return view('admin.user.index')->with($data);
    }

    public function create(Request $request)
    {
        $data['categories'] = Category::orderBy('id', 'desc')->get();
        $data['provided'] = UserProvided::orderBy('id', 'desc')->get();
        $data['vaccines'] = CovidVaccine::orderBy('id', 'desc')->get();
        $data['positions'] = Position::orderBy('id', 'desc')->get();
        $data['facilities'] = Facility::orderBy('id', 'desc')->get();
        return view('admin.user.user-form')->with($data);
    }

    public function slots(Request $request)
    {
        $user_id = $request->id;
        $data['slots'] = Slot::where('user_id', $user_id)->orderBy('id', 'desc')->paginate(15);
        return view('admin.user.slot')->with($data);
    }


    public function timesheets(Request $request)
    {
        $user_id = $request->id;
        if ($user_id) {
            $data['timesheets'] = TimeSheet::select('users.*', 'time_sheets.*', 'time_sheets.image as image')->leftJoin('users', 'users.id', 'time_sheets.user_id')->where('time_sheets.user_id', $user_id)->orderBy('time_sheets.id', 'desc')->paginate(15);
        } else {
            $data['timesheets'] = TimeSheet::select('users.*', 'time_sheets.*', 'time_sheets.image as image')->leftJoin('users', 'users.id', 'time_sheets.user_id')->orderBy('time_sheets.id', 'desc')->paginate(15);
        }

        return view('admin.user.timesheet')->with($data);
    }


    public function save(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email',
            'status' => 'required'
        ]);

        if (!$request->user_id) {
            $user = new User();
            $msg = "User Added Successfully.";
        } else {
            $user = User::findOrFail($request->user_id);
            $msg = "User updated Successfully.";
        }
        try {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            if (!$request->user_id) {
                $user->password = $request->password;
            }
            $user->uuid =  Str::uuid($request->email);
            $user->status = $request->status;
            $user->save();

            $detail = UserDetail::firstOrNew(['user_id' =>  $user->id]);
            $detail->user_id = $user->id;
            $detail->position = $request->position;
            $detail->facility = $request->facility;
            $detail->street_address = $request->street_address;
            $detail->apartment = $request->apartment;
            $detail->city = $request->city;
            $detail->prov = $request->prov;
            $detail->postal_code = $request->postal_code;
            $detail->dob = $request->dob;
            $detail->insurance_no = $request->insurance_no;
            $detail->career = $request->career;
            ///dd($request->user_provided);
            $detail->user_provided =  serialize($request->user_provided);
            $detail->covid_vaccines = serialize($request->covid_vaccines);
            $detail->save();


            return redirect()->back()->with(["msg" => $msg, 'msg_type' => 'success']);
        } catch (Exception $e) {
            return redirect()->back()->with(["msg" => $e->getMessage(), 'msg_type' => 'danger']);
        }
    }

    public function booking(Request $request)
    {
        $validated = $request->validate([
            'job_id' => 'required'
        ]);

        $userCount = User::where(['id' => $request->user_id])->count();
        if ($userCount < 1) {
            return redirect()->back()->with(["msg" => "User does not exist.", 'msg_type' => 'danger']);
        } else {
            $user = User::where('id', $request->user_id)->first();
        }

        $jobCount = Job::where(['id' => $request->job_id])->count();
        if ($jobCount < 1) {
            $job = Job::where(['id' => $request->job_id])->first();
            return redirect()->back()->with(["msg" => "Job does not exist.", 'msg_type' => 'danger']);
        }

        $bookingCount = Booking::where(['job_id' => $request->job_id])->count();
        if ($bookingCount > 0) {
            return redirect()->back()->with(["msg" => "Job already booked.", 'msg_type' => 'danger']);
        }

        try {
            $bjob = Job::where(['id' => $request->job_id])->first();
            $booking = new Booking();
            $booking->user_id = $user->id;
            $booking->job_id = $request->job_id;
            $booking->job_date = $bjob->date;
            $booking->status = 1;
            $booking->save();
            $msg = "Job booked successfully";

            $jobData = Job::find($request->job_id);
            $jobData->status =  2;
            $jobData->save();

            //////////////////notify start////////////////

            $job = new Job();
            $msg = "By admin, you have been assigned a new job " . $job->title;
            $notify_msg = "By admin, you have been assigned a new job " . $job->title;

            $this->firebaseServices->sendNotification(['title' => $msg, 'body' => $notify_msg, 'token' => '', 'user_id' => $user->id]);

            //////////////////notify end////////////////



            return redirect()->back()->with(["msg" => $msg, 'msg_type' => 'success']);
        } catch (Exception $e) {
            return redirect()->back()->with(["msg" => $e->getMessage(), 'msg_type' => 'danger']);
        }
    }


    public function action($type, $id)
    {
        if (!in_array($type, ['edit', 'allocate', 'delete', 'status']))
            return redirect()->back()->with(['message' => 'Invalid Action']);

        $user = User::findOrFail($id);

        if ($type == "allocate") {
            $facilities = Facility::orderBy('id', 'desc')->get();
            $positions = Position::orderBy('id', 'desc')->get();
            $jobs = Job::where('status',1)->orderBy('id', 'desc')->get();
            return view('admin.user.job-allocate', compact('user', 'facilities', 'positions', 'jobs'));
        }

        if ($type == "edit") {
            $data['categories'] = Category::orderBy('id', 'desc')->get();
            $data['provided'] = UserProvided::orderBy('id', 'desc')->get();
            $data['vaccines'] = CovidVaccine::orderBy('id', 'desc')->get();
            $data['positions'] = Position::orderBy('id', 'desc')->get();
            $data['facilities'] = Facility::orderBy('id', 'desc')->get();
            $data['user'] = $user;
            $data['user_detail'] = UserDetail::where('user_id', $data['user']->id)->first();
            return view('admin.user.user-form')->with($data);
        }
        if ($type == "delete") {
            if (\File::exists(public_path($user->image))) {
                \File::delete(public_path($user->image));
            }
            $delUserDetail = UserDetail::where('user_id', $id)->delete();
            $delUserDevice = Device::where('user_id', $id)->delete();
            $delUserTimeSheet = TimeSheet::where('user_id', $id)->delete();
            $delUserVerify = UserVerify::where('user_id', $id)->delete();
            $delUserBooking = Booking::where('user_id', $id)->delete();
            $delUserSlot = Slot::where('user_id', $id)->delete();
            $delData = User::where('id', $id)->delete();
            return response()->json(['msg' => 'deleted']);
        }
        if ($type == "status") {
            $user->status = $user->status == 1 ? 0 : 1;
            $user->save();
            return redirect()->back()->with(['message' => 'Status changed successfully.']);
        }
        return abort(404);
    }
}
