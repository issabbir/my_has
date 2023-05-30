@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
    <style>
        .row {
            cursor: pointer;
        }

        .displayNone {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            {{--<div class="card bg-transparent border">
                <div class="card-content">
                    <div class="card-body pt-1">
                        hello

                    </div>
                </div>
            </div>--}}

            <div class="card"><!----><!---->
                <div class="card-body"><h4 class="card-title">Residential Area Assign</h4><!---->
                    <hr>

                    <form id="colony-register" method="POST"

                          @if(isset($data->user_wise_colony_id))
                          action="{{ route('user-wise-area.update', ['id' => $data->user_wise_colony_id]) }}">
                        <input name="_method" type="hidden" value="PUT">

                        @else
                            action="{{route('user-wise-area.store')}}">
                        @endif

                        {{ csrf_field() }}
                        <div class="row justify-content-center">
                            <div class="col-md-11">
                                {{--                                @dd($data);--}}
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="required">User Role</label>
                                        <select required class="custom-select select2" name="role_id" id="role_id">
                                            <option value="">---Select One---</option>
                                            @foreach($role as $item)
                                                <option
                                                    value="{{$item->role_id}}" {{isset($data->role_id) && $data->role_id == $item->role_id ? 'selected' : ''}}
                                                >{{$item->role_name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">{{ $errors->first('role_id') }}</span>
                                    </div>
                                    {{--                                    @dd($data);--}}
                                    <div class="col-md-6">
                                        <label class="required">Employee</label>
                                        <select required class="custom-select select2" name="emp_id" id="emp_id">
                                            {{--                                            <option value="">---Select One---</option>--}}
                                            @if(isset($data))
                                               @foreach($roleUser as $list)
                                                    <option
                                                        value="{{$list->emp_id}}" {{isset($data->emp_id) && $data->emp_id == $list->emp_id ? 'selected' : ''}}
                                                    >{{$list->emp_code.'-'.$list->emp_name.' -('.$list->department_name.')'}}</option>
                                                @endforeach


                                                  @endif
                                        </select>
                                        <span class="text-danger">{{ $errors->first('emp_id') }}</span>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="required">Residential Area</label>
                                        <select class="custom-select select2" name="colonyId" id="colony_id">
                                            <option value="">---Select One---</option>
                                            @foreach($colony as $item)
                                                <option
                                                    value="{{$item->colony_id}}" {{isset($data->colony_id) && $data->colony_id == $item->colony_id ? 'selected' : ''}}>{{$item->colony_name}} </option>
                                            @endforeach

                                        </select>
                                        <span class="text-danger">{{ $errors->first('emp_id') }}</span>
                                    </div>
                                    @if(isset($data))
                                        <div class="col-md-8">
                                            <label class="required">Remarks</label>
                                            <textarea class="form-control" name="remarks">{{$data->remarks}}</textarea>
                                            <span class="text-danger">{{ $errors->first('emp_id') }}</span>
                                        </div>
                                    @endif
                                    @if(!isset($data))
                                        <div class="col-md-1 mt-2">
                                            <button type="button"
                                                    class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary add_area">
                                                Add
                                            </button>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                        @if(!isset($data))
                            <div class="col-sm-12 mt-1">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-bordered"
                                           id="table-operator">
                                        <thead>
                                        <tr>
                                            <th role="columnheader" scope="col"
                                                aria-colindex="1" class="text-center" width="5%">Action
                                            </th>
                                            <th role="columnheader" scope="col"
                                                aria-colindex="2" class="text-center" width="50%">Residential Area

                                            </th>
                                            <th role="columnheader" scope="col"
                                                aria-colindex="2" class="text-center" width="40%">Remarks

                                            </th>

                                        </tr>
                                        </thead>


                                        <tbody role="rowgroup" id="comp_body">

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-start">

                                <button type="button"
                                        class="btn btn-primary mb-1 delete-row">
                                    Delete
                                </button>
                            </div>
                        @endif

                        <div class="row">
{{--                            <input type="hidden" name="colony_id" id="colony_id">--}}
                            <div class="d-flex justify-content-end col">
                                @if(isset($data->user_wise_colony_id))
                                    <button type="submit" id="submit"
                                            class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                        Update
                                    </button>
                                @else
                                    <button type="submit" id="submit"
                                            class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                        Submit
                                    </button>
                                @endif&nbsp;
                                <button type="button" id="reset" class="btn btn btn-outline shadow mb-1 btn-secondary">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card"><!----><!---->
                <div class="card-body"><h4 class="card-title"> Residential Area Search</h4><!---->
                    <hr/>

                    <div class="row">
                        <div class="col-md-3">
                            <label class="">User Role</label>
                            <select class="custom-select select2" name="role_id" id="role_idd">
                                <option value="">---Select One---</option>
                                @foreach($approve_role as $item)
                                    <option value="{{$item->role_id}}">{{$item->role_name}}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{ $errors->first('role_id') }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="">Employee</label>
                            <select class="custom-select select2" name="emp_id" id="emp_idd">
                                <option value="">---Select One---</option>

                            </select>
                            <span class="text-danger">{{ $errors->first('emp_id') }}</span>
                        </div>
                        <div class="col-md-3 mt-2">
                            <button type="submit" class=" btn btn-primary mb-2 mr-1" id="res_area_submit">Search
                            </button>

                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table width="100%" class="table table-sm mdl-data-table dataTable"
                               id="residential_area">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Residential Area</th>
                                <th>Remarks</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="exampleid">

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
        $(document).ready(function () {

            $('#res_area_submit').on('click', function (e) {
                let role = $('#role_idd').val();
                let employee = $('#emp_idd').val();

                if (role != '' && employee != '') {
                    $.ajax({
                        // type:"post",
                        url: APP_URL + "/load-role-to-user-search/" + role + '/' + employee,

                        success: function (data) {
                            let content = "";
                            global_content = data;
                            if (data != '') {
                                $.each(data, function (key, value) {
                                    let id = value.user_wise_colony_id;
                                    let url = '/user-wise-area-edit' + '/' + id

                                    content += "<tr>\
                                            <td>" + value.sl + "</td>\
                                            <td>" + value.colony_name + "</td>\
                                            <td>" + value.remarks + "</td>\
                                            <td ><a href='" + url + "' id='editUser' data-id=" + value.user_wise_colony_id + "><i class='bx bxs-edit-alt'></i></a></td>\
                                            </tr>";
                                })
                                $('#exampleid').html(content);
                            } else {
                                $('#exampleid').val('');
                            }


                        }
                    })
                }
            })

            function load_role_to_user(role_id, selected_dist = '') {
                //console.log(window.baseUrl);

                if (role_id !== undefined && role_id) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL + "/load-role-to-user/" + role_id,
                        data: {'selected_dist': selected_dist},
                        success: function (data) {
                            //console.log(data);
                            $('#emp_id').html(data);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });

                } else {
                    $('#emp_id').html('');

                }
            }


            $('#role_id').on("change", function () {
                let role_id = $('#role_id').val();
                let selected_dist = '';
                load_role_to_user(role_id, selected_dist);
            });

            function load_role_to_userdup(role_idd, selected_dist = '') {
                //console.log(window.baseUrl);

                if (role_idd !== undefined && role_idd) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL + "/load-role-to-approve-user/" + role_idd,
                        data: {'selected_dist': selected_dist},
                        success: function (data) {
                            //console.log(data);
                            $('#emp_idd').html(data);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });

                } else {
                    $('#emp_idd').html('');

                }
            }


            $('#role_idd').on("change", function () {
                let role_idd = $('#role_idd').val();
                let selected_dist = '';
                load_role_to_userdup(role_idd, selected_dist);
            });
            oTable.draw();
        });
        var dataArray = new Array();
        $(".add_area").click(function () {
            let colony_id = $("#colony_id option:selected").val();
            let colony = $("#colony_id option:selected").text();


            if (colony_id != '') {

                if ($.inArray(colony_id, dataArray) > -1) {
                    alert('This Residental Area Already Added.')
                } else {
                    let markup = "<tr role='row'>" +
                        "<td aria-colindex='1' role='cell' class='text-center'>" +
                        "<input type='checkbox' name='record' value='" + "" + "+" + "" + "'>" +
                        "<input type='hidden' name='tab_colony_id[]' value='" + colony_id + "'>" +
                        "</td><td aria-colindex='2' role='cell'>" + colony + "</td><td aria-colindex='3' role='cell'><input type='text' name='remarks[]' class='form-control'></td></tr>";

                    $("#colony_id").val('').trigger('change');
                    $("#table-operator tbody").append(markup);
                    dataArray.push(colony_id);
                }
            } else {
                alert('Please Select Residential Area')
            }

        });
        $(".delete-row").click(function () {
            let arr_stuff = [];
            let colony_id = [];
            let emp_id = [];


            $(':checkbox:checked').each(function (i) {
                arr_stuff[i] = $(this).val();
                let sd = arr_stuff[i].split('+');
                colony_id.push(sd[0]);


            });

            if (emp_id.length != 0) {

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
                    for (var j = 0; j < colony_id.length; j++) {
                        // alert(house_id[j]);
                        if (dataArray[i] === colony_id[j]) {
                            dataArray.splice(i, 1);
                        }
                    }
                }

                $('td input:checked').closest('tr').remove();


            }

        });


    </script>

@endsection


