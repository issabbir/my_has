@if($acknowledgementRow)
    <option value="">--Please Select--</option>
    @foreach($acknowledgementRow as $ack)
        <option value="{{$ack->dept_ack_id}}">{{$ack->dept_ack_no}}</option>
    @endforeach
@else
    <option value="">--Please Select--</option>
@endif
