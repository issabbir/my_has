<form id="house-form" method="POST"
      @if($data['house']->house_id)
        action="{{ route('house.update', ['id' => $data['house']->house_id]) }}">
        <input name="_method" type="hidden" value="PUT">
{{--      @else--}}
{{--        action="{{ route('house.store') }}">--}}
      @endif

    {{ csrf_field() }}
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="row mt-1">
                <div class="col-md-3">
                    <label class="required">Building Name</label>
                    <select class="custom-select select2" name="building_id" id="building_id" required>
                        <option value="">--Please Select--</option>
                        @foreach($data['buildings'] as $building)
                            <option value="{{ $building->building_id  }}"
                                    @if($building->building_id == $data['house']->building_id)
                                    selected
                                @endif
                            > {{ $building->building_name  }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('building_id') }}</span>
                </div>




                <div class="col-md-3">
                    <label class="required">Dormitory (Yes/No) </label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="dormitory_yn"
                                   id="dormitory_y"  value="{{\App\Enums\YesNoFlag::YES}}"
                                   @if(\App\Enums\YesNoFlag::YES == $data['house']->dormitory_yn)
                                            checked
                                @endif
                            >
                            <label class="form-check-label" for="dormitory_y">Yes</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="dormitory_yn"
                                   id="dormitory_n"  value="{{\App\Enums\YesNoFlag::NO}}"
                                   @if(\App\Enums\YesNoFlag::YES != $data['house']->dormitory_yn)
                                   checked
                                @endif
                            >
                            <label class="form-check-label" for="dormitory_n">No</label>
                        </div>
                        <span class="text-danger">{{ $errors->first('dormitory_yn') }}</span>
                    </div>
                </div>



                <div class="col-md-3">
                    <label for="house_code">Flat Number </label>
                    <input type="text" placeholder="Flat Number"
                           name="house_code" class="form-control"  value="{{$data['house']->house_code}}"
                           id="house_code">
                    <span class="text-danger">{{ $errors->first('house_code') }}</span>
                </div>

{{--                old --}}
{{--                <div class="col-md-3">--}}
{{--                    <label class="required">Flat Name</label>--}}
{{--                    <input type="text" placeholder="Flat Name"--}}
{{--                           name="house_name" class="form-control" required value="{{$data['house']->house_name}}"--}}
{{--                           id="house_name">--}}
{{--                    <span class="text-danger">{{ $errors->first('house_name') }}</span>--}}
{{--                </div>--}}



                <div class="col-md-3">
                    <label  for="room_number">Room Number </label>
                    <input type="text" placeholder="Room Number"
                           name="room_number" class="form-control"   value="{{$data['house']->dormitory_room_no}}"
                           id="room_number">
                    <span class="text-danger">{{ $errors->first('house_code') }}</span>
                </div>







{{--comment--}}
{{--                <div class="col-md-3">--}}
{{--                    <label>Dormitory Seat Quantity</label>--}}
{{--                    <input type="number" maxlength="3" max="15" placeholder="Seat Quantity"--}}
{{--                           name="dormitory_total_seat" class="form-control" value="{{$data['house']->dormitory_total_seat}}"--}}
{{--                           @if(\App\Enums\YesNoFlag::YES != $data['house']->dormitory_yn)--}}
{{--                           disabled--}}
{{--                           @endif--}}
{{--                           id="dormitory_total_seat">--}}
{{--                    <span class="text-danger">{{ $errors->first('dormitory_total_seat') }}</span>--}}
{{--                </div>--}}

            </div>

            <div class="row mt-1">

                <div class="col-md-3">
                    <label class="required">Flat Name</label>
                    <select class="custom-select select2" name="house_name" id="house_name" required>
                        <option value="">--Please Select--</option>
                        @foreach($flatList as $flat)
                            <option value="{{ $flat->flat_name_id  }}"
                                    @if($flat->flat_name == $data['house']->house_name)
                                    selected
                                @endif
                            > {{ $flat->flat_name }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('house_name') }}</span>
                </div>

                <div class="col-md-3">
                    <label class="required">Residential Area</label>
                    <input type="text" placeholder="Residential Area"
                           name="colony_id" class="form-control" readonly
                           @if($data['house']->buildinglist)
                           value="{{$data['house']->buildinglist->colony->colony_name}}"
                           @else
                           value=""
                           @endif
                           id="colony_id">
                </div>

                <div class="col-md-3">
                    <label class="required">Floor</label>
                    <select class="custom-select" name="floor_number" id="floor_number" required>
                        <option value="">--Please Select--</option>
                        @foreach($data['floors'] as $floor)
                            <option value="{{ $floor  }}"
                                    @if($floor == $data['house']->floor_number)
                                    selected
                                    @endif
                            > {{ $floor  }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('floor_number') }}</span>
                </div>
                <div class="col-md-3">
                    <label class="required">Flat Type</label>
                    <select class="custom-select" name="house_type_id" id="house_type_id" required>
                        <option value="">--Please Select--</option>
                        @foreach($data['house_types'] as $houseType)
                            <option value="{{ $houseType->house_type_id  }}"
                                    @if($houseType->house_type_id == $data['house']->house_type_id)
                                    selected
                                    @endif
                            > {{ $houseType->house_type }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('house_type_id') }}</span>
                </div>



            </div>

            <div class="row mt-1">

                <div class="col-md-3">
                    <label class="required" for="house_size">Flat Size</label>
                    <input type="text" placeholder="Flat Size"
                           name="house_size" class="form-control" required value="{{$data['house']->house_size}}"
                           id="house_size">
                    <span class="text-danger">{{ $errors->first('house_size') }}</span>
                </div>
{{--                @dd($data['house']->house_size);--}}
                <div class="col-md-3">
                    <div>
                        <label class="required">Car Parking</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="parking_yn" required
                                       id="car_parking_yes" value="{{\App\Enums\YesNoFlag::YES}}"
                                       @if(\App\Enums\YesNoFlag::YES == $data['house']->parking_yn)
                                       checked
                                        @endif
                                >
                                <label class="form-check-label" for="car_parking_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="parking_yn" required
                                       id="car_parking_no"  value="{{\App\Enums\YesNoFlag::NO}}"
                                       @if(\App\Enums\YesNoFlag::YES != $data['house']->parking_yn)
                                       checked
                                        @endif
                                >
                                <label class="form-check-label" for="car_parking_no">No</label>
                            </div>
                            <span class="text-danger">{{ $errors->first('parking_yn') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label>Parking No.</label>
                    <input type="text" placeholder="Parking No."
                           name="parking_id" class="form-control" value="{{$data['house']->parking_id}}"
                           id="parking_id">
                    <span class="text-danger">{{ $errors->first('parking_id') }}</span>
                </div>
                <div class="col-md-3">
                    <label class="required">Gas Burner</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="double_gas_yn" required
                                   id="gas_burner_single"  value="{{\App\Enums\YesNoFlag::NO}}"
                                   @if(\App\Enums\YesNoFlag::YES == $data['house']->double_gas_yn)
                                   checked
                                    @endif
                            >
                            <label class="form-check-label" for="gas_burner_single">Single</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="double_gas_yn" required
                                   id="gas_burner_double"  value="{{\App\Enums\YesNoFlag::YES}}"
                                   @if(\App\Enums\YesNoFlag::YES != $data['house']->double_gas_yn)
                                   checked
                                    @endif
                            >
                            <label class="form-check-label" for="gas_burner_double">Double</label>
                        </div>
                        <span class="text-danger">{{ $errors->first('double_gas_yn') }}</span>
                    </div>
                </div>




            </div>
            <div class="row mt-1">

                <div class="col-md-3">
                    <label>Water Tap No.</label>
                    <input type="number" placeholder="Water Tap"
                           name="water_tap" maxlength="3" class="form-control" value="{{$data['house']->water_tap}}"
                           id="water_tap">
                    <span class="text-danger">{{ $errors->first('water_tap') }}</span>
                </div>

                <div class="col-md-3">
                    <label>Electric Meter</label>
                    <input type="number" placeholder="Electric Meter"
                           name="electric_meter_number" class="form-control" value="{{$data['house']->electric_meter_number}}"
                           id="electric_meter_number">
                    <span class="text-danger">{{ $errors->first('electric_meter_number') }}</span>
                </div>
                <div class="col-md-3">
                    <div>
                        <label class="required">BTCL Connection</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="btcl_connection_yn" required
                                       id="btcl_connection_yes"  value="{{\App\Enums\YesNoFlag::YES}}"
                                       @if(\App\Enums\YesNoFlag::YES == $data['house']->btcl_connection_yn)
                                       checked
                                        @endif
                                >
                                <label class="form-check-label" for="btcl_connection_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="btcl_connection_yn" required
                                       id="btcl_connection_no"  value="{{\App\Enums\YesNoFlag::NO}}"
                                       @if(\App\Enums\YesNoFlag::YES != $data['house']->btcl_connection_yn)
                                       checked
                                        @endif
                                >
                                <label class="form-check-label" for="btcl_connection_no">No</label>
                            </div>
                            <span class="text-danger">{{ $errors->first('btcl_connection_yn') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label>BTCL Number</label>
                    <input type="text" placeholder="BTCL Number"
                           name="btcl_number" class="form-control"
                           id="btcl_number" value="{{$data['house']->btcl_number}}">
                    <span class="text-danger">{{ $errors->first('btcl_number') }}</span>
                </div>



            </div>
            <div class="row mt-1">

                <div class="col-md-3">
                    <div>
                        <label class="required">Intercom</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="intercom_yn" required
                                       id="intercom_yes"  value="{{\App\Enums\YesNoFlag::YES}}"
                                       @if(\App\Enums\YesNoFlag::YES == $data['house']->intercom_yn)
                                       checked
                                    @endif
                                >
                                <label class="form-check-label" for="intercom_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="intercom_yn" required
                                       id="intercom_yn"  value="{{\App\Enums\YesNoFlag::NO}}"
                                       @if(\App\Enums\YesNoFlag::YES != $data['house']->intercom_yn)
                                       checked
                                    @endif
                                >
                                <label class="form-check-label" for="intercom_yn">No</label>
                            </div>
                            <span class="text-danger">{{ $errors->first('intercom_yn') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label>Intercom Number</label>
                    <input type="text" placeholder="Intercom Number"
                           name="intercom_no" class="form-control" value="{{$data['house']->intercom_no}}"
                           id="intercom_no">
                    <span class="text-danger">{{ $errors->first('intercom_no') }}</span>
                </div>
                <div class="col-md-3">
                    <label class="required">Reserve</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="reserve_yn" required
                                   id="reserve_yes"  value="{{\App\Enums\YesNoFlag::YES}}"
                                   @if(\App\Enums\YesNoFlag::YES == $data['house']->reserve_yn)
                                   checked
                                    @endif
                            >
                            <label class="form-check-label" for="reserve_yes">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="reserve_yn" required
                                   id="reserve_no"  value="{{\App\Enums\YesNoFlag::NO}}"
                                   @if(\App\Enums\YesNoFlag::YES != $data['house']->reserve_yn)
                                   checked
                                    @endif
                            >
                            <label class="form-check-label" for="reserve_no">No</label>
                        </div>
                        <span class="text-danger">{{ $errors->first('reserve_yn') }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label>Reserve For</label>
                    <select class="custom-select" name="designation_id" id="designation_id">
                        <option value="">--Please Select--</option>
                        @foreach($data['designations'] as $designation)
                            <option value="{{ $designation->designation_id  }}"
                                    @if($designation->designation_id  == $data['house']->reserve_for)
                                    selected
                                    @endif
                            > {{ $designation->designation  }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('designation_id') }}</span>
                </div>

            </div>

            <div class="row mt-1">

            <div class="col-md-3">
                <label class="required">Status</label>
                <select class="custom-select" name="house_status_id" id="house_status_id" required>
                    <option value="">--Please Select--</option>
                    @foreach($data['house_statuses'] as $houseStatus)
                        <option value="{{ $houseStatus->house_status_id  }}"
                                @if($houseStatus->house_status_id  == $data['house']->house_status_id)
                                selected
                            @endif
                        > {{ $houseStatus->house_status  }}</option>
                    @endforeach
                </select>
                <span class="text-danger">{{ $errors->first('house_status_id') }}</span>
            </div>

            </div>

            <div class="row">
                <div class="col mt-2">
                    <div class="d-flex justify-content-end">

                        @if($data['house']->house_id)
                            <button type="submit" class="btn btn btn-dark shadow mb-1 btn-secondary ">
                                Update
                            </button>
                        @else
                            <button type="button" onclick="validation()" class="btn btn btn-dark shadow mb-1 btn-secondary">
                                Add
                            </button>
                            <button type="reset" class="btn btn btn-outline shadow mb-1 ml-1 btn-secondary">
                                Reset
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
    function validation() {
        var building_id = $('#building_id').val();
        var house_name = $('#house_name').val();
        var floor_number = $('#floor_number').val();
        var house_type_id = $('#house_type_id').val();
        var house_size = $('#house_size').val();
        var house_status_id = $('#house_status_id').val();
        var dormitory_total_seat = $('#dormitory_total_seat').val() ? $('#dormitory_total_seat').val():'';
        var room_number = $('#room_number').val();
        var house_code = $('#house_code').val();
        // alert( 'building_id ' + building_id + ' house_name '+ house_name + ' floor_number ' + floor_number + ' house_type_id ' + house_type_id + ' house_size ' + house_size + ' house_status_id  '+
        //     house_status_id + ' dormitory_total_seat ' + dormitory_total_seat +' room_number ' + room_number + ' house_code '+  house_code );
        //return ;
        if($('#dormitory_y').is(':checked'))
        {

            var dormitory_yn = 'Y';
           // $('#dormitory_total_seat').removeAttr('disabled');

        }
        else{
            var dormitory_yn = 'N';
           // $("#dormitory_total_seat").attr('disabled', 'disabled');
        }
        var colony_id = $('#colony_id').val();
        if($('#car_parking_yes').is(':checked'))
        {
            var parking_yn = 'Y';
        }
        else{
            var parking_yn = 'N';
        }
        var parking_id = $('#parking_id').val();
        if($('#gas_burner_double').is(':checked'))
        {
            var double_gas_yn = 'Y';
        }
        else{
            var double_gas_yn = 'N';
        }
        var water_tap = $('#water_tap').val();
        var electric_meter_number = $('#electric_meter_number').val();
        if($('#btcl_connection_yes').is(':checked'))
        {
            var btcl_connection_yn = 'Y';
        }
        else{
            var btcl_connection_yn = 'N';
        }
        var btcl_number = $('#btcl_number').val();
        if($('#intercom_yes').is(':checked'))
        {
            var intercom_yn = 'Y';
        }
        else{
            var intercom_yn = 'N';
        }
        var intercom_no = $('#intercom_no').val();
        if($('#reserve_yes').is(':checked'))
        {
            var reserve_yn = 'Y';
        }
        else{
            var reserve_yn = 'N';
        }
        var designation_id = $('#designation_id').val();

        if(!building_id)
        {
            //alert('building_id');
            Swal.fire({
                title: 'Building Name Cannot Be Empty!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }
        else if(!house_name)
        {  //alert('house_name');
            Swal.fire({
                title: 'Flat Name Cannot Be Empty!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        } else if(dormitory_yn == 'Y' && !house_code)
        {  //alert('house_name');
            Swal.fire({
                title: 'Seat No Cannot Be Empty!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }
        // else if(dormitory_yn === 'Y' && !dormitory_total_seat)
        // {
        //     Swal.fire({
        //         title: 'Dormitory total seat Cannot Be Empty!',
        //         icon: 'error',
        //         confirmButtonText: 'OK'
        //     });
        //     return false;
        // }

        else if(!floor_number)
        { //alert('floor_number');
            Swal.fire({
                title: 'Floor Cannot Be Empty!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }else if(!house_type_id)
        { // alert('house_type_id');
            Swal.fire({
                title: 'Flat Type Cannot Be Empty!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }else if(!house_size)
        { // alert('house_size');
            Swal.fire({
                title: 'Flat Size Cannot Be Empty!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }else if(!house_status_id)
        { //alert//('house_status_id');
            Swal.fire({
                title: 'Status Cannot Be Empty!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return false;
        }else{
            //alert('ajax req');
            $.ajax({
                type: "POST",
                url: APP_URL+'/houses',
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    building_id: building_id,
                    house_name: house_name,
                    floor_number: floor_number,
                    house_type_id: house_type_id,
                    house_size: house_size,
                    house_status_id: house_status_id,
                    house_code: house_code,
                    designation_id: designation_id,
                    reserve_yn: reserve_yn,
                    intercom_no: intercom_no,
                    intercom_yn: intercom_yn,
                    btcl_number: btcl_number,
                    btcl_connection_yn: btcl_connection_yn,
                    electric_meter_number: electric_meter_number,
                    water_tap: water_tap,
                    double_gas_yn: double_gas_yn,
                    parking_id: parking_id,
                    parking_yn: parking_yn,
                    colony_id: colony_id,
                    dormitory_yn: dormitory_yn,
                    dormitory_total_seat: dormitory_total_seat,
                     room_number : room_number,

                },

                success: function (data) {

                    // alert(data.o_status_message);
                    if(data.o_status_code == 1)
                    { //alert('success');
                        Swal.fire({
                            title: data.o_status_message,
                            icon: 'success',
                            // confirmButtonText: 'OK'
                            showConfirmButton:false
                        });

                    }
                    else
                    { //alert('error 1');
                        Swal.fire({
                            title: data.o_status_message,
                            icon: 'error',
                            // confirmButtonText: 'OK'
                            showConfirmButton:false
                        });
                    }
                    tempTable.draw();
                    $('#houses').DataTable().ajax.reload();
                },
                error: function (data) {
                    //alert('error 2');
                    alert('error');
                }
            });
        }
    }
</script>
