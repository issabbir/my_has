<form id="house-form" method="POST" enctype="multipart/form-data"

        action="{{ route('hand-over-application.handOverRequest') }}">

    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12 mb-1 mt-1">
            <h5>Employee Information</h5>
        </div>
        <div class="col-md-3">
            <label class="required" for="employee_code">Employee Code</label>
            <input type="hidden" name="employee_id" id="employee_id" class="form-control" value="{{ $data[0]->emp_id }}">
            <input type="hidden" name="request_from" id="request_from" value="user">
            <input type="text" class="form-control" value="{{ $data[0]->emp_code }}" readonly>
        </div>

        <div class="col-md-3">
            <label>Employee Name</label>
            <input type="text" placeholder="Employee Name"
                   class="form-control" disabled
                   id="employee_name" value="{{ $data[0]->emp_name }}">
            <span class="text-danger">{{ $errors->first('employee_name') }}</span>
        </div>
        <div class="col-md-3">
            <label>Designation</label>
            <input type="text" placeholder="Designation"
                   class="form-control" disabled
                   id="employee_designation" value="{{ $data[0]->designation }}">
            <span class="text-danger">{{ $errors->first('employee_designation') }}</span>
        </div>
        <div class="col-md-3">
            <label>Department</label>
            <input type="text" placeholder="Department"
                   class="form-control" disabled
                   id="employee_department" value="{{ $data[0]->department_name }}">
            <span class="text-danger">{{ $errors->first('employee_department') }}</span>
        </div>

    </div>


    <div class="row">

        <div class="col-md-12 mb-1 mt-1">
            @if($data[0]->dormitory_yn == 'Y')
                <h5>Dormitory Information</h5>
            @else
                <h5>House Information</h5>
            @endif
        </div>

        <div class="col-md-2">
            @if($data[0]->dormitory_yn == 'Y')
                <label class="required" for="house_name">Dormitory Name</label>
            @else
                <label class="required" for="house_name">House Name</label>
            @endif
            <input name="house_id" type="hidden" value="{{ $data[0]->house_id }}" required>
            <input class="form-control" id="house_name" type="text" value="{{ $data[0]->house_name }}" readonly>
        </div>

        <div class="col-md-2">
            <label for="floor_number">Floor Number</label>
            <input class="form-control" id="floor_number" type="text" value="{{ $data[0]->floor_number }}" readonly>
        </div>

        <div class="col-md-2">
            @if($data[0]->dormitory_yn == 'Y')
                <label for="house_size">Dormitory Size</label>
            @else
                <label for="house_size">House Size</label>
            @endif
            <input class="form-control" id="house_size" type="text" value="{{ $data[0]->house_size }}" readonly>
        </div>

        <div class="col-md-3">
            <label for="building_name">Building Name</label>
            <input class="form-control" id="building_name" type="text" value="{{ $data[0]->building_name }}" readonly>
        </div>

        <div class="col-md-3">
            <label for="area_name">Area Name</label>
            <input class="form-control" id="area_name" type="text" value="{{ $data[0]->colony_name }}" readonly>
        </div>
        <div class="col-md-3 mt-1">
            <label class="required">Application Date</label>
            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                <input type="text" value="{{date('Y-m-d')}}"
                       class="form-control datetimepicker-input" data-toggle="datetimepicker"
                       data-target="#datetimepicker1"
                       required
                       id="application_date"
                       name="application_date"
                       autocomplete="off" readonly
                />
            </div>

        </div>
        <div class="col-md-3 mt-1">
            <label>Attachment</label>
            <input type="file" class="form-control" name="applicaton_doc" id="applicaton_doc">
        </div>

        <div class="col-md-6 mt-2">
            <label class="required" for="handover_reason">Handover Reason</label>
            <textarea name="handover_reason" id="handover_reason" class="form-control" cols="30" rows="3" required></textarea>
        </div>
        <input type="hidden" name="cparequest_yn" value="N">
    </div>

    <div class="row">
        <div class="col mt-2">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                    Handover Request
                </button>
                <button type="reset" class="btn btn btn-outline shadow mb-1 btn-secondary">
                    Reset
                </button>
            </div>
        </div>
    </div>

</form>
