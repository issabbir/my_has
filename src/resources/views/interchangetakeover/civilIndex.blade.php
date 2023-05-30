@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <form id="house-interchange-takeover" method="POST" action="{{ route('interchange-takeover.civil-Store') }}">
                {{ csrf_field() }}
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class=" shadow p-1 mb-2 bg-white rounded col-sm-12">
                                    <div class=" panel-body">
                                        <div class="row">
                                            <div class="col-md-3" id="takeOver">
                                                <label>Allotment Letter Order No.</label>
                                                <!-- Enabling input field should work! -->
                                                {{--<input type="text" value="" name="allotment_no_search"
                                                       placeholder="Search data using Allotment letter No."
                                                       class="form-control" id="allotment_no_search"/>--}}
                                                <select class="custom-select select2 form-control" id="allotment_no_search" name="allotment_no_search" required></select>
                                            </div>
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
                                        </div>
                                    </div>
                                </div>

                                <div class=" shadow p-1 mb-2 bg-white rounded col-12">
                                    <h4 class="card-title" id="entryHeading">Interchange Takeover Entry</h4>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6 grid-divider">
                                            <h5>First Employee's Information</h5>
                                            <div class="row my-1">
                                                <div class="col-md-6">
                                                    <label>Employee Code</label>
                                                    <input type="text" disabled value="" name="first_emp_code" class="form-control"
                                                           id="first_emp_code"/>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Employee Name</label>
                                                    <input type="text" disabled value="" name="first_emp_name" class="form-control"
                                                           id="first_emp_name"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Designation</label>
                                                    <input type="text" disabled value="" name="first_emp_designation"
                                                           class="form-control" id="first_emp_designation"/>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Department</label>
                                                    <input type="text" disabled value="" name="first_emp_department"
                                                           class="form-control" id="first_emp_department"/>
                                                </div>


                                                <div class="col-md-6">
                                                    <label>Section</label>
                                                    <input type="text" disabled value="" name="first_emp_section" class="form-control"
                                                           id="first_emp_section"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Allotted Building</label>
                                                    <input type="text" disabled value="" name="first_emp_allotted_building"
                                                           class="form-control" id="first_emp_allotted_building"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Allotted House(Before Takeover)</label>
                                                    <input type="text" disabled value="" name="first_emp_allotted_house"
                                                           class="form-control" id="first_emp_allotted_house"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>House Type</label>
                                                    <input type="text" disabled value="" name="first_house_type" class="form-control"
                                                           id="first_house_type"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>House Size</label>
                                                    <input type="text" disabled value="" name="first_house_size" class="form-control"
                                                           id="first_house_size"/>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>House Floor</label>
                                                    <input type="text" disabled value="" name="first_house_floor" class="form-control"
                                                           id="first_house_floor"/>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Colony</label>
                                                    <input type="text" disabled value="" name="first_colony" class="form-control"
                                                           id="first_colony"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>House Details</label>
                                                    <textarea placeholder="Enter House Details" rows="2" wrap="soft"
                                                              name="first_house_details"
                                                              class="form-control" id="first_house_details"></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Sanitary Fittings</label>
                                                    <textarea placeholder="Enter Sanitary Fittings" rows="2" wrap="soft"
                                                              name="first_sanitary_fittings"
                                                              class="form-control" id="first_sanitary_fittings"></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Electrical Fittings</label>
                                                    <textarea placeholder="Enter Electrical Fittings" rows="2" wrap="soft" disabled
                                                              name="first_electrical_fittings"
                                                              class="form-control" id="first_electrical_fittings"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h5>Second Employee's Information</h5>
                                            <div class="row my-1">
                                                <div class="col-md-6">
                                                    <label>Employee Code</label>
                                                    <input type="text" disabled value="" name="second_emp_code" class="form-control"
                                                           id="second_emp_code"/>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Employee Name</label>
                                                    <input type="text" disabled value="" name="second_emp_name" class="form-control"
                                                           id="second_emp_name"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Designation</label>
                                                    <input type="text" disabled value="" name="second_emp_designation"
                                                           class="form-control" id="second_emp_designation"/>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Department</label>
                                                    <input type="text" disabled value="" name="second_emp_department"
                                                           class="form-control" id="second_emp_department"/>
                                                </div>


                                                <div class="col-md-6">
                                                    <label>Section</label>
                                                    <input type="text" disabled value="" name="second_emp_section" class="form-control"
                                                           id="second_emp_section"/>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Allotted Building</label>
                                                    <input type="text" disabled value="" name="second_emp_allotted_building"
                                                           class="form-control" id="second_emp_allotted_building"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Allotted House(Before Takeover)</label>
                                                    <input type="text" disabled value="" name="second_emp_allotted_house"
                                                           class="form-control" id="second_emp_allotted_house"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>House Type</label>
                                                    <input type="text" disabled value="" name="second_house_type" class="form-control"
                                                           id="second_house_type"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>House Size</label>
                                                    <input type="text" disabled value="" name="second_house_size" class="form-control"
                                                           id="second_house_size"/>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>House Floor</label>
                                                    <input type="text" disabled value="" name="second_house_floor" class="form-control"
                                                           id="second_house_floor"/>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Colony</label>
                                                    <input type="text" disabled value="" name="second_colony" class="form-control"
                                                           id="second_colony"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>House Details</label>
                                                    <textarea placeholder="Enter House Details" rows="2" wrap="soft"
                                                              name="second_house_details"
                                                              class="form-control" id="second_house_details"></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Sanitary Fittings</label>
                                                    <textarea placeholder="Enter Sanitary Fittings" rows="2" wrap="soft"
                                                              name="second_sanitary_fittings"
                                                              class="form-control" id="second_sanitary_fittings"></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label>Electrical Fittings</label>
                                                    <textarea placeholder="Enter Electrical Fittings" rows="2" wrap="soft" disabled
                                                              name="second_electrical_fittings"
                                                              class="form-control" id="second_electrical_fittings"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <label class="required">Take Over Date</label>
                                            <div class="input-group date" id="datetimepicker1"
                                                 data-target-input="nearest">
                                                <input type="text" value="{{date('Y-m-d')}}"
                                                       class="form-control datetimepicker-input"
                                                       data-toggle="datetimepicker" data-target="#datetimepicker1"
                                                       required
                                                       id="take_over_date"
                                                       name="take_over_date"
                                                />
                                            </div>
                                            <input type="hidden" value="" name="allot_letter_id" class="form-control" id="allot_letter_id"/>
                                        </div>
                                        <div class="col-md-3">
                                            <label> Civil Engineer</label>
                                            <input type="text" class="form-control" readonly

                                            id="civilEng"
                                            value="{{$loggedUser->user_name}}">
                                        </div>
                                        <input type="hidden" name="civilEng" value="{{$loggedUser->emp_id}}">
                                        <div class="col-9 mt-2">
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" id="submit"
                                                        class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                                    Submit
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
        </div>
    </div>

@endsection

@section('footer-script')
    <!--Load custom script-->
    <script type="text/javascript">
        function interchangeAllotLetterOptions(data)
        {
            var formattedResults = $.map(data, function(obj, idx) {
                obj.id = obj.allot_letter_no;
                obj.text = obj.allot_letter_no;
                return obj;
            });
            return {
                results: formattedResults,
            };
        }

        $(document).ready(function () {
            select('#allotment_no_search', '/ajax/new-interchange-allot-letters', ajaxParams, interchangeAllotLetterOptions);
            $(document).on("click", "#submitSearch", function (event) {
                setEmployeeInformation(event);
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

            function populateInterchangeForm(data)
            {
                $('#allot_letter_id').val(data.interchangeInformation.allot_letter_id);
                $('#first_emp_id').val(data.interchangeInformation.fe_id);
                $('#first_emp_code').val(data.interchangeInformation.fe_code);
                $('#first_emp_name').val(data.interchangeInformation.fe_name);
                $('#first_emp_designation').val(data.interchangeInformation.fe_designation);
                $('#first_emp_department').val(data.interchangeInformation.fe_department_name);
                $('#first_emp_section').val(data.interchangeInformation.fe_section_name);
                $('#first_emp_allotted_building').val(data.interchangeInformation.fe_building_name);
                $('#first_emp_allotted_house').val(data.interchangeInformation.fe_house_name);
                $('#first_house_type').val(data.interchangeInformation.fe_house_type);
                $('#first_house_size').val(data.interchangeInformation.fe_house_size);
                $('#first_house_floor').val(data.interchangeInformation.fe_floor_number);
                $('#first_colony').val(data.interchangeInformation.fe_colony_name);
                $('#first_house_details').val(data.interchangeInformation.fe_house_details);
                $('#first_sanitary_fittings').val(data.interchangeInformation.fe_sanitary_fettings);
                $('#first_electrical_fittings').val(data.interchangeInformation.fe_electrical_fettings);

                $('#second_emp_id').val(data.interchangeInformation.se_id);
                $('#second_emp_code').val(data.interchangeInformation.se_code);
                $('#second_emp_name').val(data.interchangeInformation.se_name);
                $('#second_emp_designation').val(data.interchangeInformation.se_designation);
                $('#second_emp_department').val(data.interchangeInformation.se_department_name);
                $('#second_emp_section').val(data.interchangeInformation.se_section_name);
                $('#second_emp_allotted_building').val(data.interchangeInformation.se_building_name);
                $('#second_emp_allotted_house').val(data.interchangeInformation.se_house_name);
                $('#second_house_type').val(data.interchangeInformation.se_house_type);
                $('#second_house_size').val(data.interchangeInformation.se_house_size);
                $('#second_house_floor').val(data.interchangeInformation.se_floor_number);
                $('#second_colony').val(data.interchangeInformation.se_colony_name);
                $('#second_house_details').val(data.interchangeInformation.se_house_details);
                $('#second_sanitary_fittings').val(data.interchangeInformation.se_sanitary_fettings);
                $('#second_electrical_fittings').val(data.interchangeInformation.se_electrical_fettings);
            }

            function initInterchangeForm()
            {
                $('#first_emp_id').val('');
                $('#first_emp_code').val('');
                $('#first_emp_name').val('');
                $('#first_emp_designation').val('');
                $('#first_emp_department').val('');
                $('#first_emp_section').val('');
                $('#first_emp_allotted_building').val('');
                $('#first_emp_allotted_house').val('');
                $('#first_house_type').val('');
                $('#first_house_size').val('');
                $('#first_house_floor').val('');
                $('#first_colony').val('');
                $('#first_house_details').val('');
                $('#first_sanitary_fittings').val('');
                $('#first_electrical_fittings').val('');

                $('#second_emp_id').val('');
                $('#second_emp_code').val('');
                $('#second_emp_name').val('');
                $('#second_emp_designation').val('');
                $('#second_emp_department').val('');
                $('#second_emp_section').val('');
                $('#second_emp_allotted_building').val('');
                $('#second_emp_allotted_house').val('');
                $('#second_house_type').val('');
                $('#second_house_size').val('');
                $('#second_house_floor').val('');
                $('#second_colony').val('');
                $('#second_house_details').val('');
                $('#second_sanitary_fittings').val('');
                $('#second_electrical_fittings').val('');
            }

            function setEmployeeInformation(evt) {
                evt.preventDefault();
                let allotmentNoOrEmpCode = '';
                let urlDetails = '';
                allotmentNoOrEmpCode = $('#allotment_no_search').val();

                if ($('#allotment_no_search').val().length <= 0) {
                    alert("Please Type Allotment Letter");
                    return false;
                }
                urlDetails = APP_URL + '/ajax/interchange-information/' + allotmentNoOrEmpCode;


                $.ajax({
                    type: "GET",
                    url: urlDetails,
                    success: function (data) {
                        if (data.interchangeInformation) {
                            populateInterchangeForm(data);
                        } else {
                            initInterchangeForm();
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


