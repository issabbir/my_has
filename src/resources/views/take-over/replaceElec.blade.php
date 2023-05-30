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

            <form id="allotment-register" method="POST" action="{{ route('replaceTakeOver.elecStore') }}">
                {{ csrf_field() }}

                <div class="card"><!----><!---->
                    <div class="card-body">
                        <h4 class="card-title" id="topHeading">Search Here <!-- / Employee Code --></h4>

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
                                            <div class="col-md-3" id="takeOver">
                                                <label>Allotment Letter Order No.</label>
                                                <!-- Enabling input field should work! -->
                                                {{--<input type="text" value="" name="allotment_no_search" placeholder="Search data using Allotment letter No." class="form-control" id="allotment_no_search" />--}}
                                                <select class="custom-select select2 form-control"
                                                        id="allotment_no_search" name="allotment_no_search"
                                                        required></select>
                                            </div>
                                            <div class="col-md-3 displayNone" id="handOver">
                                                <label>Employee Code</label>
                                                <input type="text" value="" name="emp_code_search"
                                                       placeholder="Search data using Employee No." class="form-control"
                                                       id="emp_code_search"/>
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

                                        </div>
                                    </div>
                                </div>

                                <div class=" shadow p-3 mb-5 bg-white rounded col-sm-12">
                                    <h4 class="card-title" id="entryHeading">Takeover Entry</h4><!---->
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
                                            <label>Allotted Flat</label>
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
                                            <label>Flat Approval Date Time</label>
                                            <input type="text" readonly value="" name="emp_house_approval_date"
                                                   class="form-control" id="emp_house_approval_date"/>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Flat Type</label>
                                            <input type="text" readonly value="" name="house_type" class="form-control"
                                                   id="house_type"/>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Flat Size</label>
                                            <input type="text" readonly value="" name="house_size" class="form-control"
                                                   id="house_size"/>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Flat Floor</label>
                                            <input type="text" readonly value="" name="house_floor" class="form-control"
                                                   id="house_floor"/>
                                        </div>

                                    </div>

                                    <div class="row my-1">
                                        <div class="col-md-3">
                                            <label>Water Tap</label>
                                            <input type="text"   name="water_tap" class="form-control"
                                                   id="water_tap"/>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Colony</label>
                                            <input type="text" readonly value="" name="colony" class="form-control"
                                                   id="colony"/>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Flat Details</label>
                                            <textarea placeholder="Enter House Details" rows="2" wrap="soft"
                                                      name="house_details"
                                                      class="form-control" id="house_details"></textarea>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Electrical Fittings</label>
                                            <textarea placeholder="Enter Electrical Fittings" rows="2" wrap="soft" name="electrical_fittings"
                                                      class="form-control" id="electrical_fittings"></textarea>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Electrical Engineer's Comment</label>
                                            <textarea placeholder="Enter Electrical Engineer's Comment" rows="2" wrap="soft" name="electrical_eng_comment"
                                                      class="form-control" id="electrical_eng_comment"></textarea>
                                        </div>


                                        <div class="col-md-3 mt-1">
                                            <label>Electric Engineer</label>

                                            <input type="text" class=" form-control " readonly name=""
                                                   value="{{$data['loggedUser']->user_name}}">
                                            <input type="hidden" id="elec_engg" name="elec_engg"
                                                   value="{{$data['loggedUser']->emp_id}}">
                                        </div>


                                            <div class="col-md-6 mt-1">
                                                <label>Remarks</label>
                                                <textarea placeholder="Enter Remarks" rows="2" wrap="soft"
                                                          name="remarks"
                                                          class="form-control" id="remarks"></textarea>
                                            </div>

                                            <div class="col-md-3 mt-1">
                                                <label class="required" id="takeOverHandOverDateHeading">Take Over
                                                    Date</label>
                                                <div class="input-group date" id="datetimepicker1"
                                                     data-target-input="nearest">
                                                    <input type="text" value="{{date('Y-m-d')}}"
                                                           class="form-control datetimepicker-input"
                                                           data-toggle="datetimepicker" data-target="#datetimepicker1"
                                                           required
                                                           id="take_over_date"
                                                           name="take_over_date"
                                                           autocomplete="off"
                                                    />
                                                </div>
                                                <input type="hidden" value="" name="allot_letter_id"
                                                       class="form-control" id="allot_letter_id"/>
                                                <input type="hidden" readonly value="" name="emp_id"
                                                       class="form-control" id="emp_id"/>

                                            </div>

                                            <div class="col-md-12">
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
                    </div>
            </form>
            <div class="card"><!----><!---->
                <div class="card-body"><h4 class="card-title" id="listHeading">Takeover Employee List</h4><!---->

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
                                <th>Allotted Letter Order No.</th>
                                <th>Allotted Letter Order Date</th>
                                <th>Takeover Date</th>
                                <th>Handover Date</th>
                                {{--                                <th>Status</th>--}}
                                {{--                                <th>Print</th>--}}
                                <th>Takeover/Handover Letter</th>
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
        select('#electrical_eng', '/ajax/electrical-engineers', ajaxParams, employeeOptions);
        select('#civil_eng', '/ajax/civil-engineers', ajaxParams, employeeOptions);

        function replaceAllotLetterOptions(data) {
            var formattedResults = $.map(data, function (obj, idx) {
                obj.id = obj.allot_letter_no;
                obj.text = obj.allot_letter_no;
                return obj;
            });
            return {
                results: formattedResults,
            };
        }

        $(document).ready(function () {
            select('#allotment_no_search', '/ajax/new-replace-allot-elec-letters', ajaxParams, replaceAllotLetterOptions);
            //searchEmployeeAndAllotmentWithAllotmentLetter('#allotment_no_search', setEmployeeInformation);

            $(document).on("click", "#submitSearch", function (event) {
                var OperationType = $('#takeOverType').val();
                setEmployeeInformation(event, OperationType);
            });

            $(document).on("change", "#takeOverType", function (event) {
                if ($(this).val() == 1) {
                    let entryHeading = 'Takeover Entry';
                    let listHeading = 'Takeover Employee List';
                    let takeOverHandOverDateHeading = 'Take Over Date';

                    $('#handOver').hide(1);
                    $('#takeOver').show(1);
                    $('#entryHeading').html(entryHeading);
                    $('#listHeading').html(listHeading);
                    $('#takeOverHandOverDateHeading').html(takeOverHandOverDateHeading);
                } else {
                    let entryHeading = 'Handover Entry';
                    let listHeading = 'Handover Employee List';
                    let takeOverHandOverDateHeading = 'Hand Over Date';

                    $('#takeOver').hide(1);
                    $('#handOver').show(1);
                    $('#entryHeading').html(entryHeading);
                    $('#listHeading').html(listHeading);
                    $('#takeOverHandOverDateHeading').html(takeOverHandOverDateHeading);
                }
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

            $('#allotmentLetterTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/replace-take-over-elec-list",
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
                if (OperationType == 1) {
                    allotmentNoOrEmpCode = $('#allotment_no_search').val();
                    if ($('#allotment_no_search').val().length <= 0) {
                        alert("Please Type Allotment Letter");
                        return false;
                    }
                    urlDetails = APP_URL + '/ajax/allotted-letter-wise-replaced-employee-details/' + allotmentNoOrEmpCode;
                } else {
                    allotmentNoOrEmpCode = $('#emp_code_search').val();
                    if ($('#emp_code_search').val().length <= 0) {
                        alert("Please Type Employee Code");
                        return false;
                    }
                    urlDetails = APP_URL + '/ajax/emp-code-wise-replace-employee-details/' + allotmentNoOrEmpCode;
                }

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

                            $('#house_type').val(data.employeeInformation.house_type);
                            $('#house_size').val(data.employeeInformation.house_size);
                            $('#house_floor').val(data.employeeInformation.floor_number);
                            $('#colony').val(data.employeeInformation.colony_name);

                            $('#house_details').val(data.employeeInformation.house_details);
                            $('#sanitary_fittings').val(data.employeeInformation.sanitary_fittings);
                            $('#electrical_fittings').val(data.employeeInformation.electrical_fittings);
                            $('#water_tap').val(data.employeeInformation.water_tap);

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

                            $('#house_type').val();
                            $('#house_size').val();
                            $('#house_floor').val();
                            $('#colony').val();

                            $('#house_details').val();
                            $('#sanitary_fittings').val();
                            $('#electrical_fittings').val();
                            $('#water_tap').val();
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


