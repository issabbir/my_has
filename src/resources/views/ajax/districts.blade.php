@if($districts)
    <option value="">--Please Select--</option>
    @foreach($districts as $district)
        <option value="{{$district->geo_district_id}}">{{$district->geo_district_name}}</option>
    @endforeach
@endif