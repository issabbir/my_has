@if($building->no_of_floor)
    <label class="required">Total Floors</label>
    <input type="text" placeholder="Total Floors" name="no_of_floor" class="form-control" readonly value="{{$building->no_of_floor}}" id="no_of_floor">
    <span class="text-danger">{{ $errors->first('no_of_floor') }}</span>
@endif