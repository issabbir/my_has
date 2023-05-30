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
                <div class="card-body"><h4 class="card-title">House Handover Application</h4>
                    <hr>
                    @if($data == !null)
                        @include('handOverApplication.h-form')
                    @else
                        <p>You have no allotted house.</p>
                    @endif
                </div>
            </div>

            <div class="card"><!----><!---->
                <div class="card-body"><h4 class="card-title">Handover Request List</h4><!---->
                    <hr/>
                    <div class="table-responsive">
                        <table id="handover_reqs" class="table table-sm datatable mdl-data-table dataTable">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>House Name</th>
                                <th>Department</th>
                                <th>Reason</th>
                                <th>Document</th>

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
    <!--Load custom script-->
    <script type="text/javascript">

        $(document).ready(function () {
            $('#handover_reqs').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL+"/hand-over-application-datatable-list",
                columns: [
                    {data: 'DT_RowIndex', "name": 'DT_RowIndex'},
                    {data: 'house_name', name: 'house_name',searchable: true },
                    {data: 'emp_department', name: 'emp_department',searchable: true },
                    {data: 'hand_over_reason', name: 'hand_over_reason',searchable: true },
                    {data: 'document', name: 'document', searchable: true},


                ]
            });

        });


    </script>
@endsection


