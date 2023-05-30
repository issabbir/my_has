@extends('layouts.default')

@section('title')
    Flat Information
@endsection

@section('header-style')
    <!--Load custom style link or css-->
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body"><h4 class="card-title">Flat Information</h4>
                    <hr>
                    @include('house.form')
                </div>
            </div>

            <div id="temp_div" style="display: none">
                <div class="card">
                    <div class="card-body"><h4 class="card-title">Temp List</h4><!---->
                        <hr>
                        <div class="table-responsive">
                            <form action="{{ route('house.permanent') }}" method="POST">
                            @csrf
                            <table  class="table table-sm datatable mdl-data-table dataTable" id="temp_house" style="width: 100% !important">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Flat Name</th>
                                    <th>Building</th>
                                    <th>Road No.</th>
                                    <th>Flat Type</th>
                                    <th>Flat Size</th>
                                    <th>Floor</th>
                                    <th>Water Tap No.</th>
                                    <th>Status</th>
                                    <th>In Adv.</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                            <div class="row">
                                <div class="col mt-2">
                                    <div class="d-flex justify-content-end">
                                        <input type="hidden" name="building" id="building">
                                        <button type="submit" class="btn btn btn-dark shadow mb-1 btn-secondary">Submit</button>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body"><h4 class="card-title">Flat List</h4><!---->
                    <hr>
                    <div class="table-responsive">
                        <table  class="table table-sm datatable mdl-data-table dataTable" id="houses">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Seat No</th>
                                <th>Flat Name</th>
                                <th>Building</th>
                                <th>Road No.</th>
                                <th>Flat Type</th>
                                <th>Flat Size</th>
                                <th>Floor</th>
                                <th>Water Tap No.</th>
                                <th>Status</th>
                                <th>Department</th>
                                <th>In Adv.</th>
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
    <script type="text/javascript">
        /** FUNCTIONS **/
        function init() {
            let parkingYn = $('[name="parking_yn"]:checked').val();
            if(parkingYn == 'Y') {
                $('#parking_id').removeAttr('disabled');
            } else {
                $('#parking_id').attr('disabled', 'disabled');
                $('#parking_id').val('');
            }
            let btclConnectionYn = $('[name="btcl_connection_yn"]:checked').val();
            if(btclConnectionYn == 'Y') {
                $('#btcl_number').removeAttr('disabled');
            } else {
                $('#btcl_number').attr('disabled', 'disabled');
                $('#btcl_number').val('');
            }
            let intercomYn = $('[name="intercom_yn"]:checked').val();
            if(intercomYn == 'Y') {
                $('#intercom_no').removeAttr('disabled');
            } else {
                $('#intercom_no').attr('disabled', 'disabled');
                $('#intercom_no').val('');
            }
            let reserveYn = $('[name="reserve_yn"]:checked').val();
            if(reserveYn == 'Y') {
                $('#designation_id').removeAttr('disabled');
            } else {
                $('#designation_id').attr('disabled', 'disabled');
                $('#designation_id').val('');
            }
        }

        function activeParkingNo(parkingYn='')
        {
            if(parkingYn) {
                if(parkingYn == 'Y') {
                    $('#parking_id').removeAttr('disabled');
                } else  if(parkingYn == 'N') {
                    $('#parking_id').attr('disabled', 'disabled');
                    $('#parking_id').val('');
                }
            } else {
                $('[name="parking_yn"]').on('change', function() {
                    let parkingYn = $(this).val();

                    if(parkingYn == 'Y') {
                        $('#parking_id').removeAttr('disabled');
                    } else  if(parkingYn == 'N') {
                        $('#parking_id').attr('disabled', 'disabled');
                        $('#parking_id').val('');
                    }
                });
            }
        }

        function activeBtclConnection(btclConnectionYn='')
        {
            if(btclConnectionYn) {
                if(btclConnectionYn == 'Y') {
                    $('#btcl_number').removeAttr('disabled');
                } else  if(btclConnectionYn == 'N') {
                    $('#btcl_number').attr('disabled', 'disabled');
                    $('#btcl_number').val('');
                }
            } else {
                $('[name="btcl_connection_yn"]').on('change', function() {
                    let btclConnectionYn = $(this).val();

                    if(btclConnectionYn == 'Y') {
                        $('#btcl_number').removeAttr('disabled');
                    } else  if(btclConnectionYn == 'N') {
                        $('#btcl_number').attr('disabled', 'disabled');
                        $('#btcl_number').val('');
                    }
                });
            }
        }

        function activeIntercomNo(intercomYn='')
        {
            if(intercomYn) {
                if(intercomYn == 'Y') {
                    $('#intercom_no').removeAttr('disabled');
                } else  if(intercomYn == 'N') {
                    $('#intercom_no').attr('disabled', 'disabled');
                    $('#intercom_no').val('');
                }
            } else {
                $('[name="intercom_yn"]').on('change', function() {
                    let intercomYn = $(this).val();

                    if(intercomYn == 'Y') {
                        $('#intercom_no').removeAttr('disabled');
                    } else  if(intercomYn == 'N') {
                        $('#intercom_no').attr('disabled', 'disabled');
                        $('#intercom_no').val('');
                    }
                });
            }
        }

        function activeReserveFor(reserveYn='')
        {
            if(reserveYn) {
                if(reserveYn == 'Y') {
                    $('#designation_id').removeAttr('disabled');
                } else  if(reserveYn == 'N') {
                    $('#designation_id').attr('disabled', 'disabled');
                    $('#designation_id').val('');
                }
            } else {
                $('[name="reserve_yn"]').on('change', function() {
                    let reserve_yn = $(this).val();

                    if(reserve_yn == 'Y') {
                        $('#designation_id').removeAttr('disabled');
                    } else  if(reserve_yn == 'N') {
                        $('#designation_id').attr('disabled', 'disabled');
                        $('#designation_id').val('');
                    }
                });
            }
        }

        function findColony(buildingId='')
        {
            if(buildingId) {
                if(buildingId !== undefined && buildingId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL+"/houses/load-colony/" + buildingId,
                        success: function (data) {
                            $('#colony_id').val(data.colony_name);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                } else {
                    $('#colony_id').val('');
                }
            } else {
                $('#building_id').on('change', function() {
                    let buildingId = $(this).val();
                    let min = 0;
                    let max = 0;

                    var temp_div = document.getElementById("temp_div");
                    if(buildingId !== undefined && buildingId) {
                        temp_div.style.display = "block";
                        tempTable.draw();
                        $.ajax({
                            type: "GET",
                            url: APP_URL+"/houses/load-data/" + buildingId,
                            success: function (data) {
                                    if(data.dormitory_yn == 'Y')
                                    {
                                        $("#dormitory_y").prop("checked", true);
                                        $('#dormitory_total_seat').removeAttr('disabled');
                                        $("#house_size").attr({
                                            "max" : '1',        // substitute your own
                                            "min" : ''          // values (or variables) here
                                        });
                                    }
                                    else
                                    {
                                        $("#dormitory_n").prop("checked", true);
                                        $('#dormitory_total_seat').attr('disabled', 'disabled');
                                        let splitted_house_size = data.house_size.split('-');

                                        min = splitted_house_size[0]? Number(splitted_house_size[0]): min;

                                        if(splitted_house_size[1] == 'ABOVE'){
                                            max = 10000000;  // for above
                                        }else{
                                            max = splitted_house_size[1]? Number(splitted_house_size[1]): max;
                                        }
                                        $('#house_size').attr("min",min);
                                        $('#house_size').attr("max",max);
                                    }
                                $('#colony_id').val(data.colony.colony_name);
                                $('#floor_number').html(data.floors);
                                $('#house_type_id').html(data.housetypes);

                                // $('#houses').DataTable().ajax.reload();
                                $("#building").val(buildingId);
                                oTable.draw();

                                let selectedDormatoryStatus = $('input[type=radio][name=dormitory_yn]:checked').val();
                                changeDormitoryLableOnDormatoryStatusYN(selectedDormatoryStatus);
                            },
                            error: function (data) {
                                alert('error');
                            }
                        });
                    } else {
                        temp_div.style.display = "none";
                        $('#colony_id').val('');
                    }
                });
            }
        }

        $('#house_size').on('keyup',function(){
            toCheckMaxMinValue('#house_size');
        });

        /** END OF FUNCTIONS **/

        $(document).ready(function () {
            /*$('#house-datatable').DataTable({});*/

            // var temp_div = document.getElementById("temp_div");
            // temp_div.style.display = "none";
            // if (x.style.display === "none") {
            //     x.style.display = "block";
            // } else {
            //     x.style.display = "none";
            // }
            // oTable.draw();
            init();

            findColony();
            activeParkingNo();
            activeBtclConnection();
            activeIntercomNo();
            activeReserveFor();

            let selectedDormatoryStatus = $('input[type=radio][name=dormitory_yn]:checked').val();
            changeDormitoryLableOnDormatoryStatusYN(selectedDormatoryStatus);
        });

        function changeDormitoryLableOnDormatoryStatusYN(selectedDormatoryStatus){
            switch (selectedDormatoryStatus) {
                case 'Y':
                    //$('#dormitory_total_seat').removeAttr('disabled');
                    $('label[for=house_code]').html('Dormitory Seat No');
                    $('input[id=house_code]').attr("placeholder", "Dormitory Seat No");
                    $('label[for=house_code]').addClass('required');

                    $('label[for=house_size]').html('Dormitory Seat Quantity');
                    $('input[id=house_size]').prop({value:1, readonly:true}).css({"background-color": '#bcbcbc'});


                    if($('input[id=room_number]').val() == ''){
                     $('input[id=room_number]').prop({value:'' , disabled:false,  }).css({"background-color": '#ffffff'});
                    }
                    // $('label[for=room_number]').addClass('required');

                    $("#house_size").attr({
                        "max" : '1',        // substitute your own
                        "min" : ''          // values (or variables) here
                    });
                    break;

                case 'N':
                    $('label[for=house_code]').html('Flat Number');
                    $('input[id=house_code]').prop({placeholder:"Flat Number"});
                    $('label[for=house_code]').removeClass('required')
                    $('input[id=house_size]').prop({value:'', readonly:false}).css({"background-color": '#FFFFFF'});;
                    $('label[for=house_size]').html('Flat Size');

                    // if($('input[id=house_size]').val() == ''){
                    //  $('input[id=house_size]').prop({value:'', readonly:false }).css({"background-color": '#FF0000'});
                    // }



                    $('input[id=room_number]').prop({value:'' , disabled:true,  }).css({"background-color": '#bcbcbc'});
                    $('label[for=room_number]').removeClass('required');

                    break;
            }
        }

        $(document).on('change','input[type=radio][name=dormitory_yn]', function() {
            let selectedDormatoryStatus = $(this).val();
            changeDormitoryLableOnDormatoryStatusYN(selectedDormatoryStatus);
        });

        var oTable = $('#houses').DataTable({
            processing: true,
            serverSide: true,
            // ajax: APP_URL+"/houses-datatable-list",
            ajax: {
                "url" : APP_URL+"/houses-datatable-list",
                data: function (params) {
                    params.building_id = $('#building_id').val();
                }
            },
            columns: [
                /*{ data: 'house_id', name: 'house_id', searchable: false },*/
                { data: 'DT_RowIndex', "name": 'DT_RowIndex'},
                { data: 'house_code', name: 'house_code' },
                { data: 'house_name', name: 'house_name' },
                // { data: 'name', name: 'Name' },
                { data: 'buildinglist.building_name', name: 'buildinglist.building_name' },
                { data: 'buildinglist.building_road_no', name: 'buildinglist.building_road_no' },
                { data: 'housetype.house_type', name: 'housetype.house_type' },
                { data: 'house_size', name: 'house_size' },
                { data: 'floor_number', name: 'floor_number' },
                { data: 'water_tap', name: 'water_tap' },
                { data: 'housestatus.house_status', name: 'housestatus.house_status' },
                { data: 'dept', name: 'dept' },
                { data: 'in_advertisement', name: 'in_advertisement', searchable: false, orderable: false },
                { data: 'action', name: 'Action', searchable: false },
            ],
            columnDefs: [
                {
                    "targets": 9,
                    "className": "text-center"
                }
            ],
        });

        var tempTable = $('#temp_house').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            // ajax: APP_URL+"/houses-datatable-list",
            ajax: {
                "url" : APP_URL+"/temp-datatable-list",
                data: function (params) {
                    params.building_id = $('#building_id').val();
                }
            },
            columns: [
                /*{ data: 'house_id', name: 'house_id', searchable: false },*/
                { data: 'DT_RowIndex', "name": 'DT_RowIndex'},
                // { data: 'house_code', name: 'house_code' },
                { data: 'house_name', name: 'house_name' },
                // { data: 'name', name: 'Name' },
                { data: 'buildinglist.building_name', name: 'buildinglist.building_name' },
                { data: 'buildinglist.building_road_no', name: 'buildinglist.building_road_no' },
                { data: 'housetype.house_type', name: 'housetype.house_type' },
                { data: 'house_size', name: 'house_size' },
                { data: 'floor_number', name: 'floor_number' },
                { data: 'water_tap', name: 'water_tap' },
                { data: 'housestatus.house_status', name: 'housestatus.house_status' },
                { data: 'in_advertisement', name: 'in_advertisement', searchable: false, orderable: false },
                { data: 'action', name: 'Action', searchable: false },
            ],
            columnDefs: [
                {
                    "targets": 9,
                    "className": "text-center"
                }
            ],
        });

        function deleteTemp(house_id) {
            // alert(house_id);
            if(house_id)
            {
                $.ajax({
                    type: "GET",
                    url: APP_URL+'/houses-delete',
                    data: {
                        house_id: house_id,
                    },
                    success: function (data) {
                        // alert(data.o_status_message);
                        if(data == 1)
                        {
                            Swal.fire({
                                title: 'Successfully Deleted',
                                icon: 'success',
                                // confirmButtonText: 'OK'
                                showConfirmButton:false
                            });

                        }
                        else
                        {
                            Swal.fire({
                                title: 'Error Occured',
                                icon: 'error',
                                // confirmButtonText: 'OK'
                                showConfirmButton:false
                            });
                        }
                        tempTable.draw();
                        // $('#houses').DataTable().ajax.reload();
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            }
        }
    </script>
@endsection
