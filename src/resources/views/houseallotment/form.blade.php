<form id="house-allotment-search-form" method="GET">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="row mt-1">
                <div class="col-md-3">
                    <label class="required">Advertisement</label>
                    <select class="custom-select select2" name="advertisement_id" id="advertisement_id" required>
                        <option value="">--Please Select--</option>
                        @foreach($data['advertisements'] as $advertisement)
                            <option value="{{ $advertisement->adv_id }}"
                                    @if($data['advertisement_id'] == $advertisement->adv_id)
                                        selected
                                    @endif
                            > {{ $advertisement->adv_number  }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('advertisement_id') }}</span>
                </div>
                <div class="col mt-2">
                    <div class="d-flex justify-content-start">
                        <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
