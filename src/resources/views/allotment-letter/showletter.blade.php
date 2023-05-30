@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
	<!--Load custom style link or css-->
    <style>
        #ha-applications table tr{
            cursor: pointer;
        }
        .displayNone{
            display: none;
        }
    </style>
@endsection

@section('content')
	<div class="row">
		<div class="col-12">
                <div class="card"><!----><!---->
				<div class="card-body"><h4 class="card-title">Allotment Letter List</h4><!---->

{{--                    <a target="_blank" class="btn btn-primary mr-1" href="/report/render?xdo=/~weblogic/HAS/RPT_COLONY_LIST.xdo&type=pdf&filename=colony_list">--}}
{{--                        PDF--}}
{{--                    </a>--}}
					<hr/>
                    <div class="table-responsive">
                        <table id="allotmentLetterTable" class="table table-sm datatable mdl-data-table dataTable">
                            <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Employee Name</th>
                                <th>Advertisement No</th>
                                <th>Order No.</th>
                                <th>Order Allotted Letter Date</th>
                                <th>Status</th>
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
	<!--Load custom script-->
    <script type="text/javascript" >
    $(document).ready(function() {


        $('#allotmentLetterTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: APP_URL+"/user-wise-allot-letter",
            columns: [
                {data: 'emp_code', name: 'emp_code',searchable: true },
                {data: 'emp_name', name: 'emp_name',searchable: true },
                {data: 'adv_number', name: 'adv_number',searchable: true },
                //{data: 'colony_type.colony_type', name:'colony_type.colony_type',searchable: false },
                {data:'allot_letter_no', name:'allot_letter_no',searchable: true },
                {data:'allot_letter_date', name:'allot_letter_date',searchable: true },
                {data:'delivery_yn', name:'delivery_yn',searchable: true },
                {data: 'action', name: 'Action', searchable: false },
            ]
        });






    });

     </script>
@endsection


