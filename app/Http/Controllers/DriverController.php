<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\Notify;
use App\Model\User;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DriverController extends Controller
{
    public $user;
    public $columns;
    // public $phonecode;

    public function __construct()
    {
        $this->user = new User;
        // $this->phonecode = Country::select('phonecode')->where('status', 'AC')->where('phonecode', '!=', '0')->get();
        $this->columns = [
            "select", "user_code", "name", "email", "mobile_number", "status", "activate", "action",
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('drivers.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function driverAjax(Request $request)
    {
        if(isset($request->search['value'])){
            $request->search = $request->search['value'];
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->user->fetchDrivers($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $users = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $users = $records->offset($request->start)->limit(count($total))->get();
        }
        // echo $total;
        $result = [];
        foreach ($users as $user) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $user->id . '"><i class="input-helper"></i></label></div>';
            $data['user_code'] = $user->user_code;
            $data['name'] = $user->first_name . " " . $user->last_name;
            $data['email'] = $user->email;
            $data['mobile_number'] = $user->mobile_number;
            $data['status'] = ucfirst(config('constants.STATUS.' . $user->status));

            if (Helper::checkAccess(route('changeStatusDriver'))) {
                $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->status == 'AC' ? ' checked' : '') . ' data-id="' . $user->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusDriver"></div></div>';
            } else {
                $data['activate'] = ucfirst(config('constants.STATUS.' . $user->status));
            }
            $action = '';

            // if (Helper::checkAccess(route('changeStatusDriver'))) {

            //     if ($user->status == 'AC') {
            //         $action .= '<a href="javascript:void(0)" class="changeStatus toolTip" data-status="' . $user->status . '" data-code="' . $user->first_name . " " . $user->last_name . '" data-id="' . $user->id . '" data-toggle="tooltip" data-placement="bottom" title="Deactivate Driver"><i class="fa fa-lock" aria-hidden="true"></i></a>';
            //     } else {
            //         $action .= '<a href="javascript:void(0)" class="changeStatus toolTip" data-status="' . $user->status . '" data-id="' . $user->id . '" data-code="' . $user->first_name . " " . $user->last_name . '" data-toggle="tooltip" data-placement="bottom" title="Activate Driver"><i class="fa fa-unlock" aria-hidden="true"></i></a>';
            //     }
            // }
            if (Helper::checkAccess(route('editDriver'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editDriver', ['id' => $user->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            }
            if (Helper::checkAccess(route('viewDriver'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewDriver', ['id' => $user->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
            }
            $data['action'] = $action;

            $result[] = $data;
        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);
        // &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteUser" data-code="{{$user->name}}" data-id="{{$user->id}}" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a></td>
        echo $data;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $type = 'add';
        $url = route('addDriver');
        $driver = new User;
        // $phonecode = $this->phonecode;

        return view('drivers.create', compact('type', 'url', 'driver'));
    }

    /**
     * check for unique email during adding staff
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkDriver(Request $request, $id = null)
    {
        if (isset($request->driver_email)) {
            $check = User::where('email', $request->driver_email);
            if (isset($id) && $id != null) {
                $check = $check->where('id', '!=', $id);
            }
            $check = $check->where('status', '!=', 'DL')->count();
            if ($check > 0) {
                return "false";
            } else {
                return "true";
            }

        } else {
            return "true";
        }
    }

    public function checkPhone(Request $request, $id = null)
    {
        if (isset($request->driver_phone)) {
            // $number = '+'.$request->phone_code.'-'.$request->driver_phone;

            $check = User::where('mobile_number', $request->driver_phone);

            if (isset($id) && $id != null) {
                $check = $check->where('id', '!=', $id);
            }
            $check = $check->where('status', '!=', 'DL')->count();
            // dd($check);
            if ($check > 0) {
                return "false";
            } else {
                return "true";
            }

        } else {
            return "true";
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validate = Validator($request->all(), [
            'pass' => 'required',
            'driver_phone' => 'required|digits_between:8,15',
            'driver_email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',

        ]);

        $attr = [
            'pass' => 'Password',
            'driver_phone' => 'Contact Number',
            'driver_email' => 'Email Address',
            'first_name' => 'FirstName',
            'last_name' => 'LastName',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->route('createDriver')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $driver = new User;

                $imageName = '';
                if ($request->file('profile_picture') != null) {
                    $image = $request->file('profile_picture');
                    $imageName = time() . $image->getClientOriginalName();
                    $imageName = str_replace(' ', '', $imageName);
                    $imageName = str_replace('.jpeg', '.jpg', $imageName);
                    $image->move(public_path('uploads/profiles'), $imageName);
                    Helper::compress_image(public_path('uploads/profiles/' . $imageName), Notify::getBusRuleRef('image_quality'));
                    $imageName = str_replace('.jpeg', '.jpg', $imageName);
                }

                $driver->user_code = Helper::generateNumberRole('driver');
                $driver->first_name = $request->post('first_name');
                $driver->last_name = $request->post('last_name');
                $driver->mobile_number = $request->post('driver_phone');
                // $driver->mobile_number = '+' . $request->post('phonecode') . '-' . $request->post('driver_phone');
                $driver->email = $request->post('driver_email');
                $driver->profile_picture = str_replace('.jpeg', '.jpg', $imageName);

                $driver->password = bcrypt($request->pass);
                $driver->is_verified = 1;

                $driver->status = trim($request->post('status'));
                $driver->created_at = date('Y-m-d H:i:s');

                if ($driver->save()) {
                    //assign specified role to user
                    $driver->assignRole('driver');

                    //send mail to newly created user with login credentials
                    $data = [
                        'first_name' => $request->post('first_name'),
                        'last_name' => $request->post('last_name'),
                        'email' => $request->post('driver_email'),
                        'role' => Helper::getRoleName('driver'),
                        'password' => $request->post('pass'),
                    ];
                    Notify::sendMail('emails.new_account_driver', $data, 'Welcome to omrbranch');

                    $request->session()->flash('success', 'Driver added successfully');
                    return redirect()->route('drivers');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('drivers');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('drivers');
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id = null)
    {
        if (isset($id) && $id != null) {
            $driver = User::where('id', $id)->first();
            if (isset($driver->id)) {
                return view('drivers.view', compact('driver'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('drivers');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('drivers');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id = null)
    {
        if (isset($id) && $id != null) {
            $driver = User::where('id', $id)->first();
            if (isset($driver->id)) {
                $type = 'edit';
                // $phonecode = $this->phonecode;
                $url = route('updateDriver', ['id' => $driver->id]);
                return view('drivers.create', compact('driver', 'type', 'url'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('drivers');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('drivers');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        if (isset($id) && $id != null) {
            $driver = User::where('id', $id)->first();
            if (isset($driver->id)) {

                $validate = Validator($request->all(), [
                    // 'role' => 'required',
                    'driver_phone' => 'required|digits_between:8,15',
                    'driver_email' => 'required|email',
                    'first_name' => 'required',
                    'last_name' => 'required',

                ]);

                $attr = [
                    // 'role' => 'Role',
                    'driver_phone' => 'Contact Number',
                    'driver_email' => 'Email Address',
                    'first_name' => 'FirstName',
                    'last_name' => 'LastName',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editDriver', ['id' => $driver->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $imageName = '';
                        if ($request->file('profile_picture') != null) {
                            $image = $request->file('profile_picture');
                            $imageName = time() . $image->getClientOriginalName();
                            if ($driver->profile_picture != null && file_exists(public_path('uploads/profiles/' . $driver->profile_picture))) {
                                unlink(public_path('uploads/profiles/' . $driver->profile_picture));
                            }

                            $imageName = str_replace(' ', '', $imageName);
                            $imageName = str_replace('.jpeg', '.jpg', $imageName);
                            $image->move(public_path('uploads/profiles'), $imageName);
                            Helper::compress_image(public_path('uploads/profiles/' . $imageName), Notify::getBusRuleRef('image_quality'));
                            $driver->image = str_replace('.jpeg', '.jpg', $imageName);
                        }

                        $driver->first_name = $request->post('first_name');
                        $driver->last_name = $request->post('last_name');
                        // $driver->mobile_number = '+' . $request->post('phonecode') . '-' . $request->post('driver_phone');
                        $driver->mobile_number = $request->post('driver_phone');
                        $driver->email = $request->post('driver_email');

                        if (isset($request->pass) && $request->pass != null) {
                            $driver->password = bcrypt($request->pass);
                        }

                        $driver->status = trim($request->post('status'));

                        if ($driver->save()) {
                            // $user->assignRole($request->role);
                            $request->session()->flash('success', 'Driver updated successfully');
                            return redirect()->route('drivers');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('drivers');
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('drivers');
                    }

                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('drivers');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('drivers');
        }

    }

    // activate/deactivate staff
    public function updateStatus(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $driver = User::find($request->statusid);

            if (isset($driver->id)) {
                $driver->status = $request->status;
                if ($driver->save()) {
                    $request->session()->flash('success', 'Driver updated successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to update driver. Please try again later.');
                    return redirect()->back();
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->back();
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->back();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (isset($request->deleteid) && $request->deleteid != null) {
            $driver = User::find($request->deleteid);

            if (isset($driver->id)) {
                $driver->status = 'DL';
                if ($driver->save()) {
                    $request->session()->flash('success', 'Driver deleted successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to delete driver. Please try again later.');
                    return redirect()->back();
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->back();
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->back();
        }
    }
    /**
     * Remove multiple resource from storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function bulkdelete(Request $request)
    {

        if (isset($request->ids) && $request->ids != null) {
            $ids = count($request->ids);
            $count = 0;
            foreach ($request->ids as $id) {
                $driver = User::find($id);

                if (isset($driver->id)) {
                    $driver->status = 'DL';
                    if ($driver->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Driver deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all driver were deleted. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }
    /**
     * activate/deactivate multiple resource from storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function bulkchangeStatus(Request $request)
    {

        if (isset($request->ids) && $request->ids != null) {
            $ids = count($request->ids);
            $count = 0;
            foreach ($request->ids as $id) {
                $driver = User::find($id);

                if (isset($driver->id)) {
                    if ($driver->status == 'AC') {
                        $driver->status = 'IN';
                    } elseif ($driver->status == 'IN') {
                        $driver->status = 'AC';
                    }

                    if ($driver->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Driver updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all driver were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }

}
