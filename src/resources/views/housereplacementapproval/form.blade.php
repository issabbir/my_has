<form id="house-form" method="POST" action="
        @if($data['houseReplacementApplication']->approved_yn != 'Y')
            {{ route('house-replacement-approval.update', ['id' => $data['houseReplacementApplication']->replace_app_id]) }}
        @else
            {{ route('house-replacement-approval.un-assign', ['id' => $data['houseReplacementApplication']->replace_app_id]) }}
        @endif
        ">
    <input name="_method" type="hidden" value="PUT">
    {{ csrf_field() }}
    <div class="row justify-content-center mt-2">
        <div class="col-md-12">
            <div class="row mt-1">
                <div class="col-4">
                    <label class="required" for="application_date">Application Date</label>&nbsp
                    <input type="text" value="{{ date("d-m-Y", strtotime($data['houseReplacementApplication']->replace_app_date)) }}" class="form-control" disabled id="application_date" name="application_date" />
                </div>
                <div class="col-md-4">
                    <label class="required">Employee Code</label>
                    <input type="text" placeholder="Employee Code"
                           name="employee_code" class="form-control" required autocomplete="off" disabled
                           id="employee_code" value="{{$data['employeeInformation']['emp_code']}}">
                    <span class="text-danger">{{ $errors->first('employee_code') }}</span>
                </div>
                <!--House Name-->
                <div class="col-md-4">
                    <label>Residential Area</label>
                    <input type="text" placeholder="Residential Area"class="form-control" disabled id="employee_residential_area" value="{{$data['employeeInformation']['residential_area']}}" />
                </div>
                <div class="col-md-4">
                    <label>Building Name</label>
                    <input type="text" placeholder="Building Name" class="form-control" disabled id="employee_building_name" value="{{$data['employeeInformation']['building_name']}}" />
                </div>
                <div class="col-md-4">
                    <label>House Type</label>
                    <input type="text" placeholder="House Type" class="form-control" disabled id="employee_house_type" value="{{$data['employeeInformation']['house_type']}}" />
                </div>
                <div class="col-md-4">
                    <label>House Name</label>
                    <input type="text" placeholder="House Name" name="employee_house_name" class="form-control" disabled id="employee_house_name" value="{{$data['employeeInformation']['house_name']}}" />
                </div>
                <!--House Name End-->
            </div>

            <div class="row mt-1">
                <div class="col-md-4">
                    <label>Employee Name</label>
                    <input type="text" placeholder="Employee Name"
                           name="employee_name" class="form-control" disabled
                           id="employee_name" value="{{$data['employeeInformation']['emp_name']}}">
                    <span class="text-danger">{{ $errors->first('employee_name') }}</span>
                </div>
                <div class="col-md-4">
                    <label>Designation</label>
                    <input type="text" placeholder="Designation"
                           name="employee_designation"class="form-control" disabled
                           id="employee_designation" value="{{$data['employeeInformation']['designation']}}">
                    <span class="text-danger">{{ $errors->first('employee_designation') }}</span>
                </div>
                <div class="col-md-4">
                    <label>Department</label>
                    <input type="text" placeholder="Department"
                           name="employee_department" class="form-control" disabled
                           id="employee_department" value="{{$data['employeeInformation']['department']}}">
                    <span class="text-danger">{{ $errors->first('employee_department') }}</span>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-4">
                    <label>Section</label>
                    <input type="text" placeholder="Section"
                           name="employee_section" class="form-control" disabled
                           id="employee_section" value="{{$data['employeeInformation']['section']}}">
                    <span class="text-danger">{{ $errors->first('employee_section') }}</span>
                </div>
                <div class="col-md-4">
                    <label>Reason</label>
                    <textarea placeholder="Enter Reason" rows="3" wrap="soft" name="replace_reason" class="form-control" id="replace_reason" disabled>{{$data['houseReplacementApplication']->replace_reason}}</textarea>
                </div>

                @if($showBtn=='1' && $data['houseReplacementApplication']->approved_yn != 'Y')
                    <div class="col-md-4">
                        <label class="required" for="house_id">Newly Assigned House</label>
                        @if($data['houseReplacementApplication']->approved_yn != 'Y')
                            <select class="custom-select select2" name="house_id" id="house_id" required>
                                <option value="">--Please Select--</option>
                                @foreach($data['houses'] as $house)
                                    <option value="{{ $house->house_id  }}"> {{ $house->colony_name.'-'.$house->building_name.'-'.$house->house_name  }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{ $errors->first('house_id') }}</span>
                        @else
                            <input type="text" placeholder="Section" name="house_id" id="house_id" class="form-control" disabled value="{{$data['houseReplacementApplication']->house->house_name}}">
                        @endif
                    </div>
                @endif
{{--                <div class="col-md-4">--}}
{{--                    <label class="required" for="house_id">Newly Assigned House</label>--}}
{{--                    @if($data['houseReplacementApplication']->approved_yn != 'Y')--}}
{{--                        <select class="custom-select" name="house_id" id="house_id" required>--}}
{{--                            <option value="">--Please Select--</option>--}}
{{--                            @foreach($data['houses'] as $house)--}}
{{--                                <option value="{{ $house->house_id  }}"> {{ $house->colony_name.'-'.$house->building_name.'-'.$house->house_name  }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                        <span class="text-danger">{{ $errors->first('house_id') }}</span>--}}
{{--                    @else--}}
{{--                        <input type="text" placeholder="Section" name="house_id" id="house_id" class="form-control" disabled value="{{$data['houseReplacementApplication']->house->house_name}}">--}}
{{--                    @endif--}}
{{--                </div>--}}
            </div>
        </div>
    </div>

    @if($showBtn=='1')
        <div class="row">

            <div class="col mt-2">
                <div class="d-flex justify-content-end">
                    @if($data['houseReplacementApplication']->approved_yn != 'Y')
                        <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                            Approve
                        </button>
                    @else
                        <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-danger">
                            Un-Assign
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @elseif($showBtn=='0' && $data['houseReplacementApplication']->workflow_process==null)
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="d-flex justify-content-end col">
                    <a href="javascript:void(0)" class="show-receive-modal workflowBtn" title="Assign Workflow"><button class="btn btn btn-dark shadow btn-success" type="button" onclick="getFlow('raa');">Assign Workflow</button></a>
                </div>
            </div>
        </div>
    @else
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="d-flex justify-content-end col">
                    <a href="javascript:void(0)" class="show-receive-modal workflowBtn" title="Assign Workflow"><button class="btn btn btn-dark shadow btn-success" type="button" onclick="goFlow('raa');">Workflow Approve</button></a>
                </div>
            </div>
        </div>
    @endif

</form>
