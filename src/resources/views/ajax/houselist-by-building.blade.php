
    <label class="required" for="flat_name_id">
		{{$dormitoryYN == 'N'?'Flat Number':'Dormitory Seat No'}}	
	</label>
    <select class="custom-select select2" name="house_id" id="house_id" required>
        <option value="">Please select any one</option>
        @if($houseList)
            @foreach($houseList as $house)
                <option value="{{$house->house_id}}">{{$house->house_name}} {{$house->house_code? '(Floor No: '.$house->floor_number.', ':''}}{{$house->house_code? 'Seat No: '.$house->house_code.')':''}}</option>
            @endforeach
        @endif
    </select>
    <span class="text-danger">{{ $errors->first('house_id') }}</span>

