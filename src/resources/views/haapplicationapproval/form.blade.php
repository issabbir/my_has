    <div class="row justify-content-center">
        <div class="col-md-12">
            @if($haApplication->allot_point->approve_yn=='D')
            <div
                class="alert alert-danger show"
                role="alert">
                DENIED
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                </button>
            </div>
            @endif
            <form id="house-form" method="POST" enctype="multipart/form-data"
                  @if($haApplication->houseallotment)
                  action="{{ route('ha-application-approval.un-assign', ['applicationId' => $haApplication->application_id]) }}"
                  @else
                  action="{{ route('ha-application-approval.store', ['applicationId' => $haApplication->application_id]) }}"
                @endif
            >
                {{ csrf_field() }}

            <div class="row">
                <div class="col-md-6">
                    <label>Employee</label>
                    <input type="text" placeholder="Employee"
                           name="employee_name" class="form-control"
                           id="employee_name" value="{{$employeeInformation['emp_name']}} ({{$employeeInformation['emp_code']}})" disabled>
                    <span class="text-danger">{{ $errors->first('employee_name') }}</span>
                </div>
                <div class="col-md-6">
                    <label>Designation</label>
                    <input type="text" placeholder="Designation"
                           name="employee_designation" class="form-control"
                           id="employee_designation" value="{{$employeeInformation['designation']}} - {{$employeeInformation['department']}}" disabled>
                    <span class="text-danger">{{ $errors->first('employee_designation') }}</span>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label>Join Date</label>
                    <input type="text" placeholder="Join Date"
                           name="employee_join_date" class="form-control"
                           id="employee_join_date" value="@if($employeeInformation['emp_join_date']){{ date('d-m-Y', strtotime($employeeInformation['emp_join_date'])) }}@endif" disabled>
                    <span class="text-danger">{{ $errors->first('employee_join_date') }}</span>
                </div>
                <div class="col-md-6">
                    <label>PRL Date</label>
                    <input type="text" placeholder="PRL Date"
                           name="employee_prl_date" class="form-control"
                           id="employee_prl_date" value="@if($employeeInformation['emp_lpr_date']){{ date('d-m-Y', strtotime($employeeInformation['emp_lpr_date'])) }}@endif" disabled>
                    <span class="text-danger">{{ $errors->first('employee_prl_date') }}</span>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label>Pay Scale</label>
                    <input type="text" placeholder="Pay Scale"
                           name="employee_payscale" class="form-control"
                           id="employee_payscale" value="{{$employeeInformation['payscale']}}" disabled>
                    <span class="text-danger">{{ $errors->first('employee_payscale') }}</span>
                </div>
                <div class="col-md-6">
                    <label>Eligible For</label>
                    <input type="text" placeholder="Eligible For"
                           name="employee_eligible_for" class="form-control"
                           id="employee_eligible_for" value="{{$employeeInformation['eligible_for']}}" disabled>
                    <span class="text-danger">{{ $errors->first('employee_eligible_for') }}</span>
                </div>
            </div>
                <div class="row mt-1">
                    <div class="col-md-6">
                        <label>Eligible Promotion Date</label>
                        <input type="text" placeholder="Eligible Promotion Date"
                               name="promo_date" class="form-control"
                               id="promo_date" value="@if($haApplication['eligable_promotion_date']){{ date('d-m-Y', strtotime($haApplication['eligable_promotion_date'])) }}@endif" disabled>
                        <span class="text-danger">{{ $errors->first('promo_date') }}</span>
                    </div>
                    <div class="col-md-6">
                        <label>Eligible Promotion Grade</label>
                        <input type="text" placeholder="Eligible Promotion Grade"
                               name="promo_grade" class="form-control"
                               id="promo_grade" value="{{$haApplication['eligable_emp_grade_id']}}" disabled>
                        <span class="text-danger">{{ $errors->first('promo_grade') }}</span>
                    </div>
                </div>
                @if($employeeInformation['emp_email'])
                    <div class="row mt-1">
                        <div class="col-md-6">
                            <label>Email ID</label>
                            <input type="text" placeholder="Email ID"
                                   name="employee_email" class="form-control"
                                   id="employee_email" value="{{$employeeInformation['emp_email']}}" disabled>
                            <span class="text-danger">{{ $errors->first('emp_email') }}</span>
                        </div>
                    </div>
                @endif
                @if(isset($haApplication['eligable_attachment']))
                <div class="row mt-1">
                    <div class="col-md-12 d-flex justify-content-end">
                        <a class="btn btn-facebook d-flex align-items-center" href="{{ route('ha-application-approval.eligible-attachment-download', [$haApplication['application_id']]) }}"
                           target="_blank"><i class="bx bx-download cursor-pointer" style="margin-right: 5px;"></i> Download Attachment</a>
                    </div>
                </div>
                @endif
            <hr/>
            <div class="row mt-1">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th colspan="2" class="text-center">Point Table</th>
                        </tr>
                            <tr>
                                <th>Criteria</th>
                                <th class="text-right">Point</th>
                            </tr>
                        </thead>
                        <tbody>

                        <tr>
                            <td>Female</td>
                            <td class="text-right">@if(isset($haApplication->allot_point)) {{$haApplication->allot_point->female_point}} @else 0 @endif</td>
                        </tr>
                        <tr>
                            <td>Point From Entitlement Date</td>
                            <td class="text-right">@if(isset($haApplication->allot_point)) {{$haApplication->allot_point->regular_job_point}} @else 0 @endif</td>
                        </tr>
                        <tr>
                            <td>1st Class Officer</td>
                            <td class="text-right">@if(isset($haApplication->allot_point)) {{$haApplication->allot_point->first_class_point}} @else 0 @endif</td>
                        </tr>
                        <tr>
                            <td class="text-bold-500">Total</td>
                            <td class="text-bold-500 text-right">@if(isset($haApplication->allot_point)) {{$haApplication->allot_point->tot_point}} @else 0 @endif</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="2" class="text-center">Other Information</th>
                                    </tr>
                                    <tr>
                                        <th>Criteria</th>
                                        <th class="text-right">Information</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Age</td>
                                        <td class="text-right">
                                            @if($employeeInformation['age']){{ $employeeInformation['age'] }}@endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Grade</td>
                                        <td class="text-right">{{$employeeInformation['grade_id']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Current Basic</td>
                                        <td class="text-right">{{$employeeInformation['current_basic']}}</td>
                                    </tr>
                                    <tr>
                                        <td>Merit Position</td>
                                        <td class="text-right">{{$employeeInformation['merit_position']}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-md-3">
                    <label class="required" for="house_id">House</label>
                    @if($allottedHouse == null)
                        <select class="custom-select" name="house_id" id="house_id" >
                            <option value="">--Please Select--</option>
                            @foreach($preferenceHouses as $preferenceHouse)
                                <option value="{{ $preferenceHouse->house_id  }}"> {{ isset($preferenceHouse->house_code) ? $preferenceHouse->colony_name . ' (Colony) ' . $preferenceHouse->building_name . ' (Building) ' . $preferenceHouse->house_name  . '(' . $preferenceHouse->house_code . ')' : $preferenceHouse->colony_name . ' (Colony) ' . $preferenceHouse->building_name . ' (Building) ' . $preferenceHouse->house_name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('house_id') }}</span>
                    @else
                        <input type="text" placeholder="Allotted House" name="allotted_house" class="form-control" id="allotted_house" @if($allottedHouse->dormitory_yn == 'Y') value="{{$allottedHouse->house_name}} ({{$allottedHouse->house_code}})" @else value="{{$allottedHouse->house_name}}" @endif disabled>
                    @endif
                </div>
                <div class="col-md-3">
                    <label>Building</label>
                    @if($allottedHouse == null)
                    <input type="text" placeholder="Building"
                           name="building" class="form-control"
                           id="building" value="" disabled>
                    <span class="text-danger">{{ $errors->first('building') }}</span>
                    @else
                        <input type="text" placeholder="Building" name="allotted_building" class="form-control" id="allotted_building" value="{{$allottedHouse->buildinglist->building_name}}" disabled>
                    @endif
                </div>
                <div class="col-md-3">
                    <label>Colony</label>
                    @if($allottedHouse == null)
                    <input type="text" placeholder="Colony"
                           name="colony" class="form-control"
                           id="colony" value="" disabled>
                    <span class="text-danger">{{ $errors->first('colony') }}</span>
                    @else
                        <input type="text" placeholder="Colony" name="allotted_colony" class="form-control" id="allotted_colony" value="{{$allottedHouse->colonylist->colony_name}}" disabled>
                    @endif
                </div>
                <div class="col-md-3">
                    <label for="board_decision_number" class="required"> Order Number</label>
                    @if($allottedHouse == null)
                    <input type="text" placeholder="Order Number" name="board_decision_number" class="form-control" id="board_decision_number" value="" required>
                    @else
                        @if($haApplication->houseallotment)
                            <input type="text" placeholder="Order Number" name="board_decision_number" class="form-control" id="board_decision_number" value="{{$haApplication->houseallotment->board_decision_number}}" disabled>
                        @endif
                    @endif
                    <span class="text-danger">{{ $errors->first('board_decision_number') }}</span>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-md-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="special_consideration_yn" name="special_consideration_yn"
                               value="{{\App\Enums\YesNoFlag::YES}}"
                               @if($haApplication->houseallotment)
                                   disabled
                                   @if($haApplication->houseallotment->special_consideration_yn == 'Y')
                                    checked
                                   @endif
                               @endif
                        >
                        <label class="form-check-label warning" for="special_consideration_yn">Special Consideration</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div id="available_house_elem"
                         @if(!$haApplication->houseallotment)
                         style="display: none;"
                         @elseif( $haApplication->houseallotment->special_consideration_yn != 'Y' )
                         style="display: none;"
                        @endif
                    >
                        <label for="remarks">Available House </label>
                 @if($allottedHouse != null)
                            <input type="text" class="form-control" disabled value="{{$allottedHouse->house_name}}">
                        @else
                            <select name="available_house" id="available_house" class="form-control">
                                <option value="">---Choose---</option>
                                @foreach($availableWithoutPreference as $availablehouseWithoutPreference)
                                    <option value="{{ $availablehouseWithoutPreference->house_id  }}"> {{ isset($availablehouseWithoutPreference->house_code) ? $availablehouseWithoutPreference->colony_name . ' (Colony) ' . $availablehouseWithoutPreference->building_name . ' (Building) ' . $availablehouseWithoutPreference->house_name  . '(' . $preferenceHouse->house_code . ')' : $availablehouseWithoutPreference->colony_name . ' (Colony) ' . $availablehouseWithoutPreference->building_name . ' (Building) ' . $availablehouseWithoutPreference->house_name }}</option>
                                @endforeach
                            </select>

                        @endif

                        <span class="text-danger">{{ $errors->first('available_house') }}</span>
                    </div>
                </div>
                <div class="col-md-5">
                    <div id="remarks_elem"
                         @if(!$haApplication->houseallotment)
                         style="display: none;"
                         @elseif( $haApplication->houseallotment->special_consideration_yn != 'Y' )
                         style="display: none;"
                        @endif
                    >
                        <label for="remarks">Remarks for special consideration</label>
                        <textarea name="remarks" id="remarks" class="form-control">@if($haApplication->houseallotment){{$haApplication->houseallotment->special_remarks}}@endif</textarea>
                        <span class="text-danger">{{ $errors->first('remarks') }}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                    <div class="col-md-4">
                        <div class="hideAttachments"
                             @if(!$haApplication->houseallotment)
                             style="display: none;"
                             @elseif( $haApplication->houseallotment->special_consideration_yn != 'Y' )
                             style="display: none;"
                            @endif
                         >
                            <label for="special_consider_file" class="hideAttachmentsRequired required" >Attachment File :</label>
                            <div class="custom-file b-form-file form-group dropdownStatus">
                                <input
                                    type="file"
                                    id="special_consider_file"
                                    name="special_consider_file"
                                    class="custom-file-input hideAttachmentsRequiredFile"
                                    accept="image/gif, image/jpeg,image/png,image/jpg"
                                >
                                <label
                                    for="special_consider_file"
                                    data-browse="Browse"
                                    accept="image/gif, image/jpeg,image/png,image/jpg"
                                    class="custom-file-label"
                                ></label>
                                <span class="defaultImgMessage">
                                    <span class="imageWidthHeightSuggesion warning">png/jpg/bmp allowed</span>
                                </span>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="hideAttachments"
                             @if(!$haApplication->houseallotment)
                             style="display: none;"
                             @elseif( $haApplication->houseallotment->special_consideration_yn != 'Y' )
                             style="display: none;"
                            @endif
                        >
                                @if(isset($haApplication->houseallotment->special_consider_file))
                                    <label for="">&nbsp;&nbsp;</label>
                                    <div class="custom-file">
                                        <a
                                           {{--class="btn btn-info"--}}
                                           id="special_consider_file_name"
                                           href="data:{{$haApplication->houseallotment->special_consider_file_type}};base64, {{$haApplication->houseallotment->special_consider_file}}"
                                           download="{{$haApplication->houseallotment->special_consider_file_name}}"
                                           target="_blank"
                                           {{--type="button"
                                           type="submit"--}}
                                        >{{$haApplication->houseallotment->special_consider_file_name}}</a>
                                    </div>
                                @endif
                        </div>
                    </div>

{{--                @if($showBtn=='1')--}}
{{--                @if()--}}
                    @if($haApplication->allot_point->approve_yn!='D'  && !is_null($haApplication->workflow_process))
                        <div class="col-md-4">
                            <div class="d-flex justify-content-end mt-2">
                                @if($haApplication->houseallotment)
                                    @if($takeOver == null)
                                        <button type="submit" class="btn btn btn-warning shadow mb-1 btn-secondary">Un-assign</button>
                                    @endif
                                @else
                                    @if(Illuminate\Support\Facades\Auth::user()->hasPermission('CAN_HOUSE_ASSIGN'))
                                        <button type="submit" class="btn btn btn-dark shadow mb-1 btn-primary">
                                            Assign
                                        </button>
                                        @endif
                                @endif
                            </div>
                        </div>
                    @endif
{{--                @endif--}}
            </div>
            </form>

            @if($haApplication->allot_point->approve_yn!='D')
            <div class="row">
                <form id="house-form" method="POST" enctype="multipart/form-data"
                    action="{{ route('ha-application-approval.deny', ['applicationId' => $haApplication->application_id]) }}">
                    {{ csrf_field() }}
                        <div class="col-md-10">&nbsp;</div>
                        <div class="d-flex justify-content-enda">
                            <button type="button" class="btn btn btn-dark shadow mb-1 btn-secondary mr-1" data-dismiss="modal">Close</button>
                            @if(!$haApplication->houseallotment)
                            <button type="submit" id="denySubmit" class="btn btn btn-danger mb-1 shadow btn-secondary mr-1 ">Deny</button>
                            @endif
                        </div>
                </form>
            </div>
            @endif
        </div>
    </div>

<script type="text/javascript">
    $('#house_id').select2()
    $('#house_id').on('change', function(e){
        e.preventDefault();
        let houseId = $(this).val();

        if( (houseId !== undefined) && (houseId != '')) {
            $.ajax({
                type: "GET",
                url: APP_URL+'/ajax/house/'+houseId,
                success: function (data) {
                    $('#building').val(data.buildinglist.building_name);
                    $('#colony').val(data.buildinglist.colony.colony_name);
                },
                error: function (err) {
                    alert('error', err);
                }
            });
        }
    });
    function changeSpecialConsiderarionEffect(){
        let isSpecialConsideration = $('#special_consideration_yn').prop('checked');

        if(isSpecialConsideration) {
            @if(!$haApplication->houseallotment)
                $('#special_consider_file').attr('required', 'required');
            @endif
            $('#special_consider_file').parent().find('label').addClass('required');
            $('.hideAttachments').show();

            $('#remarks_elem').show();
            $('#available_house_elem').show();
            $('#available_house').attr('required', 'required');
            $('#available_house').parent().find('label').addClass('required');
            $('#remarks').attr('required', 'required');
            $('#remarks').parent().find('label').addClass('required');
            $(".hideAttachmentsRequired").addClass('required');
            $(".hideAttachmentsRequiredFile").addClass('required');
        } else {

            $('#remarks').removeAttr('required');
            $('#remarks').parent().find('label').removeClass('required');
            $('#remarks').val('');
            $('#remarks_elem').hide();
            $('#available_house_elem').hide();

            $('#special_consider_file').removeAttr('required');
            $('#special_consider_file').parent().find('label').removeClass('required');
            $('#special_consider_file').val('');
            $('#available_house').removeAttr('required');
            $('#available_house').parent().find('label').removeClass('required');
            $('#available_house').val('');

            $(".hideAttachmentsRequired").removeClass('required');
            $(".hideAttachmentsRequiredFile").removeClass('required');
            $('.hideAttachments').hide();
        }
    }
    $('#special_consideration_yn').on('change', function(e) {
        changeSpecialConsiderarionEffect();
    });

    @if(isset($haApplication->houseallotment->special_consider_file))
        @if($haApplication->houseallotment->special_consideration_yn == 'Y')
                changeSpecialConsiderarionEffect();
        @endif
    @endif

        $("#denySubmit").on('click',function(){
            $('#houseAllotmentApprovalModal').modal('hide');
        });

</script>

