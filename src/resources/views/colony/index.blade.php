@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
	<!--Load custom style link or css-->
    <style>
        .row{
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
			{{--<div class="card bg-transparent border">
				<div class="card-content">
					<div class="card-body pt-1">
						hello

					</div>
				</div>
			</div>--}}

			<div class="card"><!----><!---->
				<div class="card-body"><h4 class="card-title">Residential Area Register</h4><!---->
					<hr>
					<form id="colony-register" method="POST"

                        @if($data['colony']->colony_id)
                            action="{{ route('colony.update', ['id' => $data['colony']->colony_id]) }}">
                            <input name="_method" type="hidden" value="PUT">

                        @else
                            action="{{ route('colony.store') }}">
                        @endif

                            {{ csrf_field() }}
                            <div class="row justify-content-center">
                                <div class="col-md-11">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="required">Residential Area Type</label>
                                            <select required class="custom-select" name="colony_type" id="colony_type">
                                                @foreach($colonyTypeOptionList as $item)
                                                    {!!$item!!}
                                                 @endforeach
                                            </select>
                                            <span class="text-danger">{{ $errors->first('colony_type') }}</span>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="required">Residential Area Name</label>
                                            <input required type="text" value="{{isset($data['colony']->colony_name)? $data['colony']->colony_name : ''}}" placeholder="Enter Residential Area Name" name="colony_name_english" min="3" class="form-control" id="colony_name_english" >
                                            <span class="text-danger">{{ $errors->first('colony_name_english') }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Residential Area Name(Bangla)</label>
                                            <input type="text" value="{{$data['colony']->colony_name_bng}}" placeholder="Enter Residential Area Name(Bangla)" name="colony_name_bangla" min="3" class="form-control" id="colony_name_bangla">
                                        </div>

                                    </div>

                                    <div class="row my-1">
                                        <div class="col-md-4">
                                            <label>Division</label>
                                            <select class="custom-select select2" name="division" id="division">
                                                @if(count($divisionOptionList) > 0)
                                                    @foreach($divisionOptionList as $option)
                                                        {!!$option!!}
                                                    @endforeach
                                                @endif
                                            </select>
                                            <span class="text-danger">{{ $errors->first('division') }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label>District</label>
                                            <select class="custom-select select2" name="district" id="district">
{{--                                                @if(!$districtOptionList)--}}
{{--                                                    <option disabled="disabled" value="">-- Please select an option -- </option>--}}
{{--                                                @endif--}}
                                                @if($data['colony']->colony_id)
                                                    @foreach($districtOptionList as $option)
                                                        {!!$option!!}
                                                    @endforeach
                                                @endif
                                            </select>
                                            <span class="text-danger">{{ $errors->first('district') }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Thana</label>
                                            <select class="custom-select select2" name="thana" id="thana">
{{--                                                @if(!$thanaOptionList)--}}
{{--                                                    <option disabled="disabled" value="">-- Please select an option -- </option>--}}
{{--                                                @endif--}}
                                                    @if($data['colony']->colony_id)
                                                        @foreach($thanaOptionList as $option)
                                                            {!!$option!!}
                                                        @endforeach
                                                    @endif

                                            </select>
                                            <span class="text-danger">{{ $errors->first('thana') }}</span>
                                        </div>
                                    </div>

                                    <div class="row my-1">
                                        <div class="col-md-6">
                                            <label>Description</label>
                                            <textarea placeholder="Enter Description" rows="3" wrap="soft" name="colony_description_english"
                                            class="form-control" id="colony_description_english">{{$data['colony']->description}}</textarea>

                                        </div>
                                        <div class="col-md-6">
                                            <label>Description(Bangla)</label>
                                            <textarea placeholder="Enter Description(Bangla)" rows="3" wrap="soft" name="colony_description_bangla"
                                            class="form-control" id="colony_description_bangla">{{$data['colony']->description_bng}}</textarea>
                                        </div>
                                    </div>

                                    <div class="row my-1">
                                        <div class="col-md-6">
                                            <label>Address</label>
                                            <textarea placeholder="Enter Address" rows="3" wrap="soft" name="colony_address_english"
                                            class="form-control" id="colony_address_english">{{$data['colony']->colony_address}}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Address(Bangla)</label>
                                            <textarea placeholder="Enter Address(Bangla)" rows="3" wrap="soft" name="colony_address_bangla"
                                            class="form-control" id="colony_address_bangla">{{$data['colony']->colony_address_bng}}</textarea>
                                        </div>
                                    </div>
                                    <div class="row my-1">
                                        <div class="col-md-4 displayNone" id="colonyType">
                                            <label class="required">Status</label>
                                            <select required class="custom-select" name="colony_yn" id="colony_yn">
                                                <option value="Y">Active</option>
                                                <option value="N">Inactive</option>
                                            </select>
                                        </div>
                                        <span class="text-danger">{{ $errors->first('colony_yn') }}</span>
                                    </div>

                                    <div class="row">
                                        <input type="hidden" name="colony_id" id="colony_id">
                                        <div class="d-flex justify-content-end col">
                                            <button type="submit" id="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                                Submit
                                            </button> &nbsp;
                                            <button type="button" id="reset" class="btn btn btn-outline shadow mb-1 btn-secondary">
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

            <div class="card"><!----><!---->
				<div class="card-body"><h4 class="card-title">Residential Area Register List</h4><!---->

{{--                    <a target="_blank" class="btn btn-primary mr-1" href="/report/render?xdo=/~weblogic/HAS/RPT_COLONY_LIST.xdo&type=pdf&filename=colony_list">--}}
{{--                        PDF--}}
{{--                    </a>--}}
					<hr/>
                    <div class="table-responsive">
                        <table id="colonyTable" class="table table-sm datatable mdl-data-table dataTable">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Address(English)</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Thana</th>
                                <th>Residential Area Type</th>
{{--                                <th>Status</th>--}}
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

        $('#reset').on('click', function(){
            $('#colony_type').val('');
            $('#colony_name_english').val('');
            $('#colony_name_bangla').val('');
            $('#division').val('');
            $('#district').val('');
            $('#thana').val('');
            $('#colony_description_english').val('');
            $('#colony_description_bangla').val('');
            $('#colony_address_english').val('');
            $('#colony_address_bangla').val('');
        });
        $('#colonyTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: APP_URL+"/colony/colony-datatable-list",
            columns: [
                {data: 'DT_RowIndex', "name": 'DT_RowIndex'},
                /*{data: 'colony_id', name: 'colony_id', searchable: false },*/
                {data: 'colony_name', name: 'colony_name',searchable: true },
                {data: 'colony_address', name:'colony_address',searchable: false },
                {data: 'division.geo_division_name', name:'division.geo_division_name',searchable: false },
                {data: 'district.geo_district_name', name:'district.geo_district_name',searchable: false },
                {data: 'thana.geo_thana_name', name:'thana.geo_thana_name',searchable: false },
                {data: 'colony_type.colony_type', name:'colony_type.colony_type',searchable: false },
                // {data:'colony_yn', name:'colony_yn'},
                { data: 'action', name: 'Action', searchable: false },
            ]
        });

        function load_div_to_dist(div_id,selected_dist=''){
            //console.log(window.baseUrl);
            if(div_id !== undefined && div_id) {
                $.ajax({
                    type: "GET",
                    url: APP_URL+"/colony/load-division-to-district/"+div_id,
                    data: {'selected_dist': selected_dist},
                    success: function (data) {
                        //console.log(data);
                        $('#district').html(data);
                    },
                    error: function (data) {
                        alert('error');
                    }
                });

            }else{
                $('#district').html('');
                $('#thana').html('');
            }
        }

        function load_dist_to_thana(dist_id,selected_thana =''){

            if(dist_id !== undefined && dist_id) {
                $.ajax({
                    type: "GET",
                    url: APP_URL+"/colony/load-district-to-thana/"+dist_id,
                    data:{'selected_thana':selected_thana},
                    success: function (data) {
                        //console.log(data);
                        $('#thana').html(data);
                    },
                    error: function (data) {
                        alert('error');
                    }
                });

            }else{
                $('#thana').html('');
            }
        }

        $('#division').on("change", function () {
            let div_id = $('#division').val();
            let selected_dist = '';
            load_div_to_dist(div_id,selected_dist);
        });


        $('#district').on("change", function () {
            let dist_id = $('#district').val();
            let selected_thana = '';
            load_dist_to_thana(dist_id,selected_thana);
        });


    });

     </script>
@endsection


