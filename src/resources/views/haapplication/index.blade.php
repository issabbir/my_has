@extends('layouts.default')

@section('title')
    House Allotment Application
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
                <div class="card-body"><h4 class="card-title">House Allotment Application</h4>
                    <hr>
                    @include('haapplication.form')
                </div>
            </div>

            <div class="card">
                <div class="card-body"><h4 class="card-title">House Allotment Application List</h4><!---->
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-sm datatable mdl-data-table dataTable" id="ha-applications">
                            <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Advertisement</th>
                                <th>Application Date</th>
                                <th>Employee</th>
                                <th>Employee Code</th>
                                <th>Grade</th>
                                <th>Applied House Type</th>
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
    <!--Load custom script-->
    <script type="text/javascript">
        let userRoles = '@php echo json_encode(Auth::user()->roles->pluck('role_key')); @endphp';

        $('#ha-applications tbody').on('click', '.workflowBtn', function () {
            var data_row = $('#ha-applications').DataTable().row($(this).parents('tr')).data();
            var row_id = data_row.application_id;
            getFlow(row_id, data_row.prefix);
        });

        function getFlow(row_id, prefix = '') {
            let myModal = $('#workflowM');

            $('#application_id_flow').val(prefix+row_id);
            $('#t_name').val('ha_application');
            $('#c_name').val('application_id');
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

        $('#ha-applications tbody').on('click', '.approveBtn', function () {
            let data_row = $('#ha-applications').DataTable().row($(this).parents('tr')).data();
            let row_id = data_row.application_id;
            goFlow(row_id);
        });

        function goFlow(row_id) {
            let myModal = $('#status-show');
            let tmp = null;
            let t_name ='ha_application';
            let c_name ='application_id';

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
            $("#get_url").val(window.location.pathname.slice(1)+'?application_id='+row_id+'&pop=true');
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
                        steps += "<li data-step=" + msg.progressBarData[j].process_step + " class='step-progressbar__item'>" + msg.progressBarData[j].forward_title + "</li>"
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

        let GENDER_ID_FEMALE = 2;

        function resetFemaleRelatedInformation() {
            $('[name="husband_employee_of_cpa"]').removeAttr('checked');
            $('#husband_employee_code').val('').trigger('change.select2');
            $('#husband_name').val();
            $('#husband_occupation').val('');
            $('#husband_occupation').removeAttr('selected');
            $('[name="husband_occupation_type"]').removeAttr('checked');
            $('#husband_organization').val('');
            $('#husband_designation').val('');
            $('#husband_org_division').val('');
            $('#husband_org_division').removeAttr('selected');
            $('#husband_org_district').val('');
            $('#husband_org_district').removeAttr('selected');
            $('#husband_orga_thana').val('');
            $('#husband_orga_thana').removeAttr('selected');
            $('#husband_salary').val();
            $('[name="husband_house_status"]').removeAttr('checked');
            $('#husband_address').val('');
        }

        function resetHusbandOccupation() {
            $('#husband_occupation_service-details').hide();
            $('[name="husband_occupation_type"]').prop('checked', false);
            $('#husband_organization').val('');
            $('#husband_designation').val('');
            $('#husband_org_division').val('');
            $('#husband_org_division').removeAttr('selected');
            $('#husband_org_district').val('');
            $('#husband_org_district').removeAttr('selected');
            $('#husband_orga_thana').val('');
            $('#husband_orga_thana').removeAttr('selected');
            $('#husband_salary').val('');
            $('[name="husband_house_status"]').prop('checked', false);
            $('#husband_address').val('');
        }

        function resetHusbandJobHolderAtCpa() {
            $('#female-related-information-husband-external-employee-form').hide();
            $('#husband_name').val('');
            $('#husband_occupation').val('');
            $('#husband_occupation').removeAttr('selected');
            $('[name="husband_occupation_type"]').removeAttr('checked');
            $('#husband_organization').val('');
            $('#husband_designation').val('');
            $('#husband_org_division').val('');
            $('#husband_org_division').removeAttr('selected');
            $('#husband_org_district').val('');
            $('#husband_org_district').removeAttr('selected');
            $('#husband_orga_thana').val('');
            $('#husband_orga_thana').removeAttr('selected');
            $('#husband_salary').val('');
            $('[name="husband_house_status"]').removeAttr('checked');
            $('#husband_address').val('');
        }

        function femaleRelatedInformationForm() {
            /******NOT NEEDED SUGGESTED BY ZAHID VAI****/
            let marital_status = $('#emp_maritial_status_id').val();
            let gender_id = $('#gender_id').val();
            //console.log('marital status: ', marital_status, 'gender: ', gender_id);
            /******NOT NEEDED SUGGESTED BY ZAHID VAI****/
            if (marital_status == 2 && gender_id == GENDER_ID_FEMALE) {
                /*if(gender_id == GENDER_ID_FEMALE) {*/
                $('#female-related-information').show();
            } else {
                $('#female-related-information').hide();
                resetFemaleRelatedInformation();
            }
        }

        function ageCalculator(givenDate) {
            let givenDateMoment = moment(givenDate, 'DD-MM-YYYY');
            let duration = moment.duration(moment(moment(), 'DD-MM-YYYYY').diff(givenDateMoment));
            return duration.years() + " years " + duration.months() + " months " + duration.days() + " days";
        }

        function setEmployeeInformation(data) {
            if (data.employeeInformation) {
                $('#employee_name').val(data.employeeInformation.emp_name);
                $('#employee_designation').val(data.employeeInformation.designation);
                $('#employee_department').val(data.employeeInformation.department);
                $('#employee_section').val(data.employeeInformation.section);
                $('#father_name').val(data.employeeInformation.emp_father_name);
                $('#mother_name').val(data.employeeInformation.emp_mother_name);
                if (data.employeeInformation.emp_dob) {
                    $('#date_of_birth').val(moment(data.employeeInformation.emp_dob, 'YYYY-MM-DD').format('DD-MM-YYYY'));
                }

                if (data.employeeInformation.emp_join_date) {
                    $('#join_date').val(moment(data.employeeInformation.emp_join_date).format('DD-MM-YYYY'));
                }

                if (data.employeeInformation.emp_lpr_date) {
                    $('#prl_date').val(moment(data.employeeInformation.emp_lpr_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
                }

                $('#payscale').val(data.employeeInformation.payscale);
                $('#current_basic').val(data.employeeInformation.current_basic);
                $('#gender_id').val(data.employeeInformation.gender_id);
                $('#emp_maritial_status_id').val(data.employeeInformation.maritial_status_id);
                $('#gender').val(data.employeeInformation.gender_name);
                if(data.employeeInformation.house_category_id == '3')
                {
                    // $('#eligible_for').val(data.employeeInformation.eligible_for+'/Dormitory - Type');
                    $('#eligible_for').val(data.eligible_for);
                }
                else
                {
                    // $('#eligible_for').val(data.employeeInformation.eligible_for);
                    $('#eligible_for').val(data.eligible_for);
                }
                $('#eligible_id').val(data.employeeInformation.eligible_id);
                generateEmpFamilyDetailsTable();
                generateEmpFamilyDetailsTableWithUserCode();
            } else {
                $('#employee_name').val('');
                $('#employee_designation').val('');
                $('#employee_department').val();
                $('#employee_section').val();
                $('#father_name').val('');
                $('#mother_name').val('');
                $('#date_of_birth').val('');
                $('#join_date').val('');
                $('#prl_date').val('');
                $('#payscale').val('');
                $('#current_basic').val('');
                $('#gender_id').val('');
                $('#emp_maritial_status_id').val('');
                $('#gender').val('');
                $('#eligible_for').val();
                $('#eligible_id').val();
            }
            femaleRelatedInformationForm();

            var previouslySelectedHouseType = $('#house_type_id').val();
            if(data.house_types)
            {
                $('#house_type_id').html(data.house_types);
                $('#house_type_id').val(previouslySelectedHouseType);
            }

            if (data.availableAdvertisement > 0) {
                if (data.advertisements) {
                    var previouslySelectedAdvertisement = $('#advertisement_id').val();
                    $('#advertisement_id').html(data.advertisements);
                    $('#advertisement_id').val(previouslySelectedAdvertisement);
                    $('#house_category_id').val(data.employeeInformation.house_category_id);
                }
            } else {
                alert('No advertisement available for eligible house type for this employee!');
            }
        }

        function setEmployeeHusbandInformation(data) {
            $('#husband_name').val(data.emp_name);
            $('[name="husband_house_status"]').each(function () {
                if ($(this).val() == data.allot_yn) {
                    $(this).prop('checked', true);
                }
            });
        }

        function numericAndMaxDigit(elem) {
            var jqueryElem = $(elem);
            var filteredValue = jqueryElem.val().replace(/[^0-9]+/g, "");
            jqueryElem.val(filteredValue);

            if (jqueryElem.val() > jqueryElem.attr('maxLength')) {
                jqueryElem.val(jqueryElem.val().slice(0, jqueryElem.attr('maxLength')));
            }
        }

        /*** DYNAMIC FORM EMPLEMENTATION **/
        $(function () {
            /*let haApplicationFamilyFormIndex = $('.ha-application-family-form').length + 1;*/
            let haApplicationFamilyFormIndex = 1;
            $('a#add-application-family-form').on('click', function (e) {
                e.preventDefault();
                var arrayIndex = parseInt($(this).attr('data-row'));
                haApplicationFamilyFormIndex = arrayIndex + 1;
                let dynamicRelationshipElement = '<select class="custom-select" name="family[' + haApplicationFamilyFormIndex + '][relation_type_id]" id="family_' + haApplicationFamilyFormIndex + '_relation_type_id" required>' + $('#relation_type_id_template').html() + '</select>';
                /*let haApplicationFamilyFormTemplate = '<div class="row ha-application-family-form mt-1 text-center"><div class="col-1 font-weight-bold">'+haApplicationFamilyFormIndex+'</div><div class="col-2"><input name="family['+haApplicationFamilyFormIndex+'][name]" id="family_'+haApplicationFamilyFormIndex+'_name" type="text" value="" class="form-control" required /></div><div class="col-2"><input name="family['+haApplicationFamilyFormIndex+'][name_bng]" id="family_'+haApplicationFamilyFormIndex+'_name_bng" type="text" value="" class="form-control" required /></div><div class="col-2"><input name="family['+haApplicationFamilyFormIndex+'][mobile]" id="family_'+haApplicationFamilyFormIndex+'_mobile" type="text" value="" class="form-control" required /></div><div class="col-2"><input name="family['+haApplicationFamilyFormIndex+'][dob]" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#family_'+haApplicationFamilyFormIndex+'_dob" id="family_'+haApplicationFamilyFormIndex+'_dob" type="text" required readonly /></div><div class="col-1"><input name="family['+haApplicationFamilyFormIndex+'][age]" class="age form-control" id="family_'+haApplicationFamilyFormIndex+'_age" type="text" value="" disabled /></div><div class="col-1">'+dynamicRelationshipElement+'</div><div class="col-1"><a href="#" class="remove-application-family-form btn btn-danger"><i class="bx bx-trash cursor-pointer"></i></a></div></div>';*/
                let haApplicationFamilyFormTemplate = '<div class="row ha-application-family-form mt-1 text-center"><div class="col-2"><input name="family[' + haApplicationFamilyFormIndex + '][name]" id="family_' + haApplicationFamilyFormIndex + '_name" type="text" value="" class="form-control" required /></div><div class="col-2"><input name="family[' + haApplicationFamilyFormIndex + '][name_bng]" id="family_' + haApplicationFamilyFormIndex + '_name_bng" type="text" value="" class="form-control" /></div><div class="col-2"><input name="family[' + haApplicationFamilyFormIndex + '][mobile]" id="family_' + haApplicationFamilyFormIndex + '_mobile" type="text" value="" class="form-control mobile" maxlength="11" /></div><div class="col-2"><input name="family[' + haApplicationFamilyFormIndex + '][dob]" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#family_' + haApplicationFamilyFormIndex + '_dob" id="family_' + haApplicationFamilyFormIndex + '_dob" type="text" readonly /></div><div class="col-2"><input name="family[' + haApplicationFamilyFormIndex + '][age]" class="age form-control" id="family_' + haApplicationFamilyFormIndex + '_age" type="text" value="" disabled /></div><div class="col-1">' + dynamicRelationshipElement + '</div><div class="col-1"><a href="#" class="remove-application-family-form btn btn-danger"><i class="bx bx-trash cursor-pointer"></i></a></div></div>';
                $(haApplicationFamilyFormTemplate).fadeIn("slow").appendTo('#ha-application-family-dynamic-form');
                $('#family_' + haApplicationFamilyFormIndex + '_dob').datetimepicker(
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
                        },
                    });
                $('#family_' + haApplicationFamilyFormIndex + '_dob').datetimepicker('maxDate', moment().format('DD-MM-YYYY'));
                $('#family_' + haApplicationFamilyFormIndex + '_dob').on('change.datetimepicker', function () {
                    let changedDate = $(this).val();
                    if (changedDate) {
                        $(this).parent().next().find('.age').val(ageCalculator(changedDate));
                    }
                });
                filterMobileNumber();
                $(this).attr('data-row', haApplicationFamilyFormIndex);
            });

            $("#ha-application-family-dynamic-form").on('click', '.remove-application-family-form', function (e) {
                e.preventDefault();
                $(this).parent().parent().remove();
                /*haApplicationFamilyFormIndex--;*/
            });
        });

        function filterMobileNumber() {
            $('.mobile').on('keyup', function () {
                numericAndMaxDigit(this);
            });
        }

        function excludes() {
            var selectedEmployees = [];
            $('.emp_code').each(function (elem) {
                var value = $(this).val();

                if ((value !== null) || (value !== '') || (value !== undefined)) {
                    selectedEmployees.push(value);
                }
            });

            return JSON.stringify(selectedEmployees);
        }

        function reset(btnElem, formElem, employeeElem, husbandEmployeeElem) {
            $(btnElem).on('click', function () {
                $(formElem).trigger('reset');
                $(employeeElem).val('').trigger('change.select2');
                $(husbandEmployeeElem).val('').trigger('change.select2');
            });
        }

        function generateEmpFamilyDetailsTable() {
            let employee_code = $('#employee_code').val();
            if (employee_code !== undefined && employee_code != '' && employee_code) {

                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/load_employee_family_details/' + employee_code,
                    success: function (data) {
                        $('#empFamilyDetails').html(data.familyDetailsHtml);
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            }
        }

        function generateEmpFamilyDetailsTableWithUserCode() {
            let employee_code = $('#employee_codde').val();
            if (employee_code !== undefined && employee_code != '' && employee_code) {

                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/load_employee_family_details/' + employee_code,
                    success: function (data) {
                        $('#empFamilyDetails').html(data.familyDetailsHtml);
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            }
        }


        /*** DYNAMIC FORM EMPLEMENTATION **/

        $(document).ready(function () {
            let id = '{{request()->get('application_id')}}';
            let pop = '{{request()->get('pop')}}';

            if (id && pop) {
                if(pop=='true'){
                    goFlow(id);
                }
            }

            $("#workflow_assign_form").attr('action', '{{ route('get-approval-post') }}');
            $("#workflow_form").attr('action', '{{ route('approval-post') }}');
            reset('#resetForm', '#house-form', '#employee_code', '#husband_employee_code');
            employees('#employee_code', '/ajax/employees', '/ajax/employee/', setEmployeeInformation, excludes);

            employees('#employee_codde', '/ajax/employees', '/ajax/employee/', setEmployeeInformation, excludes);
            employees('#husband_employee_code', '/ajax/employees', '/ajax/employee-husband/', setEmployeeHusbandInformation, excludes);

            filterMobileNumber();

            $('.datetimepicker-input').each(function () {
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
                let preDefinedDateMomentFormat = moment(preDefinedDate, "YYYY-MM-DD").format("DD-MM-YYYY");
                if (preDefinedDate) {
                    $(this).datetimepicker('defaultDate', preDefinedDateMomentFormat);
                    $(this).parent().next().find('.age').val(ageCalculator(moment(preDefinedDate, 'YYYY-MM-DD').format('DD-MM-YYYY')));
                }
                /* $(this).datetimepicker('maxDate', moment().format('DD-MM-YYYY'));*/
            });

            $('#datetimepicker1').datetimepicker({
                //format: 'YYYY-MM-DD',
                // format: 'L',
                format: 'DD-MM-YYYY',
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

            $('.datetimepicker-input').on('change.datetimepicker', function () {
                let changedDate = $(this).val();
                if (changedDate) {
                    $(this).parent().next().find('.age').val(ageCalculator(changedDate));
                }
            });

            var oTable = $('#ha-applications').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/ha-applications-datatable-list",
                columns: [

                    {data: 'DT_RowIndex', "name": 'DT_RowIndex'},
                    {data: 'adv_number', name: 'adv_number'},
                    {data: 'application_date', name: 'application_date'},
                    {data: 'emp_name', name: 'emp_name'},
                    {data: 'emp_code', name: 'emp_code'},
                    {data: 'grade_range', name: 'grade_range', searchable: false},
                    /*{data: 'emp_grade_id', name: 'emp_grade_id', searchable: false},*/
                    {data: 'house_type', name: 'house_type'},
                    {data: 'action', name: 'Action', searchable: false},
                    {data: 'application_id', name: 'application_id', searchable: false},
                ]
            });
            oTable.columns([8]).visible(false);

            /*
            { data: 'advertisement.adv_number', name: 'advertisement.adv_number' },
            { data: 'application_date', name: 'application_date' },
            { data: 'employee.emp_name', name: 'employee.emp_name' },
            { data: 'employee.emp_code', name: 'employee.emp_code' },
            { data: 'employee.grade.grade_range', name: 'employee.grade.grade_range', searchable: false },
            { data: 'house_type.house_type', name: 'house_type.house_type' },
            { data: 'action', name: 'Action', searchable: false },
            */

            /** If Husband occupation is service then: **/
            $('#husband_occupation').on('change', function () {
                let husband_occupation = $(this).val();
                if (husband_occupation == 'Service') {
                    $('#husband_occupation_service').show();
                    $('#husband_occupation_service-details').show();
                } else {
                    $('#husband_occupation_service').hide();
                    resetHusbandOccupation();
                }
            });

            /** If Marital status is married, then ******NOT NEEDED SUGGESTED BY ZAHID VAI**** **/
            $('#emp_maritial_status_id').on('change', function () {
                femaleRelatedInformationForm();
            });

            /** If husband is an employee of cpa, then **/
            $('#husband_employee_of_cpa').on('change', function () {
                let isHusbandEmployeeOfCpa = $(this).prop('checked');
                if (isHusbandEmployeeOfCpa) {
                    $('#husband_employee_of_cpa_code').show();
                    resetHusbandJobHolderAtCpa();
                } else {
                    $('#husband_employee_code').val('').trigger('change.select2');
                    $('#husband_employee_of_cpa_code').hide();
                    $('#female-related-information-husband-external-employee-form').show();
                }
            });

            /** Manage district on division change **/
            $('#husband_org_division').on('change', function () {
                let divisionId = $(this).val();
                if (divisionId !== undefined && divisionId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL + '/ajax/districts/' + divisionId,
                        success: function (data) {
                            $('#husband_org_district').html(data.html);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                }
            });

            /** Manage thana on division change **/
            $('#husband_org_district').on('change', function () {
                let districtId = $(this).val();

                if (districtId !== undefined && districtId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL + '/ajax/thanas/' + districtId,
                        success: function (data) {
                            $('#husband_orga_thana').html(data.html);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                }
            });

            $('#advertisement_id, #house_type_id').on('change', function () {
                let advID = $('#advertisement_id').val();
                // let house_type = $('#eligible_id').val();
                let house_category_id = $('#house_category_id').val();
                let house_type = $('#house_type_id').val();
                let url = APP_URL + '/get-advertise-flat-data/' + advID + '/' + house_type;
                if ((advID !== undefined) && advID && house_type) {
                    $.ajax({
                        type: "GET",
                        data: { house_type : house_type},
                        url: url,
                        success: function (data) {
                            $('#avl_flat').html(data);
                            $('#building_nassssme').html(data.building_id);

                            $("#table-applicant-flat tbody").empty();
                            dataArray = []; //Empty array
                        },
                        error: function (data) {
                            alert('Error!');
                        }
                    });
                } else {
                    $('#avl_flat').val('');
                }
            });
            @if(isset($data['haApplication']))
            generateEmpFamilyDetailsTable();
            generateEmpFamilyDetailsTableWithUserCode();
            @endif
        });

        var dataArray = new Array();

        $(".add-available-flat").click(function () {
            let avl_flat = $("#avl_flat option:selected").text();
            let avl_flat_id = $("#avl_flat").val();

            if (avl_flat != '' && avl_flat_id != '') {
                if ($.inArray(avl_flat_id, dataArray) > -1) {
                    alert('This Flat Alrady Generated.');

                } else {
                    let markup = "<tr role='row'>" +
                        "<td aria-colindex='1' role='cell' class='text-center'>" +
                        "<input type='checkbox' name='record' class='avl_flat_id' value='" + avl_flat_id + "+" + "" + "'>" +
                        "<input type='hidden' name='avl_flat_id[]' value='" + avl_flat_id + "'>" +
                        "</td>" +
                        "<td aria-colindex='8' role='cell'>" + avl_flat + "</td></tr>";

                    $("#table-applicant-flat tbody").append(markup);
                    dataArray.push(avl_flat_id);
                }
            } else {
                Swal.fire('Fill required value.');
            }
        });

        $(".delete_adv_flat").click(function () {

            let arr_stuff = [];
            let avl_flat_id = [];
            let dept_ack_id = [];

            $(':checkbox:checked').each(function (i) {
                arr_stuff[i] = $(this).val();
                // alert(arr_stuff[i]);
                let sd = arr_stuff[i].split('+');
                // alert(dataArray[i]);
                avl_flat_id.push(sd[0]);

                if (sd[1]) {
                    dept_ack_id.push(sd[1]);
                }

            });

            if (dept_ack_id.length != 0) {

                $.ajax({
                    type: 'GET',
                    url: '/house-data-remove',
                    data: {dept_ack_id: dept_ack_id},
                    success: function (msg) {
                        for (var i = dataArray.length - 1; i >= 0; i--) {
                            for (var j = 0; j < house_id.length; j++) {
                                if (dataArray[i] === house_id[j]) {
                                    dataArray.splice(i, 1);
                                }
                            }
                        }
                        $('td input:checked').closest('tr').remove();
                    }
                });
            } else {
                for (var i = dataArray.length - 1; i >= 0; i--) {
                    for (var j = 0; j < avl_flat_id.length; j++) {
                        // alert(avl_flat[j]);
                        if (dataArray[i] === avl_flat_id[j]) {
                            dataArray.splice(i, 1);
                        }
                    }
                }
                $('td input:checked').closest('tr').remove();
            }
        });


        // $(document).on('change', '#advertisement_id', function() {
        //     let advertisementId = $(this).val();
        //
        //     if(advertisementId !== undefined && advertisementId) {
        //         $.ajax({
        //             type: "GET",
        //             url: APP_URL+"/ajax/house-type-by-advertisement/" + advertisementId  ,
        //             success: function (data) {
        //                 $('#house_type_id').html(data);
        //                 $('#house_type_id').addClass('select2',true);
        //             },
        //             error: function (data) {
        //                 alert('error');
        //             }
        //         });
        //     } else {
        //         // $('#houseDetails').val('');
        //         $('#house_type_id').empty();
        //     }
        //
        // });

    </script>
@endsection


