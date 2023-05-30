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
            @if(!isset($data['allotmentLetter'][0]->allot_letter_no))
            <div class="card"><!----><!---->
                <div class="card-body">
                    <h4 class="card-title">Advertisement Wise Allotment Letter List</h4>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">

                                <label class="required">Advertisement</label>
                                <select class="custom-select select2" name="advertisement_id" id="advertisement_id" required>
                                    @if(isset($data['advertisements']))
                                        @foreach($data['advertisements'] as $advertisement)
                                           {!! $advertisement !!}
                                        @endforeach
                                    @endif
                                </select>

                        </div>
                    </div>

                    <div class="row my-2">
                        <div class="col-md-12">
                            <div id="ha-applications">

                             </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

			<div class="card"><!----><!---->
				<div class="card-body"><h4 class="card-title">Allotment Letter Entry</h4><!---->
					<hr>
					<form id="allotment-register" method="POST"

                        @if(isset($data['allotmentLetter'][0]->allot_letter_no)))
                            action="{{ route('allotmentLetter.update', ['id' => $data['allotmentLetter'][0]->allot_letter_id]) }}">
                            <input name="_method" type="hidden" value="PUT">
                        @else
                            action="{{ route('allotmentLetter.store') }}">
                        @endif
                            {{ csrf_field() }}
                        <div class="row">

                            <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Employee Name</label>
                                            <input type="text" readonly value="" name="emp_name" class="form-control" id="emp_name" />
                                        </div>
                                        <div class="col-md-3">
                                            <label>Designation</label>
                                            <input type="text" readonly value="" name="emp_designation" class="form-control" id="emp_designation" />
                                        </div>

                                        <div class="col-md-3">
                                            <label>Department</label>
                                            <input type="text" readonly value="" name="emp_department" class="form-control" id="emp_department" />
                                        </div>

                                        <div class="col-md-3">
                                            <label>Section</label>
                                            <input type="text" readonly value="" name="emp_section" class="form-control" id="emp_section" />
                                        </div>
                                    </div>
                                    <div class="row my-1">
                                        <div class="col-md-3">
                                            <label>Allotted Building</label>
                                            <input type="text" readonly value="" name="emp_allotted_building" class="form-control" id="emp_allotted_building" />
                                        </div>
                                        <div class="col-md-3">
                                            <label>Allotted House</label>
                                            <input type="text" readonly value="" name="emp_allotted_house" class="form-control" id="emp_allotted_house" />
                                        </div>

                                        <div class="col-md-3">
                                            <label>Advertisement NO.</label>
                                            <input type="text" readonly value="" name="emp_allotted_house_adv_no" class="form-control" id="emp_allotted_house_adv_no" />
                                            <input type="hidden" value="" name="emp_application_id" class="form-control" id="emp_application_id" />
                                            <input type="hidden" value="" name="emp_allotted_house_adv_id" class="form-control" id="emp_allotted_house_adv_id" />
                                        </div>
                                        <div class="col-md-3">
                                            <label>House Approval Date Time</label>
                                            <input type="text" readonly value="" name="emp_house_approval_date" class="form-control" id="emp_house_approval_date" />
                                        </div>
                                    </div>

                                    <div class="row my-1">
                                        {{--                                        <div class="col-md-6">--}}
                                        {{--                                            <label class="required">Employee Code</label>--}}
                                        {{--                                            <input required type="text" value="" placeholder="Enter Employee Code" name="emp_code" min="3" class="form-control" id="emp_code" />--}}
                                        {{--                                        </div>--}}
                                        <div class="col-md-3">
                                            <label class="required">Order No.</label>
                                            <input required type="text" value="{{ isset($data['allotmentLetter'][0]->allot_letter_no) ? $data['allotmentLetter'][0]->allot_letter_no : '' }}" placeholder="Order No." name="allotment_letter_no" class="form-control" id="allotment_letter_no" />
                                        </div>

                                        <div class="col-md-3">
                                            <label class="required">Allotted Letter Order Date</label>
                                            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                                <input type="text" value="{{ isset($data['allotmentLetter'][0]->allot_letter_date) ? $data['allotmentLetter'][0]->allot_letter_date : '' }}"
                                                       class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker1"
                                                       required
                                                       id="allotment_letter_date"
                                                       name="allotment_letter_date"
                                                       autocomplete="off"
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
                                                           autocomplete="off"
                                                    />
                                                </div>
                                            </div>
{{--                                        @endif--}}

{{--                                        @if(isset($data['allotmentLetter'][0]->allot_letter_no))--}}
                                            <div class="col-md-3" id="letterDeliveryStatus">
                                                <label> Memo No.</label>
                                                <input type="text" value="{{ isset($data['allotmentLetter'][0]->memo_no) ? $data['allotmentLetter'][0]->memo_no : '' }}"
                                                       placeholder="Allotted Letter Memo No." name="memo_no" class="form-control" id="memo_no" />
                                            </div>
{{--                                        @endif--}}

                                    </div>


                                @if(isset($data['allotmentLetter'][0]->allot_letter_no))
                                    <div class="row my-1">

                                        <div class="col-md-3 ">
                                            <label class="required">Delivered By</label>
                                            <select class="custom-select" name="deliveredBy" id="deliveredBy" required>
                                                @if(isset($data['employeeList']))
                                                        @foreach($data['employeeList'] as $option)
                                                            {!!$option!!}
                                                        @endforeach
                                                @endif
                                            </select>
                                        </div>

                                            <div class="col-md-3 ">
                                                <label class="required">Letter Delivery Date</label>
                                                <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                                                    <input type="text" value="{{ isset($data['allotmentLetter'][0]->delivery_date) ? $data['allotmentLetter'][0]->delivery_date : '' }}"
                                                           class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker2"
                                                           required
                                                           id="allotment_letter_delivery_date"
                                                           name="allotment_letter_delivery_date"
                                                           autocomplete="off"
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
            ajax: APP_URL+"/allotment-letter-list",
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

        $('#advertisement_id').on("change", function () {
            let advertisement_id = $('#advertisement_id').val();
            loadAdvertisementList(advertisement_id);
        });

        $(document).on("click",".selectApplication",function(event) {
           setEmployeeInformation($(this).val());
        });

        @if(isset($data['allotmentLetter'][0]->allot_letter_no))
            setEmployeeInformation('{!! $data['allotmentLetter'][0]->emp_code !!}');
        @endif

        function loadAdvertisementList(advertisement_id){

            if(advertisement_id !== undefined && advertisement_id) {
                $.ajax({
                    type: "GET",
                    url: APP_URL+"/ha-applications-datatable-list-to-allotment/"+advertisement_id,
                    success: function (data) {
                        $('#ha-applications').html(data);
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });

                //$('#applicationListTable').DataTable({});
            }

        }

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


