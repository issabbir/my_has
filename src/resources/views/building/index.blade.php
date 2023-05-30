@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
	<!--Load custom style link or css-->

@endsection

@section('content')
	<div class="row">
		<div class="col-12">

			<div class="card"><!----><!---->
				<div class="card-body"><h4 class="card-title">Building Information</h4><!---->
					<hr>

					<form id="building-register" method="POST"
{{--                        {{$data['building']->building_id}}--}}

                        @if($data['building']->building_id)
                            action="{{ route('building.update', ['id' => $data['building']->building_id]) }}">
                            <input name="_method" type="hidden" value="PUT">
                        @else
                            action="{{ route('building.store') }}">
                        @endif

                            {{ csrf_field() }}
                            <div class="row justify-content-center">
                                <div class="col-md-11">

                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="required">Residential Area </label>
                                            <select required class="custom-select select2" name="colony" id="colony">

                                                @foreach($colonyOptionList as $option)
                                                    {!!$option!!}
                                                @endforeach

                                            </select>
                                            <span class="text-danger">{{ $errors->first('colony') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="required">Flat Type</label>
                                            <select required class="custom-select select2" name="house_type" id="house_type">

                                                @foreach($houseTypeOptionList as $option)
                                                    {!!$option!!}
                                                @endforeach

                                            </select>
                                            <span class="text-danger">{{ $errors->first('house_type') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="required">Building Name</label>
                                            <input type="text" value="{{$data['building']->building_name}}" required placeholder="Enter Building Name" name="name_english" min="3" class="form-control" id="name_english" >
                                            <span class="text-danger">{{ $errors->first('house_code') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label >Building Name(Bangla)</label>
                                            <input type="text" value="{{$data['building']->building_name_bng}}" placeholder="Enter Building Name(Bangla)" name="name_bangla" min="3" class="form-control" id="name_bangla" >
                                        </div>


                                    </div>

                                    <div class="row my-1">
                                        <div class="col-md-3">
                                            <label class="required">Road No.</label>
                                            <input type="text" value="{{$data['building']->building_road_no}}" placeholder="Building Road No." name="building_road_no" class="form-control" id="building_road_no" >
                                            <span class="text-danger">{{ $errors->first('building_road_no') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Handover Date</label>
                                            <div class="input-group date" id="datetimepicker14" data-target-input="nearest">
                                                <input type="text"  value="@if($data['building']->building_id){{$dates['hand_over_date']}}@endif" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker14"
                                                       id="handover_date"
                                                       name="handover_date"
                                                       autocomplete="off"
                                                />
{{--                                                <div class="input-group-append" data-target="#datetimepicker14" data-toggle="datetimepicker">--}}
{{--                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>--}}
{{--                                                </div>--}}
                                            </div>
{{--                                            <input required  value="{{$data['building']->hand_over_date}}" type="date" name="handover_date" class="form-control" id="handover_date">--}}
                                            <span class="text-danger">{{ $errors->first('handover_date') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Inauguration Date</label>
                                            <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                                <input type="text"  value="@if($data['building']->building_id) {{$dates['inauguration_date']}}@endif" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker1"

                                                       id="inauguration_date"
                                                       name="inauguration_date"
                                                       autocomplete="off"
                                                />
{{--                                                <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">--}}
{{--                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>--}}
{{--                                                </div>--}}
                                            </div>
                                            <span class="text-danger">{{ $errors->first('inauguration_date') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Expiration Date</label>
                                            <div class="input-group date" id="datetimepicker9" data-target-input="nearest">
                                                <input type="text" value="@if($data['building']->building_id) {{$dates['expiration_date']}}@endif" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker9"
                                                       id="expiration_date"
                                                       name="expiration_date"
                                                       autocomplete="off"
                                                />
{{--                                                <div class="input-group-append" data-target="#datetimepicker9" data-toggle="datetimepicker9">--}}
{{--                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>--}}
{{--                                                </div>--}}
                                            </div>
{{--                                            <input required type="date" value="{{$data['building']->expiration_date}}"  name="expiration_date" class="form-control" id="expiration_date">--}}
                                            <span class="text-danger">{{ $errors->first('expiration_date') }}</span>
                                        </div>
                                    </div>

                                    <div class="row my-1">
                                        <div class="col-md-3">
                                            <label class="required">No. of Floor</label>
                                            <input required value="{{$data['building']->no_of_floor}}"   type="text" placeholder="No. of Floor" name="no_of_floor" min="1" class="form-control" id="no_of_floor" >
                                            <span class="text-danger">{{ $errors->first('no_of_floor') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="required">No. of Flat</label>
                                            <input required value="{{$data['building']->no_of_house}}" type="text" placeholder="No. of Flat" name="no_of_house" min="1" class="form-control" id="no_of_house" >
                                            <span class="text-danger">{{ $errors->first('no_of_house') }}</span>
                                        </div>

                                        <div class="col-md-3">
                                            <label>No. of Water Tank</label>
                                            <input value="{{$data['building']->no_water_tank}}" type="text" placeholder="No. of Floor" name="no_of_water_tank" min="1" class="form-control" id="no_of_water_tank" >
                                            <span class="text-danger">{{ $errors->first('no_of_water_tank') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label>No. of Reserve Tank</label>
                                            <input value="{{$data['building']->no_reserve_tank}}"  type="text" placeholder="No. of Reserve Tank" name="no_of_reserve_tank" min="1" class="form-control" id="no_of_reserve_tank" >
                                            <span class="text-danger">{{ $errors->first('no_of_reserve_tank') }}</span>
                                        </div>
                                    </div>

                                    <div class="row my-1">
                                        <div class="col-md-3">
                                            <label>Lift (Yes/No)</label>
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-inline-block mr-2 mb-1">
                                                    <fieldset>
                                                        <div class="radio">
                                                            <input type="radio"
                                                                   @if('Y' == $data['building']->lift_yn)
                                                                   checked
                                                                   @endif
                                                                   value="{{\App\Enums\YesNoFlag::YES}}" name="lift_yn" class="form-control" id="lift_y" >
                                                            <label for="lift_y">Yes</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-2 mb-1">
                                                    <fieldset>
                                                        <div class="radio">
                                                            <input type="radio"
                                                                   @if('N' == $data['building']->lift_yn)
                                                                        checked
                                                                   @endif

                                                                   @if(!$data['building']->building_id)
                                                                   checked
                                                                   @endif

                                                                   value="{{\App\Enums\YesNoFlag::NO}}" name="lift_yn" class="form-control" id="lift_n">
                                                            <label for="lift_n">No</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-3">
                                            <label>No. of Lift </label>
                                            <input type="number"
                                                   @if('N'== $data['building']->lift_yn)
                                                   readonly
                                                   @endif

                                                   @if(!$data['building']->building_id)
                                                   readonly
                                                   @endif
                                                   value="{{$data['building']->no_of_lift}}"  placeholder="No. of lift" name="no_of_lift" min="1" class="form-control" id="no_of_lift" >
                                        </div>

                                        <div class="col-md-3">
                                            <label>Parking (Yes/No)</label>
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-inline-block mr-2 mb-1">
                                                    <fieldset>
                                                        <div class="radio">
                                                            <input type="radio"
                                                                   @if('Y' == $data['building']->parking_yn)
                                                                   checked
                                                                   @endif

                                                                   value="Y" name="parking_yn" class="form-control" id="parking_y" >
                                                            <label for="parking_y">Yes</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-2 mb-1">
                                                    <fieldset>
                                                        <div class="radio">
                                                            <input type="radio"
                                                                   @if('N'== $data['building']->parking_yn)
                                                                   checked
                                                                   @endif
                                                                   @if(!$data['building']->building_id)
                                                                   checked
                                                                   @endif
                                                                   value="N" name="parking_yn" class="form-control" id="parking_n">
                                                            <label for="parking_n">No</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-3">
                                            <label>No. of Parking </label>
                                            <input type="number"
                                                   @if('N'== $data['building']->parking_yn)
                                                        readonly
                                                   @endif

                                                   @if(!$data['building']->building_id)
                                                   readonly
                                                   @endif
                                                   value="{{$data['building']->no_of_parking}}"  placeholder="No. of Parking" name="no_of_parking" min="1" class="form-control" id="no_of_parking" >
                                        </div>
                                    </div>

                                    <div class="row my-1">
                                        <div class="col-md-3">
                                            <label>Generator (Yes/No)</label>
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-inline-block mr-2 mb-1">
                                                    <fieldset>
                                                        <div class="radio">
                                                            <input type="radio" value="Y"
                                                                   @if('Y'== $data['building']->generator_yn)
                                                                   checked
                                                                   @endif
                                                                   name="generator_yn" class="form-control" id="generator_y">
                                                            <label for="generator_y">Yes</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-2 mb-1">
                                                    <fieldset>
                                                        <div class="radio">
                                                            <input type="radio"  value="N"
                                                                   @if('N'== $data['building']->generator_yn)
                                                                   checked
                                                                   @endif
                                                                   @if(!$data['building']->building_id)
                                                                   checked
                                                                   @endif

                                                                   name="generator_yn" class="form-control" id="generator_n">
                                                            <label for="generator_n">No</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-3">
                                            <label>No. of Generator</label>
                                            <input type="number" value="{{$data['building']->no_of_generator}}"
                                                   @if('N'== $data['building']->generator_yn)
                                                   readonly
                                                   @endif

                                                   @if(!$data['building']->building_id)
                                                   readonly
                                                   @endif
                                                   placeholder="No. of Generator" name="no_of_generator" min="1"  class="form-control" id="no_of_generator" >
                                        </div>

                                        <div class="col-md-3">
                                            <label>Fire (Yes/No)</label>
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-inline-block mr-2 mb-1">
                                                    <fieldset>
                                                        <div class="radio">
                                                            <input type="radio" value="Y"
                                                                   @if('Y' == $data['building']->fire_ext_yn)
                                                                   checked
                                                                   @endif
                                                                   name="fire_yn" class="form-control" id="fire_y" >
                                                            <label for="fire_y">Yes</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-2 mb-1">
                                                    <fieldset>
                                                        <div class="radio">
                                                            <input type="radio" value="N"
                                                                   @if('N'== $data['building']->fire_ext_yn)
                                                                   checked
                                                                   @endif
                                                                   @if(!$data['building']->building_id)
                                                                   checked
                                                                   @endif

                                                                   name="fire_yn" class="form-control" id="fire_n">
                                                            <label for="fire_n">No</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-3">
                                            <label>No. of Fire Exit</label>
                                            <input type="number"
                                                   @if('N'== $data['building']->fire_ext_yn)
                                                   readonly
                                                   @endif
                                                   @if(!$data['building']->building_id)
                                                   readonly
                                                   @endif
                                                   value="{{$data['building']->no_of_fire_ext}}" placeholder="No. of Fire Exit" name="no_of_fire_ext" min="1" class="form-control" id="no_of_fire_ext" >

                                        </div>
                                    </div>

                                    <div class="row my-1">
                                        <div class="col-md-3">
                                            <label class="required">Building No.</label>
                                            <input type="number" value="{{$data['building']->building_no}}" placeholder="Building No." name="building_no" class="form-control" id="building_no" >
                                         </div>
                                        <div class="col-md-3">
                                            <label>Building Block</label>
                                            <input type="text" value="{{$data['building']->building_block}}" placeholder="Building Block" name="building_block" class="form-control" id="building_block" >
                                        </div>
                                        <div class="col-md-3">
                                            <label>Building Width</label>
                                            <input type="number" value="{{$data['building']->building_width}}" placeholder="Building Width" name="building_width" class="form-control" id="building_width" >
                                        </div>

                                        <div class="col-md-3">
                                            <label>Building Length</label>
                                            <input type="number" value="{{$data['building']->building_length}}" placeholder="Building Length" name="building_length" class="form-control" id="building_length" >
                                        </div>
                                    </div>

                                    <div class="row my-1">
                                        <div class="col-md-3">
                                            <label>Security No.</label>
                                            <input type="text" value="{{$data['building']->sec_intercom_no}}" placeholder="Security No." name="security_no" class="form-control" id="security_no" >
                                        </div>
                                        <div class="col-md-3">
                                            <label>Manager</label>
                                            <select class="custom-select empList select2" name="manager" id="manager">
                                                       {{-- @foreach($empManagerList as $option)
                                                            {!!$option!!}
                                                        @endforeach--}}
                                                @if(isset($data['building']->building_name))
                                                        {!! $empManagerList !!}
                                                @endif
                                            </select>
                                            <span class="text-danger">{{ $errors->first('manager') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="required">Building Infrastructure</label>
                                            <select required class="custom-select" name="building_infrastructure" id="building_infrastructure">
                                                @foreach($buildingInfraList as $option)
                                                    {!!$option!!}
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{ $errors->first('building_infrastructure') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="required">Building Status</label>
                                            <select required class="custom-select" name="building_status" id="building_status">
                                                @foreach($buildingStatusOptionList as $option)
                                                    {!!$option!!}
                                                @endforeach
                                            </select>
                                            <span class="text-danger">{{ $errors->first('building_status') }}</span>
                                        </div>


                                    </div>

{{--                                    <div class="row my-1">--}}
{{--                                        <div class="col-md-6">--}}
{{--                                            &nbsp;--}}
{{--                                            <label>Manager Mobile</label>--}}
{{--                                            <input type="text" readonly name="manager_mbl" min="3" class="form-control" id="manager_mbl" >--}}
{{--                                            --}}
{{--                                        </div>--}}
{{--                                        <div class="col-md-6">--}}
{{--                                            <label>Contractor</label>--}}
{{--                                            <select class="custom-select" name="contractor" id="contractor">--}}
{{--                                               --}}
{{--                                                <option>select one </option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

                                    <div class="row my-1">
                                        <div class="col-md-3">
                                            <label>Name of Contractor</label>
                                            <input type="text" value="@if($data['building']->contractor){{$data['building']->contractor}}@endif" placeholder="" name="contractor" class="form-control" id="contractor" />
                                            <span class="text-danger">{{ $errors->first('contractor') }}</span>
                                        </div>

                                        <div class="col-md-3">
                                            <label>Year of Construction</label>
                                            <div class="input-group date" id="yearPicker" data-target-input="nearest">
                                                <input type="text" value="@if($data['building']->construction_year) {{$data['building']->construction_year}}@endif" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#yearPicker"
                                                       id="construction_year"
                                                       name="construction_year"
                                                       autocomplete="off"
                                                />
                                            </div>
                                            <span class="text-danger">{{ $errors->first('expiration_date') }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Civil Construction Cost</label>
                                            <input type="number" value="@if($data['building']->civil_construction_cost){{$data['building']->civil_construction_cost}}@endif" placeholder="Civil Construction Cost" name="civil_construction_cost" class="form-control" id="civil_construction_cost" />
                                        </div>

                                        <div class="col-md-3">
                                            <label>Electrical works Cost</label>
                                            <input type="number" value="@if($data['building']->electric_work_cost){{$data['building']->electric_work_cost}}@endif" placeholder="Electric work Cost" name="electric_work_cost" class="form-control" id="electric_work_cost" />
                                        </div>
                                    </div>

                                    <div class="row my-1">

                                        <div class="col-md-3">
                                            <label class="required">Dormitory (Yes/No)</label>
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-inline-block mr-2 mb-1">
                                                    <fieldset>
                                                        <div class="radio">
                                                            <input type="radio"
                                                                   @if('Y' == $data['building']->dormitory_yn)
                                                                   checked
                                                                   @endif
                                                                   value="{{\App\Enums\YesNoFlag::YES}}" name="dormitory_yn" class="form-control" id="dormitory_y" >
                                                            <label for="dormitory_y">Yes</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-2 mb-1">
                                                    <fieldset>
                                                        <div class="radio">
                                                            <input type="radio"
                                                                   @if('N' == $data['building']->dormitory_yn)
                                                                   checked
                                                                   @endif

                                                                   @if(!$data['building']->building_id)
                                                                   checked
                                                                   @endif

                                                                   value="{{\App\Enums\YesNoFlag::NO}}" name="dormitory_yn" class="form-control" id="dormitory_n">
                                                            <label for="dormitory_n">No</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            </ul>
                                        </div>


                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Description</label>
                                                    <textarea placeholder="Enter Description" rows="3" wrap="soft" name="description_english"
                                                              class="form-control" id="description_english">{{$data['building']->building_description}}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Description(Bangla)</label>
                                                    <textarea placeholder="Enter Description(Bangla)" rows="3" wrap="soft" name="description_bangla" class="form-control" id="description_bangla">{{$data['building']->buidling_description_bng}}</textarea>
                                                </div>
                                            </div>
                                        </div>




                                    </div>
                                    <div class="row">
{{--                                        <input type="hidden" name="building_id" id="building_id">--}}
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

					</form>
				</div>
            </div>


    <div class="card"><!----><!---->
             <div class="card-body"><h4 class="card-title">Building List</h4><!---->

{{--            <a target="_blank" class="btn btn-primary mr-1" href="/report/render?xdo=/~weblogic/HAS/RPT_COLONY_LIST.xdo&type=pdf&filename=colony_list">--}}
{{--                PDF--}}
{{--            </a>--}}
            <hr>

                        <div class="table-responsive">
                            <table id="buildings" class="table table-sm datatable mdl-data-table dataTable">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Building</th>
                                    <th>Residential Area</th>
                                    <th>Road No.</th>
                                    <th>Flat Type</th>
                                    <th>Infrastructure</th>
                                    <th>No. of Floor</th>
                                    <th>No. of Flat</th>
                                    <th>Block</th>
                                    <th>Dormitory</th>
                                    {{--                        <th>Width</th>--}}
                                    {{--                        <th>Height</th>--}}

                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>

                                </tbody>

                            </table>
                        </div>
                    </div>

            </div>

		</div>
	</div>

@endsection

    @section('footer-script')
	<!--Load custom script-->

    <script type="text/javascript">

    $(document).ready(function() {
//        selectedPerDistrict = '<option value="'+data[1].district_id+'">'+data[1].district_name+'</option>';

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
        $('#datetimepicker9').datetimepicker({
            format: 'YYYY-MM-DD',
            // format: 'L',
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            },
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
            }/*,
            onClose: function() {
                $('#expiration_date').trigger('blur');
            }*/
        });

         $('#yearPicker').datetimepicker({
            format: 'YYYY',
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
            }/*,
            onClose: function() {
                $('#expiration_date').trigger('blur');
            }*/
        });

        $('#buildings').DataTable({
            processing: true,
            serverSide: true,
            // order: true,
            ajax: APP_URL+'/buildings-datatable-list',
            columns: [
                /*{ data: 'building_id', name: 'building_id', searchable: false },*/
                { data: 'DT_RowIndex', "name": 'DT_RowIndex'},
                { data: 'building_name', name: 'building_name', searchable: true },
                { data: 'colony.colony_name', name: 'colony.colony_name',searchable: true  },
                { data: 'building_road_no', name: 'building_road_no',searchable: true  },
                { data: 'house_type.house_type', name: 'house_type.house_type',searchable: true  },
                { data: 'building_infra.building_infra', name: 'building_infra.building_infra',searchable: true  },
                { data: 'no_of_floor', name: 'no_of_floor',searchable: true },
                { data: 'no_of_house', name: 'no_of_house',searchable: true },
                { data: 'building_block', name: 'building_block',searchable: true  },
                { data: 'dormitory_yn', name: 'dormitory_yn',searchable: true  },
                { data: 'building_status.building_status', name: 'building_status.building_status',searchable: true},
                { data: 'action', name: 'Action', searchable: false },
            ]
        });

        $("#house_type").on("change", function () {
            if($(this).val() === '11') {
                $("#dormitory_y").prop("checked", true);
                $("#dormitory_n").prop("disabled", true);
            }else{
                $("#dormitory_y").prop("checked", false);
                $("#dormitory_n").prop("checked", true);
                $("#dormitory_n").prop("disabled", false);
            }
        });

        $("input[name='lift_yn']").on("change", function () {
            if($("input[name='lift_yn']:checked").val() == 'Y'){
                $('#no_of_lift').prop('readonly', false);
            }else{
                $('#no_of_lift').prop('readonly', true);
            }
        });

        $("input[name='parking_yn']").on("change", function () {
            if($("input[name='parking_yn']:checked").val() == 'Y'){
                $('#no_of_parking').prop('readonly', false);
            }else{
                $('#no_of_parking').prop('readonly', true);
            }
        });

        $("input[name='generator_yn']").on("change", function () {
            if($("input[name='generator_yn']:checked").val() == 'Y'){
                $('#no_of_generator').prop('readonly', false);
            }else{
                $('#no_of_generator').prop('readonly', true);
            }
        });

        $('#expiration_date').on({
            change:function(){
                //expirationDateCheck();
                //return false;
            },
            blur:function(){
                expirationDateCheck();
                //return false;
            }
        });

        $("input[name='fire_yn']").on("change", function () {
            if($("input[name='fire_yn']:checked").val() == 'Y'){
                $('#no_of_fire_ext').prop('readonly', false);
            }else{
                $('#no_of_fire_ext').prop('readonly', true);
            }
        });

        let empDept = '';
        let searchListUrl = APP_URL+'/ajax/employeesWithDept/'+empDept;
        selectCpaEmployee('.empList',searchListUrl);
        function selectCpaEmployee(clsSelector,searchListUrl)
        {
            $(clsSelector).each(function() {

                $(this).select2({
                    placeholder: "Please select an option",
                    allowClear: false,
                    ajax: {
                        delay: 250,
                        //url: APP_URL+'/ajax/employees/'+empDept,
                        url: searchListUrl,
                        data: function (params) {
                            if(params.term) {
                                if (params.term.trim().length  < 1) {
                                    return false;
                                }
                            } else {
                                return false;
                            }

                            return params;
                        },
                        dataType: 'json',
                        processResults: function(data) {
                            var formattedResults = $.map(data, function(obj, idx) {
                                obj.id = obj.emp_id;
                                obj.text = obj.emp_code+' '+obj.emp_name;
                                return obj;
                            });
                            return {
                                results: formattedResults,
                            };
                        },
                        cache: true
                    }
                });

            });
        }

    });

    function expirationDateCheck(){
        let handover_date       = $("#handover_date").val();
        let inauguration_date   = $("#inauguration_date").val();
        let expiration_date     = $("#expiration_date").val();

        handover_date     = new Date(handover_date);
        inauguration_date = new Date(inauguration_date);
        expiration_date   = new Date(expiration_date);

        if(!handover_date.getTime()){
            alert('Please Fill up Handover Date first');
            $("#handover_date").focus();
            $("#expiration_date").val('');
        }else{
            if(!inauguration_date.getTime()) {
                alert('Please Fill up Inauguration Date first');
                $("#inauguration_date").focus();
                $("#expiration_date").val('');
            }else{
                if (expiration_date <= handover_date) {
                    alert('Expiration Date Must be Larger than Handover Date');
                    $("#expiration_date").focus();
                    $("#expiration_date").val('');
                    return false;
                } else if (expiration_date <= inauguration_date) {
                    alert('Expiration Date Must be Larger than Inauguration Date');
                    $("#expiration_date").focus();
                    $("#expiration_date").val('');
                    return false;
                }
            }
        }

        return false;
    }

     </script>
@endsection


