@if($building->no_of_house)
    <label class="required">Total Houses</label>
    <input type="text" placeholder="Total Houses" name="no_of_house" class="form-control" readonly value="{{$building->no_of_house}}" id="no_of_house">
    <span class="text-danger">{{ $errors->first('no_of_house') }}</span>
@endif