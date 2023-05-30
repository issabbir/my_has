@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
    <style>
        .makeReadOnly{
            pointer-events:none;
            background-color:#F6F6F6
        }
        .displayNone{
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body"><h4 class="card-title">House Old Allotte Flat Information</h4>
                    <hr>
                    @include('haallottee.form')
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-12">

        <div class="card"><!----><!---->
            <div class="card-body"><h4 class="card-title" id="listHeading">Employee List Handover To Civil Department</h4><!---->
                <hr/>
                <div class="table-responsive">
                    <table id="house-allotment" class="table table-sm datatable mdl-data-table dataTable">
                        <thead>
                        <tr>
                            <th>Employee Code</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Building Name</th><!-- New added column -->
                            <th>Road No.</th><!-- New added column -->
                            <th>House Type</th><!-- New added column -->
                            <th>House</th>
                            <th>Acknowledgement</th> <!-- new colum-->
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

        function dorm_yn() {
            flat_dorm_yn();
            if($('#dormitory_y').is(':checked'))
            {
                var dormitory_yn = 'Y';
                $('label[for=flat_name_id]').html('Dormitory Seat No');
                //$('label[for=flat_name_id]' ).addClass( "required" );
                //$("#flat_name_id").attr("placeholder", "Dormitory Seat No");
                //$('#flat_name_id').addClass( "required" );

                $('#flat_dormitory_y').prop('checked',true);
                $('#flat_dormitory_n').prop('checked',false);

                $('.flat_dormitory_yn').prop('disabled',true);

            }else{
                var dormitory_yn = 'N';
                $('label[for=flat_name_id]').html('Flat Number');
                //$('label[for=flat_name_id]' ).removeClass( "required" );
                //$("#flat_name_id").attr("placeholder", "Flat Number");
                //$('#flat_name_id').removeClass( "required" );

                $('#flat_dormitory_y').prop('checked',false);
                $('#flat_dormitory_n').prop('checked',true);

                $('.flat_dormitory_yn').prop('disabled',false);
            }

            let colonyId = $('#colony_id').val();
            if(colonyId.length>0){
                loadBuildingByColonyArea(colonyId);
            }
        }

        function loadBuildingByColonyArea(colonyId){
            //let x = $( '#dormitory_y').val();
            let dormitory_yn = "";
            if ($("#dormitory_y").is(':checked')){
                dormitory_yn = 'Y';
            }else{
                dormitory_yn = 'N';
            }

            let houseTypeId = $('#house_type_id').val();

            if(colonyId !== undefined && colonyId) {
                $.ajax({
                    type: "GET",
                    url: APP_URL+"/ajax/buildings-by-colony/" + colonyId + "/"+dormitory_yn+ "/" + houseTypeId,
                    async: false,
                    success: function (data) {
                        $('#building-list').html(data);
                        $(".select2").select2();
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            } else {
                // $('#building-list').html('');
                $('#building_id').empty();
            }
        }

        // New created by Md. Mashud Rana according to CR no 2679
        function loadHouseTypesByBuildingDormitory(buildingId) {
            let flat_dormitory_yn = "";
            if ($("#flat_dormitory_y").is(':checked')){
                flat_dormitory_yn = 'Y';
            }else{
                flat_dormitory_yn = 'N';
            }

            if(buildingId !== undefined && buildingId) {
                $.ajax({
                    type: "GET",
                    url: APP_URL+"/ajax/house-types-by-building-dormitory/" + buildingId + "/"+flat_dormitory_yn,
                    async: false,
                    success: function (data) {
                        $('#flatType').html(data);
                        $(".select2").select2();
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            } else {
                $('#flatType').val('');
            }
        }

        function loadFlatByBuilding(buildingId){
            let flat_dormitory_yn = "";
            if ($("#flat_dormitory_y").is(':checked')){
                flat_dormitory_yn = 'Y';
            }else{
                flat_dormitory_yn = 'N';
            }

            let houseTypeId = $('#house_type_id').val();

            if(buildingId !== undefined && buildingId) {
                $.ajax({
                    type: "GET",
                    url: APP_URL+"/ajax/houselist-by-building/" + buildingId + "/"+flat_dormitory_yn + "/" + houseTypeId,
                    // url: APP_URL+"/ajax/houselist-by-building/" + buildingId + "/"+flat_dormitory_yn,
                    async: false,
                    success: function (data) {

                        $('#houseList').html(data);
                        $(".select2").select2();
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            } else {
                $('#houseList').val('');
            }
        }

        function flat_dorm_yn() {
            let buildingId = $('#building_id').val();
            if(buildingId || buildingId != null || buildingId != 'undefined' ){
                loadFlatByBuilding(buildingId);
            }

            if($('#flat_dormitory_y').is(':checked'))
            {
                var flat_dormitory_yn = 'Y';
                $('label[for=flat_name_id]').html('Dormitory Seat No');
                //$('label[for=flat_name_id]' ).addClass("required");

                //$('#house_id').prop("required",true);
                // $("#room_number").prop('disabled', false);
                // $("#house_size").val('1');
                // $("#house_size").prop('disabled', true);
                //$('label[for=house_size]').html('Dormitory Seat Quantity');
                //$('input[for=flat_name_id]').html('Dormitory Seat No');
                //$("#flat_name_id").attr("placeholder", "Dormitory Seat No");
            }

            else{
                var flat_dormitory_yn = 'N';
                $('label[for=flat_name_id]').html('Flat Number');
                //$('label[for=flat_name_id]' ).removeClass("required");

                //$('#house_id').prop("required",false);
                // $("#room_number").prop('disabled', true);
                // $("#room_number").val('');
                // $("#house_size").prop('disabled',false );
                // $("#house_size").val('');
                // $('label[for=house_size]').html('Flat Size');
                //$("#flat_name_id").attr("placeholder", "Flat Number");
            }
        }

        function showData(allot_id, emp_id) {
            urlDetails = APP_URL+'/allottee-show';

            $.ajax({
                type: "GET",
                data: {
                    allot_id : allot_id,
                    emp_id : emp_id
                },
                url: urlDetails,
                async: false,
                success: function (data) {

                    if(data.employeeInformation) {
                        $('#emp_id').val(data.employeeInformation.emp_id);
                        $('#emp_code').val(data.employeeInformation.emp_code);
                        $('#emp_name').val(data.employeeInformation.emp_name);
                        $('#emp_designation').val(data.employeeInformation.designation);
                        $('#emp_department').val(data.employeeInformation.department);

                        $('#email').val(data.employeeInformation.emp_email);
                        $('#contact_no').val(data.employeeInformation.emp_mbl);

                        var emp_dob = data.employeeInformation.emp_dob.split(" ");
                        $('#date_of_birth').val(emp_dob[0]);

                        var emp_lpr_date = data.employeeInformation.emp_lpr_date.split(" ");
                        $('#prl_date').val(emp_lpr_date[0]);

                        $('#office_order_no').val(data.allotInformation.office_order_no);

                        if(data.allotInformation.office_order_date) {
                            var ofc_order_date = data.allotInformation.office_order_date.split(" ");
                            $('#office_order_date').val(ofc_order_date[0]);
                        }
                        if(data.houseInformation.dormitory_yn == 'Y') {
                            $("#dormitory_y").prop("checked", true);
                            $("#flat_dormitory_y").prop("checked", true);
                        } else {
                            $("#dormitory_n").prop("checked", true);
                            $("#flat_dormitory_n").prop("checked", true);
                        }
                        $('#colony_id').val(data.houseInformation.colony_id).trigger('change');
                        $('#house_type_id').val(data.houseInformation.house_type_id).trigger('change');
                        $('#building_id').val(data.houseInformation.building_id).trigger('change');


                        var date_of_allotment = data.allotInformation.date_of_allotment.split(" ");
                        $('#date_of_allotment').val(date_of_allotment[0]);


                        if(data.houseInformation.dormitory_yn == 'Y')
                        {
                            $('label[for=flat_name_id]').html('Dormitory Seat No');
                            $('#house_id').append($('<option>', {
                                value: data.houseInformation.house_id,
                                text: data.houseInformation.house_name+'(Floor No: '+data.houseInformation.floor_number+', Seat No: '+data.houseInformation.house_code+')'
                            }));
                        }
                        else
                        {
                            $('label[for=flat_name_id]').html('Flat Number');
                            $('#house_id').append($('<option>', {
                                value: data.houseInformation.house_id,
                                text: data.houseInformation.house_name
                            }));
                        }
                        $('#house_id').val(data.houseInformation.house_id).trigger('change');

                        if(data.allotInformation.special_consideration_yn == 'Y')
                        {
                            $("#special_consideration_y").prop("checked", true);
                        }
                        else
                        {
                            $("#special_consideration_n").prop("checked", true);
                        }

                        $('#house_type').val(data.employeeInformation.house_type_name);
                        $('#house_size').val(data.employeeInformation.house_size);
                        $('#house_floor').val(data.employeeInformation.floor_number);
                        $('#colony').val(data.employeeInformation.colony_name);

                        $('#searchResult').html('');
                        $('#searchResult').addClass('d-none');

                        $('#office_order_no').attr('readonly', 'readonly');
                        $('#office_order_date').attr('readonly', 'readonly');
                        $('#date_of_allotment').attr('readonly', 'readonly');
                        $('.custom-control-input').attr("disabled", true);
                        $('#submit').hide();
                        $('#reset').hide();
                        // $('#building_id').addClass('select2-accessible');
                        // $('#house_id').addClass('select2-accessible');
                    } else {
                        $('#emp_id').val('');
                        $('#emp_code').val('');
                        $('#emp_name').val('');
                        $('#emp_designation').val('');
                        $('#emp_department').val();
                        $('#date_of_birth').val();
                        $('#prl_date').val();

                        $('#house_type').val();
                        $('#house_size').val();
                        $('#house_floor').val();
                        $('#colony').val();

                        $('#searchResult').html(alertNotFound);
                        $('#searchResult').removeClass('d-none');
                        $('#emp_code_search').focus();
                        setTimeout(function(){
                            $('#searchResult').addClass('d-none');
                        }, 3000);

                    }
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    // $('#colony_id').attr("disabled", true);
                    // $('#building_id').attr("disabled", true);
                    // $('#house_id').attr("disabled", true);
                },
                error: function (err) {
                    alert('error', err);
                }
            });


        }
        $('#emp_code_search').select2({
            placeholder: "Select",
            allowClear: true,
            ajax: {
                url: APP_URL + '/ajax/alloted-employee',
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

                        obj.id = obj.emp_code;
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

            $(document).on("click", '.confirm-delete', function (e) {
                let url_data = $(this).attr('href');
                let fields = url_data.split('-');
                let allot_id = fields[0];
                let emp_id = fields[1];

                e.preventDefault();
                swal({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#dd3333",
                    confirmButtonText: "Yes, delete it!",
                    confirmButtonClass: "btn btn-primary",
                    cancelButtonClass: "btn btn-danger ml-1",
                    buttonsStyling: !1
                }).then(function (e) {
                    if (e.value === true) {
                        $.ajax({
                            type: 'GET',
                            url: APP_URL + "/allottee-remove",
                            data: {allot_id: allot_id, emp_id: emp_id},
                            success: function (results) {
                                let field = results.split('+');
                                let status_code = field[0];
                                let status_msg = field[1];

                                if (status_code == 1) {
                                    swal({
                                        title: status_msg,
                                        confirmButtonText: 'OK',
                                        type: 'success'
                                    }).then(function () {
                                        $('#house-allotment').DataTable().ajax.reload();
                                    });
                                } else {
                                    swal("Error!", status_msg, "error");
                                }
                            }
                        });

                    } else {
                        e.dismiss;
                    }

                }, function (dismiss) {
                    return false;
                })
            });


            $('#special_consideration_y').click(function () {
                if ($(this).is(':checked')) {
                    $("#hide_remarks").show();
                    $("#hide_file").show();
                }
            });

            $('#special_consideration_n').click(function () {
                if ($(this).is(':checked')) {
                    $("#hide_remarks").hide();
                    $("#hide_file").hide();
                }
            });


            select('#electrical_eng', '/ajax/electrical-engineers', ajaxParams, employeeOptions);
            select('#civil_eng', '/ajax/civil-engineers', ajaxParams, employeeOptions);

            let entryHeading = 'Allottee Entry Form';
            let listHeading = 'Allottee List';
            $('#entryHeading').html(entryHeading);
            $('#listHeading').html(listHeading);


            $(document).on("click","#submitSearch",function(event) {
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

            $('#house-allotment').DataTable({
                processing: true,
                serverSide: true,
                order: true,
                ajax: APP_URL + "/allottee-information-datatable-list",
                columns: [
                    {data: 'emp_code', name: 'emp_code'},
                    {data: 'emp_name', name: 'emp_name'},
                    {data: 'department_name', name: 'department_name', searchable: false, orderable: false },
                    {data: 'designation', name: 'designation', searchable: false, orderable: false },
                    {data: 'building_name', name: 'building_name'}, //New column added at 3rd January 2022 according to New CR
                    {data: 'building_road_no', name: 'building_road_no'}, //New column added at 7th Feb 2022 according to New CR
                    {data: 'house_type', name: 'house_type'}, //New column added at 3rd January 2022 according to New CR
                    {data: 'house_name', name: 'house_name'},
                    {data: 'dept_ack_no', name: 'dept_ack_no'},
                    {data: 'action', name: 'action'},
                ]
            });



            // $(document).on('change', '#colony_id', function() {
            //     let colonyId = $(this).val();
            //     loadBuildingByColonyArea(colonyId);
            // });

            $(document).on('change', '#colony_id', function() {
                let colonyId = $(this).val();

                if(colonyId !== undefined && colonyId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL+"/ajax/colony-wise-assign-type/" + colonyId,
                        async: false,
                        success: function (data) {
                            $('#house_type_id').html(data);
                            $('#house_type_id').addClass('select2',true);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                } else {
                    // $('#houseDetails').val('');
                    $('#house_id').empty();
                }

            });

            $(document).on('change', '#house_id', function() {
                let houseId = $(this).val();

                if(houseId !== undefined && houseId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL+"/ajax/housedetails-by-house/" + houseId  ,
                        success: function (data) {
                            $('#houseDetails').html(data);
                            $('#house_id').addClass('select2',true);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                } else {
                    // $('#houseDetails').val('');
                    $('#house_id').empty();
                }

            });

            $(document).on('change', '#building_id', function() {
                let buildingId = $(this).val();
                loadFlatByBuilding(buildingId);
                // loadHouseTypesByBuildingDormitory(buildingId);
            });

            $(document).on('change', '#house_type_id', function() {

                let colonyId = $('#colony_id').val();
                loadBuildingByColonyArea(colonyId);


                // let buildingId = $('#building_id').val();
                // loadFlatByBuilding(buildingId);
            });




            function setEmployeeInformation(evt)
            {
                evt.preventDefault();
                let emp_code = '';
                let urlDetails = '';
                let alertTypeEmpCode = '<strong>Please Type Employee Code</strong>';
                let alertNotFound = '<strong>No data Found</strong>';
                emp_code = $('#emp_code_search').val();
                if($('#emp_code_search').val().length <= 0){
                    $('#searchResult').removeClass('d-none');
                    $('#searchResult').html(alertTypeEmpCode);
                    alert("Please Type Employee Code");
                    $('#emp_code_search').focus();
                    setTimeout(function(){
                        $('#searchResult').addClass('d-none');
                    }, 3000);
                    return false;
                }
                urlDetails = APP_URL+'/ajax/emp-code-wise-employee-details-for-allottee/'+emp_code;

                $.ajax({
                    type: "GET",
                    url: urlDetails,
                    success: function (data) {

                        if(data.employeeInformation) {
                            $('#emp_id').val(data.employeeInformation.emp_id);
                            $('#emp_code').val(data.employeeInformation.emp_code);
                            $('#emp_name').val(data.employeeInformation.emp_name);
                            $('#emp_designation').val(data.employeeInformation.designation);
                            $('#emp_department').val(data.employeeInformation.department);

                            $('#email').val(data.employeeInformation.emp_email);
                            $('#contact_no').val(data.employeeInformation.emp_mbl);
                            $('#emp_join_date').val(data.employeeInformation.emp_join_date);

                            var emp_dob = data.employeeInformation.emp_dob.split(" ");
                            $('#date_of_birth').val(emp_dob[0]);

                            var emp_lpr_date = data.employeeInformation.emp_lpr_date.split(" ");
                            $('#prl_date').val(emp_lpr_date[0]);

                            $('#house_type').val(data.employeeInformation.house_type_name);
                            $('#house_size').val(data.employeeInformation.house_size);
                            $('#house_floor').val(data.employeeInformation.floor_number);
                            $('#colony').val(data.employeeInformation.colony_name);

                            $('#searchResult').html('');
                            $('#searchResult').addClass('d-none');

                        } else {
                            $('#emp_id').val('');
                            $('#emp_code').val('');
                            $('#emp_name').val('');
                            $('#emp_designation').val('');
                            $('#emp_department').val();
                            $('#date_of_birth').val();
                            $('#prl_date').val();

                            $('#house_type').val();
                            $('#house_size').val();
                            $('#house_floor').val();
                            $('#colony').val();

                            $('#searchResult').html(alertNotFound);
                            $('#searchResult').removeClass('d-none');
                            $('#emp_code_search').focus();
                            setTimeout(function(){
                                $('#searchResult').addClass('d-none');
                            }, 3000);

                        }
                        $('#office_order_no').attr('readonly', false);
                        $('#office_order_date').attr('readonly', false);
                        $('#date_of_allotment').attr('readonly', false);
                        // $("#colony_id").val('').trigger('change');
                        $('#colony_id').val('').trigger('change');
                        $('#building_id').val('').trigger('change');
                        $('#house_id').val('').trigger('change');
                        $('#date_of_allotment').val('');
                        $('#office_order_no').val('');
                        $('#office_order_date').val('');
                        $('#floor').val('');
                        $('#road_no').val('');
                        $('#building_no').val('');
                        $("#dormitory_n").prop("checked", true);
                        $("#special_consideration_n").prop("checked", true);

                        $('.custom-control-input').attr("disabled", false);
                        $('#submit').show();
                        $('#reset').show();
                    },
                    error: function (err) {
                        alert('error', err);
                    }
                });
            }

        });







    </script>
@endsection



