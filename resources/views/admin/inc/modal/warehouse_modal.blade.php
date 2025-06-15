<!-- Add Warehouse Modal -->
<div class="modal fade" id="addWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="addWarehouseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Warehouse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newWarehouseForm">
                    <div class="form-group">
                        <label for="warehouse_name">Name <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="warehouse_name" name="name" placeholder="Enter warehouse name" required>
                    </div>
                    <div class="form-group">
                        <label for="warehouse_location">Location <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="warehouse_location" name="location" placeholder="Enter location" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveWarehouseBtn">Save Warehouse</button>
            </div>
        </div>
    </div>
</div>