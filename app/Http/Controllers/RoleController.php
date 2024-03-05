<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Model\Action;
use App\Model\Module;
use App\Model\Permission;
use App\Model\Role;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    public $role;
    public $module;
    public $permission;
    public $columns;

    public function __construct()
    {
        $this->role = new Role;
        $this->permission = new Permission;
        $this->module = Module::with(['module_action' => function ($q) {
            $q->where('g_module_actions.status', 'AC');
        }])->where('status', 'AC')->get();
        $this->columns = [
            "select", "role", "name", "status", "activate", "action",
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        return view('roles.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function roleAjax(Request $request)
    {
        if(isset($request->search['value'])){
            $request->search = $request->search['value'];
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->role->fetchRoles($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $roles = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $roles = $records->offset($request->start)->limit(count($total))->get();
        }
        $result = [];
        foreach ($roles as $role) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $role->id . '"><i class="input-helper"></i></label></div>';
            $data['role'] = $role->role;
            $data['name'] = $role->name;
            $data['status'] = ucfirst(config('constants.STATUS.' . $role->status));

            if (Helper::checkAccess(route('changeStatusRole'))) {
                $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($role->status == 'AC' ? ' checked' : '') . ' data-id="' . $role->id . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusRole"></div></div>';
            } else {
                $data['activate'] = ucfirst(config('constants.STATUS.' . $role->status));
            }
            $action = '';
            if (Helper::checkAccess(route('editRole'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editRole', ['id' => $role->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            }
            if (Helper::checkAccess(route('viewRole'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewRole', ['id' => $role->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
            }
            $data['action'] = $action;

            $result[] = $data;
        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);
        // &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteRole" data-code="' . $role->name . '" data-id="' . $role->id . '" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a></td>
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
        $url = route('addRole');
        $role = new Role;
        $modules = $this->module;
        $actions = Action::where('status', 'AC')->pluck('name', 'id');
        $role_module = [];
        $role_operations = [];

        return view('roles.create', compact('type', 'url', 'role', 'modules', 'role_module', 'role_operations', 'actions'));
    }

    /**
     * check for unique name during adding role
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkRole(Request $request, $id = null)
    {
        if (isset($request->role_name)) {
            $check = Role::where('name', $request->role_name);
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
            'role_name' => 'required',
            'module.*' => 'required',
            'permission.*' => 'required',

        ]);

        $attr = [
            'role_name' => 'Name',
            'module.*' => 'Module',
            'permission.*' => 'Operation',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->route('createRole')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $role = new Role;

                $role->role = strtolower(str_replace(' ', '_', $request->role_name));
                $role->name = ucfirst($request->role_name);
                $role->status = $request->status;
                $role->created_at = date('Y-m-d H:i:s');
                $role->updated_at = date('Y-m-d H:i:s');

                if ($role->save()) {
                    //add module permissions to this role
                    $role->assignModule($request->module, $request->permission, $role->id);

                    $request->session()->flash('success', 'Role added successfully');
                    return redirect()->route('roles');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('roles');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('roles');
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
            $role = Role::with(['permission' => function ($q) {
                $q->where('status', 'AC');
            }])->where('id', $id)->first();
            // dd($role);

            if (isset($role->id)) {
                $modules = Module::pluck('name', 'id');
                $actions = Action::pluck('name', 'id');

                $role_module = [];
                $role_operations = [];

                foreach ($role->permission as $value) {
                    if (!in_array($value->module_action->module_id, $role_module)) {
                        array_push($role_module, $value->module_action->module_id);
                        $role_operations[$value->module_action->module_id] = [];
                    }

                    array_push($role_operations[$value->module_action->module_id], Config::get('constants.OPERATIONS.' . $value->module_action->action->name));

                }

                return view('roles.view', compact('role', 'modules', 'role_operations', 'role_module'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('roles');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('roles');
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
            $role = Role::with(['permission' => function ($q) {
                $q->where('status', 'AC');
            }])->where('id', $id)->first();

            if (isset($role->id)) {
                $type = 'edit';
                $url = route('updateRole', ['id' => $role->id]);
                $modules = $this->module;
                $actions = Action::pluck('name', 'id');

                $role_module = [];
                $role_operations = [];

                foreach ($role->permission as $value) {
                    if (!in_array($value->module_action->module_id, $role_module)) {
                        array_push($role_module, $value->module_action->module_id);
                        $role_operations[$value->module_action->module_id] = [];
                    }

                    array_push($role_operations[$value->module_action->module_id], Config::get('constants.OPERATIONS.' . $value->module_action->action->name));

                }
                return view('roles.create', compact('role', 'type', 'url', 'modules', 'actions', 'role_module', 'role_operations'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('roles');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('roles');
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
            $role = Role::where('id', $id)->first();
            if (isset($role->id)) {
                $validate = Validator($request->all(), [
                    'role_name' => 'required',
                    'module.*' => 'required',
                    'permission.*' => 'required',

                ]);

                $attr = [
                    'role_name' => 'Name',
                    'module.*' => 'Module',
                    'permission*' => 'Operation',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editRole', ['id' => $role->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $role->role = strtolower(str_replace(' ', '_', $request->role_name));
                        $role->name = ucfirst($request->role_name);
                        $role->status = $request->status;
                        $role->created_at = date('Y-m-d H:i:s');
                        $role->updated_at = date('Y-m-d H:i:s');

                        if ($role->save()) {
                            //add module permissions to this role
                            $role->assignModule($request->module, $request->permission, $role->id);

                            $request->session()->flash('success', 'Role updated successfully');
                            return redirect()->route('roles');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('roles');
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('roles');
                    }

                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('roles');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('roles');
        }

    }

    // activate/deactivate role
    public function updateStatus(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $role = Role::find($request->statusid);

            if (isset($role->id)) {
                $role->status = $request->status;
                if ($role->save()) {
                    $request->session()->flash('success', 'Role updated successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to update role. Please try again later.');
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

    // activate/deactivate role
    public function updateStatusAjax(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $role = Role::find($request->statusid);

            if (isset($role->id)) {
                $role->status = $request->status;
                if ($role->save()) {
                    echo json_encode(['status' => 1, 'message' => 'Role updated successfully.']);
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
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (isset($request->deleteid) && $request->deleteid != null) {
            $role = Role::find($request->deleteid);

            if (isset($role->id)) {
                $role->status = 'DL';
                if ($role->save()) {
                    $request->session()->flash('success', 'User deleted successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to delete role. Please try again later.');
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
                $role = Role::find($id);

                if (isset($role->id)) {
                    $role->status = 'DL';
                    if ($role->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Roles deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all roles were deleted. Please try again later.']);
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
                $role = Role::find($id);

                if (isset($role->id)) {
                    if ($role->status == 'AC') {
                        $role->status = 'IN';

                    } elseif ($role->status == 'IN') {
                        $role->status = 'AC';
                    }

                    if ($role->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Roles updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all roles were updated. Plese try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }

}
