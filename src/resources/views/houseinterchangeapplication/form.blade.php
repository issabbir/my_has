<form id="house-form" method="POST"
      @if($data['houseInterchangeApplication']->int_change_id)
      action="{{ route('house-interchange-application.update', ['id' => $data['houseInterchangeApplication']->int_change_id]) }}">
        <input name="_method" type="hidden" value="PUT">
    @else
        action="{{ route('house-interchange-application.store') }}">
    @endif
    {{ csrf_field() }}
    <div class="row justify-content-center">
        <div class="col-4 form-inline">
            <label class="required" for="application_date">Application Date</label>&nbsp;<input type="text" autocomplete="off" value="" class="form-control datetimepicker-input" data-predefined-date="@if($data['houseInterchangeApplication']->int_change_app_date) {{$data['houseInterchangeApplication']->int_change_app_date}} @endif" data-toggle="datetimepicker" data-target="#application_date" required id="application_date" name="application_date" />
        </div>
        <div class="col-md-12">
            <hr>
        </div>
    </div>

    <div class="row justify-content-center mt-2">
        <div class="col-md-6 grid-divider">
            <h5>First Employee's Information</h5>
            <div class="row mt-1">
                <div class="col-md-6">
                    <label class="required" for="first_employee_code">Employee Code</label>
                    <select class="custom-select select2 form-control emp_code" id="first_employee_code" name="first_employee_code" required data-emp-code="{{$data['firstEmployeeInformation']['emp_code']}}"></select>
                    <span class="text-danger">{{ $errors->first('first_employee_code') }}</span>
                </div>

                <div class="col-md-6">
                    <label>Employee Name</label>
                    <input type="text" placeholder="Employee Name"
                           name="first_employee_name" class="form-control" disabled
                           id="first_employee_name" value="{{$data['firstEmployeeInformation']['emp_name']}}">
                    <span class="text-danger">{{ $errors->first('first_employee_name') }}</span>
                </div>

            </div>

            <div class="row mt-1">

                <div class="col-md-6">
                    <label>Designation</label>
                    <input type="text" placeholder="Designation"
                           name="first_employee_designation"class="form-control" disabled
                           id="first_employee_designation" value="{{$data['firstEmployeeInformation']['designation']}}">
                    <span class="text-danger">{{ $errors->first('first_employee_designation') }}</span>
                </div>
                <div class="col-md-6">
                    <label>Department</label>
                    <input type="text" placeholder="Department"
                           name="first_employee_department" class="form-control" disabled
                           id="first_employee_department" value="{{$data['firstEmployeeInformation']['department']}}">
                    <span class="text-danger">{{ $errors->first('first_employee_department') }}</span>
                </div>
            </div>

            <div class="row mt-1">

                <div class="col-md-6">
                    <label>Section</label>
                    <input type="text" placeholder="Section"
                           name="first_employee_section" class="form-control" disabled
                           id="first_employee_section" value="{{$data['firstEmployeeInformation']['section']}}">
                    <span class="text-danger">{{ $errors->first('first_employee_section') }}</span>
                </div>
                <input type="hidden" name="first_alloted_id" id="first_alloted_id">
                <input type="hidden" name="f_emp_code" id="f_emp_code">

                <!--House Name-->
                <div class="col-md-6">
                    <label>House Name</label>
                    <input type="text" placeholder="House Name" name="first_employee_house_name" class="form-control" disabled id="first_employee_house_name" value="{{$data['firstEmployeeInformation']['house_name']}}" />
                </div>
                <!--House Name End-->

            </div>

            <div class="row mt-1">

                <div class="col-md-6">
                    <label>House Type</label>
                    <input type="text" placeholder="House Type" name="first_employee_house_type" class="form-control" disabled id="first_employee_house_type" value="{{$data['firstEmployeeInformation']['house_type']}}" />
                </div>
                <div class="col-md-6">
                    <label for="first_emp_building_name">Building Name</label>
                    <input type="text" placeholder="Building Name" name="first_emp_building_name" class="form-control" disabled id="first_emp_building_name" value="{{$data['firstEmployeeInformation']['building_name']}}" />
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label>Road No</label>
                    <input type="text" placeholder="Road No" name="first_employee_building_road_no" class="form-control" disabled id="first_employee_building_road_no" value="{{$data['firstEmployeeInformation']['building_road_no']}}" />
                </div>
                <div class="col-md-6">
                    <label for="first_emp_residential_area">Residential Area</label>
                    <input type="text" placeholder="Residential Area" name="first_emp_residential_area" class="form-control" disabled id="first_emp_residential_area" value="{{$data['firstEmployeeInformation']['residential_area']}}" />
                </div>
            </div>




        </div>

        <div class="col-md-6">
            <h5>Second Employee's Information</h5>
            <div class="row mt-1">
                <div class="col-md-6">
                    <label class="required" for="second_employee_code">Employee Code</label>
                    <select class="custom-select select2 form-control emp_code" id="second_employee_code" name="second_employee_code" required data-emp-code="{{$data['secondEmployeeInformation']['emp_code']}}"></select>
                    <span class="text-danger">{{ $errors->first('second_employee_code') }}</span>
                </div>

                <div class="col-md-6">
                    <label>Employee Name</label>
                    <input type="text" placeholder="Employee Name"
                           name="second_employee_name" class="form-control" disabled
                           id="second_employee_name" value="{{$data['secondEmployeeInformation']['emp_name']}}">
                    <span class="text-danger">{{ $errors->first('second_employee_name') }}</span>
                </div>

            </div>




            <div class="row mt-1">

                <div class="col-md-6">
                    <label>Designation</label>
                    <input type="text" placeholder="Designation"
                           name="second_employee_designation"class="form-control" disabled
                           id="second_employee_designation" value="{{$data['secondEmployeeInformation']['designation']}}">
                    <span class="text-danger">{{ $errors->first('second_employee_designation') }}</span>
                </div>
                <div class="col-md-6">
                    <label>Department</label>
                    <input type="text" placeholder="Department"
                           name="second_employee_department" class="form-control" disabled
                           id="second_employee_department" value="{{$data['secondEmployeeInformation']['department']}}">
                    <span class="text-danger">{{ $errors->first('second_employee_department') }}</span>
                </div>
            </div>

            <div class="row mt-1">

                <div class="col-md-6">
                    <label>Section</label>
                    <input type="text" placeholder="Section"
                           name="second_employee_section" class="form-control" disabled
                           id="second_employee_section" value="{{$data['secondEmployeeInformation']['section']}}">
                    <span class="text-danger">{{ $errors->first('second_employee_section') }}</span>
                </div>
                <input type="hidden" name="second_alloted_id" id="second_alloted_id">
                <input type="hidden" name="s_emp_code" id="s_emp_code">

                <!--House Name-->
                <div class="col-md-6">
                    <label>House Name</label>
                    <input type="text" placeholder="House Name" name="second_employee_house_name" class="form-control" disabled id="second_employee_house_name" value="{{$data['secondEmployeeInformation']['house_name']}}" />
                </div>
                <!--House Name End-->
            </div>


            <div class="row mt-1">
                <div class="col-md-6">
                    <label>House Type</label>
                    <input type="text" placeholder="House Type" name="second_emp_house_type" class="form-control" disabled id="second_emp_house_type" value="{{$data['secondEmployeeInformation']['house_type']}}" />
                </div>
                <div class="col-md-6">
                    <label for="second_emp_building_name">Building Name</label>
                    <input type="text" placeholder="Building Name" name="second_emp_building_name" class="form-control" disabled id="second_emp_building_name" value="{{$data['secondEmployeeInformation']['building_name']}}" />
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label>Road No</label>
                    <input type="text" placeholder="Road No" name="second_emp_building_road_no" class="form-control" disabled id="second_emp_building_road_no" value="{{$data['secondEmployeeInformation']['building_road_no']}}" />
                </div>
                <div class="col-md-6">
                    <label for="second_emp_residential_area">Residential Area</label>
                    <input type="text" placeholder="Residential Area" name="second_emp_residential_area" class="form-control" disabled id="second_emp_residential_area" value="{{$data['secondEmployeeInformation']['residential_area']}}" />
                </div>
            </div>

        </div>
    </div>

    <div class="row mt-1">
            <div class="col-md-7">
                <label>Remarks</label>
                <textarea placeholder="Enter Remarks" rows="3" wrap="soft" name="remarks"  class="form-control" id="remarks">{{ isset($data['secondEmployeeInformation']['remarks']) ? $data['secondEmployeeInformation']['remarks'] : '' }}</textarea>
            </div>

            <div class="col-md-5">
                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn btn-dark shadow btn-secondary">
                            Submit
                        </button>
                    </div>
            </div>
    </div>
</form>
