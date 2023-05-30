@if($houseDetails)
            {{--<div class="col">House Size</div>
            <div class="col">House Name</div>--}}


            <div class="col-md-4">
                <label>Road no.</label>
                <input type="text" readonly value="{{$houseDetails->building_road_no}}" name="road_no" placeholder="Road no." class="form-control" id="road_no" />
            </div>
            <div class="col-md-4">
                <label>Building No.</label>
                <input type="text" readonly value="{{$houseDetails->building_no}}" placeholder="Building No." name="building_no" class="form-control" id="building_no" />
            </div>

            <div class="col-md-4">
                <label class="required">Floor</label>
                <input type="text" readonly value="{{$houseDetails->floor_number}}" name="floor" id="floor" class="form-control"/>
            </div>

@endif
