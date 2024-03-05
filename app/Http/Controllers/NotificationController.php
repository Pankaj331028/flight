<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\Notify;
use App\Model\Notification;
use App\Model\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotificationController extends Controller
{
    public $notification;
    public $columns;

    public function __construct()
    {
        $this->notification = new Notification;
        $this->columns = [
            "sno", "user_id", "parent_id", "title", "description", "notification_type", "status", "created_at",
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $types = Notification::selectRaw('distinct notification_type')->pluck('notification_type');
        return view('notifications.index', compact('types'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function notificationsAjax(Request $request)
    {
        if(isset($request->search['value']))
        {
            $request->search = $request->search['value'];
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->notification->fetchNotifications($request, $this->columns);
        $total = $records->get();
        $notifications = $records->offset($request->start)->limit($request->length)->get();
        // echo $total;
        $result = [];
        $i = $request->start + 1;
        foreach ($notifications as $notification) {
            $data = [];
            $data['sno'] = $i++;
            $data['user_id'] = ucfirst($notification->sender->first_name . " " . $notification->sender->last_name);
            $data['parent_id'] = ucfirst($notification->receiver->first_name . " " . $notification->receiver->last_name);
            $data['title'] = $notification->title;
            $data['description'] = $notification->description;
            $data['notification_type'] = ucfirst($notification->notification_type);
            $data['created_at'] = date('d M,Y', strtotime($notification->created_at));
            $data['status'] = ucfirst(config('constants.STATUS.' . $notification->status));

            $result[] = $data;
        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);
        echo $data;

    }

    public function create(Request $request)
    {
        $type = 'add';
        $url = route('storeNotification');
        return view('notifications.create', compact('type', 'url'));
    }

    public function store(Request $request)
    {
        $validate = Validator($request->all(), [
            'noti_title' => 'required',
            'noti_description' => 'required',
            'noti_image' => 'nullable|dimensions:max_width=100,max_height=100',

        ], [
            'noti_image.dimensions' => 'Please upload an image with maximum 100 x 100 pixels dimension',
        ]);

        $attr = [
            'noti_title' => 'Title',
            'noti_description' => 'Notification',
            'noti_image' => 'Image',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->route('createNotification')->withInput($request->all())->withErrors($validate);
        } else {
            $tokensList = array();
            $userIdList = array();
            $users = User::select('device_token', 'id')->where(['status' => 'AC'])->whereNotNull('device_token')->get();
            $count = 0;
            // dd($request->all());
            $imageName = '';
            if ($request->file('noti_image') != null) {
                $image = $request->file('noti_image');
                $imageName = time() . $image->getClientOriginalName();
                $imageName = str_replace('.jpeg', '.jpg', $imageName);
                $image->move(public_path('uploads/notifications'), $imageName);
                Helper::compress_image(public_path('uploads/notifications/' . $imageName), Notify::getBusRuleRef('image_quality'));
            }
            foreach ($users as $item) {
                array_push($tokensList, $item->device_token);
                array_push($userIdList, $item->id);
                $notification = new Notification();

                $notification->image = $imageName;
                $notification->is_read = '0';
                $notification->title = $request->noti_title;
                $notification->description = $request->noti_description;
                $notification->notification_type = "custom";
                $notification->receiver_id = $item->id;
                $notification->sender_id = Auth::guard('admin')->user()->id;

                if ($notification->save()) {
                    $count++;
                }
            }

            if ($count == count($users)) {

                Notify::sendBulkNotifications(array_filter($tokensList), $notification);
                $request->session()->flash('success', 'Notifications Sent');
                return redirect()->route("notifications");
            } else {
                $request->session()->flash('danger', 'Something goes wrong');
                return redirect()->route("notifications");
            }
        }
    }
}
