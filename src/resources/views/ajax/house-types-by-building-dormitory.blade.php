<label class="required" for="house_type_id">House/Flat Type</label>
<select class="custom-select select2" name="house_type_id" id="house_type_id" required>
    <option value="">Please select any one</option>
    @if($houseTypes)
        @foreach($houseTypes as $houseType)
            <option value="{{$houseType->house_type_id}}">{{$houseType->house_type}} </option>
        @endforeach
    @endif
</select>
<span class="text-danger">{{ $errors->first('house_id') }}</span>
