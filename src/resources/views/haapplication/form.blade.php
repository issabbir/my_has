<form id="house-form" method="POST" enctype="multipart/form-data"
      @if(isset($data['haApplication']->application_id))
      action="{{ route('ha-application.update', ['id' => $data['haApplication']->application_id]) }}">
    <input name="_method" type="hidden" value="PUT">
    @else
        action="{{ route('ha-application.store') }}">
    @endif
    {{ csrf_field() }}
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="row mt-1">
                <div class="col-md-3">
                    <label class="required" for="employee_code">Employee Code</label>
                    @if($loggedUserCode == 'admin')
                        <select class="custom-select select2 form-control emp_code" id="employee_code"
                                name="employee_code" required
                                data-emp-code="{{$data['employeeInformation']['emp_code']}}"></select>
                    @else
                        <select class="custom-select select2 form-control emp_code" id="employee_codde" required
                                data-emp-code="{{$loggedUserCode}}" disabled>
                            <option value="{{$loggedUserCode}}" selected>{{$loggedUserCode}}</option>
                        </select>
                        <input name="employee_code" type="hidden" value="{{$loggedUserCode}}">
                    @endif
                    <span class="text-danger">{{ $errors->first('employee_code') }}</span>
                </div>
                <!--Eligible for house type-->
                <div class="col-md-3">
                    <label>Eligible For</label>
                    <input type="text" placeholder="Eligible For" name="eligible_for" class="form-control" disabled
                           id="eligible_for" value="{{$data['employeeInformation']['eligible_for']}}"/>
                </div>
                <input type="hidden" id="eligible_id" value="">
                <input type="hidden" id="house_category_id" value="">
                @if(isset($data['haApplication']->application_id))
                <div class="col-md-3">
                    <label class="required">Advertisement</label>
                    <select class="custom-select select2
                        {{ isset($data['haApplication']->application_id) ? 'makeReadOnly':'' }}
                        " name="advertisement_id" id="advertisement_id" required disabled>
                        <option value="">--Please Select--</option>
                        @foreach($data['advertisements'] as $advertisement)
                            <option value="{{ $advertisement->adv_id  }}"
                                    @if($advertisement->adv_id == $data['haApplication']->advertisement_id)
                                    selected
                                @endif
                            > {{ $advertisement->adv_number  }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('advertisement_id') }}</span>
                </div>

                    <div class="col-md-3">
                        <label class="required">House Type</label>
                        <select class="custom-select select2
                        {{ isset($data['haApplication']->application_id) ? 'makeReadOnly':'' }}
                            " name="house_type_id" id="house_type_id" required disabled>
                            <option value="">--Please Select--</option>
                            @foreach($data['house_types'] as $house_type)

                                <option value="{{ $house_type->house_type_id  }}"
                                        @if($house_type->house_type_id == $data['haApplication']->applied_house_type_id)
                                        selected
                                    @endif
                                > {{ $house_type->house_type  }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('house_type_id') }}</span>
                    </div>
            @else
                    <div class="col-md-3">
                        <label class="required">Advertisement</label>
                        <select class="custom-select select2
                        {{ isset($data['haApplication']->application_id) ? 'makeReadOnly':'' }}
                            " name="advertisement_id" id="advertisement_id" required>
                            <option value="">--Please Select--</option>
                            @foreach($data['advertisements'] as $advertisement)
                                <option value="{{ $advertisement->adv_id  }}"
                                        @if($advertisement->adv_id == $data['haApplication']->advertisement_id)
                                        selected
                                    @endif
                                > {{ $advertisement->adv_number  }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('advertisement_id') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required">House Type</label>
                        <select class="custom-select select2
                        {{ isset($data['haApplication']->application_id) ? 'makeReadOnly':'' }}
                            " name="house_type_id" id="house_type_id" required>
                            <option value="">--Please Select--</option>
                            @foreach($data['house_types'] as $house_type)
                                <option value="{{ $house_type->house_type_id  }}"
                                        @if($house_type->house_type_id == $data['haApplication']->applied_house_type_id)
                                        selected
                                    @endif
                                > {{ $house_type->house_type  }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('house_type_id') }}</span>
                    </div>
                @endif

                <!--Eligible for house type End-->
            </div>

            <div class="row mt-1">
                <div class="col-md-3">
                    <label>Employee Name</label>
                    <input type="text" placeholder="Employee Name"
                           name="employee_name" class="form-control" disabled
                           id="employee_name" value="{{$data['employeeInformation']['emp_name']}}">
                    <span class="text-danger">{{ $errors->first('employee_name') }}</span>
                </div>
                <div class="col-md-3">
                    <label>Designation</label>
                    <input type="text" placeholder="Designation"
                           name="employee_designation" class="form-control" disabled
                           id="employee_designation" value="{{$data['employeeInformation']['designation']}}">
                    <span class="text-danger">{{ $errors->first('employee_designation') }}</span>
                </div>
                <div class="col-md-3">
                    <label>Department</label>
                    <input type="text" placeholder="Department"
                           name="employee_department" class="form-control" disabled
                           id="employee_department" value="{{$data['employeeInformation']['department']}}">
                    <span class="text-danger">{{ $errors->first('employee_department') }}</span>
                </div>
                <div class="col-md-3">
                    <label>Section</label>
                    <input type="text" placeholder="Section"
                           name="employee_section" class="form-control" disabled
                           id="employee_section" value="{{$data['employeeInformation']['section']}}">
                    <span class="text-danger">{{ $errors->first('employee_section') }}</span>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-6">
                    <label>Father's Name</label>
                    <input type="text" placeholder="Father's Name"
                           name="father_name" class="form-control" disabled
                           id="father_name" value="{{$data['employeeInformation']['emp_father_name']}}">
                    <span class="text-danger">{{ $errors->first('father_name') }}</span>
                </div>
                <div class="col-md-6">
                    <label>Mother's Name</label>
                    <input type="text" placeholder="Mother's Name"
                           name="mother_name" class="form-control" disabled
                           id="mother_name" value="{{$data['employeeInformation']['emp_mother_name']}}">
                    <span class="text-danger">{{ $errors->first('mother_name') }}</span>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-3">
                    <label>Date of Birth</label>
                    <input type="text" placeholder="Date of Birth"
                           name="date_of_birth" class="form-control" disabled
                           id="date_of_birth"
                           value="@if($data['employeeInformation']['emp_dob']){{ date('d-m-Y', strtotime($data['employeeInformation']['emp_dob'])) }}@endif">
                    <span class="text-danger">{{ $errors->first('date_of_birth') }}</span>
                </div>
                <div class="col-md-3">
                    <label>Join Date</label>
                    <input type="text" placeholder="Join Date"
                           name="join_date" class="form-control" disabled
                           id="join_date"
                           value="@if($data['employeeInformation']['emp_join_date']){{ date('d-m-Y', strtotime($data['employeeInformation']['emp_join_date'])) }}@endif">
                    <span class="text-danger">{{ $errors->first('join_date') }}</span>
                </div>
                <div class="col-md-3">
                    <label>PRL Date</label>
                    <input type="text" placeholder="PRL Date"
                           name="prl_date" class="form-control" disabled
                           id="prl_date"
                           value="@if($data['employeeInformation']['emp_lpr_date']){{ date('d-m-Y', strtotime($data['employeeInformation']['emp_lpr_date'])) }}@endif">
                    <span class="text-danger">{{ $errors->first('prl_date') }}</span>
                </div>
                <div class="col-md-3">
                    <label>Eligible Promotion Date</label>
                    <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                        <input type="text" placeholder="Date of Promotion"
                               name="promo_date" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datetimepicker1" autocomplete="off"
                               id="promo_date"  data-predefined-date="@if($data['haApplication']['eligable_promotion_date']){{ date('d-m-Y', strtotime($data['haApplication']['eligable_promotion_date'])) }}@endif"
                               value="@if($data['haApplication']['eligable_promotion_date']){{ date('d-m-Y', strtotime($data['haApplication']['eligable_promotion_date'])) }}@endif">
                        <span class="text-danger">{{ $errors->first('promo_date') }}</span>
                    </div>
                </div>
            </div>

            <div class="row my-1">
                <div class="col-md-3">
                    <label>Pay Scale</label>
                    <input type="text" placeholder="Pay Scale"
                           name="payscale" class="form-control" disabled
                           id="payscale" value="{{$data['employeeInformation']['payscale']}}">
                    <span class="text-danger">{{ $errors->first('payscale') }}</span>
                </div>
                <div class="col-md-3">
                    <label>Current Basic</label>
                    <input type="text" placeholder="Current Basic"
                           name="current_basic" class="form-control" disabled
                           id="current_basic" value="{{$data['employeeInformation']['current_basic']}}">
                    <span class="text-danger">{{ $errors->first('current_basic') }}</span>
                </div>
                <div class="col-md-3">
                    <label>Gender</label>
                    <input type="text" placeholder="Gender"
                           name="gender" class="form-control" disabled
                           id="gender" value="{{$data['employeeInformation']['gender_name']}}">
                    <span class="text-danger">{{ $errors->first('gender') }}</span>
                    <input type="hidden" name="gender_id" id="gender_id"
                           value="{{$data['haApplication']->emp_gender_id}}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="required">Current Marital Status</label>
                    <select class="custom-select makeReadOnly" name="emp_maritial_status_id" id="emp_maritial_status_id"
                            required>
                        <option value="">--Please Select--</option>
                        @foreach($data['marital_statuses'] as $maritalStatus)
                            <option value="{{ $maritalStatus->maritial_status_id }}"
                                    @if($maritalStatus->maritial_status_id == $data['haApplication']->emp_maritial_status_id)
                                    selected
                                @endif
                            >{{ $maritalStatus->maritial_status  }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('emp_maritial_status_id') }}</span>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-3">
                    <label>Eligible Employee Grade ID</label>
                    <input type="text" placeholder="Eligible Employee Grade ID"
                           name="eligible_grade_id" class="form-control"
                           id="eligible_grade_id" value="{{$data['haApplication']['eligable_emp_grade_id']}}">
                    <span class="text-danger">{{ $errors->first('eligible_grade_id') }}</span>
                </div>
                <div class="col-md-3">
                    <label class="">Eligible Attachment</label>
                    <div class="custom-file b-form-file form-group">
                        <input type="file" id="attach_eligible" name="attach_eligible"
                               class="custom-file-input"
                        />
                        <label for="attach_eligible" data-browse="Browse"
                               class="custom-file-label required">Attachment
                        </label>
                    </div>
                </div>
            </div>
            <!---Female Related Information-->
            <div id="female-related-information"
                 @if( !($data['haApplication']->emp_gender_id == '2' && $data['haApplication']->emp_maritial_status_id == '2') )
                 style="display: none;"
                @endif
            >
                <div>
                    <div class="card-title">Female Related Information</div>
                    <hr/>
                    <div class="row my-1">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="husband_employee_of_cpa"
                                       name="husband_employee_of_cpa"
                                       @if(\App\Enums\YesNoFlag::YES == $data['haApplication']->husband_cpa_yn)
                                       checked
                                       @endif
                                       value="{{\App\Enums\YesNoFlag::YES}}"
                                >
                                <label class="form-check-label" for="husband_employee_of_cpa">Is Husband an employee of
                                    Chittagong port authority?</label>
                            </div>
                        </div>
                        <div class="col-md-3" id="husband_employee_of_cpa_code"

                             @if(\App\Enums\YesNoFlag::YES != $data['haApplication']->husband_cpa_yn)
                             style="display: none;"
                            @endif
                        >
                            {{--<input type="text" placeholder="Enter Husband's Employee Code"
                                   name="husband_employee_code" class="form-control"
                                   id="husband_employee_code" value="{{$data['haApplication']->husband_emp_code}}">--}}
                            <label>Employee</label>
                            <select class="custom-select select2 form-control emp_code" id="husband_employee_code"
                                    name="husband_employee_code"
                                    data-emp-code="{{$data['haApplication']->husband_emp_code}}"></select>
                            <span class="text-danger">{{ $errors->first('husband_employee_code') }}</span>
                        </div>

                        <div class="col-md-3">
                            <label>Husband Name</label>
                            <input type="text" placeholder="Husband Name"
                                   name="husband_name" class="form-control"
                                   id="husband_name" value="{{$data['haApplication']->husband_name}}">
                            <span class="text-danger">{{ $errors->first('husband_name') }}</span>
                        </div>
                        <div class="col">
                            <label>House Status</label>
                            <div>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="husband_house_status"
                                               id="husband_house_status_1"
                                               @if(\App\Enums\HusbandHouseStatus::ALLOTTED_VALUE == $data['haApplication']->husband_house_status)
                                               checked
                                               @endif
                                               value="{{\App\Enums\HusbandHouseStatus::ALLOTTED_VALUE}}">
                                        <label class="form-check-label"
                                               for="husband_house_status_1">{{\App\Enums\HusbandHouseStatus::ALLOTTED}}</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="husband_house_status"
                                               id="husband_house_status_2"
                                               @if(\App\Enums\HusbandHouseStatus::NOT_ALLOTTED_VALUE == $data['haApplication']->husband_house_status)
                                               checked
                                               @endif
                                               value="{{\App\Enums\HusbandHouseStatus::NOT_ALLOTTED_VALUE}}">
                                        <label class="form-check-label"
                                               for="husband_house_status_2">{{\App\Enums\HusbandHouseStatus::NOT_ALLOTTED}}</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="husband_house_status"
                                               id="husband_house_status_3"
                                               @if(\App\Enums\HusbandHouseStatus::NA_VALUE == $data['haApplication']->husband_house_status)
                                               checked
                                               @endif
                                               value="{{\App\Enums\HusbandHouseStatus::NA_VALUE}}">
                                        <label class="form-check-label"
                                               for="husband_house_status_3">{{\App\Enums\HusbandHouseStatus::NA}}</label>
                                    </div>
                                </div>
                                <span class="text-danger">{{ $errors->first('husband_house_status') }}</span>
                            </div>
                        </div>
                    </div>
                    <div id="female-related-information-husband-external-employee-form"
                         @if(\App\Enums\YesNoFlag::YES == $data['haApplication']->husband_cpa_yn)
                         style="display: none;"
                        @endif
                    >
                        <div class="row">
                            <div class="col-md-3">
                                <lazbel>Occupation</lazbel>
                                <select class="custom-select" name="husband_occupation" id="husband_occupation">
                                    <option value="">--Please Select--</option>
                                    <option value="{{\App\Enums\HusbandOccupation::SERVICE}}"
                                            @if(\App\Enums\HusbandOccupation::SERVICE == $data['haApplication']->husband_occupation)
                                            selected
                                        @endif
                                    >{{\App\Enums\HusbandOccupation::SERVICE}}</option>
                                    <option value="{{\App\Enums\HusbandOccupation::BUSINESS}}"
                                            @if(\App\Enums\HusbandOccupation::BUSINESS == $data['haApplication']->husband_occupation)
                                            selected
                                        @endif
                                    >{{\App\Enums\HusbandOccupation::BUSINESS}}</option>
                                    <option value="{{\App\Enums\HusbandOccupation::UNEMPLOYED}}"
                                            @if(\App\Enums\HusbandOccupation::UNEMPLOYED == $data['haApplication']->husband_occupation)
                                            selected
                                        @endif
                                    >{{\App\Enums\HusbandOccupation::UNEMPLOYED}}</option>
                                </select>
                                <span class="text-danger">{{ $errors->first('husband_occupation') }}</span>
                            </div>
                            <div class="col" id="husband_occupation_service"
                                 @if(\App\Enums\HusbandOccupation::SERVICE != $data['haApplication']->husband_occupation)
                                 style="display: none;"
                                @endif
                            >
                                <div class="mt-2">
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="husband_occupation_type"
                                                   id="husband_occupation_type_1"
                                                   value="{{\App\Enums\HusbandOccupationType::GOVERNMENT_VALUE}}"
                                                   @if(\App\Enums\HusbandOccupationType::GOVERNMENT_VALUE == $data['haApplication']->husband_occupation_type)
                                                   checked
                                                @endif
                                            >
                                            <label class="form-check-label"
                                                   for="husband_occupation_type_1">{{\App\Enums\HusbandOccupationType::GOVERNMENT}}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="husband_occupation_type"
                                                   id="husband_occupation_type_2"
                                                   value="{{\App\Enums\HusbandOccupationType::NON_GOVERNMENT_VALUE}}"
                                                   @if(\App\Enums\HusbandOccupationType::NON_GOVERNMENT_VALUE == $data['haApplication']->husband_occupation_type)
                                                   checked
                                                @endif
                                            >
                                            <label class="form-check-label"
                                                   for="husband_occupation_type_2">{{\App\Enums\HusbandOccupationType::NON_GOVERNMENT}}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="husband_occupation_type"
                                                   id="husband_occupation_type_3"
                                                   value="{{\App\Enums\HusbandOccupationType::AUTONOMOUS_VALUE}}"
                                                   @if(\App\Enums\HusbandOccupationType::AUTONOMOUS_VALUE == $data['haApplication']->husband_occupation_type)
                                                   checked
                                                @endif
                                            >
                                            <label class="form-check-label"
                                                   for="husband_occupation_type_3">{{\App\Enums\HusbandOccupationType::AUTONOMOUS}}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="husband_occupation_type"
                                                   id="husband_occupation_type_4"
                                                   value="{{\App\Enums\HusbandOccupationType::PRIVATE_JOB_VALUE}}"
                                                   @if(\App\Enums\HusbandOccupationType::PRIVATE_JOB_VALUE == $data['haApplication']->husband_occupation_type)
                                                   checked
                                                @endif
                                            >
                                            <label class="form-check-label"
                                                   for="husband_occupation_type_4">{{\App\Enums\HusbandOccupationType::PRIVATE_JOB}}</label>
                                        </div>
                                    </div>

                                    <span class="text-danger">{{ $errors->first('husband_occupation_type') }}</span>
                                </div>
                            </div>
                        </div>
                        <div id="husband_occupation_service-details"
                             @if((\App\Enums\YesNoFlag::YES == $data['haApplication']->husband_cpa_yn) || ($data['haApplication']->husband_occupation != \App\Enums\HusbandOccupation::SERVICE))
                             style="display: none;"
                            @endif
                        >
                            <div class="row mt-1">
                                <div class="col-md-3">
                                    <label>Organization</label>
                                    <input type="text" placeholder="Organization"
                                           name="husband_organization" class="form-control"
                                           id="husband_organization"
                                           value="{{$data['haApplication']->husband_organization}}">
                                    <span class="text-danger">{{ $errors->first('husband_organization') }}</span>
                                </div>
                                <div class="col-md-3">
                                    <label>Designation</label>
                                    <input type="text" placeholder="Designation"
                                           name="husband_designation" class="form-control"
                                           id="husband_designation"
                                           value="{{$data['haApplication']->husband_designation}}">
                                    <span class="text-danger">{{ $errors->first('husband_designation') }}</span>
                                </div>
                                <div class="col-md-2">
                                    <label>Organization Division</label>
                                    <select class="custom-select" name="husband_org_division" id="husband_org_division">
                                        <option value="">--Please Select--</option>
                                        @foreach($data['divisions'] as $division)
                                            <option value="{{ $division->geo_division_id  }}"
                                                    @if($division->geo_division_id == $data['haApplication']->husband_org_division)
                                                    selected
                                                @endif
                                            >{{ $division->geo_division_name  }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('husband_org_division') }}</span>
                                </div>
                                <div class="col-md-2">
                                    <label>Organization District</label>
                                    <select class="custom-select" name="husband_org_district" id="husband_org_district">
                                        <option value="">--Please Select--</option>
                                        @if($data['districts'])
                                            @foreach($data['districts'] as $district)
                                                <option value="{{ $district->geo_district_id  }}"
                                                        @if($district->geo_district_id == $data['haApplication']->husband_org_district)
                                                        selected
                                                    @endif
                                                >{{ $district->geo_district_name  }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger">{{ $errors->first('husband_org_district') }}</span>
                                </div>
                                <div class="col-md-2">
                                    <label>Organization Thana</label>
                                    <select class="custom-select" name="husband_orga_thana" id="husband_orga_thana">
                                        <option value="">--Please Select--</option>
                                        @if($data['thanas'])
                                            @foreach($data['thanas'] as $thana)
                                                <option value="{{ $thana->geo_thana_id  }}"
                                                        @if($thana->geo_thana_id == $data['haApplication']->husband_orga_thana)
                                                        selected
                                                    @endif
                                                >{{ $thana->geo_thana_name  }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger">{{ $errors->first('husband_orga_thana') }}</span>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-3">
                                    <label>Address</label>
                                    <textarea name="husband_address" id="husband_address"
                                              class="form-control">{{$data['haApplication']->husband_org_address}}</textarea>
                                    <span class="text-danger">{{ $errors->first('husband_address') }}</span>
                                </div>
                                <div class="col-md-3">
                                    <label>Monthly Salary</label>
                                    <input type="text" placeholder="Monthly Salary"
                                           name="husband_salary" class="form-control"
                                           id="husband_salary" value="{{$data['haApplication']->husband_salary}}">
                                    <span class="text-danger">{{ $errors->first('husband_salary') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!---Female Related Information End-->
            <!---Female Member Details Information-->
            <div class="my-1">
                <div class="card-title">Family Member Details
                    {{-- <a id="add-application-family-form" href="#" class="btn btn-primary float-right" data-row="{{ isset($data['haApplication']->ha_app_emp_families) ? $data['haApplication']->ha_app_emp_families->count() : 0 }}"><i class="bx bx-add-to-queue cursor-pointer"></i> Add New Family Member</a>--}}
                </div>
                <hr/>
                <div id="empFamilyDetails">

                </div>

                {{--<div id="ha-application-family-dynamic-form">
                    @if($data['haApplication']->ha_app_emp_families)
                        @foreach($data['haApplication']->ha_app_emp_families as $key => $family)
                            <div class="row ha-application-family-form mt-1 text-center">
                                --}}{{--<div class="col-1 font-weight-bold">{{$key + 1}}</div>--}}{{--
                                <div class="col-2"><input name="family[{{$key}}][name]" id="family_{{$key}}_name" type="text" value="{{$family->member_name}}" class="form-control" required /></div>
                                <div class="col-2"><input name="family[{{$key}}][name_bng]" id="family_{{$key}}_name_bng" type="text" value="{{$family->member_name_bng}}" class="form-control" /></div>
                                <div class="col-2"><input name="family[{{$key}}][mobile]" id="family_{{$key}}_mobile" type="text" value="{{$family->member_mobile}}" class="form-control mobile" maxlength="11" /></div>
                                <div class="col-2">
                                    <input name="family[{{$key}}][dob]" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-predefined-date="{{$family->member_dob}}" data-target="#family_{{$key}}_dob" id="family_{{$key}}_dob" type="text" readonly />
                                </div>
                                <div class="col-2"><input name="family[{{$key}}][age]" class="age form-control" id="family_{{$key}}_age" type="text" value="" disabled /></div>
                                <div class="col-1">
                                    <select class="custom-select" name="family[{{$key}}][relation_type_id]" id="family_{{$key}}_relation_type_id" required>
                                        <option value="">--Please Select--</option>
                                        @foreach($data['relationships'] as $relationship)
                                            <option value="{{ $relationship->relation_type_id  }}"
                                                    @if($relationship->relation_type_id == $family->relation_type_id)
                                                        selected
                                                    @endif
                                            >
                                                {{ $relationship->relation_type  }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-1"><a href="#" class="remove-application-family-form btn btn-danger"><i class="bx bx-trash cursor-pointer"></i></a></div></div>
                        @endforeach
                    @endif
                </div>--}}


            </div>
            <hr>
            @if(!isset($data['haApplication']->application_id))
            <div class="row">
                <div class="card-title">Available Flat/Dormitory</div>
                <div class="col-md-4">
                    <select class="form-control select2" name="avl_flat" id="avl_flat">
                        <option value="">---Choose---</option>
                    </select>

                </div>
                <div class="col-md-2">
                    <div id="start-no-field">
                        <label for="seat_to1">&nbsp;</label>
                        <button type="button" id="append"
                                class="btn btn-primary mb-1 add-available-flat">
                            ADD
                        </button>
                    </div>
                </div>

            </div>
                @else
                <div class="card-title">Applied Flat/Dormitory</div>
            @endif

            <div class="col-sm-12 mt-2">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="table-applicant-flat">
                        <thead>
                        <tr>
                            @if(!isset($data['haApplication']->application_id))
                            <th style="height: 25px;text-align: left; width: 5%">Action</th>
                            @endif
                            <th style="height: 25px;text-align: left; width: 30%">Flat/Dormitory</th>
                        </tr>
                        </thead>
{{--@dd($data['application'])--}}
                        <tbody id="comp_body">
                        @if(!empty($data['application']))

                            @foreach($data['application'] as $datas)
                                <tr>
                                    @if(!isset($data['haApplication']->application_id))
                                    <td class="text-center"><input type="checkbox" name="record"
                                                                   value="{{$datas->house_id}}"></td>
                                    @endif
                                    @if($datas->dormitory_yn == 'Y')
                                        <td>{{$datas->colony_name.' (Colony) '.$datas->building_name.' (Building) '.$datas->house_name.' ('.$datas->house_code.')'}} (Dormitory)</td>
                                    @else
                                        <td>{{$datas->colony_name.' (Colony) '.$datas->building_name.' (Building) '.$datas->house_name}}</td>
                                    @endif
                                </tr>

                            @endforeach
                        @endif


                        </tbody>
                    </table>

                </div>
                @if(!isset($data['haApplication']->application_id))
                <div class="col-12 d-flex justify-content-start">

                    <button type="button"
                            class="btn btn-primary mb-1 delete_adv_flat">
                        Delete
                    </button>

                </div>
                    @endif
            </div>

            <!---Female Member Details Information End-->
            @if(!$data['haApplication']->application_id)
                <div class="row">
                    <div class="col mt-2">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                Submit
                            </button>
                            <button type="button" class="btn btn btn-outline shadow mb-1 btn-secondary" id="resetForm">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</form>
<div class="d-none">
    <select class="custom-select" name="relation_type_id_template" id="relation_type_id_template">
        <option value="">--Please Select--</option>
        @foreach($data['relationships'] as $relationship)
            <option value="{{ $relationship->relation_type_id  }}">{{ $relationship->relation_type  }}</option>
        @endforeach
    </select>
</div>
