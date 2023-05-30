@extends('layouts.default')

@section('title')
    House Interchange
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
                <div class="card-body"><h4 class="card-title">House Interchange Application</h4>
                    <hr>
                    @include('houseinterchangeapplication.form')
                </div>
            </div>
            <div class="card">
                <div class="card-body"><h4 class="card-title">House Interchange Applications</h4><!---->
                    <hr>
                    <div class="table-responsive">
                        <table  class="table table-sm datatable mdl-data-table dataTable" id="house-interchange-applications">
                            <thead>
                            <tr>
                                <th>1st Employee Code</th>
                                <th>1st Employee Name</th>
                                <th>1st Employee House</th>
                                <th>2nd Employee Code</th>
                                <th>2nd Employee Name</th>
                                <th>2nd Employee House</th>
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

        $('#house-interchange-applications tbody').on('click', '.workflowBtn', function () {
            var data_row = $('#house-interchange-applications').DataTable().row($(this).parents('tr')).data();
            var row_id = data_row.int_change_id;
            getFlow(row_id);
        });

        function getFlow(row_id) {
            let myModal = $('#workflowM');
            $('#application_id_flow').val(row_id);
            $('#t_name').val('interchange_application');
            $('#c_name').val('int_change_id');
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

        $('#house-interchange-applications tbody').on('click', '.approveBtn', function () {
            var data_row = $('#house-interchange-applications').DataTable().row($(this).parents('tr')).data();
            var row_id = data_row.int_change_id;
            goFlow(row_id);
        });

        function goFlow(row_id) {
            let myModal = $('#status-show');
            let tmp = null;
            let t_name ='interchange_application';
            let c_name ='int_change_id';

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
            $("#get_url").val(window.location.pathname.slice(1)+'?int_change_id='+int_change_id+'&pop=true');
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

        function setFirstEmployeeInformation(data)
        {
            if(data.employeeInformation) {
                $('#first_employee_name').val(data.employeeInformation.emp_name);
                $('#first_employee_house_name').val(data.employeeInformation.house_name);
                $('#first_employee_designation').val(data.employeeInformation.designation);
                $('#first_employee_department').val(data.employeeInformation.department);
                $('#first_employee_section').val(data.employeeInformation.section);
            } else {
                $('#first_employee_name').val('');
                $('#first_employee_designation').val('');
                $('#first_employee_department').val();
                $('#first_employee_section').val();
            }
        }

        function setSecondEmployeeInformation(data)
        {
            if(data.employeeInformation) {
                $('#second_employee_name').val(data.employeeInformation.emp_name);
                $('#second_employee_house_name').val(data.employeeInformation.house_name);
                $('#second_employee_designation').val(data.employeeInformation.designation);
                $('#second_employee_department').val(data.employeeInformation.department);
                $('#second_employee_section').val(data.employeeInformation.section);
            } else {
                $('#second_employee_name').val('');
                $('#second_employee_designation').val('');
                $('#second_employee_department').val();
                $('#second_employee_section').val();
            }
        }

        function houseInterchangeApplicationDatatable()
        {
            let oTable = $('#house-interchange-applications').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/house-interchange-applications-datatable-list",
                columns: [
                    {data: 'first_employee_code', name: 'first_employee_code'},
                    {data: 'first_employee_name', name: 'first_employee_name'},
                    {data: 'first_employee_house_name', name: 'first_employee_house_name'},
                    {data: 'second_employee_code', name: 'second_employee_code'},
                    {data: 'second_employee_name', name: 'second_employee_name'},
                    {data: 'second_employee_house_name', name: 'second_employee_house_name'},
                    {data: 'action', name: 'Action', searchable: false},
                    {data: 'int_change_id', name: 'int_change_id', searchable: false},
                ]
            });
            oTable.columns([7]).visible(false);
        }

        function excludes() {
            var selectedEmployees = [];
            $('.emp_code').each(function(elem) {
                var value = $(this).val();
                if( (value !== null) || (value !== '') || (value !== undefined) ) {
                    selectedEmployees.push(value);
                }
            });

            return JSON.stringify(selectedEmployees);
        }

        $(document).ready(function() {
            let id = '{{request()->get('int_change_id')}}';
            let pop = '{{request()->get('pop')}}';

            if (id && pop) {
                if(pop=='true'){
                    goFlow(id);
                }
            }

            $("#workflow_assign_form").attr('action', '{{ route('get-approval-post') }}');
            $("#workflow_form").attr('action', '{{ route('approval-post') }}');
            houseInterchangeApplicationDatatable();

            /*searchEmployee('#first_employee_code', setFirstEmployeeInformation);
            searchSecondaryEmployee('#second_employee_code', setSecondEmployeeInformation, '#first_employee_code');*/
            selectInterchangeEmployee('#first_employee_code', '/ajax/employees-interchange-request-houses/');
            selectInterchangeEmployee('#second_employee_code', '/ajax/second-employees-interchange-request-houses/');
            // employees('#first_employee_code', '/ajax/employees-with-allotted-houses/', '/ajax/employee-with-allotted-house/', setFirstEmployeeInformation, excludes);
            // employees('#second_employee_code', '/ajax/employees-with-allotted-houses/', '/ajax/employee-with-allotted-house/', setSecondEmployeeInformation, excludes);

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


        function selectInterchangeEmployee(idselect,employeesFilterUrl) {
            $(idselect).select2({
                placeholder: "Select",
                allowClear: true,
                ajax: {
                    url: APP_URL + employeesFilterUrl,
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
                            obj.text = obj.emp_code + '-' + obj.emp_name;
                            return obj;
                        });
                        return {
                            results: formattedResults,
                        };
                    }
                }
            });

        }

        $('#first_employee_code').on('change', function () {
            var first_employee_id = $('#first_employee_code').val();

            if (first_employee_id !== undefined && first_employee_id) {

                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/request-interchange-first-emp/' + first_employee_id,
                    success: function (data) {

                        $('#first_employee_house_name').val(data.house_name);
                        $('#first_employee_name').val(data.emp_name);
                        $('#first_employee_designation').val(data.designation);
                        $('#first_employee_department').val(data.department_name);
                        $('#first_employee_department').val(data.department_name);
                        $('#first_employee_section').val(data.section);
                        $('#first_alloted_id').val(data.allot_id);
                        $('#f_emp_code').val(data.emp_code);

                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            } else {
                $('#first_employee_house_name').val('');
                $('#first_employee_name').val('');
                $('#first_employee_designation').val('');
                $('#first_employee_department').val('');
                $('#first_employee_department').val('');
                $('#first_employee_section').val('');
                $('#first_alloted_id').val('');
            }
            });


        $('#second_employee_code').on('change', function () {
            var second_employee_id = $('#second_employee_code').val();

            if (second_employee_id !== undefined && second_employee_id) {

                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/request-interchange-second-emp/' + second_employee_id,
                    success: function (data) {

                        $('#second_employee_name').val(data.emp_name);
                        $('#second_employee_house_name').val(data.house_name);
                        $('#second_employee_designation').val(data.designation);
                        $('#second_employee_department').val(data.department_name);
                        $('#second_employee_department').val(data.department_name);
                        $('#second_employee_section').val(data.section);
                        $('#second_alloted_id').val(data.allot_id);
                        $('#s_emp_code').val(data.emp_code);

                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            } else {
                $('#second_employee_house_name').val('');
                $('#second_employee_name').val('');
                $('#second_employee_designation').val('');
                $('#second_employee_department').val('');
                $('#second_employee_department').val('');
                $('#second_employee_section').val('');
                $('#second_alloted_id').val('');
            }
            });


    </script>


@endsection
