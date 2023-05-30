<form id="allotment-register" method="POST" action="{{ route('allottee_informations.store') }}" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="card">
        <div class="card-body">
            {{--<h4 class="card-title" id="topHeading">Search Here</h4><hr>--}}
            <div class="row">
                <div class="col-md-12">
                    <div class=" shadow p-1 mb-1 bg-white rounded col-sm-12">
                        <div class=" panel-body">
                            <div class="row">
                                <div class="col-md-3" id="handOver">
                                    <label>Employee Code</label>

                                    <select value="" name="emp_code_search"
                                            class="form-control select2"
                                            id="emp_code_search">

{{--                                        @foreach($employee as $list)--}}
{{--                                            <option value="{{$list->emp_code}}">{{$list->emp_code.'-'.$list->emp_name}}</option>--}}
{{--                                        @endforeach--}}
                                    </select>
                                </div>
                                {{--                                                            --}}
                                {{--                                                            <div class="col-md-3">--}}
                                {{--                                                                <label>Employee Code</label>--}}
                                {{--                                                                <input type="text" value="" name="emp_code_search" placeholder="Search data using Employee Code" class="form-control" id="emp_code_search" />--}}
                                {{--                                                            </div>--}}
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <div class="d-flex col">
                                        &nbsp;
                                        <button type="submit" id="submitSearch"
                                                class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                            Search
                                        </button> &nbsp;
                                    </div>
                                </div>

                                <div class="col-md-4 my-2">
                                    <div class="alertModify alert-danger alert-block d-none" id="searchResult">

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="shadow p-3 mb-5 bg-white rounded col-sm-12" id="entry_form">
                        <h4 class="card-title" id="entryHeading">Entry Form</h4><!---->
                        <hr>
                        <div class="row my-1">

                            <div class="col-md-3">
                                <label>Employee Code</label>
                                <input type="text" readonly value="" name="emp_code" class="form-control"
                                       id="emp_code"/>

                                <input type="hidden"   value="" name="emp_id" class="form-control " id="emp_id"/>
                            </div>

                            <div class="col-md-3">
                                <label>Employee Name</label>
                                <input type="text" readonly value="" name="emp_name" class="form-control"
                                       id="emp_name"/>
                            </div>
                            <div class="col-md-3">
                                <label>Designation</label>
                                <input type="text" readonly value="" name="emp_designation" class="form-control"
                                       id="emp_designation"/>
                            </div>

                            <div class="col-md-3">
                                <label>Department</label>
                                <input type="text" readonly value="" name="emp_department" class="form-control"
                                       id="emp_department"/>
                            </div>

                        </div>
                        <div class="row my-1">

                            <div class="col-md-3">
                                <label>Contact No.</label>
                                <input type="text" readonly value="" name="contact_no" class="form-control"
                                       placeholder="Contact No." id="contact_no"/>
                            </div>

                            <div class="col-md-3">
                                <label>Email</label>
                                <input type="text" readonly value="" name="email" class="form-control" id="email"/>
                            </div>
                            <div class="col-md-3">
                                <label>Date of birth</label>
                                <input type="text" readonly value="" placeholder="Date of birth" name="date_of_birth"
                                       class="form-control" id="date_of_birth"/>
                            </div>

                            <div class="col-md-3">
                                <label>PRL Date</label>
                                <input type="text" readonly value="" name="prl_date" class="form-control"
                                       id="prl_date"/>
                            </div>


                        </div>

                        <div class="row my-1">
                            <div class="col-md-3">
                                <label>Date of Join</label>
                                <input type="text" readonly value="" name="emp_join_date" class="form-control"
                                       id="emp_join_date"/>
                            </div>

                            <div class="col-md-3">
                                <label class="">Office Order no.</label>
                                <input type="text"  value="" name="office_order_no" class="form-control"
                                       id="office_order_no"/>
                            </div>
                            <div class="col-md-3">
                                <label class="">Office Order Date</label>
                                <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                                    <input type="text" value=""
                                           class="form-control datetimepicker-input" data-toggle="datetimepicker"
                                           data-target="#datetimepicker1"

                                           id="office_order_date"
                                           name="office_order_date"
                                           autocomplete="off"
                                    />
                                </div>
                            </div>



                            {{-------------------------------------------------------------- work--}}

                            <div class="col-md-3">
                                <label class="required">Dormitory Building (Yes/No) </label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input dormitory_yn" type="radio" name="dormitory_yn" onclick="dorm_yn()"
                                               id="dormitory_y" value='Y'
                                            {{--                                               value="{{\App\Enums\YesNoFlag::YES}}" @if(\App\Enums\YesNoFlag::YES == $data['house']->dormitory_yn)  checked  @endif --}}

                                        >
                                        <label class="form-check-label" for="dormitory_y">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input dormitory_yn" type="radio" name="dormitory_yn" onclick="dorm_yn()"
                                               id="dormitory_n" value='N'
                                               {{--                                               value="{{\App\Enums\YesNoFlag::NO}}" @if(\App\Enums\YesNoFlag::YES != $data['house']->dormitory_yn) checked  @endif--}}

                                               checked
                                        >
                                        <label class="form-check-label" for="dormitory_n">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('dormitory_yn') }}</span>
                                </div>
                            </div>


                            <div class="col-md-3 mt-1">
                                <label class="required">Date of allotment</label>
                                <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                                    <input type="text" value=""
                                           class="form-control datetimepicker-input" data-toggle="datetimepicker"
                                           data-target="#datetimepicker2"
                                           required
                                           id="date_of_allotment"
                                           name="date_of_allotment"
                                           autocomplete="off"
                                    />
                                </div>
                            </div>

                            {{--                            <div class="col-md-3">--}}
                            {{--                                <div class="form-group">--}}
                            {{--                                    <label for="other_exam" class="required">Dormitory</label>--}}
                            {{--                                    <ul class="list-unstyled mb-0">--}}
                            {{--                                        <li class="d-inline-block mr-2 mb-1">--}}
                            {{--                                            <fieldset>--}}
                            {{--                                                <div--}}
                            {{--                                                    class="custom-control custom-radio">--}}
                            {{--                                                    <input type="radio"--}}
                            {{--                                                           class="custom-control-input"--}}
                            {{--                                                           name="dormitory_yn"--}}
                            {{--                                                           id="dormitory_y"--}}
                            {{--                                                           value="{{ \App\Enums\YesNoFlag::YES }}"--}}
                            {{--                                                    >--}}
                            {{--                                                    <label--}}
                            {{--                                                        class="custom-control-label"--}}
                            {{--                                                        for="dormitory_y">Yes</label>--}}
                            {{--                                                </div>--}}
                            {{--                                            </fieldset>--}}
                            {{--                                        </li>--}}
                            {{--                                        <li class="d-inline-block mr-2 mb-1">--}}
                            {{--                                            <fieldset>--}}
                            {{--                                                <div--}}
                            {{--                                                    class="custom-control custom-radio">--}}
                            {{--                                                    <input type="radio" checked--}}
                            {{--                                                           class="custom-control-input"--}}
                            {{--                                                           name="dormitory_yn"--}}
                            {{--                                                           id="dormitory_n"--}}
                            {{--                                                           value="{{\App\Enums\YesNoFlag::NO}}"--}}
                            {{--                                                    >--}}
                            {{--                                                    <label--}}
                            {{--                                                        class="custom-control-label"--}}
                            {{--                                                        for="dormitory_n">No</label>--}}
                            {{--                                                </div>--}}
                            {{--                                            </fieldset>--}}
                            {{--                                        </li>--}}
                            {{--                                    </ul>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}



                            {{--                            <div class="col-md-3">--}}
                            {{--                                <label  for="room_number">Room Number </label>--}}
                            {{--                                <input type="text" placeholder="Room Number"--}}
                            {{--                                       name="room_number" class="form-control"--}}
                            {{--                                       value="{{$data['house']->dormitory_room_no}}"--}}
                            {{--                                       id="room_number">--}}
                            {{--                                <span class="text-danger">{{ $errors->first('house_code') }}</span>--}}
                            {{--                            </div>--}}

                            {{--                            <div class="col-md-3">--}}
                            {{--                                <label class="required" for="house_size">Flat Size</label>--}}
                            {{--                                <input type="text" placeholder="Flat Size"--}}
                            {{--                                       name="house_size" class="form-control" required--}}
                            {{--                                       value="{{$data['house']->house_size}}"--}}
                            {{--                                       id="house_size" min="300" max="500">--}}
                            {{--                                <span class="text-danger">{{ $errors->first('house_size') }}</span>--}}
                            {{--                            </div>--}}


                            {{-------------------------------------------------------------- work--}}




                            <div class="col-md-3 mt-1">
                                <label class="required">Residential Area</label>
                                <select class="custom-select select2" name="colony_id" id="colony_id" required>
                                    @foreach($colonyOptionList as $option)
                                        {!!$option!!}
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('colony') }}</span>
                            </div>


                            <div class="col-md-3 mt-1">
                                <label class="required">House Type</label>
                                <select class="custom-select select2" name="house_type_id" id="house_type_id" required>
{{--                                    @foreach($house_types_option as $option)--}}
{{--                                        {!!$option!!}--}}
{{--                                    @endforeach--}}
                                </select>
                                <span class="text-danger">{{ $errors->first('house_type_id') }}</span>
                            </div>



                            <div class="col-md-3 mt-1" id="building-list">
                                <label class="required">Building</label>
                                <select class="custom-select select2">
                                    <option value="">Please select any one</option>
                                </select>
                            </div>

                            <div class="col-md-3 mt-1">
                                <label class="required">Dormitory Flat (Yes/No) </label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input flat_dormitory_yn" type="radio" name="flat_dormitory_yn" onclick="flat_dorm_yn()"
                                               id="flat_dormitory_y" value='Y'
                                            {{--                                               value="{{\App\Enums\YesNoFlag::YES}}" @if(\App\Enums\YesNoFlag::YES == $data['house']->dormitory_yn)  checked  @endif --}}

                                        >
                                        <label class="form-check-label" for="flat_dormitory_y">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input flat_dormitory_yn" type="radio" name="flat_dormitory_yn" onclick="flat_dorm_yn()"
                                               id="flat_dormitory_n" value='N'
                                               {{--                                               value="{{\App\Enums\YesNoFlag::NO}}" @if(\App\Enums\YesNoFlag::YES != $data['house']->dormitory_yn) checked  @endif--}}

                                               checked
                                        >
                                        <label class="form-check-label" for="flat_dormitory_n">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('flat_dormitory_yn') }}</span>
                                </div>
                            </div>


{{--                            <div class="col-md-3 mt-1" id="flatType">--}}
{{--                                <label class="required">Flat/House Type</label>--}}
{{--                                <select class="custom-select select2">--}}
{{--                                    <option value="">Please select any one</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}



                            <div class="col-md-3 mt-1" id="houseList">
                                <label class="required" for="flat_name_id">Flat Number</label>
                               {{-- <select name="flat_name_id" class="custom-select select2"  id="flat_name_id">--}}
                                <select class="custom-select select2" name="house_id" id="house_id" required>
                                <option value="">Please select any one</option>
                                </select>
                                {{--<input type="text" readonly value="" class="form-control"/>--}}
                                <span class="text-danger">{{ $errors->first('flat_name_id') }}</span>
                            </div>


                            <div id="houseDetails" class="row col-sm-6 mt-1">
                                <div class="col-md-4">
                                    <label>Road no.</label>
                                    <input readonly type="text" value="" name="road_no" placeholder="Road no." class="form-control" id="road_no" />
                                </div>

                                <div class="col-md-4">
                                    <label>Building No.</label>
                                    <input readonly type="text" value="" placeholder="Building No." name="building_no" class="form-control" id="building_no" />
                                </div>

                                <div class="col-md-4">
                                    <label>Floor</label>
                                    <input readonly type="text" value="0" name="floor" id="floor" class="form-control"/>
                                </div>
                            </div>


                            <div class="col-md-3 mt-1">
                                <div class="form-group">
                                    <label for="other_exam" class="required">Special Consideration</label>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block mr-2 mb-1">
                                            <fieldset>
                                                <div
                                                    class="custom-control custom-radio">
                                                    <input type="radio"
                                                           class="custom-control-input"
                                                           name="special_consideration_yn"
                                                           id="special_consideration_y"
                                                           value="{{ \App\Enums\YesNoFlag::YES }}"
                                                    >
                                                    <label
                                                        class="custom-control-label"
                                                        for="special_consideration_y">Yes</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block mr-2 mb-1">
                                            <fieldset>
                                                <div
                                                    class="custom-control custom-radio">
                                                    <input type="radio" checked
                                                           class="custom-control-input"
                                                           name="special_consideration_yn"
                                                           id="special_consideration_n"
                                                           value="{{\App\Enums\YesNoFlag::NO}}"
                                                    >
                                                    <label
                                                        class="custom-control-label"
                                                        for="special_consideration_n">No</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

{{--                        </div>--}}

                        <div class="row my-2">

                            <div class="col-sm-3" id="hide_file" style="display: none">
                                <div class="form-group">
                                    <label for="attachment" class="">Attachment</label>
                                    <input type="file" class="form-control" id="special_consider_attachment" name="special_consider_attachment"/>
                                </div>
                                @if(isset($trainingInfo->attachment))
                                    <a href="{{ route('training-information.training-info-file-download', [$trainingInfo->training_id]) }}"
                                       target="_blank">{{$trainingInfo->attachment_name}}</a>
                                @endif
                            </div>

                            <div class="col-md-6" id="hide_remarks" style="display: none">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea rows="2" wrap="soft"
                                              name="special_consider_remarks"
                                              class="form-control"
                                              id="special_consider_remarks">{{old('remarks',isset($locationentry->remarks) ? $locationentry->remarks : '')}}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label>&nbsp;</label>
                                <div class="d-flex justify-content-end col">
                                    <button type="submit" id="submit"
                                            class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                        Submit
                                    </button> &nbsp;
                                    <button type="reset" id="reset"
                                            class="btn btn btn-outline shadow mb-1 btn-secondary">
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</form>
