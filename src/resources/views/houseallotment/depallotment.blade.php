@extends('layouts.default')

@section('title')
    House Allotment
@endsection

@section('header-style')
    <!--Load custom style link or css-->
    <style>
        .sts-btn-s {
            min-width: 85px;
        }
        .sts-btn {
            min-width: 108px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body"><h4 class="card-title">Allotment Form for Department</h4>
                    <hr>

                    <form enctype="multipart/form-data" method="post"

                        @if(isset($data[0]->dept_ack_id))
                            action="{{ route('house-allotment.update', ['id' => $data[0]->dept_ack_id]) }}" >
                            <input name="_method" type="hidden" value="PUT">
                        @else
                             action="{{ route('house-allotment.store') }}">
                        @endif

                        {{ csrf_field() }}

                        <div class="row">

                            <div class="col-md-3 mt-1">
                                <label class="required">Dormitory (Yes/No) </label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dormitory_yn" required
                                               id="dormitory_y"  value="{{\App\Enums\YesNoFlag::YES}}"
                                        @if(isset($data[0]->dormitory_yn))
                                            @if($data[0]->dormitory_yn=='Y')
                                                checked
                                               @endif
                                            @endif
{{--                                               @if(\App\Enums\YesNoFlag::YES == $data['house']->dormitory_yn)--}}
{{--                                               checked='checked'--}}
{{--                                         @endif--}}
                                        >
                                        <label class="form-check-label" for="dormitory_y">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dormitory_yn" required
                                               id="dormitory_n"  value="{{\App\Enums\YesNoFlag::NO}}"

                                               @if(isset($data[0]->dormitory_yn))
                                                   @if($data[0]->dormitory_yn=='N')
                                                   checked
                                                @endif
                                            @else
                                               checked
                                            @endif

                                        >
                                        <label class="form-check-label" for="dormitory_n">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('dormitory_yn') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3 mt-1">
                                <label class="required">Acknowledgment No.</label>
                                <input required type="text"
                                       placeholder="Enter Acknowledgment No."
                                       name="acknoledgement_no"
                                       @if(isset($data[0]->dept_ack_no))
                                       value="{{ $data[0]->dept_ack_no }}"
                                       @endif
                                       min="3" class="form-control"
                                       id="acknoledgement_no">
                            </div>

                            <div class="col-md-3 mt-1">
                                <label class="">Acknowledgment No. Bangla</label>
                                <input type="text"
                                       placeholder="Enter Acknowledgment No. Bangla"
                                       name="acknoledgement_no_bn"
                                       @if(isset($data[0]->dept_ack_no_bn))
                                       value="{{ $data[0]->dept_ack_no_bn }}"
                                       @endif
                                       min="3" class="form-control"
                                       id="acknoledgement_no_bn">
                            </div>

                            <div class="col-md-3 mt-1">
                                <label class="required">Department Acknowledgment Date</label>
                                <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                    <input type="text"
                                           class="form-control datetimepicker-input" data-toggle="datetimepicker"
                                           data-target="#datetimepicker1"
                                           required
                                           id="dep_ack_date"
                                           name="dep_ack_date"
                                           @if(isset($data[0]->dept_ack_date))
                                           value="{{ $data[0]->dept_ack_date }}"
                                           @endif
                                           autocomplete="off"
                                    />
                                </div>
                            </div>

                            <div class="col-md-3 mt-1">
                                <label class="required">Department</label>

                                <select class="custom-select select2 required
                                        " name="dept_id" id="dept_id" required>
                                    <option value="">--Please Select--</option>
                                        @foreach($department as $value)
                                            <option @if( isset($data[0]->dpt_department_id) && $data[0]->dpt_department_id == $value->department_id)  selected @endif value="{{ $value->department_id  }}">
                                                {{$value->department_name}}</option>
                                        @endforeach

                                </select>
                            </div>

                            <div class="col-md-3 mt-1">

                                <label for="req_flat">Request Flat Quantity</label>
                                <input type="text"
                                       placeholder="Enter Request Flat Quantity"
                                       name="req_flat"
                                       @if(isset($data[0]->no_of_req_flat))
                                       value="{{ $data[0]->no_of_req_flat }}"
                                       @endif
                                       min="3" class="form-control"
                                       id="req_flat">
                            </div>

                            <div class="col-md-3 mt-1">
                                <label class="required" for="alloted_flat">Alloted Flat Quantity</label>
                                <input required type="text"
                                       placeholder="Enter Alloted Flat Quantity"
                                       name="alloted_flat"
                                       @if(isset($data[0]->no_of_alloted_flat))
                                       value="{{ $data[0]->no_of_alloted_flat }}"
                                       @endif
                                       min="3" class="form-control"
                                       id="alloted_flat">
                            </div>

                            <div class="col-md-3 mt-1">
                                <label class="">Valid From</label>
                                <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                                    <input type="text"
                                           class="form-control datetimepicker-input" data-toggle="datetimepicker"
                                           data-target="#datetimepicker2"
                                           id="valid_from"
                                           name="valid_from"
                                           @if(isset($data[0]->dept_req_valid_from))
                                           value="{{ $data[0]->dept_req_valid_from }}"
                                           @endif
                                           autocomplete="off"
                                    />
                                </div>
                            </div>

                            <div class="col-md-3 mt-1">
                                <label class="">Valid To</label>
                                <div class="input-group date" id="datetimepicker3" data-target-input="nearest">
                                    <input type="text"
                                           class="form-control datetimepicker-input" data-toggle="datetimepicker"
                                           data-target="#datetimepicker3"
                                           id="valid_to"
                                           name="valid_to"
                                           @if(isset($data[0]->dept_req_valid_to))
                                           value="{{ $data[0]->dept_req_valid_to }}"
                                           @endif
                                           autocomplete="off"
                                    />
                                </div>
                            </div>

                            <div class="col-md-3 mt-1">
                                <label class="">Department Requisition File</label>
                                <div class="custom-file b-form-file form-group">
                                    <input type="file" id="depAttachedFile" name="depAttachedFile"
                                        class="custom-file-input"
                                        @if(!isset($data[0]->dept_ack_id))

                                        @endif
                                    />
                                    <label for="depAttachedFile" data-browse="Browse"
                                           class="custom-file-label required">
                                        @if(isset($data[0]->dept_ack_id))
                                            {{ $data[0]->dept_ack_doc_name }}
                                        @else
                                            Attached File
                                        @endif
                                    </label>
                                </div>

                            </div>

                            <div class="col-md-3 mt-1">
                                <label class="">Allotment Acknowledgment File</label>
                                <div class="custom-file b-form-file form-group">
                                    <input type="file" id="AllotAttachedFile" name="allotAttachedFile"
                                        class="custom-file-input"
                                        @if(!isset($data[0]->dept_ack_id))

                                        @endif
                                    />
                                    <label for="AllotAttachedFile" data-browse="Browse"
                                           class="custom-file-label required">
                                        @if(isset($data[0]->dept_ack_id))
                                            {{ $data[0]->accept_doc_name }}
                                        @else
                                            Attached File
                                        @endif
                                    </label>
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col mt-2">
                                <div class="d-flex justify-content-end">
                                    @if(isset($data[0]->dept_ack_id))
                                        @if($data[0]->old_ack_yn != 'Y')
                                            <button type="submit" class="btn btn btn-dark shadow mb-1 btn-secondary">
                                                Update
                                            </button>
                                        @endif
                                    @else
                                        <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                            Submit
                                        </button>
                                        <button type="button" class="btn btn btn-outline shadow mb-1 btn-secondary" id="resetForm">
                                            Reset
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body"><h4 class="card-title">Department Allotment List</h4>
                    <hr>
                    <div class="table-responsive">
                        <table  class="table table-sm datatable mdl-data-table dataTable" id="house-allotment">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Ack. No</th>
                                    <th>Department</th>
                                    <th>Requested</th>
                                    <th>Allotted</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th class="text-center">Active</th>
                                    <th class="text-center">Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('footer-script')
    <script type="text/javascript">

        function houseAllotmentDatatable()
        {
            $('#house-allotment').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/house-allotments-datatable-list",
                columns: [
                    {data: 'DT_RowIndex', "name": 'DT_RowIndex'},
                    {data: 'dept_ack_no', name: 'dept_ack_no'},
                    {data: 'department_name', name: 'department_name'},
                    {data: 'no_of_req_flat', name: 'no_of_req_flat'},
                    {data: 'no_of_alloted_flat', name: 'no_of_alloted_flat'},
                    {data: 'dept_req_valid_from', name: 'dept_req_valid_from'},
                    {data: 'dept_req_valid_to', name: 'dept_req_valid_to'},
                    {data: 'dept_req_active_yn', name: 'dept_req_active_yn'},
                    {data: 'ack_status', name: 'ack_status'},
                    {data: 'action', name: 'Action', searchable: false},
                ]
            });
        }

        $(document).ready(function() {

            $('#datetimepicker1').datetimepicker({
                format: 'YYYY-MM-DD',
                // format: 'L',
                // maxDate: new Date(),
                icons: {
                    time: 'bx bx-time',
                    date: 'bx bxs-calendar',
                    up: 'bx bx-up-arrow-alt',
                    down: 'bx bx-down-arrow-alt',
                    previous: 'bx bx-chevron-left',
                    next: 'bx bx-chevron-right',
                    today: 'bx bxs-calendar-check',
                    clear: 'bx bx-trash',
                    close: 'bx bx-window-close'
                }
            });

            $('#datetimepicker2').datetimepicker({
                format: 'YYYY-MM-DD',
                // format: 'L',
                // maxDate: new Date(),
                // minDate: Date(),
                icons: {
                    time: 'bx bx-time',
                    date: 'bx bxs-calendar',
                    up: 'bx bx-up-arrow-alt',
                    down: 'bx bx-down-arrow-alt',
                    previous: 'bx bx-chevron-left',
                    next: 'bx bx-chevron-right',
                    today: 'bx bxs-calendar-check',
                    clear: 'bx bx-trash',
                    close: 'bx bx-window-close'
                }
            });

            $('#datetimepicker3').datetimepicker({
                format: 'YYYY-MM-DD',
                // format: 'L',
                // maxDate: new Date(),
                // minDate: Date(),
                icons: {
                    time: 'bx bx-time',
                    date: 'bx bxs-calendar',
                    up: 'bx bx-up-arrow-alt',
                    down: 'bx bx-down-arrow-alt',
                    previous: 'bx bx-chevron-left',
                    next: 'bx bx-chevron-right',
                    today: 'bx bxs-calendar-check',
                    clear: 'bx bx-trash',
                    close: 'bx bx-window-close'
                }
            });

            houseAllotmentDatatable();


            $('input[type=radio][name=dormitory_yn]').on('change', function() {
                switch ($(this).val()) {
                    case 'Y':

                        $('label[for=alloted_flat]').html('Alloted Seat Quantity');
                        $('label[for=req_flat]').html('Request Seat Quantity');

                        $('input[id=req_flat]').attr("placeholder", "Enter Request Seat Quantity");
                        $('input[id=alloted_flat]').attr("placeholder", "Enter Alloted Seat Quantity");


                        break;

                    case 'N':
                        $('label[for=alloted_flat]').html('Alloted Flat Quantity');
                        $('label[for=req_flat]').html('Request Flat Quantity');

                        $('input[id=req_flat]').attr("placeholder", "Enter Request Flat Quantity");
                        $('input[id=alloted_flat]').attr("placeholder", "Enter Alloted Flat Quantity");


                        break;
                }
            });



        });






        // Date validation codes
        // $('#valid_from').on({
        //     blur:function(){
        //         validDateCheck();
        //     }
        // });
        //
        // $('#valid_to').on({
        //     blur:function(){
        //         validDateCheck();
        //     }
        // });

        // function validDateCheck(){
        //     let start_date   = $("#valid_from").val();
        //     let end_date     = $("#valid_to").val();
        //
        //     start_date = new Date(start_date);
        //     end_date   = new Date(end_date);
        //
        //     if (end_date < start_date) {
        //         alert('Valid To Date Must be Larger than Valid From Date');
        //         $("#valid_to").focus();
        //         $("#valid_to").val('');
        //         return false;
        //     }
        //
        // }


    </script>
@endsection
