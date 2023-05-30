@extends('layouts.default')

@section('title')
	hello
@endsection

@section('header-style')
	<!--Load custom style link or css-->
    <style>

        .displayNone{
            display: none;
        }
    </style>
@endsection

@section('content')
	<div class="row">
		<div class="col-12">

            <div class="card"><!----><!---->
				<div class="card-body"><h4 class="card-title">Available Building list</h4><!---->
					<hr>
                        <table id="advMainTable" class="table display"  cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>Advertise No.</th>
                                <th>Advertise Date</th>
                                <th>Application Start</th>
                                <th>Application End</th>
                                <th>Advertised House</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
            </div>


            <div class="card"><!----><!---->
                <div class="card-body"><h4 class="card-title">Advertisement Entry</h4><!---->
                    <hr>
                    <form id="colony-register" method="POST"

{{--                          action="{{ route('search_advertisements.update', ['id' => '3']) }}"--}}
                    >

                        {{--                        @if($data['colony']->colony_id)--}}
                        {{--                            action="{{ route('colony.update', ['id' => $data['colony']->colony_id]) }}">--}}
                        {{--                            <input name="_method" type="hidden" value="PUT">--}}
                        {{--                        @else--}}
                        {{--                            action="{{ route('colony.store') }}">--}}
                        {{--                        @endif--}}

                        {{ csrf_field() }}
                        <div class="row justify-content-center">
                            <div class="col-md-11">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="required">Advertisement No.</label>
                                        <input required type="text" value="" placeholder="Enter Advertisement No." name="advertisement_no" min="3" class="form-control" id="advertisement_no" >
                                    </div>

                                    <div class="col-md-3">
                                        <label class="required">Publish Date</label>
                                        <div class="input-group date" id="datetimepicker14" data-target-input="nearest">
                                            <input type="text"  value="" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker14"
                                                   required
                                                   id="publish_date"
                                                   name="publish_date"
                                            />
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Application Start Date</label>
                                        <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                            <input type="text"  value="" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker1"
                                                   required
                                                   id="application_start_date"
                                                   name="application_start_date"
                                            />
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Application Deadline</label>
                                        <div class="input-group date" id="datetimepicker7" data-target-input="nearest">
                                            <input type="text"  value="" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker7"
                                                   required
                                                   id="application_end_date"
                                                   name="application_end_date"
                                            />
                                        </div>
                                    </div>

                                </div>
                                <div class="row my-2">
                                    <div class="col-md-12">
                                        <div id="houseSelectionPanel" class="card
                                        border ">
                                            <div class="card-header hidePanel border"><i class='bx bx-window-close cursor-pointer'>close</i></div>
                                            <div class="card card-body col-sm-12" id="houseList">

{{--                                                    {!! $data['houseListOtpion'] !!}--}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="required">Total Available</label>
                                        <input required type="text" value="" name="total_avilable" min="3" class="form-control" id="total_avilable" >
                                    </div>
                                    <div class="col-md-4">
                                        <label class="required">Description</label>
                                        <textarea placeholder="Enter Description" rows="3" wrap="soft" name="description"
                                                  class="form-control" id="description"></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="required">Description (Bangla)</label>
                                        <textarea placeholder="Enter Description (Bangla)" rows="3" wrap="soft" name="description_bang"
                                                  class="form-control" id="description_bang"></textarea>
                                    </div>
                                </div>


                                <div class="row my-2">
                                    <input type="hidden" name="colony_id" id="colony_id">
                                    <div class="d-flex justify-content-end col">
                                        <button type="submit" id="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                            Submit
                                        </button> &nbsp;
                                        <button type="reset" id="reset" class="btn btn btn-outline shadow mb-1 btn-secondary">
                                            Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- {{ Form::close() }} --}}
                    </form>
                </div>
            </div>

		</div>
	</div>

@endsection

    @section('footer-script')
	<!--Load custom script-->
    <script type="text/javascript" >
    $(document).ready(function() {

        $('#advMainTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: APP_URL+'/advertisement-list',
            columns: [
                  {data:'adv_number', name:'adv_number'},
                  { data: 'adv_date', name: 'adv_date', searchable: true },
                  {data: 'app_start_date', name: 'app_start_date'},
                  {data: 'app_end_date', name: 'app_end_date'},
                  {data: 'house_no', name:'house_no', searchable: true},
                  { data:'active_yn', name:'active_yn'},
                  { data: 'action', name: 'Action', searchable: false },
            ]
        });

        $('#datetimepicker14').datetimepicker({
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
        $('#datetimepicker7').datetimepicker({
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

    });

    function displayPanel(advId){
        $('#houseSelectionPanel').show(500);
        if(advId !== undefined && advId) {
            $.ajax({
                type: "GET",
                url: APP_URL+"/advertisements-list-edit/"+advId,
                success: function (data) {
                    // console.log(data.advMstData[0].adv_number);
                    // console.log(data.advMstData);
                    $('#houseList').html(data.houseListOtpion);
                    $('#advertisement_no').val(data.advMstData[0].adv_number);
                    $('#publish_date').val(data.advMstData[0].adv_date);
                    $('#application_start_date').val(data.advMstData[0].app_start_date);
                    $('#application_end_date').val(data.advMstData[0].app_end_date);
                    $('#total_avilable').val();
                    $('#description').val(data.advMstData[0].description);
                    $('#description_bang').val(data.advMstData[0].description_bng);
                },
                error: function (data) {
                    alert('error');
                }
            });

        }
    }
    function hidePanel(){
        $('#houseSelectionPanel').hide(500);
    }
    $('.hidePanel').on("click",function(){
        hidePanel();
    });
     </script>
@endsection


