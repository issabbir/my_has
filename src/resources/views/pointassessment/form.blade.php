<form id="house-form" method="POST" action="{{ route('point-assessment.store') }}">
    {{ csrf_field() }}
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="row mt-1">
                <div class="col-md-1
                    @if(!$loggedUserEFG_PermissionChecked)
                     d-none
                    @endif
                    ">
                    <label for="load_advertisement_for_efg">E/F/G</label><br/>
                    <input type="checkbox" id="load_advertisement_for_efg" name="is_efg" value="2"
                    @if($data['checked_advertisement_for_efg'] ==1)
                        checked
                    @endif
                    />
                    <span class="text-danger">{{ $errors->first('load_advertisement_for_efg') }}</span>
                </div>
                <div class="col-md-3">
                    <label class="required">Advertisement</label>
                    <select class="custom-select select2" name="advertisement_id" id="advertisement_id" required>
                        <option value="">--Please Select--</option>
                        @foreach($data['advertisements'] as $advertisement)
                            <option value="{{ $advertisement->adv_id }}"
                                    @if($data['advertisement_id'] == $advertisement->adv_id)
                                        selected
                                    @endif
                            > {{ $advertisement->adv_number  }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('advertisement_id') }}</span>
                </div>
                <div class="col-md-2">
                    <label class="required">Applied For</label>
                    <select class="custom-select" name="house_type_id" id="house_type_id" required>
                        <option value="">--Please Select--</option>
                        @if($data['houseTypes'])
                            @php
                                $allowedHouseTypeArray = (array)json_decode(env('HOD_ALLOWED_HOUSE_TYPE'));
                            @endphp
                            @foreach($data['houseTypes'] as $houseType)
                                {{--
								@if(Illuminate\Support\Facades\Auth::user()->hasPermission('HAS_HOD_CAN_ADVERTISE_A_D'))
                                      @if(1!=App\Helpers\HelperClass::custom_array_search($houseType->house_type, $allowedHouseTypeArray, 'bool'))
                                           @continue;
                                      @endif
                                @endif
								--}}
                                <option value="{{ $houseType->house_type_id }}"
                                        @if($data['house_type_id'] == $houseType->house_type_id)
                                        selected
                                        @endif
                                > {{ $houseType->house_type  }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span class="text-danger">{{ $errors->first('house_type_id') }}</span>
                </div>
                <div class="col-md-2">
                        <label class="required">Approval Process Type</label>
                        <select class="custom-select" name="approval_process_type_id" id="approval_process_type_id" required>
                            <option value="1"
                                    @if($data['approval_process_type_id'] == 1)
                                        selected
                                    @endif
                            >--Individual Approval--</option>
                            <option value="2"
                                    @if($data['approval_process_type_id'] == 2)
                                    selected
                                @endif
                            >--Multiple Approval--</option>
                        </select>
                </div>
                <div class="col-md-2">
                        <label class="required">Assign/Approve workflow</label>
                        <select class="custom-select" name="multi_workflow_id" id="multi_workflow_id" required>
                            <option value="1"
                                    @if($data['multi_workflow_id'] == 1)
                                        selected
                                    @endif
                            >Need to Assigned</option>
                            <option value="2"
                                    @if($data['multi_workflow_id'] == 2)
                                    selected
                                @endif
                            >Workflow</option>
                        </select>
                </div>
                <div class="col-md-2 mt-2">
                    <div class="d-flex justify-content-start">
                        <button style="min-height: 38.8px; margin-top: -3px;" type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                            Process&Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
