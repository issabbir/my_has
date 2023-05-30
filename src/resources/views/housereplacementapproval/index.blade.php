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

@section('footer-script')
    <script type="text/javascript">
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
            houseReplacementApplicationDatatable();
        });
    </script>
@endsection
