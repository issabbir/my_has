<form id="house-form" method="POST"

      action="{{ route('takeOver.takeOverRequest') }}">

    {{ csrf_field() }}

    <div class="row">
        <div class="col-md-12 mb-1 mt-1">
            <h6># Employee Information</h6>
        </div>
        <div class="col-md-3">
            <label class="required" for="employee_code">Employee Code</label>
            <input type="hidden" name="employee_id" class="form-control"
                   value="{{ isset($data[0]->emp_id)?$data[0]->emp_id:'' }}">
            <input type="text" class="form-control" value="{{ isset($data[0]->emp_code)?$data[0]->emp_code:'' }}"
                   readonly>
        </div>

        <div class="col-md-3">
            <label>Employee Name</label>
            <input type="text" placeholder="Employee Name"
                   class="form-control" disabled
                   id="employee_name" value="{{ isset($data[0]->emp_name)?$data[0]->emp_name:'' }}">
            <span class="text-danger">{{ $errors->first('employee_name') }}</span>
        </div>
        <div class="col-md-3">
            <label>Designation</label>
            <input type="text" placeholder="Designation"
                   class="form-control" disabled
                   id="employee_designation" value="{{ isset($data[0]->designation)?$data[0]->designation:'' }}">
            <span class="text-danger">{{ $errors->first('employee_designation') }}</span>
        </div>
        <div class="col-md-3">
            <label>Department</label>
            <input type="text" placeholder="Department"
                   class="form-control" disabled
                   id="employee_department" value="{{ isset($data[0]->department_name)?$data[0]->department_name:'' }}">
            <span class="text-danger">{{ $errors->first('employee_department') }}</span>
        </div>

    </div>


    <div class="row">

        <div class="col-md-12 mb-1 mt-1">
            <h6># House Information</h6>
        </div>
        {{--        code strated from here--}}
        <div class="col-md-3">
            <label class="required">Dormitory (Yes/No) </label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="dormitory_yn"
                           id="dormitory_y" disabled value="{{\App\Enums\YesNoFlag::YES}}"
                           @if(\App\Enums\YesNoFlag::YES == $data[0]->dormitory_yn)
                           checked
                        @endif
                    >
                    <label class="form-check-label" for="dormitory_y">Yes</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="dormitory_yn"
                           id="dormitory_n" disabled value="{{\App\Enums\YesNoFlag::NO}}"
                           @if(\App\Enums\YesNoFlag::YES != $data[0]->dormitory_yn)
                           checked
                        @endif
                    >
                    <label class="form-check-label" for="dormitory_n">No</label>
                </div>
                <span class="text-danger">{{ $errors->first('dormitory_yn') }}</span>
            </div>
        </div>


        <div class="col-md-3">
            <label for="area_name">Area Name</label>
            <input class="form-control" id="area_name" type="text"
                   value="{{ isset($data[0]->colony_name)?$data[0]->colony_name:'' }}" readonly>
        </div>
        <div class="col-md-3">
            <label for="building_name">Building Name</label>
            <input class="form-control" id="building_name" type="text"
                   value="{{ isset($data[0]->building_name)?$data[0]->building_name:'' }}" readonly>
        </div>


        <div class="col-md-3">
            <label for="building_name">House Type</label>
            <input class="form-control" id="house_type" type="text"
                   value="{{ isset($data[0]->house_type)?$data[0]->house_type:'' }}" readonly>
        </div>


        <div class="col-md-3 mt-2">
            <label for="house_size">
                @if(isset($data[0]->dormitory_yn))
                    @if(isset($data[0]->dormitory_yn) == 'Y')
                        Flat Size
                    @else
                        House Size
                    @endif
                @else
                    House Size
                @endif
            </label>

            <input class="form-control" id="house_size" type="text"
                   value="{{ isset($data[0]->house_size)?$data[0]->house_size:'' }}" readonly>
        </div>

        <div class="col-md-3 mt-2">
            <label for="floor_number">Floor Number</label>
            <input class="form-control" id="floor_number" type="text"
                   value="{{ isset($data[0]->floor_number)?$data[0]->floor_number:"" }}" readonly>
        </div>
        <div class="col-md-3 mt-2">

            <label class="required" for="house_name">Flat Name
                {{--                @if(isset($data[0]->dormitory_yn))--}}
                {{--                    @if(isset($data[0]->dormitory_yn) == 'Y')--}}
                {{--                        Flat Name--}}
                {{--                    @else--}}
                {{--                        House Name--}}
                {{--                    @endif--}}
                {{--                @else--}}
                {{--                    House Name--}}
                {{--                @endif--}}
            </label>

            <input name="house_id" type="hidden" value="{{ isset($data[0]->house_id)?$data[0]->house_id:'' }}" required>
            <input class="form-control" id="house_name" type="text"
                   value="{{ isset($data[0]->house_name)? $data[0]->house_name:'' }}" readonly>
        </div>


            @if($data[0]->dormitory_yn == 'Y')
                <div class="col-md-3 mt-2">
                    <label for="seat_no">Seat No.</label>
                    <input class="form-control" id="seat_no" type="text"
                           value="{{ isset($data[0]->house_code)?$data[0]->house_code:"" }}" readonly>
                </div>
            @endif

        <div class="col-md-6 mt-2">
            <label class="" for="handover_reason">Takeover Reason</label>
            <textarea name="handover_reason" id="handover_reason" class="form-control" cols="30" rows="3"
                      ></textarea>
        </div>


    </div>

    <div class="row">
        <div class="col mt-2">
            <div class="d-flex justify-content-end">
                <input class="form-control" id="allot_letter_id" name="allot_letter_id" type="hidden"
                       value="{{ isset($data[0]->allot_letter_id)?$data[0]->allot_letter_id:'' }}" readonly>
                <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                    Takeover Request
                </button>
                <button type="reset" class="btn btn btn-outline shadow mb-1 btn-secondary">
                    Reset
                </button>
            </div>
        </div>
    </div>

</form>
