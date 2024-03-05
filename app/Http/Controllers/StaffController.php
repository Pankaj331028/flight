<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\Notify;
use App\Model\Country;
use App\Model\Role;
use App\Model\User;
use App\Model\UserRole;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StaffController extends Controller
{
    public $user;
    public $role;
    public $phonecode;
    public $userrole;
    public $columns;

    public function __construct()
    {
        $this->user = new User;
        $this->role = Role::where('role', '!=', 'admin')->select('role', 'name', 'id')->get();
        $this->userrole = new UserRole;
        $this->phonecode = Country::select('phonecode')->where('status', 'AC')->where('phonecode', '!=', '0')->get();
        $this->columns = [
            "select", "user_code", "role", "name", "email", "mobile_number", "status", "activate", "action",
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = $this->role;
        return view('staffs.index', compact('roles'));
    }

    public function updateStatusAjax(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $role = User::find($request->statusid);

            if (isset($role->id)) {
                $role->status = $request->status;
                if ($role->save()) {
                    echo json_encode(['status' => 1, 'message' => 'Staff updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update role. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid Role']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Role']);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function staffAjax(Request $request)
    {
        if(isset($request->search['value'])){
            $request->search = $request->search['value'];
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->user->fetchSubadmins($request, $this->columns);
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
            $data['role'] = $user->user_role[0]->name;
            $data['name'] = $user->first_name . " " . $user->last_name;
            $data['email'] = $user->email;
            $data['mobile_number'] = $user->mobile_number;
            $data['status'] = ucfirst(config('constants.STATUS.' . $user->status));

            if (Helper::checkAccess(route('changeStatusStaff'))) {
                $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($user->status == 'AC' ? ' checked' : '') . ' data-id="' . $user->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusStaff"></div></div>';
            } else {
                $data['activate'] = ucfirst(config('constants.STATUS.' . $user->status));
            }
            $action = '';

            // if (Helper::checkAccess(route('changeStatusStaff'))) {

            //     if ($user->status == 'AC') {
            //         $action .= '<a href="javascript:void(0)" class="changeStatus toolTip" data-status="' . $user->status . '" data-code="' . $user->first_name . " " . $user->last_name . '" data-id="' . $user->id . '" data-toggle="tooltip" data-placement="bottom" title="Deactivate Staff"><i class="fa fa-lock" aria-hidden="true"></i></a>';
            //     } else {
            //         $action .= '<a href="javascript:void(0)" class="changeStatus toolTip" data-status="' . $user->status . '" data-id="' . $user->id . '" data-code="' . $user->first_name . " " . $user->last_name . '" data-toggle="tooltip" data-placement="bottom" title="Activate Staff"><i class="fa fa-unlock" aria-hidden="true"></i></a>';
            //     }
            // }
            if (Helper::checkAccess(route('editStaff'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editStaff', ['id' => $user->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            }
            if (Helper::checkAccess(route('viewStaff'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewStaff', ['id' => $user->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
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
        $url = route('addStaff');
        $staff = new User;
        $roles = $this->role;
        $phonecode = $this->phonecode;

        return view('staffs.create', compact('type', 'url', 'staff', 'roles', 'phonecode'));
    }

    /**
     * check for unique email during adding staff
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkStaff(Request $request, $id = null)
    {
        if (isset($request->staff_email)) {
            $check = User::where('email', $request->staff_email);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validate = Validator($request->all(), [
            'role' => 'required',
            'pass' => 'required',
            'mobile_number' => 'required|digits_between:8,15',
            'staff_email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',

        ]);

        $attr = [
            'role' => 'Role',
            'pass' => 'Password',
            'mobile_number' => 'Contact Number',
            'staff_email' => 'Email Address',
            'first_name' => 'FirstName',
            'last_name' => 'LastName',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->route('createStaff')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $staff = new User;

                $imageName = '';
                $staff->user_code = Helper::generateNumberRole($request->role);
                $staff->first_name = $request->post('first_name');
                $staff->last_name = $request->post('last_name');
                $staff->mobile_number = '+' . $request->post('phonecode') . '-' . $request->post('mobile_number');
                $staff->email = $request->post('staff_email');
                $staff->profile_picture = $imageName;
                $staff->password = bcrypt($request->pass);

                $staff->status = trim($request->post('status'));
                $staff->created_at = date('Y-m-d H:i:s');

                if ($staff->save()) {
                    //assign specified role to user
                    $staff->assignRole($request->role);

                    //send mail to newly created user with login credentials
                    $data = [
                        'first_name' => $request->post('first_name'),
                        'last_name' => $request->post('last_name'),
                        'email' => $request->post('staff_email'),
                        'role' => Helper::getRoleName($request->role),
                        'password' => $request->post('pass'),
                    ];
                    $sender_id = Config::get('constants.MAIL_SENDER_ID');
                    Notify::sendMail('emails.new_account', $data, $staff, $sender_id, 'Welcome to omrbranch');

                    $request->session()->flash('success', 'Staff added successfully');
                    return redirect()->route('staffs');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('staffs');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('staffs');
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
            $staff = User::where('id', $id)->first();
            if (isset($staff->id)) {
                return view('staffs.view', compact('staff'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('staffs');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('staffs');
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
            $staff = User::where('id', $id)->first();
            if (isset($staff->id)) {
                $roles = $this->role;
                $type = 'edit';
                $phonecode = $this->phonecode;
                $url = route('updateStaff', ['id' => $staff->id]);
                return view('staffs.create', compact('staff', 'type', 'url', 'roles', 'phonecode'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('staffs');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('staffs');
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
            $staff = User::where('id', $id)->first();
            if (isset($staff->id)) {

                $validate = Validator($request->all(), [
                    // 'role' => 'required',
                    'mobile_number' => 'required|digits_between:8,15',
                    'staff_email' => 'required|email',
                    'first_name' => 'required',
                    'last_name' => 'required',

                ]);

                $attr = [
                    // 'role' => 'Role',
                    'mobile_number' => 'Contact Number',
                    'staff_email' => 'Email Address',
                    'first_name' => 'FirstName',
                    'last_name' => 'LastName',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editStaff', ['id' => $staff->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {

                        $imageName = '';
                        $staff->first_name = $request->post('first_name');
                        $staff->last_name = $request->post('last_name');
                        $staff->mobile_number = '+' . $request->post('phonecode') . '-' . $request->post('mobile_number');
                        $staff->email = $request->post('staff_email');
                        $staff->profile_picture = $imageName;
                        if (isset($request->pass) && $request->pass != null) {
                            $staff->password = bcrypt($request->pass);
                        }

                        $staff->status = trim($request->post('status'));

                        if ($staff->save()) {
                            // $user->assignRole($request->role);
                            $request->session()->flash('success', 'Staff updated successfully');
                            return redirect()->route('staffs');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('staffs');
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('staffs');
                    }

                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('staffs');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('staffs');
        }

    }

    // activate/deactivate staff
    public function updateStatus(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $staff = User::find($request->statusid);

            if (isset($staff->id)) {
                $staff->status = $request->status;
                if ($staff->save()) {
                    $request->session()->flash('success', 'Staff updated successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to update staff. Please try again later.');
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
            $staff = User::find($request->deleteid);

            if (isset($staff->id)) {
                $staff->status = 'DL';
                if ($staff->save()) {
                    $request->session()->flash('success', 'Staff deleted successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to delete staff. Please try again later.');
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
                $staff = User::find($id);

                if (isset($staff->id)) {
                    $staff->status = 'DL';
                    if ($staff->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Staff deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all staff were deleted. Please try again later.']);
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
                $staff = User::find($id);

                if (isset($staff->id)) {
                    if ($staff->status == 'AC') {
                        $staff->status = 'IN';
                    } elseif ($staff->status == 'IN') {
                        $staff->status = 'AC';
                    }

                    if ($staff->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Staff updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all staff were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }

}
