@if($buildings)
    <label class="required">Building</label>
    <select class="custom-select select2" name="building_id" id="building_id" required>
        <option value="">--Please Select--</option>
        @foreach($buildings as $building)
            <option value="{{$building->building_id}}">{{$building->building_name}}</option>
        @endforeach
    </select>
    <span class="text-danger">{{ $errors->first('building_id') }}</span>
@endif
