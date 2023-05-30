<form id="house-form" method="POST"
      @if($data['houseReplacementApplication']->replace_app_id)
      action="{{ route('house-replacement-application.update', ['id' => $data['houseReplacementApplication']->replace_app_id]) }}">
        <input name="_method" type="hidden" value="PUT">
    @else
        action="{{ route('house-replacement-application.store') }}">
    @endif
    {{ csrf_field() }}
    <div class="row justify-content-center mt-2">
        <div class="col-md-12">
            <div class="row mt-1">
                <div class="col-4">
                    <label class="required" for="application_date">Application Date</label>&nbsp;
                    <input type="text" autocomplete="off" value="" class="form-control datetimepicker-input" data-predefined-date="{{$data['houseReplacementApplication']->replace_app_date}}" data-toggle="datetimepicker" data-target="#application_date" required id="application_date" name="application_date" />
                </div>
                <div class="col-md-4">
                    <label class="required" for="employee_code">Employee Code</label>
{{--                    <select class="custom-select select2 form-control emp_code" id="employee_code" name="employee_code" required data-emp-code="{{$data['employeeInformation']['emp_code']}}"></select>--}}
                    <select class="custom-select select2 form-control emp_code" id="employee_code" required
                            data-emp-code="{{$loggedUserCode}}" disabled>
                        <option value="{{$loggedUserCode}}" selected>{{$loggedUserCode}}</option>
                    </select>
                    <input name="employee_code" type="hidden" value="{{$loggedUserCode}}">
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
                    <textarea placeholder="Enter Reason" rows="3" wrap="soft" name="replace_reason" class="form-control" id="replace_reason">{{$data['houseReplacementApplication']->replace_reason}}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col mt-2">
            <div class="d-flex justify-content-end">
                <button type="submit" id="submit_btn" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                    Submit
                </button>
            </div>
        </div>
    </div>
</form>
