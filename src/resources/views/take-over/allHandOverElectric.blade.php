@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
    <style>
        .displayNone {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">

            <form id="allotment-register" method="POST" action="{{ route('electricHandOver.electricStore') }}">
                {{ csrf_field() }}

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title" id="topHeading">Search Here</h4>
                        <hr>
                        <div class="row">

                            <div class="col-md-12">

                                <div class=" shadow p-1 mb-1 bg-white rounded col-sm-12">
                                    <div class=" panel-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Operation Type</label>
                                                <select class="custom-select" name="takeOverType" id="takeOverType">
                                                    @foreach($data['takeOverType'] as $option)
                                                        {!!$option!!}
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{--                                                    <div class="col-md-3" id="handOver">--}}
                                            {{--                                                        <label>Employee Code</label>--}}
                                            {{--                                                        <input type="text" value="" name="emp_code_search" placeholder="Search data using Employee No." class="form-control" id="emp_code_search" />--}}
                                            {{--                                                    </div>--}}

                                            <div class="col-md-3" id="handOver">
                                                <label>Employee Code</label>
                                                <select class="custom-select" name="emp_code_search"
                                                        id="emp_code_search">
                                                    <option value="">---Please Select---</option>
                                                    @foreach($data['empList'] as $emp)
                                                        <option
                                                            value="{{ $emp->emp_id  }}"> {{ $emp->emp_code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>


                                            {{--                                                            --}}
                                            {{--                                                            <div class="col-md-3">--}}
                                            {{--                                                                <label>Employee Code</label>--}}
                                            {{--                                                                <input type="text" value="" name="emp_code_search" placeholder="Search data using Employee Code" class="form-control" id="emp_code_search" />--}}
                                            {{--                                                            </div>--}}
                                            <div class="col-md-2">
                                                <label>&nbsp;</label>
                                                <div class="d-flex col">
                                                    &nbsp;
                                                    <button type="submit" id="submitSearch"
                                                            class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                                        Search
                                                    </button> &nbsp;
                                                </div>
                                            </div>

                                            <div class="col-md-4 my-2">
                                                <div class="alertModify alert-danger alert-block d-none"
                                                     id="searchResult">

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class=" shadow p-3 mb-5 bg-white rounded col-sm-12">
                                    <h4 class="card-title" id="entryHeading">Electrical Handover Entry Form</h4><!---->
                                    <hr>

                                    <div class="row my-1">

                                        <div class="col-md-3">
                                            <label>Employee Code</label>
                                            <input type="text" readonly value="" name="emp_code" class="form-control"
                                                   id="emp_code"/>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Employee Name</label>
                                            <input type="text" readonly value="" name="emp_name" class="form-control"
                                                   id="emp_name"/>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Designation</label>
                                            <input type="text" readonly value="" name="emp_designation"
                                                   class="form-control" id="emp_designation"/>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Department</label>
                                            <input type="text" readonly value="" name="emp_department"
                                                   class="form-control" id="emp_department"/>
                                        </div>

                                    </div>

                                    <div class="row my-1">

                                        <div class="col-md-3">
                                            <label>Section</label>
                                            <input type="text" readonly value="" name="emp_section" class="form-control"
                                                   id="emp_section"/>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Allotted Building</label>
                                            <input type="text" readonly value="" name="emp_allotted_building"
                                                   class="form-control" id="emp_allotted_building"/>
                                        </div>
                                        <div class="col-md-3">
                                            <label id="allotted_flat_label">Allotted Flat</label>
                                            <input type="text" readonly value="" name="emp_allotted_house"
                                                   class="form-control" id="emp_allotted_house"/>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Advertisement NO.</label>
                                            <input type="text" readonly value="" name="emp_allotted_house_adv_no"
                                                   class="form-control" id="emp_allotted_house_adv_no"/>
                                            <input type="hidden" value="" name="emp_application_id" class="form-control"
                                                   id="emp_application_id"/>
                                            <input type="hidden" value="" name="emp_allotted_house_adv_id"
                                                   class="form-control" id="emp_allotted_house_adv_id"/>
                                        </div>

                                    </div>

                                    <div class="row my-1">

                                        <div class="col-md-3">
                                            <label id="flat_approve_label">Flat Approval Date Time</label>
                                            <input type="text" readonly value="" name="emp_house_approval_date"
                                                   class="form-control" id="emp_house_approval_date"/>
                                        </div>

                                        <div class="col-md-3">
                                            <label id="flat_type_label">Flat Type</label>
                                            <input type="text" readonly value="" name="house_type" class="form-control"
                                                   id="house_type"/>
                                        </div>
                                        <div class="col-md-3">
                                            <label id="flat_size_label">Flat Size</label>
                                            <input type="text" readonly value="" name="house_size" class="form-control"
                                                   id="house_size"/>
                                        </div>

                                        <div class="col-md-3">
                                            <label id="flat_floor_label">Flat Floor</label>
                                            <input type="text" readonly value="" name="house_floor" class="form-control"
                                                   id="house_floor"/>
                                        </div>

                                    </div>

                                    <div class="row my-1">

                                        <div class="col-md-3">
                                            <label>Colony</label>
                                            <input type="text" readonly value="" name="colony" class="form-control"
                                                   id="colony"/>
                                        </div>
                                        <div class="col-md-9">
                                            <label id="flat_details_label">Flat Details</label>
                                            <textarea placeholder="Enter House Details" rows="4" wrap="soft"
                                                      name="house_details" readonly
                                                      class="form-control" id="house_details"></textarea>
                                        </div>


                                    </div>

                                    <div class="row my-1">

                                        <div class="col-md-3">
                                            <label class="required">Electrical Engineer</label>
                                            <input type="text" class="form-control" id="electrical_engg" readonly
                                                   name="electrical_engq" value="{{$user->user()->user_name}} "
                                                   required>
                                        </div>
                                        <input type="hidden" class="form-control " id="electrical_eng"
                                               name="electrical_eng" value="{{$user->user()->emp_id}} " required>

                                        <div class="col-md-9">
                                            <label>Electrical Engineer's Comment</label>
                                            <textarea placeholder="Enter Electrical Engineer's Comment" rows="4"
                                                      wrap="soft" name="electrical_eng_comment"
                                                      class="form-control" id="electrical_eng_comment"></textarea>
                                        </div>
                                    </div>

                                    <div class="row my-2">
                                        <div class="col-md-4">
                                            <label id="remove_req" style="display: none">Take Over Date (By Allottee)</label>
                                            <label id="show_req" class="required ">Take Over Date (By Allottee)</label>
                                            <div class="input-group date" id="datetimepicker2"
                                                 data-target-input="nearest">
                                                <input type="text" value=""
                                                       class="form-control datetimepicker-input"
                                                       data-toggle="datetimepicker" data-target="#datetimepicker2"
                                                       readonly
                                                       id="take_over_date"
                                                       name="take_over_date"
                                                       autocomplete="off"
                                                />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="required">Hand Over Date (By Allottee)</label>
                                            <div class="input-group date" id="datetimepicker1"
                                                 data-target-input="nearest">
                                                <input type="text" value="{{date('Y-m-d')}}"
                                                       class="form-control datetimepicker-input"
                                                       data-toggle="datetimepicker" data-target="#datetimepicker1"
                                                       required
                                                       id="hand_over_date"
                                                       name="hand_over_date"
                                                       autocomplete="off"
                                                />
                                            </div>
                                            <input type="hidden" value="" name="allot_letter_id" class="form-control"
                                                   id="allot_letter_id"/>
                                            <input type="hidden" readonly value="" name="emp_id" class="form-control"
                                                   id="emp_id"/>
                                            <input type="hidden" readonly value="" name="house_id" class="form-control" id="house_id" />
                                            <input type="hidden" readonly value="" name="old_yn" class="form-control" id="old_yn" />


                                        </div>
                                        <div class="col-md-4 ">
                                            <label class="">Attachment</label>
                                            <div class="custom-file b-form-file form-group">
                                                <input type="file" id="elecAttachment" name="elecAttachment"
                                                       class="custom-file-input"/>
                                                <label for="elecAttachment" data-browse="Browse"
                                                       class="custom-file-label required">
                                                    Attached File
                                                </label>
                                            </div>

                                        </div>

                                        <div class="col-md-6">
                                            <label>Remarks</label>
                                            <textarea placeholder="Enter Remarks" rows="4" wrap="soft" name="remarks"
                                                      class="form-control" id="remarks"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 ">
                                        <label>&nbsp;</label>
                                        <div class="d-flex justify-content-end col">
                                            <button type="submit" id="submit"
                                                    class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                                Submit
                                            </button> &nbsp;
                                            <button type="reset" id="reset"
                                                    class="btn btn btn-outline shadow mb-1 btn-secondary">
                                                Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
        </form>
        <div class="card"><!----><!---->
            <div class="card-body"><h4 class="card-title" id="listHeading">Employee List Handover To Electrical
                    Department</h4><!---->

                {{--                    <a target="_blank" class="btn btn-primary mr-1" href="/report/render?xdo=/~weblogic/HAS/RPT_COLONY_LIST.xdo&type=pdf&filename=colony_list">--}}
                {{--                        PDF--}}
                {{--                    </a>--}}
                <hr/>
                <div class="table-responsive">
                    <table id="allotmentLetterTable" class="table table-sm datatable mdl-data-table dataTable">
                        <thead>
                        <tr>
                            <th>Employee Code</th>
                            <th>Employee Name</th>
                            <th>Allotted Letter No.</th>
                            <th>Allotted Letter Date</th>
                            <th>Takeover Date</th>
                            <th>Handover Date</th>
                            {{--                                <th>Status</th>--}}
                            <th>Takeover/Handover Letter</th>
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
    <!--Load custom script-->
    <script type="text/javascript">
        $(document).ready(function () {
            // select('#electrical_engg', '/ajax/electrical-engineers', ajaxParams, employeeOptions);
            // select('#civil_engg', '/ajax/civil-engineers', ajaxParams, employeeOptions);

            let entryHeading = 'Electrical Handover Entry Form';
            let listHeading = 'Employee List Handover To Electrical Department';
            $('#entryHeading').html(entryHeading);
            $('#listHeading').html(listHeading);

            $(document).on("click", "#submitSearch", function (event) {
                var OperationType = $('#takeOverType').val();
                setEmployeeInformation(event, OperationType);
            });

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

            $('#datetimepicker2').datetimepicker({
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

            $('#allotmentLetterTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/electric-hand-over-list",
                columns: [
                    {data: 'emp_code', name: 'emp_code', searchable: true},
                    {data: 'emp_name', name: 'emp_name', searchable: true},
                    //{data: 'colony_type.colony_type', name:'colony_type.colony_type',searchable: false },
                    {data: 'allot_letter_no', name: 'allot_letter_no', searchable: true},
                    {data: 'allot_letter_date', name: 'allot_letter_date', searchable: true},
                    // {data:'delivery_yn', name:'delivery_yn',searchable: true },
                    {data: 'take_over_date', name: 'take_over_date', searchable: true},
                    {data: 'hand_over_date', name: 'hand_over_date', searchable: true},
                    // {data:'print_btn', name:'Print',searchable: false },
                    {data: 'action', name: 'Action', searchable: false},
                ]
            });

            function setEmployeeInformation(evt, OperationType) {
                evt.preventDefault();
                let allotmentNoOrEmpCode = '';
                let urlDetails = '';
                let alertTypeEmpCode = '<strong>Please Select Employee Code</strong>';
                let alertNotFound = '<strong>No data Found</strong>';
                allotmentNoOrEmpCode = $('#emp_code_search').val();
                if ($('#emp_code_search').val().length <= 0) {
                    $('#searchResult').removeClass('d-none');
                    $('#searchResult').html(alertTypeEmpCode);
                    // alert("Please Type Employee Code");
                    $('#emp_code_search').focus();
                    setTimeout(function () {
                        $('#searchResult').addClass('d-none');
                    }, 3000);
                    return false;
                }
                urlDetails = APP_URL + '/ajax/emp-code-wise-employee-details/' + allotmentNoOrEmpCode;

                $.ajax({
                    type: "GET",
                    url: urlDetails,
                    success: function (data) {

                        if (data.employeeInformation) {
                            $('#emp_id').val(data.employeeInformation.emp_id);
                            $('#emp_code').val(data.employeeInformation.emp_code);
                            $('#emp_name').val(data.employeeInformation.emp_name);
                            $('#emp_designation').val(data.employeeInformation.designation);
                            $('#emp_department').val(data.employeeInformation.department);
                            $('#emp_section').val(data.employeeInformation.section);
                            $('#emp_allotted_building').val(data.employeeInformation.building_name);
                            $('#emp_allotted_house_adv_no').val(data.employeeInformation.adv_number);
                            $('#emp_allotted_house').val(data.employeeInformation.house_name);
                            $('#emp_house_approval_date').val(data.employeeInformation.approval_date);
                            $('#emp_application_id').val(data.employeeInformation.application_id);
                            $('#emp_allotted_house_adv_id').val(data.employeeInformation.advertisement_id);
                            $('#allot_letter_id').val(data.employeeInformation.allot_letter_id);
                            $('#take_over_date').val(data.employeeInformation.take_over_date);

                            $('#house_type').val(data.employeeInformation.house_type);
                            $('#house_size').val(data.employeeInformation.house_size);
                            $('#house_floor').val(data.employeeInformation.floor_number);
                            $('#colony').val(data.employeeInformation.colony_name);

                            $('#house_details').val(data.employeeInformation.house_details);
                            $('#sanitary_fittings').val(data.employeeInformation.sanitary_fittings);
                            $('#electrical_fittings').val(data.employeeInformation.electrical_fittings);
                            $('#house_id').val(data.employeeInformation.house_id);
                            $('#old_yn').val(data.employeeInformation.old_entry_yn);

                            $('#searchResult').html('');
                            $('#searchResult').addClass('d-none');

                            if(data.employeeInformation.old_entry_yn == 'N') {
                                $('#remove_req').css('display', 'none')
                                $('#show_req').css('display', 'block')
                            }else{
                                $('#show_req').css('display', 'none')
                                $('#remove_req').css('display', 'block')
                            }

                            //If dormitory, labels change
                            if (data.employeeInformation.dormitory_yn == 'Y') {
                                $('#allotted_flat_label').text('ALLOTTED DORMITORY');
                                $('#flat_approve_label').text('DORMITORY APPROVAL DATE TIME');
                                $('#flat_type_label').text('DORMITORY TYPE');
                                $('#flat_size_label').text('DORMITORY SIZE');
                                $('#flat_floor_label').text('DORMITORY FLOOR');
                                $('#flat_details_label').text('DORMITORY DETAILS');
                            } else {
                                $('#allotted_flat_label').text('ALLOTTED FLAT');
                                $('#flat_approve_label').text('FLAT APPROVAL DATE TIME');
                                $('#flat_type_label').text('FLAT TYPE');
                                $('#flat_size_label').text('FLAT SIZE');
                                $('#flat_floor_label').text('FLAT FLOOR');
                                $('#flat_details_label').text('FLAT DETAILS');
                            }

                        } else {
                            $('#emp_id').val('');
                            $('#emp_code').val('');
                            $('#emp_name').val('');
                            $('#emp_designation').val('');
                            $('#emp_department').val();
                            $('#emp_section').val();
                            $('#emp_allotted_building').val();
                            $('#emp_allotted_house_adv_no').val();
                            $('#emp_allotted_house').val();
                            $('#emp_house_approval_date').val();
                            $('#emp_application_id').val();
                            $('#emp_allotted_house_adv_id').val();
                            $('#allot_letter_id').val();
                            $('#take_over_date').val();

                            $('#house_type').val();
                            $('#house_size').val();
                            $('#house_floor').val();
                            $('#colony').val();

                            $('#house_details').val();
                            $('#sanitary_fittings').val();
                            $('#electrical_fittings').val();

                            $('#searchResult').html(alertNotFound);
                            $('#searchResult').removeClass('d-none');
                            $('#emp_code_search').focus();
                            setTimeout(function () {
                                $('#searchResult').addClass('d-none');
                            }, 3000);

                        }

                    },
                    error: function (err) {
                        alert('error', err);
                    }
                });
            }


        });

    </script>
@endsection


