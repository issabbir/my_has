@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
	<!--Load custom style link or css-->
    <style>
        table tr{
            cursor: pointer;
        }
        .displayNone{
            display: none;
        }
        .textBlueColor{
            color: #5A8DEE;
        }
    </style>
@endsection

@section('content')
	<div class="row">
		<div class="col-12">
            @if(!isset($data['allotmentLetter'][0]->allot_letter_no))
            <div class="card"><!----><!---->
                <div class="card-body">
                    <h4 class="card-title">House Interchange Applications List</h4>
                    <hr/>

                    <div class="row">
                        <div class="col-md-12">
                                <div class="table-responsive">
                                    <table  class="table table-sm datatable mdl-data-table dataTable" id="interchangeApproveList">
                                        <thead>
                                        <tr>
                                            <th><span class="textBlueColor">1st Employee Code</span></th>
                                            <th><span class="textBlueColor">1st Employee Name</span></th>
                                            <th><span class="textBlueColor">1st Employee House</span></th>
                                            <th><span class="info">2nd Employee Code</span></th>
                                            <th><span class="info">2nd Employee Name</span></th>
                                            <th><span class="info">2nd Employee House</span></th>
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
            @endif

			<div class="card"><!----><!---->
				<div class="card-body"><h4 class="card-title">Allotment Letter Entry</h4><!---->

					<hr>
					<form id="interchange-allotment-register" method="POST"

                        @if(isset($data['allotmentLetter'][0]->allot_letter_no)))
                            action="{{ route('allotmentLetterInterchange.update', ['id' => $data['allotmentLetter'][0]->allot_letter_id]) }}">

                            <input name="_method" type="hidden" value="PUT">
                        @else
                            action="{{ route('allotmentLetterInterchange.store') }}">
                        @endif
                            {{ csrf_field() }}
                        <div class="row">

                            <div class="col-md-6 grid-divider">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Employee Name</label>
                                            <input type="text" readonly value="{{isset($data['firstEmployeeInformation']['emp_name'])? $data['firstEmployeeInformation']['emp_name'] : ''}}" name="emp_name1" class="form-control" id="emp_name" />
                                        </div>
                                        <div class="col-md-4">
                                            <label>Designation</label>
                                            <input type="text" readonly value="{{isset($data['firstEmployeeInformation']['designation'])? $data['firstEmployeeInformation']['designation']:''}}" name="emp_designation1" class="form-control" id="emp_designation" />
                                        </div>

                                        <div class="col-md-4">
                                            <label>Department</label>
                                            <input type="text" readonly value="{{isset($data['firstEmployeeInformation']['department'])? $data['firstEmployeeInformation']['department']:''}}" name="emp_department1" class="form-control" id="emp_department" />
                                        </div>

                                    </div>
                                    <div class="row my-1">
                                        <div class="col-md-4">
                                            <label>Section</label>
                                            <input type="text" readonly value="{{isset($data['firstEmployeeInformation']['section'])? $data['firstEmployeeInformation']['section']:''}}" name="emp_section1" class="form-control" id="emp_section" />
                                        </div>
                                        <div class="col-md-4">
                                            <label>Allotted Building</label>
                                            <input type="text" readonly value="{{isset($data['firstEmployeeInformation']['house_name'])? $data['firstEmployeeInformation']['house_name']:''}}" name="emp_allotted_building1" class="form-control" id="emp_allotted_building" />
                                        </div>
                                        <div class="col-md-4">
                                            <label>Allotted House</label>
                                            <input type="text" readonly value="{{isset($data['firstEmployeeInformation']['building_name'])? $data['firstEmployeeInformation']['building_name']:''}}" name="emp_allotted_house1" class="form-control" id="emp_allotted_house" />
                                        </div>
                                    </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Employee Name</label>
                                        <input type="text" readonly value="{{isset($data['secondEmployeeInformation']['emp_name'])? $data['secondEmployeeInformation']['emp_name']:''}}" name="emp_name2" class="form-control" id="emp_name" />
                                    </div>
                                    <div class="col-md-4">
                                        <label>Designation</label>
                                        <input type="text" readonly value="{{isset($data['secondEmployeeInformation']['designation'])? $data['secondEmployeeInformation']['designation']:''}}" name="emp_designation2" class="form-control" id="emp_designation" />
                                    </div>

                                    <div class="col-md-4">
                                        <label>Department</label>
                                        <input type="text" readonly value="{{isset($data['secondEmployeeInformation']['department'])? $data['secondEmployeeInformation']['department']:''}}" name="emp_department2" class="form-control" id="emp_department" />
                                    </div>

                                </div>
                                <div class="row my-1">
                                    <div class="col-md-4">
                                        <label>Section</label>
                                        <input type="text" readonly value="{{isset($data['secondEmployeeInformation']['section'])? $data['secondEmployeeInformation']['section']:''}}" name="emp_section2" class="form-control" id="emp_section" />
                                    </div>
                                    <div class="col-md-4">
                                        <label>Allotted Building</label>
                                        <input type="text" readonly value="{{isset($data['secondEmployeeInformation']['building_name'])? $data['secondEmployeeInformation']['building_name']:''}}" name="emp_allotted_building2" class="form-control" id="emp_allotted_building" />
                                    </div>
                                    <div class="col-md-4">
                                        <label>Allotted House</label>
                                        <input type="text" readonly value="{{isset($data['secondEmployeeInformation']['house_name'])? $data['secondEmployeeInformation']['house_name']:''}}" name="emp_allotted_house2" class="form-control" id="emp_allotted_house" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                    <div class="row my-1">
                                        <div class="col-md-3">
                                            <label class="required">Order No.</label>
                                            <input required type="text" value="{{ isset($data['allotmentLetter'][0]->allot_letter_no) ? $data['allotmentLetter'][0]->allot_letter_no : '' }}" placeholder="Allotted Letter Order No." name="allotment_letter_no" class="form-control" id="allotment_letter_no" />
                                        </div>

                                        <div class="col-md-3">
                                            <label class="required">Allotted Letter Order Date</label>
                                            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                                <input type="text" value="{{ isset($data['allotmentLetter'][0]->allot_letter_date) ? $data['allotmentLetter'][0]->allot_letter_date : date('Y-m-d') }}"
                                                       class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker1"
                                                       required
                                                       id="allotment_letter_date"
                                                       name="allotment_letter_date"
                                                />
                                            </div>
                                        </div>
{{--                                        @if(isset($data['allotmentLetter'][0]->allot_letter_no))--}}
                                            <div class="col-md-3 ">
                                                <label>Memo Date</label>
                                                <div class="input-group date" id="datetimepicker3" data-target-input="nearest">
                                                    <input type="text" value="{{ isset($data['allotmentLetter'][0]->memo_date) ? $data['allotmentLetter'][0]->memo_date : '' }}"
                                                           class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker3"
                                                           id="memo_date"
                                                           name="memo_date"
                                                    />
                                                </div>
                                            </div>
{{--                                        @endif--}}

{{--                                        @if(isset($data['allotmentLetter'][0]->allot_letter_no))--}}
                                            <div class="col-md-3" id="letterDeliveryStatus">
                                                <label> Memo No.</label>
                                                <input type="text" value="{{ isset($data['allotmentLetter'][0]->memo_no) ? $data['allotmentLetter'][0]->memo_no : '' }}"
                                                       placeholder="Allotted Letter Memo No." name="memo_no" class="form-control" id="memo_no" />
                                                <input type="hidden" value="{{ isset($data['interchangeInfo']->int_change_id) ? $data['interchangeInfo']->int_change_id : '' }}" name="int_change_id" id="int_change_id"  class="form-control" />

                                            </div>
{{--                                        @endif--}}

                                    </div>


                                @if(isset($data['allotmentLetter'][0]->allot_letter_no))
                                    <div class="row my-1">

                                        <div class="col-md-3 ">
                                            <label>Delivered By</label>
                                            <select class="custom-select" name="deliveredBy" id="deliveredBy">
                                                        @foreach($data['employeeList'] as $option)
                                                            {!!$option!!}
                                                        @endforeach
                                            </select>
                                        </div>

                                            <div class="col-md-3 ">
                                                <label>Letter Delivery Date</label>
                                                <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                                                    <input type="text" value="{{ isset($data['allotmentLetter'][0]->delivery_date) ? $data['allotmentLetter'][0]->delivery_date : '' }}"
                                                           class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker2"
                                                           required
                                                           id="allotment_letter_delivery_date"
                                                           name="allotment_letter_delivery_date"
                                                    />
                                                </div>
                                            </div>

                                            <div class="col-md-3" id="letterDeliveryStatus">
                                                <label class="required"> Delivery Status</label>
                                                <select required class="custom-select" name="delivery_yn" id="delivery_yn">
                                                    <option value="Y" {{ ($data['allotmentLetter'][0]->delivery_yn) == 'Y' ? 'selected': '' }}>Delivered</option>
                                                    <option value="N" {{ ($data['allotmentLetter'][0]->delivery_yn) == 'N' ? 'selected': ''  }}>Not Delivered</option>
                                                </select>
                                            </div>

                                    </div>
                                @endif

                                    <div class="row my-2">
                                        <input type="hidden" name="colony_id" id="colony_id">
                                        <div class="d-flex justify-content-end col">
                                            <button type="submit" id="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                                Submit
                                            </button> &nbsp;
                                            @if(!isset($data['allotmentLetter'][0]->allot_letter_no))
                                            <button type="reset" id="reset" class="btn btn btn-outline shadow mb-1 btn-secondary">
                                                Reset
                                            </button>
                                            @endif
                                        </div>
                                    </div>

                             </div>

                        </div>
                        {{-- {{ Form::close() }} --}}
					</form>
				</div>
            </div>

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
{{--                                <th>1st Employee Code</th>--}}
                                <th><span class="textBlueColor">1st Employee Name</span></th>
                                <th><span class="textBlueColor">1st Employee House</span></th>
{{--                                <th>2nd Employee Code</th>--}}
                                <th><span class="info">2nd Employee Name</span></th>
                                <th><span class="info">2nd Employee House</span></th>
                                <th>Approved Date</th>
                                <th>Allotted Letter Order No.</th>
                                <th>Allotted Letter Date</th>
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
        $('#datetimepicker1').datetimepicker({
            format: 'YYYY-MM-DD',
            // format: 'L',
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
        $('#datetimepicker2').datetimepicker({
            format: 'YYYY-MM-DD',
            // format: 'L',
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

        $('#datetimepicker3').datetimepicker({
            format: 'YYYY-MM-DD',
            // format: 'L',
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

        $('#allotmentLetterTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: APP_URL+"/interchange-allotment-letter-list",
            columns: [
                // {data: 'first_employee_code', name: 'first_employee_code'},
                {data: 'first_employee_name', name: 'first_employee_name'},
                {data: 'first_employee_house_name', name: 'first_employee_house_name'},
                // {data: 'second_employee_code', name: 'second_employee_code'},
                {data: 'second_employee_name', name: 'second_employee_name'},
                {data: 'second_employee_house_name', name: 'second_employee_house_name'},
                {data: 'approved_date', name: 'approved_date'},

                {data:'allot_letter_no', name:'allot_letter_no',searchable: true },
                {data:'allot_letter_date', name:'allot_letter_date',searchable: true },
                {data:'delivery_yn', name:'delivery_yn',searchable: true },
                {data: 'action', name: 'Action', searchable: false },
            ]
        });

        $('#interchangeApproveList').DataTable({
            processing: true,
            serverSide: true,
            ajax: APP_URL+"/interchange-approvals-datatable-list",
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

        function setEmployeeInformation(employeeCode)
        {
            //evt.preventDefault();
            $.ajax({
                type: "GET",
                url: APP_URL+'/ajax/employee-house-allocated-info/'+employeeCode,
                success: function (data) {
                    if(data.employeeInformation) {
                        $('#emp_name').val(data.employeeInformation.emp_name);
                        $('#emp_designation').val(data.employeeInformation.designation);
                        $('#emp_department').val(data.employeeInformation.department);
                        $('#emp_section').val(data.employeeInformation.section);
                        $('#emp_allotted_building').val(data.employeeInformation.building_name);
                        $('#emp_allotted_house_adv_no').val(data.employeeInformation.adv_number);
                        $('#emp_allotted_house').val(data.employeeInformation.house_name);
                        $('#emp_house_approval_date').val(data.employeeInformation.approval_date);
                        $('#emp_application_id').val(data.employeeInformation.application_id);
                        $('#emp_allotted_house_adv_id').val(data.employeeInformation.advertisement_id);

                    } else {
                        $('#emp_name').val('');
                        $('#emp_designation').val('');
                        $('#emp_department').val();
                        $('#emp_section').val();
                        $('#emp_allotted_building').val();
                        $('#emp_allotted_house_adv_no').val();
                        $('#emp_allotted_house').val();
                        $('#emp_house_approval_date').val();
                        $('#emp_application_id').val();
                        $('#emp_allotted_house_adv_id').val();
                    }

                },
                error: function (err) {
                    alert('error', err);
                }
            });
        }


    });

     </script>
@endsection


