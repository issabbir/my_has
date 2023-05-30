@if($floors)
    <option value="">--Please Select--</option>
    @foreach($floors as $floor)
        <option value="{{$floor}}">{{$floor}}</option>
    @endforeach
@endif