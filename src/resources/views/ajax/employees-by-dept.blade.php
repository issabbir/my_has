@if($employees)
    <option value="">--Please Select--</option>
    @foreach($employees as $employee)
        <option value="{{$employee->emp_id}}">{{$employee->emp_code}} - {{$employee->emp_name}}</option>
    @endforeach
@else
    <option value="">--Please Select--</option>
@endif
