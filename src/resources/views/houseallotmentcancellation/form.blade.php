<form id="house-form" method="POST"

    @if(Auth::user()->hasPermission('CAN_ALLOTMENT_CANCEL'))
        action="{{ route('house-allotment-cancellation.update', ['allotmentId' => $houseAllotment->allot_id]) }}" >

    <input name="_method" type="hidden" value="PUT">
    @else
        action="{{ route('house-allotment-cancellation.cancelRequest', ['allotmentId' => $houseAllotment->allot_id]) }}" >

    <input name="_method" type="hidden" value="PUT">
    @endif

    {{ csrf_field() }}

{{--    @dd($houseAllotment)--}}
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <label>Employee</label>
                    <input type="text" placeholder="Employee"
                           name="employee_name" class="form-control"
                           id="employee_name" value="{{$houseAllotment->employee->emp_name}} ({{$houseAllotment->employee->emp_code}})" disabled>
                </div>
                <div class="col-md-6">
                    <label>Designation</label>
                    <input type="text" placeholder="Designation"
                           name="employee_designation" class="form-control"
                           id="employee_designation" value="{{$houseAllotment->employee->designation->designation}} - {{$houseAllotment->employee->department->department_name}}" disabled>
                    <span class="text-danger">{{ $errors->first('employee_designation') }}</span>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label>Join Date</label>
                    <input type="text" placeholder="Join Date"
                           name="employee_join_date" class="form-control"
                           id="employee_join_date" value="@if($houseAllotment->employee->emp_join_date){{ date('d-m-Y', strtotime($houseAllotment->employee->emp_join_date)) }}@endif" disabled>
                    <span class="text-danger">{{ $errors->first('employee_join_date') }}</span>
                </div>
                <div class="col-md-6">
                    <label>PRL Date</label>
                    <input type="text" placeholder="PRL Date"
                           name="employee_prl_date" class="form-control"
                           id="employee_prl_date" value="@if($houseAllotment->employee->emp_lpr_date){{ date('d-m-Y', strtotime($houseAllotment->employee->emp_lpr_date)) }}@endif" disabled>
                    <span class="text-danger">{{ $errors->first('employee_prl_date') }}</span>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-4">
                    <label>House</label>
                    <input type="text" placeholder="Join Date"
                           name="house_name" class="form-control"
                           id="house_name" value="{{$houseAllotment->house->house_name}}" disabled>
                    <span class="text-danger">{{ $errors->first('house_name') }}</span>
                </div>


                <div class="col-md-4">
                    <label class="required">Reason</label>


                    @if(Auth::user()->hasPermission('CAN_ALLOTMENT_CANCEL'))
                    <textarea
                        rows="3" wrap="soft" name="reason"
                        class="form-control" id="reason" required
                        readonly >

                            {{ $houseAllotment->cancel_reason }}

                    </textarea>

                    @else

                    <textarea
                        rows="3" wrap="soft" name="reason"
                        class="form-control" id="reason" required
                        placeholder="Reason"></textarea>

                    @endif

                    <span class="text-danger">{{ $errors->first('reason') }}</span>
                </div>


{{--                <div class="col-md-4">--}}
{{--                    <label class="required">Status</label>--}}
{{--                    <select class="custom-select" name="house_status_id" id="house_status_id" required >--}}
{{--                        @foreach($houseStatuses as $houseStatus)--}}
{{--                            <option value="{{ $houseStatus->house_status_id  }}"--}}
{{--                            > {{ $houseStatus->house_status  }}</option>--}}
{{--                        @endforeach--}}
{{--                    </select>--}}
{{--                    <span class="text-danger">{{ $errors->first('house_status_id') }}</span>--}}
{{--                </div>--}}

            </div>

            <div class="row mt-1 d-flex justify-content-end">
                <div class="col-auto">
                    <div class="mt-2">
                        <button type="button" class="btn btn btn-dark shadow mb-1 btn-secondary mr-1" data-dismiss="modal">Close</button>

                        @if(Auth::user()->hasPermission('CAN_ALLOTMENT_CANCEL'))
                            <button type="submit" class="btn btn btn-dark shadow mb-1 btn-danger"> Cancel Allotment </button>
                        @else
                            <button type="submit" class="btn btn btn-dark shadow mb-1 btn-danger"> Allotment Cancel Request </button>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
