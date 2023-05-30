@if($thanas)
    <option value="">--Please Select--</option>
    @foreach($thanas as $thana)
        <option value="{{$thana->geo_thana_id}}">{{$thana->geo_thana_name}}</option>
    @endforeach
@endif