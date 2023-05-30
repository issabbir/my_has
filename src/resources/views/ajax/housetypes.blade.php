@if($houseTypes)
    <option value="">--Please Select--</option>
    @foreach($houseTypes as $houseType)
        @if(Illuminate\Support\Facades\Auth::user()->hasPermission('HAS_HOD_CAN_ADVERTISE_A_D'))
            @php
                $alowedHouseTypeArray = (array)json_decode(env('HOD_ALLOWED_HOUSE_TYPE'));
            @endphp
            {{--@if(1!=App\Helpers\HelperClass::custom_array_search($houseType->house_type, $alowedHouseTypeArray, 'bool'))
                @continue;
            @endif--}}
        @endif
        <option value="{{$houseType->house_type_id}}">{{$houseType->house_type}}</option>
    @endforeach
@else
    <option value="">--Please Select--</option>
@endif
