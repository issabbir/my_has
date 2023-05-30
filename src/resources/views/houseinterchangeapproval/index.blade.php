@extends('layouts.default')

@section('title')
    House Interchange Approval
@endsection

@section('header-style')
    <!--Load custom style link or css-->
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body"><h4 class="card-title">House Interchange Applications</h4><!---->
                    <hr>
                    <div class="table-responsive">
                        <table  class="table table-sm datatable mdl-data-table dataTable" id="house-interchange-approvals">
                            <thead>
                            <tr>
                                <th>1st Employee Code</th>
                                <th>1st Employee Name</th>
                                <th>1st Employee House</th>
                                <th>2nd Employee Code</th>
                                <th>2nd Employee Name</th>
                                <th>2nd Employee House</th>
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
        function houseInterchangeApplicationDatatable()
        {
            $('#house-interchange-approvals').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + "/house-interchange-approvals-datatable-list",
                columns: [
                    {data: 'first_employee_code', name: 'first_employee_code'},
                    {data: 'first_employee_name', name: 'first_employee_name'},
                    {data: 'first_employee_house_name', name: 'first_employee_house_name'},
                    {data: 'second_employee_code', name: 'second_employee_code'},
                    {data: 'second_employee_name', name: 'second_employee_name'},
                    {data: 'second_employee_house_name', name: 'second_employee_house_name'},
                    {data: 'approved_date', name: 'approved_date'},
                    {data: 'action', name: 'Action', searchable: false},
                ]
            });
        }

        $(document).ready(function() {
            houseInterchangeApplicationDatatable();
        });
    </script>
@endsection
