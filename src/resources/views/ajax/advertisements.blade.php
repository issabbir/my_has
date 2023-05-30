@if($advertisements)
    <option value="">--Please Select--</option>
    @foreach($advertisements as $advertisement)
        <option value="{{$advertisement->adv_id}}">{{$advertisement->adv_number}}</option>
    @endforeach
@else
    <option value="">--Please Select--</option>
@endif
