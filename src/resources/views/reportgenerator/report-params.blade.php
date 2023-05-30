<div class="col-12">
    <div class="row">
        @if($report)
            @if($report->params)
                @foreach($report->params as $reportParam)
                       @if($reportParam->component == 'house_id')
                            <div class="col-md-3">
                                <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                                <select class="custom-select form-control" id="{{$reportParam->param_name}}" name="{{$reportParam->param_name}}">
                                <option value="">---Please Select---</option>
                                        @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES)
                                        required
                                        @endif
                                ></select>
                            </div>
                        @elseif($reportParam->component == 'house_id_by_building')
                            <div class="col-md-3">
                                <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                                <select class="custom-select select2 form-control" id="{{$reportParam->param_name}}" name="{{$reportParam->param_name}}">
                                <option value="">---Please Select---</option>
                                        @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES)
                                        required
                                        @endif
                                ></select>
                            </div>
                    @elseif($reportParam->component == 'house_type_id_by_building')
                            <div class="col-md-3">
                                <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                                <select class="custom-select select2 form-control" id="{{$reportParam->param_name}}" name="{{$reportParam->param_name}}">
                                <option value="">---Please Select---</option>
                                        @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES)
                                        required
                                        @endif
                                ></select>
                            </div>
                        @elseif($reportParam->component == 'advertisement_id')
                            <div class="col-md-3">
                                <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                                <select class="custom-select select2 form-control" id="{{$reportParam->param_name}}" name="{{$reportParam->param_name}}">
                                        @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES)
                                        required
                                        @endif
                                ></select>
                            </div>
                        @elseif($reportParam->component == 'house_status_id')
                            <div class="col-md-3">
                                <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                                <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}" class="form-control">
                                    <option value="">ALL</option>
                                    @if($houseStatuses)
                                        @foreach($houseStatuses as $houseStatus)
                                            <option value="{{$houseStatus->house_status_id}}">{{$houseStatus->house_status}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @elseif($reportParam->component == 'employees')
                            <div class="col-md-3">
                                <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                                <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}" class="select2 form-control"></select>
                            </div>
                        @elseif($reportParam->component == 'date')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <div class="input-group date datePiker" id="{{$reportParam->param_name}}"
                                 data-target-input="nearest">
                                <input type="text" autocomplete="off"
                                       class="form-control datetimepicker-input"
                                       value="" name="{{$reportParam->param_name}}"
                                       data-toggle="datetimepicker"
                                       data-target="#{{$reportParam->param_name}}"
                                       @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required
                                       @endif onautocomplete="off"/>
                                <div class="input-group-append" data-target="#{{$reportParam->param_name}}"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="bx bxs-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        @elseif($reportParam->component == 'date_range')
                            <div class="col-md-3">
                                <label for="p_start_date"
                                       class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">From
                                    Date</label>
                                <div class="input-group date datePiker" id="p_start_date"
                                     data-target-input="nearest">
                                    <input type="text" autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           value="" name="p_start_date"
                                           data-toggle="datetimepicker"
                                           data-target="#p_start_date"
                                           @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required
                                           @endif onautocomplete="off"/>
                                    <div class="input-group-append" data-target="#p_start_date"
                                         data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="bx bxs-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="p_end_date"
                                       class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">To
                                    Date</label>
                                <div class="input-group date datePiker" id="p_end_date"
                                     data-target-input="nearest">
                                    <input type="text" autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           value="" name="p_end_date"
                                           data-toggle="datetimepicker"
                                           data-target="#p_end_date"
                                           @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required
                                           @endif onautocomplete="off"/>
                                    <div class="input-group-append" data-target="#p_end_date"
                                         data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="bx bxs-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        @elseif($reportParam->component == 'l-colony')
                            <div class="col-md-3">
                                <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                                <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}" class="form-control select2" @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                    @if($lColony)
                                        <option value="">Select One</option>
                                        @foreach($lColony as $colonyList)
                                            <option value="{{$colonyList->colony_id}}">{{$colonyList->colony_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @elseif($reportParam->component == 'buildingList')
                            <div class="col-md-3">
                                <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                                <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}" class="form-control select2" @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                    @if($buildingList)
                                        <option value="">Select One</option>
                                        @foreach($buildingList as $building)
                                            <option value="{{$building->building_id}}">{{$building->building_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @elseif($reportParam->component == 'l-house-type')
                            <div class="col-md-3">
                                <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                                <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}" class="form-control select2" @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                    @if($lHouseType)
                                        <option value="">Select One</option>
                                        @foreach($lHouseType as $houseTypeList)
                                            <option value="{{$houseTypeList->house_type_id}}">{{$houseTypeList->house_type}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
						@elseif($reportParam->component == 'dpt_department_id')
                            <div class="col-md-3">
                                <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                                <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}" class="form-control select2" @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                    @if($lDepartment)
                                        <option value="">Select One</option>
                                        @foreach($lDepartment as $lDepartmentList)
                                            <option value="{{$lDepartmentList->department_id}}">{{$lDepartmentList->department_name}}</option>
                                        @endforeach
                                    @endif

									@if(Illuminate\Support\Facades\Auth::user()->hasPermission('HAS_HOD_CAN_SEE_EFG_REPORT'))
										  <option value="679">{{$logUser->department_name.' for E/F/G'}}</option>
									@endif
									{{-- E=6,F=7,G=9 so ultimate option value become 679 --}}
                                </select>
                            </div>
						@elseif($reportParam->component == 'dpt_department_id_with_efg')
						<div class="col-md-3">
							<label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
							<select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}" class="form-control select2" @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
								@if($lDepartment)
									<option value="">Select One</option>
									@foreach($lDepartment as $lDepartmentList)
										<option value="{{$lDepartmentList->department_id}}">{{$lDepartmentList->department_name}}</option>
									@endforeach
								@endif
								@if(Illuminate\Support\Facades\Auth::user()->hasPermission('HAS_HOD_CAN_SEE_EFG_REPORT'))
									  <option value="679">{{$logUser->department_name.' for E/F/G'}}</option>
								@endif
								{{-- E=6,F=7,G=9 so ultimate option value become 679 --}}
							</select>
						</div>

                    @elseif($reportParam->component == 'dpt_ack_no')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}" class="form-control select2" @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                @if($acknowledgemnt)
                                    <option value="">Select One</option>
                                    @foreach($acknowledgemnt as $acknowledgemntNoList)
                                        <option value="{{$acknowledgemntNoList->dept_ack_no}}">{{$acknowledgemntNoList->dept_ack_no}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @elseif($reportParam->component == 'dpt_ack_id')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}" class="form-control select2" @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                @if($acknowledgemnt)
                                    <option value="">Select One</option>
                                    @foreach($acknowledgemnt as $acknowledgemntNoList)
                                        <option value="{{$acknowledgemntNoList->dept_ack_id}}">{{$acknowledgemntNoList->dept_ack_no}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        @else
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}" class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <input type="text" name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}" class="form-control" />
                        </div>
                        @endif

                @endforeach
            @endif
            <div class="col-3">
                <label for="type">Report Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="pdf">PDF</option>
                    <option value="xlsx">Excel</option>
                </select>
                <input type="hidden" value="{{$report->report_xdo_path}}" name="xdo" id="xdo" />
                <input type="hidden" value="{{$report->report_id}}" name="rid" />
                <input type="hidden" value="{{$report->report_name}}" name="filename" />
            </div>
            <div class="col-3 mt-2">
                <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">Generate Report</button>
            </div>
        @endif
    </div>
</div>
<link rel="stylesheet" type="text/css" href="{{asset('assets/vendors/css/forms/select/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/vendors/css/pickers/daterange/daterangepicker.css')}}">
<script src="{{asset('assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/scripts/forms/select/form-select2.min.js')}}"></script>
<script type="text/javascript">
    function houseOptions(data)
    {
        var formattedResults = $.map(data, function(obj, idx) {
            obj.id = obj.house_id;
            obj.text = obj.house_name;
            return obj;
        });
        return {
            results: formattedResults,
        };
    }
    function empOptions(data)
    {
        var formattedResults = $.map(data, function(obj, idx) {
            obj.id = obj.emp_id;
            obj.text = obj.emp_code +'-'+obj.emp_name;
            return obj;
        });
        return {
            results: formattedResults,
        };
    }

    function advertisementOptions(data)
    {
        var formattedResults = $.map(data, function(obj, idx) {
            obj.id = obj.adv_id;
            obj.text = obj.adv_number;
            return obj;
        });
        return {
            results: formattedResults,
        };
    }

    function callAjax(select_id, uri, load_id) {

        $(select_id).on('change', function () {
            let id = $(this).val();
            if(id){
                $.ajax({
                    type: 'GET',
                    url: APP_URL+uri+id,
                    success: function (data) {
                        console.log(data);
                        $(load_id).html(data);
                    },
                    error: function (err) {
                        alert('error', err);
                    }
                });
            }

        });
    }


    $("#p_colony_id,#p_house_type_id").on("change", function () {
        let htid = $("#p_house_type_id").val();
        let cid = $("#p_colony_id").val();
        let uri = '/ajax/buildings-by-colony-and-house-types/';
        if(cid && htid){
            $.ajax({
                type: 'GET',
                url: APP_URL+uri,
                data:{cid:cid,htid:htid},
                success: function (data) {
                    console.log(data);
                    $('#p_building_id').html(data);
                },
                error: function (err) {
                    alert('error', err);
                }
            });
        }
    });

    $('#p_empp_id').select2({
        placeholder: "Select",
        allowClear: true,
        ajax: {
            url: APP_URL + '/ajax/employees',
            data: function (params) {
                if (params.term) {
                    if (params.term.trim().length < 1) {
                        return false;
                    }
                } else {
                    return false;
                }

                return params;
            },
            dataType: 'json',
            processResults: function (data) {
                var formattedResults = $.map(data, function (obj, idx) {

                    obj.id = obj.emp_id;
                    obj.text = obj.emp_code + '-' + obj.emp_name;
                    return obj;
                });
                return {
                    results: formattedResults,
                };
            }
        }
    });

    $(document).ready(function() {

        callAjax('#p_colony_id', '/ajax/buildings-by-colony/', '#p_building_id');
        callAjax('#p_building_id', '/ajax/all-houselist-by-building-report/', '#p_house_id_by_building');

        callAjax('#p_colony_id', '/ajax/house-types-by-colony/', '#p_house_type_id');


        callAjax('#p_house_type_id', '/ajax/house-types-wise-by-building/', '#p_building_id');

        @if($report->report_id != 452 && $report->report_id != 453 && $report->report_id != 29)
            select('#p_house_id', '/ajax/houses', ajaxParams, houseOptions);
        @endif

        select('#p_emp_code', '/ajax/emp/', ajaxParams, empOptions);
        select('#p_hID', '/ajax/allotted-houses', ajaxParams, houseOptions);
        // select('#p_advertise_id', '/ajax/advertisements', ajaxParams, advertisementOptions);
        callAjax('#p_dpt_department_id', '/ajax/advertisements-by-dept/', '#p_adv_number');
        callAjax('#p_dpt_department_id', '/ajax/advertisements-by-dept/', '#p_advertise_id');
        callAjax('#p_DPT_DEPARTMENT_ID', '/ajax/dpt-ack-no-by-dept/', '#p_dpt_ack_no');
        callAjax('#p_advertise_id', '/ajax/housetype-by-adv/', '#p_house_type_id');
        callAjax('#p_department_id', '/ajax/employees-by-dept/', '#p_emp_id');
        callAjax('#p_adv_number', '/ajax/housetype-by-adv/', '#p_house_type_id');

        $('.datePiker').datetimepicker({
            format: 'DD-MM-YYYY',
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            },
            icons: {
                date: 'bx bxs-calendar',
                previous: 'bx bx-chevron-left',
                next: 'bx bx-chevron-right'
            }
        });

        $("#p_dpt_department_id").on("change", function () {
            let dpt_id = $(this).val()
            let xdo = "{{$report->report_xdo_path}}"
            let reportName = "{{$report->report_name}}"

            if(dpt_id == '679' && reportName == 'Advertisement Wise Details Report' )
            {
                $('#xdo').val('/~weblogic/HAS/RPT_ADVERTISEMENT_DETAILS_REPORT_EFG.xdo')
            }
            else
            {
                $('#xdo').val(xdo)
            }
        });

    });

</script>
@dd();
