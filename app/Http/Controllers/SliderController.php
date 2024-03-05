<?php

namespace App\Http\Controllers;

use App\Library\Helper;
use App\Library\Notify;
use App\Model\BusRuleRef;
use App\Model\Category;
use App\Model\Slider;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use URL;

class SliderController extends Controller
{
    public $slider;
    public $columns;
    public $restrict;
    public $category;

    public function __construct()
    {
        $this->slider = new Slider;
        $this->restrict = BusRuleRef::where('rule_name', 'category_restrict')->first()->rule_value;
        $this->category = new Category;
        $this->columns = [
            "select", "image", "category", "type", "status", "activate", "action",
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::where('status', '!=', 'DL')->get();
        return view('sliders.index', compact('categories'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function slidersAjax(Request $request)
    {
        if(isset($request->search['value'])){
            $request->search = $request->search['value'];
        }
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->slider->fetchSliders($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $sliders = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $sliders = $records->offset($request->start)->limit(count($total))->get();
        }
        // echo $total;
        $result = [];
        $no = 1;
        foreach ($sliders as $slider) {
            $data = [];
            $data['select'] = '<div class="form-check form-check-flat"><label class="form-check-label"><input type="checkbox" class="form-check-input" name="user_id[]" value="' . $slider->id . '"><i class="input-helper"></i></label></div>';
            $data['sno'] = $no++;
            $data['image'] = ($slider->image != null) ? '<img class="sliderImg" src="' . URL::asset('/uploads/sliders/' . $slider->image) . '">' : '-';
            $data['status'] = ucfirst(config('constants.STATUS.' . $slider->status));
            $data['category'] = ($slider->category_id != null) ? $slider->category->name : '-';
            $data['type'] = ucfirst(config('constants.SLIDER_TYPE.' . $slider->type));

            if (Helper::checkAccess(route('changeStatusSlider'))) {
                $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($slider->status == 'AC' ? ' checked' : '') . ' data-id="' . $slider->id . '" data-type="' . $slider->type . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusSlider"></div></div>';
            } else {
                $data['activate'] = ucfirst(config('constants.STATUS.' . $slider->status));
            }

            // $data['activate'] = '<div class="bt-switch"><div class="col-md-2"><input type="checkbox"' . ($slider->status == 'AC' ? ' checked' : '') . ' data-id="' . $slider->id . '" data-type="' . $slider->type . '" data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="cstatus" class="statusSlider"></div></div>';

            $action = '';
            /*
            if (Helper::checkAccess(route('changeStatusSlider'))) {
            if ($slider->status == 'AC') {
            $action .= '<a href="javascript:void(0)" class="changeStatus toolTip" data-status="' . $slider->status . '" data-code="' . $slider->name . '" data-id="' . $slider->id . '" data-toggle="tooltip" data-placement="bottom" title="Deactivate Slider"><i class="fa fa-lock" aria-hidden="true"></i></a>';
            } else {
            $action .= '<a href="javascript:void(0)" class="changeStatus toolTip" data-status="' . $slider->status . '" data-id="' . $slider->id . '" data-code="' . $slider->name . '" data-toggle="tooltip" data-placement="bottom" title="Activate Slider"><i class="fa fa-unlock" aria-hidden="true"></i></a>';
            }
             */
            if (Helper::checkAccess(route('editSlider'))) {
                $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('editSlider', ['id' => $slider->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            }
            /*if (Helper::checkAccess(route('viewSlider'))) {
            $action .= '&nbsp;&nbsp;&nbsp;<a href="' . route('viewSlider', ['id' => $slider->id]) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
            }*/
            if (Helper::checkAccess(route('deleteSlider'))) {
                $action .= ' &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" class="toolTip deleteSlide" data-id="' . $slider->id . '" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
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

    //recursive function to create array of categories and subcategories together using nested loop along with indented serial number.
    public function setList($root, $categories, $i, $level)
    {
        $child = $root->childCatList;
        $j = 1;
        if (($this->restrict == null) || ($this->restrict != null && $level <= $this->restrict)) {
            foreach ($child as $ch) {
                if ($ch->status != 'DL') {
                    //ordering for each subcategory along with nesting.
                    $k = "&nbsp;&nbsp;" . $i . "." . $j;
                    $categories[$ch->id] = $k . '. ' . $ch->name;

                    //calling function recursively to loop through subcategories of each category.
                    $categories = $this->setList($ch, $categories, $k, ++$level);
                    $level = 2;
                    $j++;
                }
            }
            //once all subcategories are done looping, move to next main subcategory.
            $root = $child;
        }
        return $categories;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $type = 'add';
        $url = route('addSlider');
        $slider = new Slider;
        $list = $this->category->fetchCategories()->get();

        $categories = [];
        $i = 1;
        $level = 1;
        foreach ($list as $key => $value) {
            $categories[$value->id] = $i . ". " . $value->name;
            $root = $value;
            $categories = $this->setList($root, $categories, $i, $level);
            $i++;
        }
        return view('sliders.create', compact('type', 'url', 'slider', 'categories'));
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
            'slider_image' => 'required|mimes:jpeg,png,jpg,gif,svg',
            'type' => 'required',

        ]);

        $attr = [
            'slider_image' => 'Slide Image',
            'type' => 'Type',
        ];

        $validate->setAttributeNames($attr);

        if ($validate->fails()) {
            return redirect()->route('createSlider')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $slider = new Slider;

                $imageName = '';
                if ($request->file('slider_image') != null) {
                    $image = $request->file('slider_image');
                    $imageName = time() . $image->getClientOriginalName();
                    $imageName = str_replace(' ', '', $imageName);
                    $imageName = str_replace('.jpeg', '.jpg', $imageName);
                    $image->move(public_path('uploads/sliders'), $imageName);
                    Helper::compress_image(public_path('uploads/sliders/' . $imageName), Notify::getBusRuleRef('image_quality'));
                }

                $slider->category_id = $request->post('parent_id') != null ? $request->post('parent_id') : null;
                $slider->image = str_replace('.jpeg', '.jpg', $imageName);
                $slider->status = trim($request->post('status'));
                $slider->type = trim($request->post('type'));
                $slider->created_at = date('Y-m-d H:i:s');

                if ($slider->save()) {

                    if ($request->banner_exists > 0) {
                        $exists = Slider::find($request->banner_exists);
                        $exists->status = 'IN';
                        $exists->save();
                    }

                    $request->session()->flash('success', 'Slider added successfully');
                    return redirect()->route('sliders');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('sliders');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('sliders');
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
            $slider = Slider::where('id', $id)->first();
            if (isset($slider->id)) {
                return view('sliders.view', compact('slider'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('sliders');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('sliders');
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
            $slider = Slider::where('id', $id)->first();
            if (isset($slider->id)) {
                $type = 'edit';
                $url = route('updateSlider', ['id' => $slider->id]);

                $list = $this->category->fetchCategories()->get();

                $categories = [];
                $i = 1;
                $level = 1;
                foreach ($list as $key => $value) {
                    $categories[$value->id] = $i . ". " . $value->name;
                    $root = $value;
                    $categories = $this->setList($root, $categories, $i, $level);
                    $i++;
                }

                return view('sliders.create', compact('slider', 'type', 'url', 'categories'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('sliders');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('sliders');
        }
    }

    /**
     * check unique banner in database
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkBanner(Request $request)
    {
        if (isset($request->id) && $request->id != null) {
            $slider = Slider::where('type', 'banner')->where('status', 'AC');

            if ($request->id > 0) {
                $slider = $slider->where('id', '!=', $request->id);
            }
            $slider = $slider->where('status', '!=', 'DL')->first();
            if (isset($slider->id)) {
                echo json_encode(['image' => $slider->image, 'id' => $slider->id]);
            } else {
                echo 'no';
            }
        } else {
            echo 'no';
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
            $slider = Slider::where('id', $id)->first();
            if (isset($slider->id)) {
                $validate = Validator($request->all(), [
                    'slider_image' => 'mimes:jpeg,png,jpg,gif,svg',
                    'type' => 'required',
                ]);

                $attr = [
                    'slider_image' => 'Slider Image',
                    'type' => 'Type',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editSlider', ['id' => $slider->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {

                        $imageName = '';
                        if ($request->file('slider_image') != null) {
                            $image = $request->file('slider_image');
                            $imageName = time() . $image->getClientOriginalName();
                            $imageName = str_replace(' ', '', $imageName);
                            $imageName = str_replace('.jpeg', '.jpg', $imageName);
                            $image->move(public_path('uploads/sliders'), $imageName);
                            if ($slider->image != null && file_exists(public_path('uploads/sliders/' . $slider->image))) {
                                unlink(public_path('uploads/sliders/' . $slider->image));
                            }
                            Helper::compress_image(public_path('uploads/sliders/' . $imageName), Notify::getBusRuleRef('image_quality'));
                            $slider->image = str_replace('.jpeg', '.jpg', $imageName);
                        }

                        $slider->category_id = $request->post('parent_id') != null ? $request->post('parent_id') : null;
                        $slider->status = trim($request->post('status'));
                        $slider->type = trim($request->post('type'));
                        if ($slider->save()) {
                            if ($request->banner_exists > 0) {
                                $exists = Slider::find($request->banner_exists);
                                $exists->status = 'IN';
                                $exists->save();
                            }
                            $request->session()->flash('success', 'Slider updated successfully');
                            return redirect()->route('sliders');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('sliders');
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('sliders');
                    }

                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('sliders');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('sliders');
        }

    }

    // activate/deactivate slider
    public function updateStatus(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $slider = Slider::find($request->statusid);

            if (isset($slider->id)) {
                $slider->status = $request->status;
                if ($slider->save()) {
                    $request->session()->flash('success', 'Slider updated successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to update slider. Please try again later.');
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

    // activate/deactivate slider
    public function updateStatusAjax(Request $request)
    {

        if (isset($request->statusid) && $request->statusid != null) {
            $slider = Slider::find($request->statusid);

            if (isset($slider->id)) {
                $slider->status = $request->status;
                if ($slider->save()) {
                    if (isset($request->newid)) {
                        $new = Slider::find($request->newid);
                        $new->status = 'IN';
                        $new->save();
                    }
                    echo json_encode(['status' => 1, 'message' => 'Slide updated successfully.']);
                } else {
                    echo json_encode(['status' => 0, 'message' => 'Unable to update slide. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 0, 'message' => 'Invalid Slider']);
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid Slider']);
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
            $slider = Slider::find($request->deleteid);

            if (isset($slider->id)) {
                $slider->status = 'DL';
                if ($slider->save()) {
                    $request->session()->flash('success', 'Slider deleted successfully.');
                    return redirect()->back();
                } else {
                    $request->session()->flash('error', 'Unable to delete slider. Please try again later.');
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
                $slider = Slider::find($id);

                if (isset($slider->id)) {
                    $slider->status = 'DL';
                    if ($slider->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Sliders deleted successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all sliders were deleted. Please try again later.']);
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
                $slider = Slider::find($id);

                if (isset($slider->id)) {
                    if ($slider->status == 'AC') {
                        $slider->status = 'IN';
                    } elseif ($slider->status == 'IN') {
                        if ($slider->type == 'banner') {
                            Slider::where(['type' => 'banner'])->update(['status' => 'IN']);
                        }
                        $slider->status = 'AC';
                    }

                    if ($slider->save()) {
                        $count++;
                    }
                }
            }
            if ($count == $ids) {
                echo json_encode(["status" => 1, 'ids' => json_encode($request->ids), 'message' => 'Sliders updated successfully.']);
            } else {
                echo json_encode(["status" => 0, 'message' => 'Not all sliders were updated. Please try again later.']);
            }
        } else {
            echo json_encode(["status" => 0, 'message' => 'Invalid Data']);
        }
    }
}
