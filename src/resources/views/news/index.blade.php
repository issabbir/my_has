<!---->
<div id="" class="modal-body"  tabindex="-1">
    <div class="row">
        <div class="col-md-12">
            <h5 class="mt-0"><i class="bx bx-news">
                </i> {{$data->title_bn}}</h5>
            <hr class="mt-0">
            <p class="mb-2 mt-0">{{$data->description_bn}}</p>

            @if($data->attachment_filename)
            <div>
                <h6 class="mt-0"><i class="bx bx-paperclip"></i> Attachment</h6>
                <hr class="mt-0">
                <a href="{{route('news-download',[$data->news_id])}}" target="_blank">
                    <i class="bx bx-file"></i>
                    {{$data->attachment_filename}}
                </a>
            </div>
            @endif

        </div>
    </div> <!---->
</div>
<footer id="" class="modal-footer"><!---->
    <button type="button" class="btn btn-primary new_close">Close</button>
</footer>

<script>
    $(".new_close").click(function() {
        $("#dynamicNewsModalContent").html("");
        $('#dynamicNewsModal').modal('hide');

    });
</script>

