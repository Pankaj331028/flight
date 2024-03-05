@extends('layouts.app')
@section('title', ucfirst($type).' Role')

@section('content')
    <div class="content-wrapper">

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('index')}}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('roles')}}">Roles</a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Role</li>
                </ol>
            </div>
        </div>
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('layouts.message')
                    <div class="card-body">

                        @if($type == 'add')
                            <h4>Add Role</h4>
                        @elseif($type == 'edit')
                            <h4>Edit Role</h4>
                        @endif
                        <hr>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="form-material m-t-50 row form-valide" method="post" action="{{$url}}" enctype="multipart/form-data">

                            {{csrf_field()}}
                            <div class="form-group col-md-6 m-t-20">
                                <label>Name</label><sup class="text-reddit"> *</sup>
                                <input type="text" class="form-control form-control-line" name="role_name" value="{{old('name', $role->name)}}" maxlength="100">
                            </div>

                            <div class="form-group col-md-12">
                                @foreach($modules as $module)
                                    <div class="roleSelect" id="role{{$module->id}}">
                                        <div class="form-check form-check-flat">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" name="module[]" value="{{$module->id}}" @if(in_array($module->id, $role_module)) checked="" @endif>
                                                {{$module->name}}
                                            </label>
                                        </div>
                                        <div class="form-group borderBox m-t-20" id="operation{{$module->id}}" @if($type=='edit') @if(!in_array($module->id, $role_module)) style="display: none" @endif @else style="display: none;" @endif>
                                            @php
                                                $count = 0;
                                                foreach($module->module_action as $op){
                                                    if(isset($role_operations[$module->id]) && in_array(config()->get('constants.OPERATIONS.'.$actions[$op->action_id]), $role_operations[$module->id])){
                                                        $count++;
                                                    }
                                                }
                                            @endphp
                                            @if(count($module->module_action)>1)
                                                <div class="form-check form-check-flat col-md-12 m-auto">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" name="select_all{{$module->id}}" @if($count == count($module->module_action)) checked="" @endif>
                                                        Select All
                                                    </label>
                                                </div>
                                            @endif
                                            @foreach($module->module_action as $op)

                                                <div class="form-check form-check-flat col-md-3 float-left">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" name="permission[]" value="{{$op->id}}" @if(isset($role_operations[$module->id]) && in_array(config()->get('constants.OPERATIONS.'.$actions[$op->action_id]), $role_operations[$module->id])) checked="" @endif>
                                                        {{config()->get('constants.OPERATIONS.'.$actions[$op->action_id])}}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <input type="hidden" name="status" value="@if(isset($role) && $role->status != null) {{$role->status}} @else AC @endif">
                            <div class="form-group bt-switch col-md-6 m-t-20">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-3" style="float: right;">
                                    <input type="checkbox" @if($type == 'edit') @if(isset($role) && $role->status == 'AC') checked @endif @else checked @endif data-on-color="success" data-off-color="info" data-on-text="Active" data-off-text="Inactive" data-size="mini" name="val-status" id="statusRole">
                                </div>
                            </div>
                            <div class="col-12 m-t-20">
                                <button type="submit" class="btn btn-success submitBtn m-r-10">Save</button>
                                <a href="{{route('roles')}}" class="btn btn-inverse waves-effect waves-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End PAge Content -->
    </div>
@endsection

@push('scripts')
<script src="{{URL::asset('/js/jquery-mask-as-number.js')}}"></script>
    <script type="text/javascript">
        $(function(){
            $('#statusRole').on('switchChange.bootstrapSwitch', function (event, state) {
                var x = $(this).data('on-text');
                var y = $(this).data('off-text');
                if($("#statusRole").is(':checked'))
                    $('input[name=status]').val('AC');
                else
                    $('input[name=status]').val('IN');
            });

            @if($type == 'edit')
                $('input[name=role_name]').rules('add', {remote: APP_NAME + "/admin/roles/checkRole/{{$role->id}}"});
            @endif

            var checkbox = $('div[id^=operation]');
            for(var i = 0; i < checkbox.length; i++){
                $(checkbox[i]).find('input[name="permission[]"]').first().prop('disabled', true);
            }

            $('input[name^=select_all]').click(function(){
                $(this).closest('div[id^=operation]').find('input[name="permission[]"]').prop('checked', $(this).prop('checked'));
                $(this).closest('div[id^=operation]').find('input[name="permission[]"]').first().prop('checked', true);
            })

            $(document).on('click', 'input[name="module[]"]', function(){
                var id = $(this).val();
                $('#operation'+id).find('input[name="permission[]"]').first().prop('checked', $(this).prop('checked')).prop('disabled', true);

                if($(this).prop('checked'))
                {
                    ($('#operation'+id).find('input[name="permission[]"]').length == 1) ? $('#operation'+id).find('input[type=checkbox]').prop('checked', true):'';
                    $('#operation'+id).show(200);
                    $('input[name^=permission]').rules('add', {required: !0, messages:{required:"Please select atleast one operation for the selected module."}});
                }
                else{
                    $('#operation'+id).find('input[name="permission[]"]').prop('checked', false);
                    $('#operation'+id).hide(200);
                    $('input[name^=permission]').rules('remove', 'required');
                }
            })

            $(document).on('click', 'input[name="permission[]"]', function(){
                var id = $(this).closest('div[id^=operation]').attr('id').split('operation')[1];
                var count = $('#operation'+id).find('input[name="permission[]"]').length;
                var checkedCount = $('#operation'+id).find('input[name="permission[]"]:checked').length;

                if(checkedCount == count){
                    $('#operation'+id).find('input[name^=select_all]').prop('checked', true);
                }else{
                    $('#operation'+id).find('input[name^=select_all]').prop('checked', false);
                }
            })

        });
    </script>
@endpush