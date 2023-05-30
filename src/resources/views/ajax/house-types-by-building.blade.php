@if($building->houseType)
    <label class="required">House Type</label>
    <input type="text" placeholder="House Type" name="house_type" class="form-control" readonly value="{{$building->houseType->house_type}}" id="house_type" />
    <input type="hidden" name="house_type_id" class="form-control" readonly value="{{$building->houseType->house_type_id}}" id="house_type_id" />
@endif