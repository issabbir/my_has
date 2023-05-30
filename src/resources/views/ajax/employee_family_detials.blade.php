<div class="table-responsive">
    <table class="table table-sm datatable mdl-data-table dataTable text-uppercase font-weight-bold text-center">
        <thead>
        <tr class="text-uppercase font-weight-bold ">
            <th>Name</th>
            <th>Name (Bangla)</th>
            <th>Mobile</th>
            <th>Date of Birth</th>
            <th>Age</th>
            <th>Relation</th>
        </tr>
        </thead>
        <body>
            @if(isset($familyDetails))
                @foreach($familyDetails as $dfamilyDetails)
                    <tr class="text-center">
                        <td>{{$dfamilyDetails->emp_member_name}}</td>
                        <td>{{$dfamilyDetails->emp_member_name_bng}}</td>
                        <td>{{$dfamilyDetails->emp_member_mobile}}</td>
                        <td>{{ date('d-m-Y',strtotime($dfamilyDetails->emp_member_dob))}}</td>
                        <td>{{date_diff(date_create(date('d-m-Y',strtotime($dfamilyDetails->emp_member_dob))), date_create('today'))->y}}</td>
                        <td>{{$dfamilyDetails->relation_type}}</td>
                    </tr>
                @endforeach
            @endif
        </body>

    </table>
</div>
