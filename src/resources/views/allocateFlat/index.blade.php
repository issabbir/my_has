@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
    <style>

        table tr {
            height: 10px;
            min-height: 10px;
        }

        .displayNone {
            display: none;
        }

        .grayBackground {
            background-color: lightgoldenrodyellow;
        }
    </style>
@endsection

@section('content')
    @if(Session::has('message'))
        <div
            class="alert {{Session::get('m-class') ? Session::get('m-class') : 'alert-danger'}} show"
            role="alert">
            {{ Session::get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <!-- form start -->
    <form id="advertisement-register" method="POST"
          @if(isset($houselist[0]->dept_ack_id))
          action="{{ route('allocate-flat.update', ['id' => $houselist[0]->dept_ack_id]) }}">
        <input name="_method" type="hidden" value="PUT">
        <input name="alloted_flat" id="alloted_flat" type="hidden" value="{{$houselist[0]->no_of_alloted_flat}}">

        @else
            action="{{ route('allocate-flat.store') }}"
        @endif
        <div class="card">
            {{ csrf_field() }}

            <div class="card-body" id="houseSelectionPanel"><h4 class="card-title">Allocate @if(isset($houselist[0]->dept_ack_id)) Update @else Entry @endif</h4>

                <hr/>
                <div class="row justify-content-center">
                    <div class="col-md-11">
                        <div class="row">
{{--                            @if(!isset($houselist[0]->dept_ack_id))--}}
                            @if(!isset($houselist[0]->dept_ack_id) || (isset($canUpdate) && $canUpdate))
                                <div class="col-md-2">
                                    <label class="required">Ack No.</label>
                                    <select class="custom-select select2 required
                                        " name="ack_no" id="ack_no">
                                        @if(isset($houselist[0]->dept_ack_id))
                                            <option value="{{$houselist[0]->dept_ack_id}}" selected>
                                                {{$houselist[0]->dept_ack_no}}
                                            </option> @endif
                                        @if(!isset($houselist[0]->dept_ack_id))
                                            <option value="">--Please Select--</option>
                                            @foreach($ackInfo as $value)
                                                <option value="{{ $value->dept_ack_id  }}"
                                                        myTag="{{ $value->no_of_alloted_flat}}">
                                                    {{$value->dept_ack_no}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="">Department</label>

                                    <input type="text"
                                           class="form-control"
                                           name="dep_id"
                                           id="dep_id" readonly
                                           @if(isset($houselist[0]->dept_ack_id))
                                           value="{{$houselist[0]->department_name}}" readonly @endif>
                                </div>
                                <div class="col-md-2">
                                    <label class="" id="flat_label">Flat Qty</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        name="setMyTag"
                                        id="setMyTag"
                                        value=""
                                        readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="required">Residential Area</label>

                                    <select class="custom-select select2 required
                                        " name="res_area" id="res_area">
                                        <option value="">--Please Select--</option>

                                        @foreach($colony as $value)
                                            <option value="{{ $value->colony_id  }}">
                                                {{$value->colony_name}}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="required">House Type</label>

                                    <select class="custom-select select2 required" name="house_type" id="house_type">
                                        <option value="">--Please Select--</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="required">BUILDING</label>
                                    <select class="custom-select select2 required
                                        " name="building_list" id="building_list">
                                        <option value="">--Please Select--</option>

                                    </select>
                                </div>

                            <div class="col-md-3">
                                <label >Road No</label>
                                <input type="text" class="form-control" name="road_no" id="road_no" readonly >
                            </div>
                                <div class="col-md-3">
                                    <label class="required">House</label>
                                    <select class="custom-select select2 required
                                        " name="house_list" id="house_list">
                                        <option value="">--Please Select--</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex justify-content-end">
                                    <div id="start-no-field">
                                        <label for="seat_to1">&nbsp;</label><br/>
                                        <button type="button" id="append"
                                                class="btn btn-primary mb-1 add-row-alloted-flat"
                                                data-row="{{ isset($houselist->no_of_alloted_flat) ? $houselist->no_of_alloted_flat :0 }}">
                                            ADD
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-12 mt-2">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="table-allocate-flat">
                                        <thead>
                                        <tr>
                                            <th style="height: 25px;text-align: left; width: 5%">SL.</th>
{{--                                            @if(!isset($houselist[0]->dept_ack_id))--}}
                                            @if(!isset($canUpdate) || $canUpdate)
                                                <th style="height: 25px;text-align: left; width: 5%">Action</th>
                                            @endif
                                            <th style="height: 25px;text-align: left; width: 5%">Ack. Number</th>
                                            <th style="height: 25px;text-align: left; width: 30%">Department</th>
                                            <th style="height: 25px;text-align: left; width: 30%">Residential Area</th>
                                            <th style="height: 25px;text-align: left; width: 10%">House Type</th>
                                            <th style="height: 25px;text-align: left; width: 30%">Building</th>
                                            <th style="height: 25px;text-align: left; width: 20%">Road No</th>
                                            <th style="height: 25px;text-align: left; width: 30%">Flat</th>
                                            @if(isset($houselist[0]->dormitory_yn) && $houselist[0]->dormitory_yn == 'Y')
                                                <th style="height: 25px;text-align: left; width: 30%">Seat No.</th>
                                            @endif
                                        </tr>
                                        </thead>

                                        <tbody id="comp_body">
                                        @if(!empty($houselist))
                                            @php $i = 0; @endphp
                                            @foreach($houselist as $key=>$value)
                                                <tr>
                                                    <td>{{ ++$i }}</td>
                                                    @if(!isset($houselist[0]->dept_ack_id))
                                                        <td>

                                                            <input type='checkbox' name='record'
                                                                   value="{{$value->dept_ack_id.'+'.$value->house_id}}">

                                                            <input type="hidden" name="ack_id[]"
                                                                   value="{{$value->dept_ack_id}}"
                                                                   class="dept_ack_id">
                                                            <input type="hidden" name="house_id[]"
                                                                   value="{{$value->house_id}}">
                                                        </td>
                                                    @endif
                                                    @if(isset($houselist[0]->dept_ack_id) && isset($canUpdate) && $canUpdate)
                                                        <td class='text-center'>
                                                            <input type='checkbox' name='record'
                                                                   value="{{$value->dept_ack_id.'+'.$value->house_id}}">
                                                            <input type="hidden" name="ack_id[]"
                                                                   value="{{$value->dept_ack_id}}"
                                                                   class="dept_ack_id">
                                                            <input type="hidden" name="house_id[]"
                                                                   value="{{$value->house_id}}">
                                                        </td>
                                                    @endif
                                                    <td>{{$value->dept_ack_no}}</td>
                                                    <td>{{$value->department_name}}</td>
                                                    <td>{{$value->colony_name}}</td>
                                                    <td>{{$value->house_type}}</td>
                                                    <td>{{$value->building_name}}</td>
                                                    <td>{{$value->building_road_no}}</td>
                                                    <td>{{$value->house_name}}</td>
                                                    @if($value->dormitory_yn == 'Y')
                                                        <td>{{$value->house_code}}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>

                                </div>
                            </div>
{{--                            @if(!isset($houselist[0]->dept_ack_id))--}}
                            @if(!isset($houselist[0]->dept_ack_id) || (isset($canUpdate) && $canUpdate))
                                <div class="col-12 d-flex justify-content-start">
                                    <button type="button"
                                            class="btn btn-primary mb-1 delete-row-exam-result">
                                        Delete
                                    </button>
                                </div>
                        </div>
                        <div class="row my-2">
                            <input type="hidden" name="colony_id" id="colony_id">
                            <div class="d-flex justify-content-end col">

                                @if(isset($houselist[0]->dept_ack_id))
                                    <button type="submit" id="submitBtnToHideModal"
                                            class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                        Update
                                    </button>
                                @else
                                    <button type="submit" id="submitBtnToHideModal"
                                            class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                        Submit
                                    </button>
                                @endif
                                <button type="reset" id="reset"
                                        class="btn btn btn-outline shadow mb-1 btn-secondary">
                                    Reset
                                </button>
                                <button type="submit" id="submit"
                                        class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary d-none"></button>
                                &nbsp;
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body" id="advertisementPanel"><h4 class="card-title">Allotment list</h4>
                <hr/>
                <div class="table-responsive">
                    <table id="advMainTable" class="table table-sm datatable mdl-data-table dataTable">

                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Ack. No.</th>
                                <th>Department</th>
                                <th>Alloted House</th>
                                <th>Action</th>
                                <th>Report</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </form>
    <!-- form End -->
    @include('approval.workflowmodal')

    @include('approval.workflowselect')

@endsection

@section('footer-script')
    <!--Load custom script-->
    <script type="text/javascript">
        const userRoles = '@php echo json_encode(Auth::user()->roles->pluck('role_key')); @endphp';//alert(userRoles)
        $(function () {
            $("#ack_no").change(function () {
                var option = $('option:selected', this).attr('myTag');
                $('#setMyTag').val(option);
            });
        });

        function addflat() {
            let allotedflatIndex = 0;
            $('.add-row-alloted-flat').on('click', function (e) {
                e.preventDefault();
                let numberOfRows = $('#comp_body tr').length;
                let totalflat = $('#setMyTag').val().trim();
                if (numberOfRows < totalflat) {
                    let arrayIndex = parseInt($(this).attr('data-row'));
                    allotedflatIndex = arrayIndex;
                    rowGenerate(allotedflatIndex);
                    $(this).attr('data-row', allotedflatIndex);
                } else {
                    Swal.fire('Maximum rows created already!');
                }
            });
        };

        $("#ack_no").on("change", function () {
            let ack_no = $("#ack_no").val();
            let url = APP_URL + '/get-dep-data/';
            if (((ack_no !== undefined) || (ack_no != null)) && ack_no) {
                $.ajax({
                    type: "GET",
                    url: url + ack_no,
                    success: function (data) {
                        console.log(data)
                        $('#dep_id').val(data.ackInfo.department_name);
                        if(data.ackInfo.dormitory_yn == 'Y')
                        {
                            $('#flat_label').text('Dormitory QTY');
                        }
                        else
                        {
                            $('#flat_label').text('Flat QTY');
                        }

                        @if(!isset($houselist[0]->dept_ack_id)) //when not update
                        dataArray = [];
                        $("#table-allocate-flat tbody").empty();
                        if(data.tempData)
                        {
                            for(let i = 0;i<data.tempData.length;i++)
                            {
                                let temp = data.tempData[i];
                                console.log(data.tempData[i])
                                let markup = "<tr role='row'>" +
                                    "<td aria-colindex='1' role='cell'>" + (i+1) + "</td>" +
                                    "<td aria-colindex='2' role='cell' class='text-center'>" +
                                    "<input type='checkbox' data-ack='" + temp.ack_id + "' name='record' class='house_id' value='" + temp.house_id + "+" + "" + "'>" +
                                    "<input type='hidden' name='ack_id[]' value='" + temp.ack_id + "'>" +
                                    "<input type='hidden' name='house_id[]' value='" + temp.house_id + "'>" +
                                    "<input type='hidden' name='colony_id[]' value='" + temp.colony_id + "'>" +
                                    "</td><td aria-colindex='3' role='cell'>" + temp.ack_no + "</td>" +
                                    "<td aria-colindex='4' role='cell'>" + temp.dep_id + "</td>" +
                                    "<td aria-colindex='5' role='cell'>" + temp.colony_name + "</td>" +
                                    "<td aria-colindex='6' role='cell'>" + temp.house_type + "</td>" +
                                    "<td aria-colindex='7' role='cell'>" + temp.building_name + "</td>" +
                                    "<td aria-colindex='8' role='cell'>" + temp.road_no + "</td>" +
                                    "<td aria-colindex='9' role='cell'>" + temp.house + "</td></tr>";

                                $("#table-allocate-flat tbody").append(markup);
                                dataArray.push(temp.house_id);
                            }
                        }
                        @endif
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            } else {
                $('#dep_id').val('');
            }
        });

        $("#res_area").on("change", function () {
            let res_area = $("#res_area").val();
            let url = APP_URL + '/get-residential-house-type-data/';
            if (((res_area !== undefined) || (res_area != null)) && res_area) {
                $.ajax({
                    type: "GET",
                    url: url + res_area,
                    async:false,
                    success: function (data) {
                        $('#house_type').html(data);
                    },
                    error: function (data) {
                        alert('error asche');
                    }
                });
            } else {
                $('#house_type').val('');
            }
        });

        $("#house_type").on("change", function () {
            let house_type = $("#house_type").val();
            let res_area = $("#res_area").val();
            let url = APP_URL + '/get-building-data/'+ house_type + '/' + res_area;
            if (((house_type !== undefined) || (house_type != null)) && house_type) {
                $.ajax({
                    type: "GET",
                    url: url ,
                    success: function (data) {
                        $('#building_list').html(data);
                    },
                    error: function (data) {
                        alert('error asche');
                    }
                });
            } else {
                $('#building_list').val('');
            }
        });

        $("#building_list").on("change", function () {
            let building_list = $("#building_list").val();
            let house_type = $("#house_type").val();
            let ack_id = $("#ack_no").val();
            let res_area = $("#res_area").val();

            let url = APP_URL + '/get-house-data/' + building_list + '/' + house_type+ '/' + res_area;

            if (((building_list !== undefined) || (building_list != null)) && building_list) {
                $.ajax({
                    type: "GET",
                    data: { ack_id : ack_id },
                    url: url,
                    success: function (data) {
                        $('#house_list').html(data);
                    },
                    error: function (data) {
                        alert('error asche');
                    }
                });
            } else {
                $('#house_list').val('');
            }
        });

        $("#building_list").on("change", function () {
            let building_list = $("#building_list").val();
            let url = APP_URL + '/get-house-road-data/' + building_list

            if (((building_list !== undefined) || (building_list != null)) && building_list) {
                $.ajax({
                    type: "GET",
                    url: url,
                    success: function (data) {
                        $('#road_no').val(data[0].building_road_no);
                    },
                    error: function (data) {
                        // alert('error asche');
                    }
                });
            } else {
                $('#road_no').val('');
            }
        });

        $(document).ready(function () {
            @if(isset($houselist[0]->dept_ack_id))
                $('#ack_no').trigger('change');
                $('#setMyTag').val({{$houselist[0]->no_of_alloted_flat}});
{{--                $('#res_area').val({{$houselist[0]->colony_id}}).trigger('change');--}}
{{--                let house_type_id = {{$houselist[0]->house_type_id}};--}}
{{--                let house_type_name = {!! $houselist[0]->house_type !!};--}}
{{--alert(house_type_name)--}}
{{--                var exists = false;--}}
{{--                $('#house_type option').each(function(){--}}
{{--                    // alert('val: '+this.value)--}}
{{--                    if (this.value == house_type_id) {--}}
{{--                        exists = true;--}}
{{--                        return false;--}}
{{--                    }--}}
{{--                });--}}

{{--                if(exists)--}}
{{--                {--}}
{{--                    $('#house_type').val(house_type_id).trigger('change');--}}
{{--                }--}}
{{--                else--}}
{{--                {--}}
{{--                    let house_type = '{{$houselist[0]->house_type}}'--}}
{{--                    $('#house_type').append($('<option>', {--}}
{{--                        value: house_type_id,--}}
{{--                        text: house_type--}}
{{--                    }));--}}

{{--                    $('#house_type').val(house_type_id).trigger('change');--}}
{{--                }--}}
            @endif
            addflat();
            $('#advMainTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 20,
                ajax: APP_URL + '/allocate-flat-datatable-list',
                columns: [
                    {data: 'sl', name: 'sl'},
                    {data: 'dept_ack_no', name: 'dept_ack_no'},
                    {data: 'department_name', name: 'department_name,', searchable: true},
                    {data: 'tot_house', name: 'tot_house', searchable: true},
                    {data: 'action', name: 'Action', searchable: false},
                    {data: 'report', name: 'report', searchable: false},

                ]
            });

        });

        var dataArray = new Array();

        function rowGenerate() {
            let ack_id = $("#ack_no option:selected").val();
            let ack_no = $("#ack_no option:selected").text();
            let dep_id = $("#dep_id").val();
            let house_type = $("#house_type option:selected").text();
            let house_type_id = $("#house_type option:selected").val();
            let building_list = $("#building_list option:selected").text();
            let building_id = $("#building_list option:selected").val();
            let house_list = $("#house_list option:selected").text();
            let house_id = $("#house_list option:selected").val();
            let house = $("#house_list option:selected").text();
            let colony_id = $("#res_area option:selected").val();
            let colonyName = $("#res_area option:selected").text();
            let alt_flat = $("#alloted_flat").val();
            let road = $('#road_no').val();
            let numberOfRows = $('#comp_body tr').length;

            if (ack_id != '' && ack_no != ''  && house_type != '' && building_list != '' && house_list != '' && colonyName !='' && road !='') {
                if ($.inArray(house_id, dataArray) > -1) {
                    swal.fire('This Flat Alrady Generated.');
                 } else {
                    $.ajax({
                        type: "get",
                        async: false,
                        url: APP_URL + "/add-to-temp/",
                        data: {
                            ack_id:ack_id,
                            ack_no:ack_no,
                            dep_id:dep_id,
                            house_type:house_type,
                            house_type_id:house_type_id,
                            building_id:building_id,
                            building_name:building_list,
                            colony_id:colony_id,
                            colony_name:colonyName,
                            road_no:road,
                            house_id:house_id,
                            house:house
                        },
                        success: function (data) {
                            console.log(data);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                    let markup = "<tr role='row'>" +
                        "<td aria-colindex='1' role='cell'>" + (numberOfRows+1) + "</td>" +
                        "<td aria-colindex='2' role='cell' class='text-center'>" +
                        "<input type='checkbox' data-ack='" + ack_id + "' name='record' class='house_id' value='" + house_id + "+" + "" + "'>" +
                        "<input type='hidden' name='ack_id[]' value='" + ack_id + "'>" +
                        "<input type='hidden' name='house_id[]' value='" + house_id + "'>" +
                        "<input type='hidden' name='colony_id[]' value='" + colony_id + "'>" +
                        "</td><td aria-colindex='3' role='cell'>" + ack_no + "</td>" +
                        "<td aria-colindex='4' role='cell'>" + dep_id + "</td>" +
                        "<td aria-colindex='5' role='cell'>" + colonyName + "</td>" +
                        "<td aria-colindex='6' role='cell'>" + house_type + "</td>" +
                        "<td aria-colindex='7' role='cell'>" + building_list + "</td>" +
                        "<td aria-colindex='8' role='cell'>" + road + "</td>" +
                        "<td aria-colindex='9' role='cell'>" + house_list + "</td></tr>";

                    $("#table-allocate-flat tbody").append(markup);
                    dataArray.push(house_id);
                }
            } else {
                Swal.fire('Fill required value.');
            }
        };

        function displayPanel(buildinId) {
            $('#houseSelectionPanel').show(500);
            if (buildinId !== undefined && buildinId) {
                $.ajax({
                    type: "GET",
                    url: APP_URL + "/advertisements-list/" + buildinId,
                    success: function (data) {
                        $('#houseList').html(data);
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            }
        }


        $(".delete-row-exam-result").click(function () {
            let arr_stuff = [];
            let house_id = [];
            let dept_ack_id = [];

            $(':checkbox:checked').each(function (i) {
                arr_stuff[i] = $(this).val();

                // console.log(arr_stuff[i]);
                let sd = arr_stuff[i].split('+');
                // alert(dataArray[i]);
                house_id.push(sd[0]);
                // if(sd[1]){
                //     dept_ack_id.push(sd[1]);
                // }
                let house = sd[0];
                let ack_id = $(this).data('ack');

                $.ajax({
                    type: "get",
                    async: false,
                    url: APP_URL + "/delete-from-temp/",
                    data: {
                        ack_id:ack_id,
                        house_id:house,
                    },
                    success: function (data) {
                        console.log(data);
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            });

            if (dept_ack_id.length != 0) {

                // $.ajax({
                //     type: 'GET',
                //     url: '/house-data-remove',
                //     data: {dept_ack_id: dept_ack_id},
                //     success: function (msg) {
                //             for( var i =dataArray.length - 1; i>=0; i--){
                //                 for( var j=0; j<house_id.length; j++){
                //                     if(dataArray[i] === house_id[j]){
                //                         dataArray.splice(i, 1);
                //                     }
                //                 }
                //             }
                //                 $('td input:checked').closest('tr').remove();
                //
                //         }
                //
                // });


            } else {

                for (var i = dataArray.length - 1; i >= 0; i--) {
                    for (var j = 0; j < house_id.length; j++) {
                        // alert(house_id[j]);
                        if (dataArray[i] === house_id[j]) {
                            dataArray.splice(i, 1);
                        }
                    }
                }

                $('td input:checked').closest('tr').remove();
            }
        });



        // START workflow
        //$('#advMainTable tbody').on('click', '.workflowBtn', function () {
        $(document).on('click', '#advMainTable tbody .workflowBtn', function () {
            var data_row = $('#advMainTable').DataTable().row($(this).parents('tr')).data();
            //console.log(data_row.dept_ack_id);
            var row_id = data_row.dept_ack_id;
            //console.log(data_row);
            getFlow(row_id, data_row.prefix);
        });
        function getFlow(row_id, prefix='') {
            let myModal = $('#workflowM');
            //console.log(myModal);
            $('#application_id_flow').val(row_id);
            $(document).find('input#prefix').val(prefix);

            $('#t_name').val('dept_acknowledgement');
            $('#c_name').val('dept_ack_id');
            $.ajax({
                type: 'GET',
                url: '/get-approval-list',
                success: function (msg) {
                    $("#flow_id").html(msg.options);
                }
            });
            myModal.modal({show: true});
            return false;
        }
        function getWorkflowAssignDropDownList(targetToshow) {
            $.ajax({
                type: 'GET',
                url: '/get-approval-list',
                success: function (msg) {
                    $(targetToshow).html(msg.options);
                }
            });
        }

        $(document).on('click', '#advMainTable tbody .approveBtn', function () {
            let data_row = $('#advMainTable').DataTable().row($(this).parents('tr')).data();
            let row_id = data_row.dept_ack_id;
            goFlow(row_id);
        });

        function goFlow(row_id) {
            let myModal = $('#status-show');
            let tmp = null;
            let t_name ='dept_acknowledgement';
            let c_name ='dept_ack_id';

            $.ajax({
                async: false,
                type: 'GET',
                url: '/get-workflow-id',
                data: {row_id: row_id, t_name: t_name, c_name: c_name},
                success: function (msg) {
                    $("#workflow_id").val(msg);
                    tmp = msg;
                }
            });
            $("#object_id").val(row_id);
            $("#get_url").val(window.location.pathname.slice(1)+'?dept_ack_id='+row_id+'&pop=true');
            $.ajax({
                type: 'GET',
                url: '/approval',
                data: {workflowId: tmp, objectid: 'acknowledge'+row_id},
                success: function (msg) {
                    let wrkprc = msg.workflowProcess;
                    if (typeof wrkprc === 'undefined' || wrkprc === null || wrkprc.length === 0) {
                        $('#current_status').hide();
                    } else {
                        $('#current_status').show();
                        $("#step_name").text(msg.workflowProcess[0].workflow_step.workflow);
                        $("#step_approve_by").text('By ' + msg.workflowProcess[0].user.emp_name);
                        $("#step_time").text(msg.workflowProcess[0].insert_date);
                        $("#step_approve_desig").text(msg.workflowProcess[0].user.designation);
                        $("#step_note").text(msg.workflowProcess[0].note);
                    }

                    let steps = "";
                    $('.step-progressbar').html(steps);
                    $.each(msg.progressBarData, function (j) {
                        steps += "<li data-step=" + msg.progressBarData[j].process_step + " class='step-progressbar__item'>" + msg.progressBarData[j].forward_title + "</li>"
                    });
                    $('.step-progressbar').html(steps);

                    $('#prefix').val('acknowledge');

                    let content = "";
                    $.each(msg.workflowProcess, function (i) {
                        let note = msg.workflowProcess[i].note;
                        if (note == null) {
                            note = '';
                        }
                        content += "<div class='row d-flex justify-content-between px-1'>" +
                            "<div class='hel'>" +
                            "<span class='ml-1 font-medium'>" +
                            "<h5 class='text-uppercase'>" + msg.workflowProcess[i].workflow_step.forward_title + "</h5>" +
                            "</span>" +
                            "<span>By " + msg.workflowProcess[i].user.emp_name + "</span>" +
                            "</div>" +
                            "<div class='hel'>" +
                            "<span class='btn btn-secondary btn-sm mt-1' style='border-radius: 50px'>" + msg.workflowProcess[i].insert_date + "</span>" +
                            "<br>" +
                            "<span style='margin-left: .3rem'>" + msg.workflowProcess[i].user.designation + "</span>" +
                            "</div>" +
                            "</div>" +
                            "<hr>" +
                            "<span class='m-b-15 d-block border p-1' style='border-radius: 5px'>" + note + "" +
                            "</span><hr>";//msg.workflowProcess[i].insert_date;
                    });

                    $('#content_bdy').html(content);

                    if (msg.current_step && msg.current_step.process_step) {
                        $('.step-progressbar li').each(function (i) {

                            if ($(this).data('step') > msg.current_step.process_step) {
                                $(this).addClass('step-progressbar__item step-progressbar__item--active');
                            } else {
                                $(this).addClass('step-progressbar__item step-progressbar__item--complete');
                            }
                        })
                    } else {
                        $('.step-progressbar li').addClass('step-progressbar__item step-progressbar__item--active');
                    }

                    $("#status_id").html(msg.options);
                    $("#status_id option:last").attr("selected", "selected");

                    if ($.isEmptyObject(msg.next_step)) {

                        // $(".no-permission").css("display", "block");
                        if(msg.is_approved)
                        {
                            $(".no-permission").css("display", "block");
                            $(document).find('#hide_portion').hide();
                        }
                        //else if (JSON.stringify(userRoles).indexOf(msg.current_step.user_role) > -1)
                        else if (JSON.parse(userRoles).includes(msg.current_step.user_role))
                        {
                            // $(document).find('#hide_div, #hide-form-btn').hide();
                            $("#status_id option:selected").removeAttr("selected");
                            $(document).find("#approve_btn").show();
                        }
                        else
                        {
                            $(".no-permission").css("display", "block");
                            $(document).find('#hide_portion').hide();
                        }
                    } else if (JSON.parse(userRoles).includes(msg.current_step.user_role)) {
                        $(document).find('.no-permission').hide();
                        $(document).find('#hide_portion').show();
                    } else {
                        $(".no-permission").css("display", "block");
                        $(document).find('#hide_portion').hide();
                    }
                    $(document).find('#workflow_form').append('<input type="hidden" id="workflow" name="workflow" value="{{\App\Enums\WorkflowIntroduce::deptAckWorkflow}}"><input type="hidden" id="reference_table" name="reference_table" value="{{\App\Enums\WorkflowIntroduce::deptAckWorkflowObjectTable}}"><input type="hidden" id="referance_key" name="referance_key" value="{{\App\Enums\WorkflowIntroduce::deptAckWorkflowObjectKey}}">');
                }
            });
            myModal.modal({show: true});
            return false;
        }

        $("#workflow_assign_form").attr('action', '{{ route('get-approval-post') }}');
        $("#workflow_form").attr('action', '{{ route('approval-post') }}');
        // END workflow
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
@endsection


