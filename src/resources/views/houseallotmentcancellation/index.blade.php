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
                <div class="card-body"><h4 class="card-title">Allotment Cancel</h4><!---->
                    <hr>
                    <div class="table-responsive">
                        <table  class="table table-sm datatable mdl-data-table dataTable" id="house-allotment">
                            <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Applied Date</th>
                                <th>Approval Date</th>
                                <th>House</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->hasPermission('CAN_ALLOTMENT_CANCEL'))
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body"><h4 class="card-title">Allotment Cancel List </h4><!---->
                    <hr>
                    <div class="table-responsive">
                        <table  class="table table-sm datatable mdl-data-table dataTable" id="house-allotment-list">
                            <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>House Name</th>
                                <th>Cancel Reason</th>

                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="modal fade" id="houseAllotmentCancellationModal" tabindex="-1" role="dialog" aria-labelledby="houseAllotmentCancellationModalLabel" aria-hidden="true">
        <div class="modal-dialog mw-100 w-50">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="houseAllotmentCancellationModalLabel">
                        @if(Auth::user()->hasPermission('CAN_ALLOTMENT_CANCEL'))
                            House Allotment Cancellation Form
                        @else
                            House Allotment Cancellation Request Form
                        @endif
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="houseAllotmentCancellationForm"></div>
            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">
        function houseAllotmentDatatable()
        {
            $('#house-allotment').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/house-allotment-cancellation-datatable",
                columns: [
                    {data: 'employee.emp_code', name: 'employee.emp_code'},
                    {data: 'employee.emp_name', name: 'employee.emp_name'},
                    {data: 'employee.department.department_name', name: 'employee.department.department_name', searchable: false, orderable: false },
                    {data: 'employee.designation.designation', name: 'employee.designation.designation', searchable: false, orderable: false },
                    {data: 'application_date', name: 'application_date'},
                    {data: 'approval_date', name: 'approval_date'},
                    {data: 'house.house_name', name: 'house.house_name'},
                    {data: 'action', name: 'Action', searchable: false},
                ]
            });
        }
        function houseAllotmentCancelDatatable()
        {
            $('#house-allotment-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/house-allotment-cancellation-list",
                columns: [
                    {data: 'emp_code', name: 'emp_code'},
                    {data: 'emp_name', name: 'emp_name'},
                    {data: 'department_name', name: 'department_name' },
                    {data: 'designation', name: 'designation' },
                    {data: 'house_name', name: 'house_name'},
                    {data: 'cancel_reason', name: 'cancel_reason'},
                ]
            });
        }

        $(document).ready(function() {

            houseAllotmentDatatable();
            houseAllotmentCancelDatatable();

            $('#houseAllotmentCancellationModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var allotmentId = button.attr('data-allotment-id');
                // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                var modal = $(this);
                if(allotmentId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL+'/house-allotment-cancellations/'+allotmentId,
                        success: function (data) {
                            modal.find('#houseAllotmentCancellationForm').html(data);
                        },
                        error: function (err) {
                            alert('error', err);
                        }
                    });
                }
            });
        });


    </script>
@endsection
