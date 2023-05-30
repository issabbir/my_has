<form id="bulk-house-form" method="POST" action="{{ route('bulk-house.store') }}">
    {{ csrf_field() }}
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="row mt-1">
                <div class="col-md-3">
                    <label class="required">Residential Area</label>
                    <select class="custom-select select2" name="colony_id" id="colony_id" required>
                        <option value="">--Please Select--</option>
                        @foreach($colonies as $colony)
                            <option value="{{ $colony->colony_id }}">{{ $colony->colony_name }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('colony_id') }}</span>
                </div>
                <div class="col-md-2" id="building-list"></div>
                <div class="col-md-2" id="house-type"></div>
                <div class="col-md-2" id="no-of-floors"></div>
                <div class="col-md-2" id="no-of-houses"></div>
            </div>
            <div class="row mt-1">
                <div class="col-md-3">
                    <label>Flat Name Start With</label>
                    <input type="text" placeholder="House Name Start With" name="house_name_start_with" maxlength="3" class="form-control" id="house_name_start_with">
                    <span class="text-danger">{{ $errors->first('house_name_start_with') }}</span>
                </div>
                <div class="col-md-3">
                    <label class="required">Flat Number Start From</label>
                    <input type="number" placeholder="House Number Start From" name="house_number_start_from" maxlength="3" class="form-control" id="house_number_start_from" required>
                    <span class="text-danger">{{ $errors->first('house_number_start_from') }}</span>
                </div>
                <div class="col-md-3">
                    <label class="required">Flat Size</label>
                    <input type="text" placeholder="House Size"
                           name="house_size" class="form-control" required id="house_size">
                    <span class="text-danger">{{ $errors->first('house_size') }}</span>
                </div>
                <div class="col-md-3">
                    <label>Water Tap</label>
                    <input type="number" placeholder="Water Tap"
                           name="water_tap" maxlength="3" class="form-control" id="water_tap">
                    <span class="text-danger">{{ $errors->first('water_tap') }}</span>
                </div>
                <div class="col-md-3 mt-1">
                    <label class="required">Gas Burner</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="double_gas_yn" required id="gas_burner_single"  value="{{\App\Enums\YesNoFlag::NO}}" checked>
                            <label class="form-check-label" for="gas_burner_single">Single</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="double_gas_yn" required id="gas_burner_double"  value="{{\App\Enums\YesNoFlag::YES}}">
                            <label class="form-check-label" for="gas_burner_double">Double</label>
                        </div>
                        <span class="text-danger">{{ $errors->first('double_gas_yn') }}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col mt-2">
                    <div class="d-flex justify-content-start">
                        <button type="submit" class="btn btn btn-dark shadow mb-1 btn-secondary">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
