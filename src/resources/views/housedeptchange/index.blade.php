@extends('layouts.default')

@section('title')
    House Department Change
@endsection

@section('header-style')
    <!--Load custom style link or css-->
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body"><h4 class="card-title">House Department Change</h4>
                    <hr>
                    <form id="house-form" method="POST" enctype="multipart/form-data"
                          action="{{ route('house-dept-change.store') }}">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-3">
                                <label class="required">Change By</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="dep_emp_change"
                                               onchange="house_change()"
                                               id="dep_emp_change_d" value='D'
                                               checked>
                                        <label class="form-check-label" for="dep_emp_change_d">Previous
                                            Department</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input " type="radio" name="dep_emp_change"
                                               onchange="house_change()"
                                               id="dep_emp_change_e" value='E'>
                                        <label class="form-check-label" for="dep_emp_change_e">Employee</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('dep_emp_change') }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="required" for="department">Department</label>
                                <select class="custom-select select2" name="prev_dept" id="department" required>
                                    <option value="">--Please Select--</option>
                                    @foreach($departments as $department)
                                        <option
                                            value="{{ $department->department_id  }}"> {{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('acknowledgment') }}</span>
                            </div>
                            <div class="col-md-3">
                                <label class="required" for="house">House</label>
                                <select class="form-control select2" name="house[]" id="house" multiple>
                                    <option value="">--Please Select--</option>
                                </select>
                                <span class="text-danger">{{ $errors->first('house') }}</span>
                            </div>
                            <div class="col-md-3" style="display: none">
                                <labe>Employee</labe>
                                <select class="form-control" name="emp_id" id="emp_id" style="width: 100%">

                                </select>
                            </div>
                            <div class="col-md-3" style="display: none">
                                <label>Designation</label>
                                <input type="text" class="form-control"
                                       name="designation"
                                       id="designation" readonly
                                       placeholder="Designation">
                            </div>
                            <div class="col-md-3" style="display: none">
                                <label>Department</label>
                                <input type="text" class="form-control"
                                       name="department"
                                       id="department_id" readonly
                                       placeholder="Department">
                            </div>
                            <div class="col-md-3 mt-1" style="display: none">
                                <label>Grade</label>
                                <input type="text" class="form-control"
                                       name="grade"
                                       id="grade" readonly
                                       placeholder="Grade">
                            </div>
                            <div class="col-md-3 mt-1" style="display: none">
                                <label>Residential Area</label>
                                <input class="form-control"
                                       name="res_area" id="res_area" readonly
                                       placeholder="Enter Residential Area">
                            </div>
                            <div class="col-md-3 mt-1" style="display: none">
                                <label>Building</label>
                                <input class="form-control"
                                       name="building" id="building" readonly
                                       placeholder="Enter Building">
                            </div>
                            <div class="col-md-3 mt-1" style="display: none">
                                <label>Building No</label>
                                <input class="form-control"
                                       name="building_no" id="building_no" readonly
                                       placeholder="Enter Building">
                            </div>
                            <div class="col-md-3 mt-1" style="display: none">
                                <label>Road No</label>
                                <input class="form-control"
                                       name="building_road_no" id="building_road_no" readonly
                                       placeholder="Enter Building Road No">
                            </div>
                            <div class="col-md-3 mt-1" style="display: none">
                                <label>House Type</label>
                                <input class="form-control"
                                       name="house_type" id="house_type" readonly
                                       placeholder="Enter House Type">
                            </div>
                            <div class="col-md-3 mt-1" style="display: none">
                                <label>Dormatory</label>
                                <input class="form-control"
                                       name="dormatory_yn" id="dormatory_yn" readonly
                                       placeholder="Enter Domatory">
                            </div>
                            <div class="col-md-3 mt-1" style="display: none">
                                <label> Flat/ Seat No</label>
                                <input class="form-control"
                                       name="house_id" id="house_id" readonly
                                       placeholder="Enter House">
                            </div>
                            <div class="col-md-3 mt-1">
                                <label class="required" for="house">Changed Department</label>
                                <select class="custom-select select2" name="chng_dept" id="chng_dept" required>
                                    <option value="">--Please Select--</option>
                                    @foreach($departments as $department)
                                        <option
                                            value="{{ $department->department_id  }}"> {{ $department->department_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('chng_dept') }}</span>
                            </div>
                            <div class="col-md-3 mt-1">
                                <label class="required" for="house">Changed Acknowledgment</label>
                                <select class="custom-select select2" name="chng_ack" id="chng_ack" required>
                                    <option value="">--Please Select--</option>
                                </select>
                                <span class="text-danger">{{ $errors->first('chng_ack') }}</span>
                            </div>
                            <div class="col-md-3 mt-1">
                                <label class="required">Chnage Date</label>
                                <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                    <input type="text" value="{{date('Y-m-d')}}"
                                           class="form-control datetimepicker-input" data-toggle="datetimepicker"
                                           data-target="#datetimepicker1"
                                           required
                                           id="change_date"
                                           name="change_date"
                                           autocomplete="off"
                                    />
                                </div>

                            </div>

                            <div class="col-md-3 mt-1">
                                <label>Attachment</label>
                                <input type="file" class="form-control" name="change_doc" id="change_doc">
                            </div>

                            <div class="col-md-6 mt-1">
                                <label class="required" for="reason">Reason</label>
                                <textarea placeholder="Enter Reason for Department Change" rows="3" wrap="soft"
                                          name="reason"
                                          class="form-control" id="reason" required></textarea>
                                <span class="text-danger">{{ $errors->first('reason') }}</span>
                            </div>

                            {{--                            <input type="hidden" name="emp_id" id="emp_id">--}}
                            <input type="hidden" name="dept_id" id="dept_id">
                            <input type="hidden" name="houses_id" id="houses_id">
                            <input type="hidden" name="house_type_id" id="house_type_id">
                            <input type="hidden" name="buildings_id" id="buildings_id">
                            <input type="hidden" name="colonys_id" id="colonys_id">


                        </div>
                        <div class="col-md-12">
                            <div class="d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn btn-dark shadow mb-1 btn-secondary">
                                    Change
                                </button>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
            <div class="card"><!----><!---->
                <div class="card-body"><h4 class="card-title">House Department Change List</h4><!---->
                    <hr/>
                    <div class="table-responsive">
                        <table id="colonyTable" class="table table-sm datatable mdl-data-table dataTable">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>House Name</th>
                                <th>House Code</th>
                                <th>Seat No</th>
                                <th>Building Name</th>
                                <th>Residential Area</th>
                                <th>Previous Department</th>
                                <th>Current Department</th>
                                {{--                                <th>Status</th>--}}
                                {{--                                <th>Action</th>--}}
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
        function house_change() {
            if ($('#dep_emp_change_e').is(':checked')) {
                $('#emp_id').parent('.col-md-3').css({"display": "block"});
                $('#department_id').parent('.col-md-3').css({"display": "block"});
                $('#house_id').parent('.col-md-3').css({"display": "block"});
                $('#res_area').parent('.col-md-3').css({"display": "block"});
                $('#building').parent('.col-md-3').css({"display": "block"});
                $('#house_type').parent('.col-md-3').css({"display": "block"});
                $('#dormatory_yn').parent('.col-md-3').css({"display": "block"});
                $('#grade').parent('.col-md-3').css({"display": "block"});
                $('#designation').parent('.col-md-3').css({"display": "block"});
                $('#building_no').parent('.col-md-3').css({"display": "block"});
                $('#building_road_no').parent('.col-md-3').css({"display": "block"});

                $('#department').parent('.col-md-3').css({"display": "none"});
                $('#house').parent('.col-md-3').css({"display": "none"});
                $("#department").prop('required', false);

            } else {
                $('#emp_id').parent('.col-md-3').css({"display": "none"});
                $('#department_id').parent('.col-md-3').css({"display": "none"});
                $('#house_id').parent('.col-md-3').css({"display": "none"});
                $('#res_area').parent('.col-md-3').css({"display": "none"});
                $('#building').parent('.col-md-3').css({"display": "none"});
                $('#house_type').parent('.col-md-3').css({"display": "none"});
                $('#dormatory_yn').parent('.col-md-3').css({"display": "none"});
                $('#grade').parent('.col-md-3').css({"display": "none"});
                $('#designation').parent('.col-md-3').css({"display": "none"});
                $('#building_no').parent('.col-md-3').css({"display": "none"});
                $('#building_road_no').parent('.col-md-3').css({"display": "none"});

                $('#department').parent('.col-md-3').css({"display": "block"});
                $('#house').parent('.col-md-3').css({"display": "block"});
                $("#department").prop('required', true);
            }

        }

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
                ajax: APP_URL + "/house-change-list",
                columns: [
                    {data: 'DT_RowIndex', "name": 'DT_RowIndex'},
                    {data: 'house_name', name: 'house_name', searchable: true},
                    {data: 'house_code', name: 'house_code', searchable: true},
                    {data: 'dormitory_yn', name: 'dormitory_yn', searchable: true},
                    {data: 'building_name', name: 'building_name', searchable: true},
                    {data: 'colony_name', name: 'colony_name', searchable: true},
                    {data: 'prev_department', name: 'prev_department', searchable: true},
                    {data: 'current_department', name: 'current_department', searchable: true},

                ]
            });

            $(document).on('change', '#department', function () {
                let dptId = $(this).val();

                if (dptId !== undefined && dptId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL + "/ajax/housedetails-by-dpt/" + dptId,
                        success: function (data) {
                            $('select[name="house[]"]').empty();
                            $('select[name="house[]"]').append('<option value="">' + '--Please Select--' + '</option>');
                            $.each(data, function (key, value) {
                                if (value.dormitory_yn == 'Y') {
                                    $('select[name="house[]"]').append('<option value="' + value.house_id + '">' + value.colonylist.colony_name + '(Colony)-' + value.buildinglist.building_name + '(Building)-' + value.house_name + '- ' + value.house_code + '</option>');

                                } else {
                                    $('select[name="house[]"]').append('<option value="' + value.house_id + '">' + value.colonylist.colony_name + '(Colony)-' + value.buildinglist.building_name + '(Building)-' + value.house_name + '</option>');
                                }
                            });
                            $('#house').addClass('select2', true);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                } else {
                    alert('Error Occurred!');
                }


            });
            $('#emp_id').select2({
                placeholder: "Select",
                allowClear: true,
                ajax: {
                    url: APP_URL + '/ajax/employees',
                    data: function (params) {
                        if (params.term) {
                            if (params.term.trim().length < 1) {
                                return false;
                            }
                        } else {
                            return false;
                        }

                        return params;
                    },
                    dataType: 'json',
                    processResults: function (data) {
                        var formattedResults = $.map(data, function (obj, idx) {
                            obj.id = obj.emp_id;
                            obj.text = obj.emp_code + ' : ' + obj.emp_name;
                            return obj;
                        });
                        return {
                            results: formattedResults,
                        };
                    }
                }
            });


            $(document).on('change', '#chng_dept', function () {
                let dptId = $(this).val();

                if (dptId !== undefined && dptId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL + "/ajax/ackdetails-by-dpt/" + dptId,
                        success: function (data) {
                            // alert(data)
                            $('select[name="chng_ack"]').empty();
                            $('select[name="chng_ack"]').append('<option value="">' + '--Please Select--' + '</option>');
                            $.each(data, function (key, value) {
                                $('select[name="chng_ack"]').append('<option value="' + value.dept_ack_id + '">' + value.dept_ack_no + '</option>');
                            });
                            $('#chng_ack').addClass('select2', true);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                } else {
                    alert('Error Occurred!');
                }
            });
            $(document).on('change', '#emp_id', function () {
                let empId = $(this).val();

                if (empId !== undefined && empId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL + "/house-by-emp/" + empId,
                        success: function (data) {

                            $('#designation').val(data['designation']);
                            $('#department_id').val(data['department_name']);
                            $('#grade').val(data['actual_grade_id']);
                            $('#res_area').val(data['colony_name']);

                            $('#building').val(data['building_name']);
                            $('#building_no').val(data['building_no']);
                            $('#building_road_no').val(data['building_road_no']);
                            $('#house_type').val(data['house_type']);
                            $('#dormatory_yn').val(data['dormitory']);
                            $('#house_id').val(data['house_name']);
                            $('#houses_id').val(data['house_id']);
                            $('#colonys_id').val(data['colony_id']);
                            $('#buildings_id').val(data['building_id']);
                            $('#house_type_id').val(data['house_type_id']);
                            $('#dept_id').val(data['department_id']);

                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                } else {
                    $('#designation').val('');
                    $('#department_id').val('');
                    $('#grade').val('');
                    $('#res_area').val('');
                    $('#building').val('');
                    $('#building_no').val('');
                    $('#building_road_no').val('');
                    $('#house_type').val('');
                    $('#dormatory_yn').val('');
                    $('#house_id').val('');
                    $('#houses_id').val('');
                    $('#colonys_id').val('');
                    $('#buildings_id').val('');
                    $('#house_type_id').val('');
                }
            });
        });
    </script>
@endsection
