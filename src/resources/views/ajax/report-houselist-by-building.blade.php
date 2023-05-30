@if($houseList)
    <option value="">--Please Select--</option>
    @foreach($houseList as $house)
        <option value="{{$house->house_id}}">{{$house->house_name}} {{$house->house_code? '(Floor No: '.$house->floor_number.', ':''}}{{$house->house_code? 'Seat No: '.$house->house_code.')':''}}</option>
    @endforeach
@else
    <option value="">--Please Select--</option>
@endif
