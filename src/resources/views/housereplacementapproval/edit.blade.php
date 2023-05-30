@extends('layouts.default')

@section('title')
    House Replacement Approval
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
                <div class="card-body"><h4 class="card-title">House Replacement Approval</h4>
                    <hr>
                    @include('housereplacementapproval.form')
                </div>
            </div>
            <div class="card">
                <div class="card-body"><h4 class="card-title">House Replacement Applications</h4><!---->
                    <hr>
                    <div class="table-responsive">
                        <table  class="table table-sm datatable mdl-data-table dataTable" id="house-replacement-approvals">
                            <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>Current House</th>
                                <th>Approved Date</th>
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

@include('approval.workflowmodal')

@include('approval.workflowselect')

@section('footer-script')
    <script type="text/javascript">
        let userRoles = '@php echo json_encode(Auth::user()->roles->pluck('role_key')); @endphp';

        function getFlow(prefix) {
            let myModal = $('#workflowM');
            let replace_app_id = '@php echo $data['houseReplacementApplication']->replace_app_id; @endphp';
            $('#application_id_flow').val(replace_app_id);
            $('#t_name').val('replacement_application');
            $('#c_name').val('replace_app_id');
            $.ajax({
                type: 'GET',
                url: '/get-approval-list',
                success: function (msg) {
                    $("#flow_id").html(msg.options);
                    $("input#prefix").val(prefix);
                }
            });
            myModal.modal({show: true});
            return false;
        }

        function goFlow(prefix) {
            let myModal = $('#status-show');
            let tmp = null;
            let t_name ='replacement_application';
            let c_name ='replace_app_id';
            let replace_app_id = '@php echo $data['houseReplacementApplication']->replace_app_id; @endphp';

            $.ajax({
                async: false,
                type: 'GET',
                url: '/get-workflow-id',
                data: {row_id: replace_app_id, t_name: t_name, c_name: c_name},
                success: function (msg) {
                    $("#workflow_id").val(msg);
                    tmp = msg;
                    $("input#prefix").val(prefix);
                }
            });
            $("#object_id").val(replace_app_id);
            $("#get_url").val(window.location.pathname.slice(1)+'?replace_app_id='+replace_app_id+'&pop=true');
            $.ajax({
                type: 'GET',
                url: '/approval',
                data: {workflowId: tmp, objectid: 'raa'+replace_app_id},
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

                    $('#prefix').val('raa');

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
                        // $(".no-permission").css("display", "block");
                        // $(document).find('#hide_portion').hide();
                        if(msg.is_approved)
                        {
                            $(".no-permission").css("display", "block");
                            $(document).find('#hide_portion').hide();
                        }
                        else if (JSON.stringify(userRoles).indexOf(msg.current_step.user_role) > -1)
                        {
                            $(document).find('#hide-form-btn').hide();
                            $(document).find("#approve_btn").show();
                        }
                        else
                        {
                            $(".no-permission").css("display", "block");
                            $(document).find('#hide_portion').hide();
                        }
                    } else if (JSON.stringify(userRoles).indexOf(msg.current_step.user_role) > -1) {
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

        function houseReplacementApplicationDatatable()
        {
            $('#house-replacement-approvals').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/house-replacement-approvals-datatable-list",
                columns: [
                    {data: 'employee_code', name: 'employee_code'},
                    {data: 'employee_name', name: 'employee_name'},
                    {data: 'employee_house_name', name: 'employee_house_name'},
                    {data: 'approved_date', name: 'approved_date'},
                    {data: 'action', name: 'Action', searchable: false},
                ]
            });
        }

        $(document).ready(function() {
            $("#workflow_assign_form").attr('action', '{{ route('get-approval-post') }}');
            $("#workflow_form").attr('action', '{{ route('approval-post') }}');
            houseReplacementApplicationDatatable();
        });
    </script>
@endsection
