<form id="house-form" method="POST" enctype="multipart/form-data"
      @if($data['haApplication']->application_id)
      action="{{ route('ha-application.update', ['id' => $data['haApplication']->application_id]) }}">
    <input name="_method" type="hidden" value="PUT">
    @else
        action="{{ route('ha-application.store') }}">
    @endif

    {{ csrf_field() }}
    <div class="row justify-content-center">
        <div class="col-md-12 mb-1 mt-1">
            <h5>Employee Information</h5>
        </div>
        <div class="col-md-3">
            <label class="required" for="employee_code">Employee Code</label>
            @if($loggedUserCode == 'admin')
                <select class="custom-select select2 form-control emp_code" id="employee_code"
                        name="employee_code" required
                        data-emp-code="{{$data['employeeInformation']['emp_code']}}"></select>
            @else
                <select class="custom-select select2 form-control emp_code" id="employee_codde" required
                        data-emp-code="{{$loggedUserCode}}" disabled>
                    <option value="{{$loggedUserCode}}" selected>{{$loggedUserCode}}</option>
                </select>
                <input name="employee_code" type="hidden" value="{{$loggedUserCode}}">
            @endif
            <span class="text-danger">{{ $errors->first('employee_code') }}</span>
        </div>

        <div class="col-md-3">
            <label>Employee Name</label>
            <input type="text" placeholder="Employee Name"
                   name="employee_name" class="form-control" disabled
                   id="employee_name" value="{{$data['employeeInformation']['emp_name']}}">
            <span class="text-danger">{{ $errors->first('employee_name') }}</span>
        </div>
        <div class="col-md-3">
            <label>Designation</label>
            <input type="text" placeholder="Designation"
                   name="employee_designation" class="form-control" disabled
                   id="employee_designation" value="{{$data['employeeInformation']['designation']}}">
            <span class="text-danger">{{ $errors->first('employee_designation') }}</span>
        </div>
        <div class="col-md-3">
            <label>Department</label>
            <input type="text" placeholder="Department"
                   name="employee_department" class="form-control" disabled
                   id="employee_department" value="{{$data['employeeInformation']['department']}}">
            <span class="text-danger">{{ $errors->first('employee_department') }}</span>
        </div>


    </div>
    <!---Female Related Information-->
    <div id="female-related-information"
         @if( !($data['haApplication']->emp_gender_id == '2' && $data['haApplication']->emp_maritial_status_id == '2') )
         style="display: none;"
        @endif
    >
        <div></div>
    </div>



    <!---Female Member Details Information End-->
    @if(!$data['haApplication']->application_id)
        <div class="row">
            <div class="col mt-2">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                        Handover Request
                    </button>
                    <button type="button" class="btn btn btn-outline shadow mb-1 btn-secondary" id="resetForm">
                        Reset
                    </button>
                </div>
            </div>
        </div>
        @endif
        </div>
        </div>
</form>
<div class="d-none">
    <select class="custom-select" name="relation_type_id_template" id="relation_type_id_template">
        <option value="">--Please Select--</option>
        @foreach($data['relationships'] as $relationship)
            <option value="{{ $relationship->relation_type_id  }}">{{ $relationship->relation_type  }}</option>
        @endforeach
    </select>
</div>
