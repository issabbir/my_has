@extends('layouts.default')

@section('title')
    CPA House Handover Application
@endsection

@section('header-style')
    <!--Load custom style link or css-->
    <style>
        .makeReadOnly {
            pointer-events: none;
            background-color: #F6F6F6
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                @if(Session::has('message'))
                    <div
                        class="alert {{Session::get('m-class') ? Session::get('m-class') : 'alert-danger'}} show"
                        role="alert">
                        {{ Session::get('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="card-body"><h4 class="card-title">CPA House Handover Application</h4>
                    <hr>
                    <form id="house-form" method="POST" enctype="multipart/form-data"

                          action="{{ route('hand-over-application.handOverRequest') }}">

                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-md-12 mb-1 mt-1">
                                <h5>Employee Information</h5>
                            </div>
                            <div class="col-md-3">
                                <label class="required" for="employee_code">Employee Code</label>
                                <input type="hidden" name="employee_id" id="employee_id" value="">
                                <input type="hidden" name="request_from" id="request_from" value="cpa">
                                <select class="select2" name="employee_code" id="employee_code">
                                    <option value="">---Please Select---</option>
                                    @if($data)
                                        @foreach($data as $emp)
                                            <option value="{{ $emp->emp_code }}">{{ $emp->emp_code }}
                                                -{{ $emp->emp_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Employee Name</label>
                                <input type="text" placeholder="Employee Name"
                                       class="form-control" disabled
                                       id="employee_name" value="">
                                <span class="text-danger">{{ $errors->first('employee_name') }}</span>
                            </div>
                            <div class="col-md-3">
                                <label>Designation</label>
                                <input type="text" placeholder="Designation"
                                       class="form-control" disabled
                                       id="employee_designation" value="">
                                <span class="text-danger">{{ $errors->first('employee_designation') }}</span>
                            </div>
                            <div class="col-md-3">
                                <label>Department</label>
                                <input type="text" placeholder="Department"
                                       class="form-control" disabled
                                       id="employee_department" value="">
                                <span class="text-danger">{{ $errors->first('employee_department') }}</span>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-12 mb-1 mt-1">
                                <h5 id="heading_house">House Information</h5>
                            </div>

                            <div class="col-md-2">
                                <label class="required" id="label_house_name" for="house_name">House Name</label>
                                <input name="house_id" id="house_id" type="hidden" required>
                                <input class="form-control" id="house_name" type="text" value=""
                                       placeholder="House/Dormitory Name" readonly>
                            </div>

                            <div class="col-md-2">
                                <label for="floor_number">Floor Number</label>
                                <input class="form-control" id="floor_number" type="text" value=""
                                       placeholder="Floor Number" readonly>
                            </div>

                            <div class="col-md-2">
                                <label id="label_house_size" for="house_size">House Size</label>
                                <input class="form-control" id="house_size" type="text" value=""
                                       placeholder="House Size" readonly>
                            </div>

                            <div class="col-md-3">
                                <label for="building_name">Building Name</label>
                                <input class="form-control" id="building_name" type="text" value=""
                                       placeholder="Building Name" readonly>
                            </div>

                            <div class="col-md-3">
                                <label for="area_name">Area Name</label>
                                <input class="form-control" id="area_name" type="text" value="" placeholder="Area Name"
                                       readonly>
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
                                           autocomplete="off"
                                    />
                                </div>

                            </div>
                            <div class="col-md-3 mt-1">
                                <label>Attachment</label>
                                <input type="file" class="form-control" name="applicaton_doc" id="applicaton_doc">
                            </div>

                            <div class="col-md-6 mt-2">
                                <label class="required" for="handover_reason">Handover Reason</label>
                                <textarea name="handover_reason" id="handover_reason" class="form-control" cols="30"
                                          rows="3" required></textarea>
                            </div>


                            <input type="hidden" name="cparequest_yn" value="Y">
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

                </div>
            </div>

            <div class="card"><!----><!---->
                <div class="card-body"><h4 class="card-title">CPA House Handover Application List</h4><!---->
                    <hr/>
                    <div class="table-responsive">
                        <table id="colonyTable" class="table table-sm datatable mdl-data-table dataTable">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Emp Code</th>
                                    <th>Emp Name</th>
                                    <th>Residential Area</th>
                                    <th>House Type</th>
                                    <th>Building Name</th>
                                    <th>House Name</th>
                                    <th>Department</th>
                                    <th>Reason</th>
                                    <th>Document</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@include('approval.workflowmodal')

@include('approval.workflowselect')

@section('footer-script')
    <!--Load custom script-->
    <script type="text/javascript">

        $("#employee_code").on("change", function () {
            let emp_code = $('#employee_code').val();
            if (emp_code !== undefined && emp_code != '' && emp_code) {
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/load_employee_code_details/' + emp_code,
                    success: function (data) {
                        $('#employee_id').val(data[0].emp_id);
                        $('#employee_name').val(data[0].emp_name);
                        $('#employee_designation').val(data[0].designation);
                        $('#employee_department').val(data[0].department_name);
                        if (data[0].dormitory_yn == 'Y') {
                            $('#label_house_name').text('Dormitory Name');
                            $('#label_house_size').text('Dormitory Size');
                            $('#heading_house').text('Dormitory Information');
                        } else {
                            $('#label_house_name').text('House Name');
                            $('#label_house_size').text('House Size');
                            $('#heading_house').text('House Information');
                        }

                        $('#house_id').val(data[0].house_id);
                        $('#house_name').val(data[0].house_name);
                        $('#floor_number').val(data[0].floor_number);
                        $('#house_size').val(data[0].house_size);
                        $('#building_name').val(data[0].building_name);
                        $('#area_name').val(data[0].colony_name);
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            }
        });

        $(document).ready(function () {

            $('#datetimepicker1').datetimepicker({
                format: 'YYYY-MM-DD',
                // format: 'L',
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

            $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/cpa-hand-over-application-list",
                columns: [
                    {data: 'DT_RowIndex', "name": 'DT_RowIndex'},
                    {data: 'emp_code', name: 'emp_code', searchable: true},
                    {data: 'emp_name', name: 'emp_name', searchable: true},
                    {data: 'colony_name', name: 'colony_name', searchable: true},
                    {data: 'house_type', name: 'house_type', searchable: true},
                    {data: 'building_name', name: 'building_name', searchable: true},
                    {data: 'house', name: 'house', searchable: true},
                    {data: 'emp_department', name: 'emp_department', searchable: true},
                    {data: 'hand_over_reason', name: 'hand_over_reason', searchable: true},
                    {data: 'document', name: 'document', searchable: true},
                ]
            });
        });

    </script>
@endsection


