@if($house_types)
    <option value="">--Please Select--</option>
    @foreach($house_types as $house_type)
        <option value="{{$house_type->house_type_id}}">{{$house_type->house_type}}</option>
    @endforeach
@else
    <option value="">--Please Select--</option>
@endif
