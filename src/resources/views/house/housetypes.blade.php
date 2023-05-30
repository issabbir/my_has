@if($housetypes)
    <option value="">--Please Select--</option>
    @foreach($housetypes as $houseType)
        <option value="{{$houseType->house_type_id}}" {{isset($houseType->house_type_id) ? ((count($housetypes)==1)? 'selected' : ''): ''}}>{{$houseType->house_type}}</option>
    @endforeach
@endif
