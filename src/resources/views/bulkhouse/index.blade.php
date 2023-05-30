@extends('layouts.default')

@section('title')
    Bulk House Creation with Default Values
@endsection

@section('header-style')
    <!--Load custom style link or css-->
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body"><h4 class="card-title">Bulk House Creation with Default Values</h4>
                <hr>
                    @include('bulkhouse.form')
                </div>
            </div>

            <div class="card">
            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">
        function findBuildings()
        {
            $('#colony_id').on('change', function() {
                let colonyId = $(this).val();

                if(colonyId !== undefined && colonyId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL+"/ajax/buildings-by-colony/" + colonyId,
                        success: function (data) {
                            $('#building-list').html(data);
                            $('#building_id').addClass('select2',true);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                } else {
                    $('#building-list').html('');
                }

                $('#house-type').html('');
                $('#no-of-floors').html('');
                $('#no-of-houses').html('');
                $('#house-form').html('');
            });
        }

        function findHouseTypes()
        {
            $(document).on('change', '#building_id', function() {
                let buildingId = $(this).val();

                if(buildingId !== undefined && buildingId) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL+"/ajax/house-types-by-building/" + buildingId,
                        success: function (data) {
                            $('#house-type').html(data.houseTypesHtml);
                            $('#no-of-floors').html(data.floorsHtml);
                            $('#no-of-houses').html(data.housesHtml);
                            $('#house-form').html(data.houseFormHtml);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                } else {
                    $('#house-type').html('');
                    $('#no-of-floors').html('');
                    $('#no-of-houses').html('');
                    $('#house-form').html('');
                }
            });
        }

        $(document).ready(function () {
            findBuildings();
            findHouseTypes();
        });
    </script>
@endsection
