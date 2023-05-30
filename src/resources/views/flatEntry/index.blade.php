@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- Table Start -->
                <div class="card-body">
                    <h4 class="card-title">Flat Name Entry</h4>
                    <hr>
                    @if(Session::has('message'))
                        <div class="alert {{Session::get('m-class') ? Session::get('m-class') : 'alert-danger'}} show"
                             role="alert">
                            {{ Session::get('message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <form
                        @if(isset($flatName->flat_name_id)) action="{{route('flat-name-entry.flat-name-entry-update',[$flatName->flat_name_id])}}"
                        @else action="{{route('flat-name-entry.flat-name-entry-post')}}" @endif method="post">
                        @csrf
                        @if (isset($flatName->flat_name_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="required">Flat Name</label>
                                    <input type="text" id="flat_name" name="flat_name"
                                           class="form-control" required
                                           value="{{old('flat_name',isset($flatName->flat_name) ? $flatName->flat_name : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="description" class="">Description</label>
                                        <textarea rows="2" wrap="soft"
                                                  name="description"
                                                  class="form-control"
                                                  id="description">{{old('description',isset($flatName->description) ? $flatName->description : '')}}</textarea>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary mb-1 mr-1">Submit</button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
            @include('flatEntry.flat_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function flatNameEntryList() {
            $('#flat-name-entry-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: APP_URL + '/flat-name-entry-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'flat_name', name: 'flat_name', searchable: true},
                    {data: 'description', name: 'description', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(document).ready(function () {
            flatNameEntryList();
        });
    </script>

@endsection


