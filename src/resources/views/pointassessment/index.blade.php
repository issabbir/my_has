@extends('layouts.default')

@section('title')
    House Information
@endsection

@section('header-style')
    <!--Load custom style link or css-->
    <style>
        th.dt-center, td.dt-center {
            text-align: center;
        }
        .sts-btn {
            min-width: 108px;
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
                <div class="card-body"><h4 class="card-title">Point Assessment</h4>
                    <hr>
                    @include('pointassessment.form')
                </div>
            </div>
            @if($data['advertisement_id'] && $data['house_type_id'])
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">House Allotment Applications</h4>
                        <form id="house-form-multisubmit" method="POST"
                              @if($data['multi_workflow_id'] == 1)
                                    action="{{ route('get-multiple-assign-post') }}"
                              @else
                                    action="{{ route('multi-approval-post') }}"
                              @endif
                        >
                            {!! csrf_field() !!}

                            {{--For Assign--}}
                            @if($data['multi_workflow_id'] == 1)
                               {{-- X <input type="hidden" id="application_id_flow" name="application_id_flow" value="20220605284">--}}
                               {{-- X <input type="hidden" id="prefix" name="prefix" value="haa">--}}
                                <input type="hidden" id="t_name" name="t_name" value="ha_application">
                                <input type="hidden" id="c_name" name="c_name" value="application_id">
                            @else
                            {{--For WorkFlow--}}
                                {{--<input type="hidden" id="workflow_id" name="workflow_id" value="607">
                                <input type="hidden" id="object_id" name="object_id" value="20220605285">
                                <input type="hidden" id="bonus_id_prm" name="bonus_id">
                                <input type="hidden" id="get_url" name="get_url" value="point-assessments?application_id=20220605285&amp;pop=true">--}}
                            @endif
                            {{--For Common--}}
                                <input type="hidden" id="prefix" name="prefix" value="haa">

                                <div class="table-responsive">
                                    <table  class="table table-sm datatable mdl-data-table dataTable" id="submitted-application">
                                        <thead>
                                        <tr>
                                            <th data-orderable="false">
                                                <span class="checkboxIdentifier">All<input type="checkbox" id="checkbox_" name="checkAll" /> </span>
                                            </th>
                                            <th>SL</th>
                                            <th >Employee Name</th>
                                            <th>Employee Code</th>
        {{--                                    <th>Department</th>--}}
                                            <th>Designation</th>
                                            <th>Current Salary Scale</th>
                                            <th>Date of Birth</th>
                                            <th>Joining Date</th>
                                            <th>Eligible Promotion Date</th>
                                            <th>Eligible Date Point</th>
                                            <th>Female Point</th>
                                            <th>Total Point</th>
                                            <th>Applied Date</th>
                                            <th>Approved House</th>
                                            <th class="text-center">Status</th>
                                            <th style="text-align:center;">Action</th>
                                             <th style="display:none;">
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    @if(($data['approval_process_type_id'] == 2)&&($data['multi_workflow_id'] == 1)) {{--approval_process_type_id =2= Multiple, multi_workflow_id =1= Assigned--}}
                                        <div class="col-sm-4" id="workflowAssignDropDownDiv">
                                            <select class="custom-select form-control select2" required id="workflowAssignDropDownList" name="workflow_assign_id">
                                            </select>
                                        </div>
                                    @endif
                                    @if(($data['approval_process_type_id'] == 2)&&($data['multi_workflow_id'] == 2)) {{--approval_process_type_id =2= Multiple, multi_workflow_id =2= workflow--}}
                                        <div class="col-sm-4" id="workflowAssignDropDownDiv">
                                            <textarea name="note" placeholder="Note" class="form-control" id="WorkFlowTextarea" cols="30" rows="2"></textarea>
                                        </div>
                                    @endif
                                    @if($data['approval_process_type_id'] == 2)
                                        <div class="d-flex justify-content-start col-sm-4">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-dark btn-sm mb-1 mr-1" id="save_btn" style="display: block"><i class="bx bx-save"></i> <span style="font-size: 12px;">Submit</span>
                                            </button>
                                            <button type="reset" class="btn btn-outline-dark btn-sm mb-1" id="close_btn" style="display: block" data-dismiss="modal"><i class="bx bx-window-close"></i> <span style="font-size: 12px;">Close</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="modal fade" id="houseAllotmentApprovalModal" tabindex="-1" role="dialog" aria-labelledby="houseAllotmentApprovalModalLabel" aria-hidden="true">
        <div class="modal-dialog mw-100 w-50">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="houseAllotmentApprovalModalLabel">House Allotment Approval Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="houseAllotmentApprovalForm"></div>
            </div>
        </div>
    </div>
@endsection

@include('approval.workflowmodal')

@include('approval.workflowselect')

@section('footer-script')
    <!--Load custom script-->
    <script type="text/javascript">
        const userRoles = '@php echo json_encode(Auth::user()->roles->pluck('role_key')); @endphp';//alert(userRoles)

        $(document).on('change','#checkbox_',function(){
            checkAll(this);
        });

        function checkAll(check) {
            var id = check.id;

            if (check.checked) {
                $('input:checkbox[id^="' + id + '"]').each(function () {
                    $('input:checkbox[id^="' + id + '"]').prop("checked", true);
                });
                //$('input[id^="vehicle_reg_no_"]').prop("readonly", false);
                //$('input[id^="myModal-"]').prop("readonly",false);
            } else {
                $('input:checkbox[id^="' + id + '"]').each(function () {
                     $('input:checkbox[id^="' + id + '"]').prop("checked", false);
                });
                //$('input[id^="vehicle_reg_no_"]').prop("readonly", true);
                //$('input[id^="myModal-"]').prop("readonly",true);
            }
        }

        $('#submitted-application tbody').on('click', '.workflowBtn', function () {
            var data_row = $('#submitted-application').DataTable().row($(this).parents('tr')).data();
            var row_id = data_row.application_id;
            getFlow(row_id, data_row.prefix);
        });

        function getFlow(row_id, prefix='') {
            let myModal = $('#workflowM');
            //console.log(myModal);
            $('#application_id_flow').val(row_id);
            $(document).find('input#prefix').val(prefix);

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
        function getWorkflowAssignDropDownList(targetToshow) {
            $.ajax({
                type: 'GET',
                url: '/get-approval-list',
                success: function (msg) {
                    $(targetToshow).html(msg.options);
                }
            });
        }

        $('#submitted-application tbody').on('click', '.approveBtn', function () {
            let data_row = $('#submitted-application').DataTable().row($(this).parents('tr')).data();
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
                data: {workflowId: tmp, objectid: 'haa'+row_id},
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

                    $('#prefix').val('haa');

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
                }
            });
            myModal.modal({show: true});
            return false;
        }

        function applicationDatatable()
        {
            let advertisementId = $('#advertisement_id').val();
            let houseTypeId = $('#house_type_id').val();
            let approvalProcessTypeId = $('#approval_process_type_id').val();
            let multiWorkflowId = $('#multi_workflow_id').val();

            if( (advertisementId !== undefined) && (advertisementId) && (houseTypeId !== undefined) && (houseTypeId) ) {

				var oTable = $('#submitted-application').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    ajax: APP_URL+"/point-assessments-applicant-datatable-list?advertisement_id="+advertisementId+'&house_type_id='+houseTypeId+'&approval_process_type_id='+approvalProcessTypeId+'&multi_workflow_id='+multiWorkflowId,
                    columns: [
                        {data: 'checkBoxInput', name: 'checkBoxInput'},
                        {data: 'DT_RowIndex', "name": 'DT_RowIndex'},
                        { data: 'emp_name', name: 'emp_name' },
                        { data: 'emp_code', name: 'emp_code' },
                        // { data: 'department_name', name: 'department_name' },
                        { data: 'designation', name: 'designation' },
                        { data: 'grade_range', name: 'grade_range' },
                        { data: 'emp_dob', name: 'emp_dob' },
                        { data: 'emp_join_date', name: 'emp_join_date' },
                        { data: 'eligable_promotion_date', name: 'eligable_promotion_date' },
                        { data: 'point_from_promo_date', name: 'point_from_promo_date' },
                        { data: 'female_point', name: 'female_point' },
                        { data: 'tot_point', name: 'tot_point' },
                        { data: 'application_date', name: 'application_date' },
                        { data: 'house_name', name: 'house_name' },
                        { data: 'status', name: 'status', searchable: false },
                        { data: 'action', name: 'Action', searchable: false ,className: 'dt-center', targets : '_all'},
                        {data: 'application_id', name: 'application_id', searchable: false},
                    ]
                });
                oTable.columns([16]).visible(false);

            }

            if($('#approval_process_type_id').val()==1)
                $('.checkboxIdentifier').addClass('d-none');
            else
                $('.checkboxIdentifier').removeClass('d-none');
        }

        function checkIndividual(check, i) {
            if (check.checked) {
                $("#checkbox_" + i).prop("readonly", false);
            } else {
                $("#checkbox_" + i).prop("readonly", true);
            }
        }

        $(document).ready(function() {
            let id = '{{request()->get('application_id')}}';
            let pop = '{{request()->get('pop')}}';

            if (id && pop) {
                if(pop=='true'){
                    goFlow(id);
                }
            }

            $("#workflow_assign_form").attr('action', '{{ route('get-approval-post') }}');
            $("#workflow_form").attr('action', '{{ route('approval-post') }}');

            function load_advertisement_for_efg(obj={}){
                var is_efg = 2;
                if(obj.checked){
                    is_efg = 1;
                    $("#load_advertisement_for_efg").val(is_efg);
                }else{
                    is_efg = 2;
                    $("#load_advertisement_for_efg").val(is_efg);
                }
                $.ajax({
                    type: 'GET',
                    data:{
                        'is_efg':is_efg
                    },
                    url: '/get-advertisement-dropdownlist',
                    success: function (msg) {
                        $("#advertisement_id").html(msg);
                    }
                });
                return false;
            }
            $(document).on('change','#load_advertisement_for_efg', function(e){
                load_advertisement_for_efg(this);
            });
            {{--@if($data['checked_advertisement_for_efg']==2)
                load_advertisement_for_efg();
            @endif--}}
            $('#houseAllotmentApprovalModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var applicationId = button.attr('data-application-id');
                // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                var modal = $(this);
                if(applicationId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL+'/ha-applications-approval/'+applicationId,
                        success: function (data) {
                            modal.find('#houseAllotmentApprovalForm').html(data);
                        },
                        error: function (err) {
                            alert('error', err);
                        }
                    });
                }
            });

            $('#advertisement_id').on('change', function(e) {
                e.preventDefault();
                let advertisementId = $(this).val();

                if( (advertisementId !== undefined) && (advertisementId != '')) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL+'/ajax/house-type/'+advertisementId,
                        success: function (data) {
                            $('#house_type_id').html(data);
                        },
                        error: function (err) {
                            alert('error', err);
                        }
                    });
                }
            });

           applicationDatatable();
            @if(($data['approval_process_type_id'] == 2)&&($data['multi_workflow_id'] == 1))
                        getWorkflowAssignDropDownList('#workflowAssignDropDownList');
            @endif
        });
    </script>
@endsection
