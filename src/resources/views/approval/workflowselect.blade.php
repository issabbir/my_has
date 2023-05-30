<div id="workflowM" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">Assign WorkFlow</h3>
            </div>
            <form id="workflow_assign_form" method="post">
                <div class="modal-body">

                    {!! csrf_field() !!}
                    <select class="custom-select form-control select2" required
                            id="flow_id" name="status_id">

                    </select>
                    <input type="hidden" id="application_id_flow" name="application_id_flow">
                    <input type="hidden" id="prefix" name="prefix">
                    <input type="hidden" id="t_name" name="t_name">
                    <input type="hidden" id="c_name" name="c_name">
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
