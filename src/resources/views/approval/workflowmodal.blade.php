<div id="status-show" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-uppercase text-left">
                    Approval Process
                </h4>
                <button class="close" type="button" data-dismiss="modal" area-hidden="true">
                    &times;
                </button>
            </div>
            <div class="modal-body">
                <form id="workflow_form" method="post">
                    {!! csrf_field() !!}
                    <div class="row mb-1">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <ul class="step-progressbar">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row border p-1 m-1" id="hide_full" style="border-radius: 5px">
                                <div class="col-md-7">
                                    <div class="p-1 border mb-1" style="border-radius: 5px;" id="current_status">
                                        <div class="col-md-12" style="padding: 0 0;">
                                            <div class="comment-text w-100" id="sjkdghfsjd">
                                                <div class="row d-flex justify-content-between px-1">
                                                    <div class="hel">
                                                            <span class="ml-1 font-medium">
                                                                <h5 class="text-uppercase" id="step_name"></h5>
                                                            </span>
                                                        <span id="step_approve_by"></span>
                                                    </div>
                                                    <div class="hefghl">
                                                        <span class="btn btn-secondary btn-sm mt-1" style="border-radius: 50px" id="step_time"></span><br>
                                                        <span style="margin-left: .3rem" id="step_approve_desig"></span>
                                                    </div>
                                                </div><hr>
                                                <span class="m-b-15 d-block border p-1" style="border-radius: 5px" id="step_note"></span><hr>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-warning mb-1" onclick="$('#commentDiv').toggle('slow')">
                                            <i class="bx bx-down-arrow-alt" style="top: 4px;"></i> Show History</button>
                                    </div>
                                    <div class="p-1 border" style="border-radius: 5px;display: none" id="commentDiv">
                                        <div class="col-md-12" style="padding: 0 0;">
                                            <div class="comment-text w-100" id="content_bdy">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5" id="hide_portion">
                                    <div class="border p-1" id="submission_msg">
                                        <div class="row" id="hide_div">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Status</label>
                                                    <select class="custom-select form-control select2" required id="status_id" name="status_id">

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Note</label>
                                                    <textarea name="note" class="form-control" id="textarea" cols="30" rows="5"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="d-flex justify-content-end">
                                                    <div class="d-flex justify-content-between">
                                                        <div id="hide-form-btn" class="d-flex">
                                                            <button type="submit" class="btn btn-dark btn-sm mb-1 mr-1" id="save_btn" style="display: block"
                                                            ><i class="bx bx-save"></i> <span
                                                                    style="font-size: 12px;">Submit</span>
                                                            </button>
                                                            <button type="reset" class="btn btn-outline-dark btn-sm mb-1" id="close_btn" style="display: block"
                                                                    data-dismiss="modal"><i class="bx bx-window-close"></i> <span
                                                                    style="font-size: 12px;">Close</span>
                                                            </button>
                                                        </div>
                                                        <button type="submit" class="btn btn-dark btn-sm mb-1 ml-1"
                                                                id="approve_btn" name="approve_btn" value="Y" style="display: none"
                                                        ><i class="bx bx-save"></i> <span
                                                                style="font-size: 12px;">Final Approve</span>
                                                        </button>
                                                    </div>

                                                    <input type="hidden" id="workflow_id" name="workflow_id">
                                                    <input type="hidden" id="object_id" name="object_id">
                                                    <input type="hidden" id="bonus_id_prm" name="bonus_id">
                                                    <input type="hidden" id="get_url" name="get_url">
                                                    <input type="hidden" id="prefix" name="prefix">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <span style="display: none; height: 30px"
                                      class="col-md-12 no-permission badge badge-danger"><b><h5>You don't have permission !!!</h5></b></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .modal-body .table td, .modal-body .table th {
        padding: 7px;
        vertical-align: top;
    }
    .step-progressbar {
        list-style: none;
        counter-reset: step;
        display: flex;
        padding: 0;
    }
    .step-progressbar__item {
        display: flex;
        flex-direction: column;
        flex: 1;
        text-align: center;
        position: relative;
    }
    .step-progressbar__item:before {
        width: 3em;
        height: 3em;
        content: counter(step);
        counter-increment: step;
        align-self: center;
        background: #999;
        color: #fff;
        border-radius: 100%;
        line-height: 3em;
        margin-bottom: 0.5em;
    }
    .step-progressbar__item:after {
        height: 2px;
        width: calc(100% - 4em);
        content: "";
        background: #999;
        position: absolute;
        top: 1.5em;
        left: calc(50% + 2em);
    }
    .step-progressbar__item:last-child:after {
        content: none;
    }
    .step-progressbar__item--active:before {
        background: #000;
    }
    .step-progressbar__item--complete:before {
        content: "✔";
    }
</style>

