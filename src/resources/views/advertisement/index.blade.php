@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
	<!--Load custom style link or css-->
    <style>

        table tr{
            height: 10px;
            min-height: 10px;
        }

        .displayNone{
            display: none;
        }
        .grayBackground{
            background-color: lightgoldenrodyellow;
        }
    </style>
@endsection

@section('content')

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
    <!-- form start -->
        <form id="advertisement-register" method="POST"
              @if(isset($data['advMstData'][0]->adv_id))
                  action="{{ route('advertisement.update', ['id' => $data['advMstData'][0]->adv_id]) }}">
                <input name="_method" type="hidden" value="PUT">
              @else
                    action="{{ route('advertisement.store') }}">
              @endif

{{--                <div class="accordion justify-content-center col-sm-12" id="accordionExample">--}}

{{--                </div>--}}

            <div class="card">
                {{ csrf_field() }}
                <div class="card-body" id="houseSelectionPanel"  ><h4 class="card-title">Advertisement Entry</h4>
                    <hr/>
                    <div class="row justify-content-center" >
                        <div class="col-md-11">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="required">Advertisement No.</label>
                                    <input required type="text" value="{{ isset($data['advMstData'][0]->adv_number) ? $data['advMstData'][0]->adv_number : '' }}" {{ isset($data['advMstData'][0]->adv_number) ? 'readonly' : '' }} placeholder="Enter Advertisement No." name="advertisement_no" min="3" class="form-control" id="advertisement_no" >
                                </div>
                                <div class="col-md-3">
                                    <label class="required">File No.</label>
                                    <input required type="text"
                                           value="{{ isset($data['advMstData'][0]->file_no) ? $data['advMstData'][0]->file_no : '' }}" {{ isset($data['advMstData'][0]->file_no) ? 'readonly' : '' }}
                                           placeholder="Enter File No."
                                           name="file_no" min="3" class="form-control" id="file_no" >
                                </div>
                                <div class="col-md-3">
                                    <label class="required">Application Start Date</label>
                                    <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                        <input type="text"  value="{{ isset($data['advMstData'][0]->app_start_date) ? $data['advMstData'][0]->app_start_date : '' }}"
                                               class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker1"
                                               required
                                               id="application_start_date"
                                               name="application_start_date"
                                               autocomplete="off"
                                               {{ isset($data['advMstData'][0]->adv_number) ? 'readonly' : '' }}
                                        />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="required">Application Deadline</label>
                                    <div class="input-group date" id="datetimepicker7" data-target-input="nearest">
                                        <input type="text"  value="{{ isset($data['advMstData'][0]->app_end_date) ? $data['advMstData'][0]->app_end_date : '' }}"
                                               class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker7"
                                               required
                                               id="application_end_date"
                                               name="application_end_date"
                                               autocomplete="off"
                                              {{ isset($data['advMstData'][0]->adv_number) ? 'readonly' : '' }}
                                        />
                                    </div>
                                </div>

                                <div class="col-md-3 mt-1">
                                    <label class="required">Publish Date</label>
                                    <div class="input-group date" id="datetimepicker14" data-target-input="nearest">
                                        <input type="text"  value="{{ isset($data['advMstData'][0]->adv_number) ? $data['advMstData'][0]->adv_date : '' }}"
                                               class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker14"
                                               required
                                               id="publish_date"
                                               name="publish_date"
                                               autocomplete="off"
                                            {{ isset($data['advMstData'][0]->adv_number) ? 'readonly' : '' }}
                                        />
                                    </div>
                                </div>


{{--                                <div class="col-md-3">--}}
{{--                                    <label class="required">Acknowledgment No</label>--}}
{{--                                    <select class="custom-select select2 required" name="ack_id" id="ack_id" required>--}}
{{--                                        <option value="">--Please Select--</option>--}}
{{--                                        @if(isset($data['ackData']))--}}
{{--                                            <option value="{{$data['ackData']->dept_ack_id}}" selected>--}}
{{--                                                {{$data['ackData']->dept_ack_no}}--}}
{{--                                            </option>--}}
{{--                                        @else--}}
{{--                                            @foreach($ack_id as $value)--}}
{{--                                                <option value="{{ $value->dept_ack_id  }}"--}}
{{--                                                > {{ $value->dept_ack_no  }}--}}
{{--                                                </option>--}}
{{--                                            @endforeach--}}
{{--                                        @endif--}}
{{--                                    </select>--}}
{{--                                </div>--}}

                                <div class="col-md-3  mt-1">
                                    <label class="required">House Type</label>
                                    <select class="custom-select select2 required" name="house_type" id="house_type" required>
                                        <option value="">--Please Select--</option>
                                        <option value="abcd" @if(isset($data['advMstData'][0]->adv_number) && $data['house_type'] == 'abcd') selected @endif>A-D Type</option>
                                        <option value="efg" @if(isset($data['advMstData'][0]->adv_number) && $data['house_type'] == 'efg') selected @endif>E,F & G Type</option>
                                    </select>
                                </div>

                                <div class="col-md-3  mt-1">
                                    <label class="required">Department</label>
                                    <select class="custom-select select2 required" name="department" id="department" required>
                                        <option value="">--Please Select--</option>
                                    </select>
                                </div>

                                <div class="col-md-3  mt-1">

                                    <label class="required">Active (Yes/No)</label>
                                    <input class="form-control" type="text" disabled @if(isset($data['advMstData'][0]->active_yn) && $data['advMstData'][0]->active_yn == 'Y') value="Active" @elseif(isset($data['advMstData'][0]->active_yn) && $data['advMstData'][0]->active_yn == 'N') value="In-active" @else value="" @endif >

                                </div>

{{--                                <div class="col-md-3">--}}
{{--                                    <label>Valid From</label>--}}
{{--                                    <div class="input-group date" id="datetimepicker14" data-target-input="nearest">--}}
{{--                                        <input type="text"--}}
{{--                                               class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker14"--}}
{{--                                               required--}}
{{--                                               id="valid_from"--}}
{{--                                               name="valid_from"--}}
{{--                                               autocomplete="off"--}}
{{--                                               @if(isset($data['ackData']))--}}
{{--                                               value="{{date('Y-m-d', strtotime($data['ackData']->dept_req_valid_from))}}"@endif--}}
{{--                                        />--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                                <div class="col-md-3">--}}
{{--                                    <label>Valid To</label>--}}
{{--                                    <div class="input-group date" id="datetimepicker14" data-target-input="nearest">--}}
{{--                                        <input type="text"--}}
{{--                                               class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker14"--}}
{{--                                               required--}}
{{--                                               id="valid_to"--}}
{{--                                               name="valid_to"--}}
{{--                                               autocomplete="off"--}}
{{--                                               @if(isset($data['ackData']))--}}
{{--                                               value="{{date('Y-m-d', strtotime($data['ackData']->dept_req_valid_to))}}"@endif--}}
{{--                                        />--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                                <div class="col-md-3">--}}
{{--                                    <label>Department Name</label>--}}
{{--                                    <div data-target-input="nearest">--}}
{{--                                        <input type="text"--}}
{{--                                               class="form-control"--}}
{{--                                               id="department"--}}
{{--                                               name="department"--}}
{{--                                               autocomplete="off"--}}
{{--                                               @if(isset($data['ackData']))--}}
{{--                                               value="{{$data['ackData']->department_name}}"@endif--}}
{{--                                        />--}}
{{--                                    </div>--}}
{{--                                </div>--}}

                            </div>

                            <div id="available_building_list" class="row my-1">
                                <div class="card col-sm-12" id="availableBuildingPanel">
                                    <div class="card-body">
                                            <span class="col-sm-12 card-title">
                                                <span class="pull-left col-sm-6 bold">
                                                     Available Building list
                                                </span>
                                                <span class="bold float-right mr-2">
                                                    House Selected:
                                                    <span id="house_selected"></span>
                                                </span>
                                            </span>

                                        <div class="accordion justify-content-center col-sm-12" id="accordionExample">
                                            @if(isset($data['advMstData'][0]->adv_id))
                                                {!! $data['htmlForm'] !!}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row my-1">
{{--                                <div class="col-md-4">--}}
{{--                                    <label class="required">Total Available</label>--}}
{{--                                    <input type="text" value="" name="total_avilable" min="3" class="form-control" id="total_avilable" >--}}
{{--                                </div>--}}
                                <div class="col-md-6">
                                    <label>Description</label>
                                    <textarea placeholder="Enter Description" rows="3" wrap="soft" name="description" {{ isset($data['advMstData'][0]->adv_number) ? 'readonly' : '' }}
                                              class="form-control" id="description">{{ isset($data['advMstData'][0]->description) ? $data['advMstData'][0]->description : '' }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label>Description (Bangla)</label>
                                    <textarea placeholder="Enter Description (Bangla)" rows="3" wrap="soft" name="description_bang" {{ isset($data['advMstData'][0]->adv_number) ? 'readonly' : '' }}
                                              class="form-control" id="description_bang">{{ isset($data['advMstData'][0]->description_bng) ? $data['advMstData'][0]->description_bng : '' }}</textarea>
                                </div>
                            </div>
                            @if(isset($data['advMstData'][0]->adv_id) == false)
                                <div class="row my-2">
                                    <input type="hidden" name="colony_id" id="colony_id">
                                    <div class="d-flex justify-content-end col">
                                        <button type="button" id="submitBtnToHideModal" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                            Submit
                                        </button>
                                        <button type="reset" id="reset" class="btn btn btn-outline shadow mb-1 btn-secondary">
                                            Reset
                                        </button>
                                        <button type="submit" id="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary d-none"></button> &nbsp;
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body" id="advertisementPanel"  ><h4 class="card-title">Advertisement list</h4>
                    <hr/>
                    <div class="table-responsive">
                        <table id="advMainTable" class="table table-sm datatable mdl-data-table dataTable">
                            <thead>
                            <tr>
                                <th>Adv. No.</th>
                                <th>Publish Date</th>
                                <th>App. Start</th>
                                <th>App. End</th>
                                <th>Advertised House</th>
                                <th>Department Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        </form>
        <!-- form End -->
    @include('approval.workflowmodal')

    @include('approval.workflowselect')

@endsection

    @section('footer-script')
	<!--Load custom script-->
    <script type="text/javascript" >
        const userRoles = '@php echo json_encode(Auth::user()->roles->pluck('role_key')); @endphp';//alert(userRoles)

        $(document).ready(function() {
        $("#valid_from").prop("disabled", true);
        $("#valid_to").prop("disabled", true);
        $("#department").prop("disabled", true);

        $("#house_type").on("change",function(){
            $("#available_building_list").hide();
            $('#accordionExample').html('');
            var house_type = $('#house_type').val();
            if(house_type)
            {
                if(house_type == 'abcd')
                    $.ajax({
                        type: "GET",
                        url: APP_URL+"/ajax/advertisements-departments/"+house_type,
                        async: false,
                        success: function (data) {
                            $('select[name="department"]').empty();
                            $('select[name="department"]').append('<option value="">'+ '--Please Select--' +'</option>');
                            $.each(data, function(key, value) {
                                $('select[name="department"]').append('<option value="'+ value.department_id +'">'+ value.department_name +'</option>');
                            });
                            $('#department').prop('disabled', false);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                else
                {
                    $('select[name="department"]').empty();
                    $('select[name="department"]').append('<option value="">'+ '--Please Select--' +'</option>');
                    $("#department").prop("disabled", true);
                    // $('#department').trigger('change');
                    loadBuildingList(117);
                }
            }
            else
            {
                $('select[name="department"]').empty();
                $('select[name="department"]').append('<option value="">'+ '--Please Select--' +'</option>');
                $("#department").prop("disabled", true);
            }
        });

        $("#department").on("change",function(){
            var dpt_id = $('#department').val();
            if(dpt_id)
            {
                loadBuildingList(dpt_id);
            }
            else
            {
                alert('Please Select Any Department!')
                $("#available_building_list").hide();
                $('#accordionExample').html('');
            }
        });

        house_select_update();
        @if(isset($data['advMstData'][0]->adv_id) == false)
            // loadBuildingList();
        $("#available_building_list").hide();

        @else
            // $("#ack_id").prop("disabled", true);

            let lowLimit    = $('#application_start_date').val();
            let heighLimit  = ''; //moment(new Date(), "YYYY-MM-DD").format("YYYY-MM-DD");
            //APPLICATION DEADLINE
            maxMinDatePickerUsingDiv('#datetimepicker7',lowLimit,heighLimit);

            heighLimit   = $('#application_end_date').val();
            //PUBLISH DATE
            maxMinDatePickerUsingDiv('#datetimepicker14',lowLimit,heighLimit);

            $("#house_type").prop("disabled", true);
            // $('#house_type').trigger('change');

            @if(isset($data['department']->department_id))
                let dept_id = {{ $data['department']->department_id }};
                let dept_name = {!! json_encode($data['department']->department_name) !!};

                $('#department').append($('<option>', {
                    value: dept_id,
                    text: dept_name
                }));
                $("#department").val(dept_id);
            @endif

            $("#department").prop("disabled", true);

        @endif

        $('#advMainTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 20,
            order: true,
            ajax: APP_URL+'/advertisement-list',
            columns: [
                {data:'adv_number', name:'adv_number'},
                {data: 'adv_date', name: 'adv_date', searchable: true },
                {data: 'app_start_date', name: 'app_start_date'},
                {data: 'app_end_date', name: 'app_end_date'},
                {data: 'house_no', name:'house_no', searchable: true},
                {data: 'department_name', name:'department_name', searchable: true},
                {data: 'status', name:'status'},
                {data: 'action', name: 'Action', searchable: false },
            ]
        });

        $('#datetimepicker14').datetimepicker({
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

        $('#datetimepicker7').datetimepicker({
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

        $('#datetimepicker1').datetimepicker({
               format: 'YYYY-MM-DD',
               // format: 'L',
               maxDate: new Date(),
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

        $('#datetimepicker1').on("change.datetimepicker", function (e) {
            let lowLimit    = $('#application_start_date').val();
            let heighLimit  = ''; //moment(new Date(), "YYYY-MM-DD").format("YYYY-MM-DD");
            maxMinDatePickerUsingDiv('#datetimepicker7',lowLimit,heighLimit);

            heighLimit   = $('#application_end_date').val();
            //PUBLISH DATE
            maxMinDatePickerUsingDiv('#datetimepicker14',lowLimit,heighLimit);
        });
    });

    // $("#ack_id").on("change",function(){
    //     var ack_id = $('#ack_id').val();
    //     if(ack_id){
    //         $.ajax({
    //             type: "GET",
    //             url: APP_URL+"/advertisements-ack-validity/"+ack_id,
    //             success: function (data) {
    //                 // console.log(data);
    //                 $('#valid_from').val(data[0].valid_from);
    //                 $('#valid_to').val(data[0].valid_to);
    //                 $('#department').val(data[0].department_name);
    //                 $("#available_building_list").show();
    //                 loadBuildingList(ack_id);
    //             },
    //             error: function (data) {
    //                 alert('error');
    //             }
    //         });
    //     }
    //     else{
    //         $('#valid_from').val('');
    //         $('#valid_to').val('');
    //         $('#department').val('');
    //         $("#available_building_list").hide();
    //     }
    // });

    // function loadBuildingList(ack_id){
    //     $.ajax({
    //         type: "GET",
    //         url: APP_URL+'/advertisement-datatable-house/'+ack_id,
    //         success: function (data) {
    //             //console.log(data);
    //             $('#accordionExample').html(data);
    //         },
    //         error: function (data) {
    //             alert('error');
    //         }
    //     });
    // }

    function loadBuildingList(dpt_id){
        let house_type = $('#house_type').val();
        $.ajax({
            type: "GET",
            url: APP_URL+'/advertisement-datatable-house/'+dpt_id+'/'+house_type,
            success: function (data) {
                //console.log(data);
                $("#available_building_list").show();
                $('#accordionExample').html(data);
            },
            error: function (data) {
                alert('error');
            }
        });
    }

    function displayPanel(buildinId){
        $('#houseSelectionPanel').show(500);
        if(buildinId !== undefined && buildinId) {
            $.ajax({
                type: "GET",
                url: APP_URL+"/advertisements-list/"+buildinId,
                success: function (data) {
                    //console.log(data);
                    $('#houseList').html(data);
                },
                error: function (data) {
                    alert('error');
                }
            });
        }
    }
    function hidePanel(){
        $('#houseSelectionPanel').hide(500);
    }
    $('.hidePanel').on("click",function(){
        hidePanel();
    });

    $("#submitBtnToHideModal").on('click',function(){
    //document.querySelector("#submitBtnToHideModal").addEventListener("click", function() {
            let result = false;
            swal.fire({
                title: "Please recheck, after submit you can't modify",
                showCancelButton: true,
                confirmButtonText: "Confirm",
                confirmButtonColor: "green",
                cancelButtonColor: "Red"
            }).then(function(result){
                if(result.value){
                    $("#submit").click()
                }
            });
        });
    //});

    //House
    function house_select_update(that=undefined) {
        let selected = $('#house_selected').text();
        if(that)
        {
            if($(that).prop('checked'))
            {
                $('#house_selected').text(++selected);
            }
            else
            {
                $('#house_selected').text(--selected);
            }
        }
        else
        {
            let checked = $('#total_checked').val();
            if(checked)
            {
                $('#house_selected').text(checked);
            }
            else
            {
                $('#house_selected').text('0');
            }
        }
    }

        // START workflow
        //$('#advMainTable tbody').on('click', '.workflowBtn', function () {
        $(document).on('click', '#advMainTable tbody .workflowBtn', function () {
            var data_row = $('#advMainTable').DataTable().row($(this).parents('tr')).data();
            //console.log(data_row.dept_ack_id);
            var row_id = data_row.adv_id;
            //console.log(data_row);
            getFlow(row_id, data_row.prefix);
        });
        function getFlow(row_id, prefix='') {
            let myModal = $('#workflowM');
            //console.log(myModal);
            $('#application_id_flow').val(row_id);
            $(document).find('input#prefix').val(prefix);

            $('#t_name').val('ha_adv_mst');
            $('#c_name').val('adv_id');
            $.ajax({
                type: 'GET',
                url: '/get-approval-list',
                success: function (msg) {
                    $("#flow_id").html(msg.options);
                }
            });
            myModal.modal({show: true});
            return false;
        }
        function getWorkflowAssignDropDownList(targetToshow) {
            $.ajax({
                type: 'GET',
                url: '/get-approval-list',
                success: function (msg) {
                    $(targetToshow).html(msg.options);
                }
            });
        }

        $(document).on('click', '#advMainTable tbody .approveBtn', function () {
            let data_row = $('#advMainTable').DataTable().row($(this).parents('tr')).data();
            let row_id = data_row.adv_id;
            goFlow(row_id);
        });

        function goFlow(row_id) {
            let myModal = $('#status-show');
            let tmp = null;
            let t_name ='ha_adv_mst';
            let c_name ='adv_id';

            $.ajax({
                async: false,
                type: 'GET',
                url: '/get-workflow-id',
                data: {row_id: row_id, t_name: t_name, c_name: c_name},
                success: function (msg) {
                    $("#workflow_id").val(msg);
                    tmp = msg;
                }
            });
            $("#object_id").val(row_id);
            $("#get_url").val(window.location.pathname.slice(1)+'?adv_id='+row_id+'&pop=true');
            $.ajax({
                type: 'GET',
                url: '/approval',
                data: {workflowId: tmp, objectid: 'advertisement'+row_id},
                success: function (msg) {
                    let wrkprc = msg.workflowProcess;
                    if (typeof wrkprc === 'undefined' || wrkprc === null || wrkprc.length === 0) {
                        $('#current_status').hide();
                    } else {
                        $('#current_status').show();
                        $("#step_name").text(msg.workflowProcess[0].workflow_step.workflow);
                        $("#step_approve_by").text('By ' + msg.workflowProcess[0].user.emp_name);
                        $("#step_time").text(msg.workflowProcess[0].insert_date);
                        $("#step_approve_desig").text(msg.workflowProcess[0].user.designation);
                        $("#step_note").text(msg.workflowProcess[0].note);
                    }

                    let steps = "";
                    $('.step-progressbar').html(steps);
                    $.each(msg.progressBarData, function (j) {
                        steps += "<li data-step=" + msg.progressBarData[j].process_step + " class='step-progressbar__item'>" + msg.progressBarData[j].forward_title + "</li>"
                    });
                    $('.step-progressbar').html(steps);

                    $('#prefix').val('advertisement');

                    let content = "";
                    $.each(msg.workflowProcess, function (i) {
                        let note = msg.workflowProcess[i].note;
                        if (note == null) {
                            note = '';
                        }
                        content += "<div class='row d-flex justify-content-between px-1'>" +
                            "<div class='hel'>" +
                            "<span class='ml-1 font-medium'>" +
                            "<h5 class='text-uppercase'>" + msg.workflowProcess[i].workflow_step.forward_title + "</h5>" +
                            "</span>" +
                            "<span>By " + msg.workflowProcess[i].user.emp_name + "</span>" +
                            "</div>" +
                            "<div class='hel'>" +
                            "<span class='btn btn-secondary btn-sm mt-1' style='border-radius: 50px'>" + msg.workflowProcess[i].insert_date + "</span>" +
                            "<br>" +
                            "<span style='margin-left: .3rem'>" + msg.workflowProcess[i].user.designation + "</span>" +
                            "</div>" +
                            "</div>" +
                            "<hr>" +
                            "<span class='m-b-15 d-block border p-1' style='border-radius: 5px'>" + note + "" +
                            "</span><hr>";//msg.workflowProcess[i].insert_date;
                    });

                    $('#content_bdy').html(content);

                    if (msg.current_step && msg.current_step.process_step) {
                        $('.step-progressbar li').each(function (i) {

                            if ($(this).data('step') > msg.current_step.process_step) {
                                $(this).addClass('step-progressbar__item step-progressbar__item--active');
                            } else {
                                $(this).addClass('step-progressbar__item step-progressbar__item--complete');
                            }
                        })
                    } else {
                        $('.step-progressbar li').addClass('step-progressbar__item step-progressbar__item--active');
                    }
                    $("#status_id").html(msg.options);
                    $("#status_id option:last").attr("selected", "selected");

                    if ($.isEmptyObject(msg.next_step)) {
                        // $(".no-permission").css("display", "block");
                        if(msg.is_approved)
                        {
                            $(".no-permission").css("display", "block");
                            $(document).find('#hide_portion').hide();
                        }
                        //else if (JSON.stringify(userRoles).indexOf(msg.current_step.user_role) > -1)
                        else if (JSON.parse(userRoles).includes(msg.current_step.user_role))
                        {
                            // $(document).find('#hide_div, #hide-form-btn').hide();
                            $("#status_id option:selected").removeAttr("selected");
                            $(document).find("#approve_btn").show();
                        }
                        else
                        {
                            $(".no-permission").css("display", "block");
                            $(document).find('#hide_portion').hide();
                        }
                    } else if (JSON.parse(userRoles).includes(msg.current_step.user_role)) {
                        $(document).find('.no-permission').hide();
                        $(document).find('#hide_portion').show();
                    } else {
                        $(".no-permission").css("display", "block");
                        $(document).find('#hide_portion').hide();
                    }

                    $(document).find('#workflow_form').append('<input type="hidden" id="workflow" name="workflow" value="{{\App\Enums\WorkflowIntroduce::advertisementWorkflow}}"><input type="hidden" id="reference_table" name="reference_table" value="{{\App\Enums\WorkflowIntroduce::advertisementWorkflowObjectTable}}"><input type="hidden" id="referance_key" name="referance_key" value="{{\App\Enums\WorkflowIntroduce::advertisementWorkflowObjectKey}}">');
                }

            });
            myModal.modal({show: true});
            return false;
        }

        $("#workflow_assign_form").attr('action', '{{ route('get-approval-post') }}');
        $("#workflow_form").attr('action', '{{ route('approval-post') }}');



        function changeStatusConfirm($adv_id) {

            Swal.fire({
                title: 'Do you want to in-active the advertisement?',
                showDenyButton: true,
                // showCancelButton: true,
                confirmButtonText: 'In-active',
                denyButtonText: `Don't change`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    let adv_id = $adv_id;
                    $.ajax({
                        async: false,
                        type: 'GET',
                        url: '/advertisement/status/'+ adv_id,
                        success: function (message) {
                            Swal.fire(message);
                            $('#advMainTable').DataTable().ajax.reload();
                        }
                    });
                } else if (result.isDenied) {
                    Swal.fire('Advertisement status is not changed!');
                }
            });


        }

        // END workflow
   </script>
@endsection


