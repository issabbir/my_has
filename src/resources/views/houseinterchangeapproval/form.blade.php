<form id="house-form" method="POST" action="{{ route('house-interchange-approval.update', ['id' => $data['houseInterchangeApplication']->int_change_id]) }}" enctype="multipart/form-data"> {{--{{ route('house-interchange-approval.update', ['id' => $data['houseInterchangeApplication']->int_change_id]) }}--}}
    <input name="_method" type="hidden" value="PUT">
    {{ csrf_field() }}
    <div class="row justify-content-center">
        <div class="col-4 form-inline">
            <label class="required" for="application_date">Application Date</label>&nbsp;<input type="text" value="{{ date("d-m-Y", strtotime($data['houseInterchangeApplication']->int_change_app_date)) }}" class="form-control" disabled id="application_date" name="application_date" />
        </div>
    </div>

    <div class="row justify-content-center mt-2">
        <div class="col-md-5 grid-divider">
            <h5>First Employee's Information</h5>
            <div class="row mt-1">
                <div class="col-md-6">
                    <label class="required">Employee Code</label>
                    <input type="text" placeholder="Employee Code"
                           name="first_employee_code" class="form-control" disabled
                           id="first_employee_code" value="{{$data['firstEmployeeInformation']['emp_code']}}">
                    <span class="text-danger">{{ $errors->first('first_employee_code') }}</span>
                </div>
                <!--House Name-->
                <div class="col-md-6">
                    <label>House Name</label>
                    <input type="text" placeholder="House Name" name="first_employee_house_name" class="form-control" disabled id="first_employee_house_name" value="{{$data['firstEmployeeInformation']['house_name']}}" />
                </div>
                <!--House Name End-->
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label>Employee Name</label>
                    <input type="text" placeholder="Employee Name"
                           name="first_employee_name" class="form-control" disabled
                           id="first_employee_name" value="{{$data['firstEmployeeInformation']['emp_name']}}">
                    <span class="text-danger">{{ $errors->first('first_employee_name') }}</span>
                </div>
                <div class="col-md-6">
                    <label>Designation</label>
                    <input type="text" placeholder="Designation"
                           name="first_employee_designation"class="form-control" disabled
                           id="first_employee_designation" value="{{$data['firstEmployeeInformation']['designation']}}">
                    <span class="text-danger">{{ $errors->first('first_employee_designation') }}</span>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label>Department</label>
                    <input type="text" placeholder="Department"
                           name="first_employee_department" class="form-control" disabled
                           id="first_employee_department" value="{{$data['firstEmployeeInformation']['department']}}">
                    <span class="text-danger">{{ $errors->first('first_employee_department') }}</span>
                </div>
                <div class="col-md-6">
                    <label>Section</label>
                    <input type="text" placeholder="Section"
                           name="first_employee_section" class="form-control" disabled
                           id="first_employee_section" value="{{$data['firstEmployeeInformation']['section']}}">
                    <span class="text-danger">{{ $errors->first('first_employee_section') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <h5>Second Employee's Information</h5>
            <div class="row mt-1">
                <div class="col-md-6">
                    <label class="required">Employee Code</label>
                    <input type="text" placeholder="Employee Code"
                           name="second_employee_code" class="form-control" disabled
                           id="second_employee_code" value="{{$data['secondEmployeeInformation']['emp_code']}}">
                    <span class="text-danger">{{ $errors->first('second_employee_code') }}</span>
                </div>
                <!--House Name-->
                <div class="col-md-6">
                    <label>House Name</label>
                    <input type="text" placeholder="House Name" name="second_employee_house_name" class="form-control" disabled id="second_employee_house_name" value="{{$data['secondEmployeeInformation']['house_name']}}" />
                </div>
                <!--House Name End-->
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label>Employee Name</label>
                    <input type="text" placeholder="Employee Name"
                           name="second_employee_name" class="form-control" disabled
                           id="second_employee_name" value="{{$data['secondEmployeeInformation']['emp_name']}}">
                    <span class="text-danger">{{ $errors->first('second_employee_name') }}</span>
                </div>
                <div class="col-md-6">
                    <label>Designation</label>
                    <input type="text" placeholder="Designation"
                           name="second_employee_designation"class="form-control" disabled
                           id="second_employee_designation" value="{{$data['secondEmployeeInformation']['designation']}}">
                    <span class="text-danger">{{ $errors->first('second_employee_designation') }}</span>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label>Department</label>
                    <input type="text" placeholder="Department"
                           name="second_employee_department" class="form-control" disabled
                           id="second_employee_department" value="{{$data['secondEmployeeInformation']['department']}}">
                    <span class="text-danger">{{ $errors->first('second_employee_department') }}</span>
                </div>
                <div class="col-md-6">
                    <label>Section</label>
                    <input type="text" placeholder="Section"
                           name="second_employee_section" class="form-control" disabled
                           id="second_employee_section" value="{{$data['secondEmployeeInformation']['section']}}">
                    <span class="text-danger">{{ $errors->first('second_employee_section') }}</span>
                </div>
            </div>
        </div>
    </div>
   {{-- <div class="row">
        <div class="col mt-2">
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                    Approve
                </button>
            </div>
        </div>
    </div>--}}
    @if($showBtn=='1')
        <div class="row my-2">

            <div class="col-md-6">
                &nbsp;
            </div>
            <div class="col-md-3">
                <label for="approved_file" class="" >Approval File :</label>
                <div class="custom-file b-form-file form-group dropdownStatus">
                    <input
                        type="file"
                        id="approved_file"
                        name="approved_file"
                        class="custom-file-input"
                        accept="image/gif, image/jpeg,image/png,image/jpg"
                        >
                    <label
                        for="approved_file"
                        data-browse="Browse"
                        accept="image/gif, image/jpeg,image/png,image/jpg"
                        class="custom-file-label required"></label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="d-flex justify-content-end col mt-2">
                    <button type="submit" class="btn btn btn-dark shadow mr-1 btn-secondary">
                        Approve
                    </button>
                </div>
            </div>
            <div class="col-md-1">&nbsp;</div>
        </div>
    @elseif($showBtn=='0' && $data['houseInterchangeApplication']->workflow_process==null)
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="d-flex justify-content-end col">
                    <a href="javascript:void(0)" class="show-receive-modal workflowBtn" data-prefix="prefix" title="Assign Workflow"><button class="btn btn btn-dark shadow btn-success" type="button" onclick="getFlow('iaa');">Assign Workflow</button></a>
                </div>
            </div>
        </div>
    @else
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="d-flex justify-content-end col">
                    <a href="javascript:void(0)" class="show-receive-modal workflowBtn" data-prefix="prefix" title="Assign Workflow"><button class="btn btn btn-dark shadow btn-success" type="button" onclick="goFlow('iaa');">Workflow Approve</button></a>
                </div>
            </div>
        </div>
    @endif

</form>
