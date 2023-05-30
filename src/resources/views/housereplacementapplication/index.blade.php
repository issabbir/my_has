@extends('layouts.default')

@section('title')
    House Allotment
@endsection

@section('header-style')
    <!--Load custom style link or css-->
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
                <div class="card-body"><h4 class="card-title">House Replacement Application</h4>
                    <hr>
                    @include('housereplacementapplication.form')
                </div>
            </div>
            <div class="card">
                <div class="card-body"><h4 class="card-title">House Replacement Applications</h4><!---->
                    <hr>
                    <div class="table-responsive">
                        <table  class="table table-sm datatable mdl-data-table dataTable" id="house-replacement-applications">
                            <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>Current House</th>
                                <th>Action</th>
                                <th style="display:none;">
                            </tr>
                            </thead>
                            <tbody></tbody>
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
    <script type="text/javascript">
        let userRoles = '@php echo json_encode(Auth::user()->roles->pluck('role_key')); @endphp';

        $('#house-replacement-applications tbody').on('click', '.workflowBtn', function () {
            var data_row = $('#house-replacement-applications').DataTable().row($(this).parents('tr')).data();
            var row_id = data_row.replace_app_id;
            getFlow(row_id);
        });

        function getFlow(row_id) {
            let myModal = $('#workflowM');
            $('#application_id_flow').val(row_id);
            $('#t_name').val('replacement_application');
            $('#c_name').val('replace_app_id');
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

        $('#house-replacement-applications tbody').on('click', '.approveBtn', function () {
            var data_row = $('#house-replacement-applications').DataTable().row($(this).parents('tr')).data();
            var row_id = data_row.replace_app_id;
            goFlow(row_id);
        });

        function goFlow(row_id) {
            let myModal = $('#status-show');
            let tmp = null;
            let t_name ='replacement_application';
            let c_name ='replace_app_id';

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
            $("#get_url").val(window.location.pathname.slice(1)+'?replace_app_id='+replace_app_id+'&pop=true');
            $.ajax({
                type: 'GET',
                url: '/approval',
                data: {workflowId: tmp, objectid: row_id},
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
                        steps += "<li data-step=" + msg.progressBarData[j].process_step + " class='step-progressbar__item'>" + msg.progressBarData[j].workflow + "</li>"
                    });
                    $('.step-progressbar').html(steps);

                    let content = "";
                    $.each(msg.workflowProcess, function (i) {
                        let note = msg.workflowProcess[i].note;
                        if (note == null) {
                            note = '';
                        }
                        content += "<div class='row d-flex justify-content-between px-1'>" +
                            "<div class='hel'>" +
                            "<span class='ml-1 font-medium'>" +
                            "<h5 class='text-uppercase'>" + msg.workflowProcess[i].workflow_step.workflow + "</h5>" +
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

                    if ($.isEmptyObject(msg.next_step)) {
                        $(".no-permission").css("display", "block");
                        $(document).find('#hide_portion').hide();
                    } else if (JSON.stringify(userRoles).indexOf(msg.next_step.user_role) > -1) {
                        $(document).find('.no-permission').hide();
                        $(document).find('#hide_portion').show();
                    } else {
                        $(".no-permission").css("display", "block");
                        $(document).find('#hide_portion').hide();
                    }
                }
            });
            myModal.modal({show: true});
            return false;
        }

        function setEmployeeInformation(data)
        {
            if(data.employeeInformation) {
                $('#employee_name').val(data.employeeInformation.emp_name);
                $('#employee_house_name').val(data.employeeInformation.house_name);
                $('#employee_residential_area').val(data.employeeInformation.residential_area);
                $('#employee_building_name').val(data.employeeInformation.building_name);
                $('#employee_house_type').val(data.employeeInformation.house_type);
                $('#employee_designation').val(data.employeeInformation.designation);
                $('#employee_department').val(data.employeeInformation.department);
                $('#employee_section').val(data.employeeInformation.section);
            } else {
                $('#employee_name').val('');
                $('#employee_designation').val('');
                $('#employee_department').val();
                $('#employee_section').val();
            }
        }

        function houseReplacementApplicationDatatable()
        {
            let oTable = $('#house-replacement-applications').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/house-replacement-applications-datatable-list",
                columns: [
                    {data: 'employee_code', name: 'employee_code'},
                    {data: 'employee_name', name: 'employee_name'},
                    {data: 'employee_house_name', name: 'employee_house_name'},
                    {data: 'action', name: 'Action', searchable: false},
                    {data: 'replace_app_id', name: 'replace_app_id', searchable: false},
                ]
            });
            oTable.columns([4]).visible(false);
        }

        function excludes() {
            var selectedEmployees = [];
            $('.emp_code').each(function(elem) {
                var value = $(this).val();
                // alert(value);
                if( (value !== null) || (value !== '') || (value !== undefined) ) {
                    selectedEmployees.push(value);
                }
            });
            checkPermission();
            return JSON.stringify(selectedEmployees);
        }

        function checkPermission() {
            $("#submit_btn").show();
        }

        $(document).ready(function() {
            let id = '{{request()->get('replace_app_id')}}';
            let pop = '{{request()->get('pop')}}';

            if (id && pop) {
                if(pop=='true'){
                    goFlow(id);
                }
            }

            $("#workflow_assign_form").attr('action', '{{ route('get-approval-post') }}');
            $("#workflow_form").attr('action', '{{ route('approval-post') }}');
            houseReplacementApplicationDatatable();

            let emp_code = {!! json_encode(Auth::user()->user_name) !!};

            var option = new Option(emp_code, emp_code, true, true);
            $("#employee_code").append(option);
            $("#employee_code select").val(emp_code).prop('selected', true);

            $.ajax({
                type: "GET",
                url: '/ajax/employee-with-allotted-house/'+emp_code,
                success: function (data) {
                    if(data.employeeInformation){
                        setEmployeeInformation(data);
                    }
                    else
                    {
                        $("#submit_btn").hide();
                    }
                },
                error: function (err) {
                    alert('error', err);
                }
            });

            // $('#employee_code').append($('<option>', {
            //     value: emp_code,
            //     text: emp_code
            // }));


            // var option = new Option(emp_code, emp_code, true, true);
            // $("#employee_code").append(option);
            // $("#employee_code select").val(emp_code).change();
            // // $('#employee_code option:eq("'+emp_code+'")').prop('selected', true);

            // $.ajax({
            //     async: false,
            //     type: 'GET',
            //     url: '/get-workflow-id',
            //     data: {row_id: row_id, t_name: t_name, c_name: c_name},
            //     success: function (msg) {
            //         $("#workflow_id").val(msg);
            //         tmp = msg;
            //     }
            // });

{{--            @if(auth()->user()->hasPermission('CAN_SEND_HOUSE_REPLACE_REQUEST'))--}}
                employees('#employee_code', '/ajax/employees-with-allotted-houses/', '/ajax/employee-with-allotted-house/', setEmployeeInformation, excludes);
{{--            @endif--}}


            $('.datetimepicker-input').each(function() {
                $(this).datetimepicker(
                    {
                        format: 'DD-MM-YYYY',
                        ignoreReadonly: true,
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
                        },
                        buttons: {
                            showClear: true
                        }
                    });

                let preDefinedDate = $(this).attr('data-predefined-date');
                if(preDefinedDate) {
                    let preDefinedDateMomentFormat = moment(preDefinedDate, "YYYY-MM-DD").format("DD-MM-YYYY");
                    $(this).datetimepicker('defaultDate', preDefinedDateMomentFormat);
                }
            });
        });
    </script>
@endsection
