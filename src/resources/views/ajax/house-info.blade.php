@if($building->no_of_house > 0)
    @for ($houseIndex = 0; $houseIndex < $building->no_of_house; $houseIndex++)
        <div class="row">
            <div class="col">House Size</div>
            <div class="col">House Name</div>
        </div>
    @endfor
@endif