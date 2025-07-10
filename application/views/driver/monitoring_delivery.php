<style>
	.badge-hover:hover {
		cursor: pointer;
	}

	.select2-container {
		z-index: 9999;
	}

	.select2-selection {
		padding-top: 4px !important;
		height: 38px !important;
	}
</style>

<section>
	<div class="row">
		<div class="col-md-12">
			<div class="card border">
				<?= form_open_multipart('driver/editDeliveryStatus'); ?>
					<div class="row mt-2 mx-2">
						<div class="col-12">
							<div class="table-responsive">
								<table id="bomTable" class="table table-bordered">
									<thead>
										<tr>
											<th class="text-center">#</th>
											<th class="text-center">Product No</th>
											<th class="text-center">Product Name</th>
											<th class="text-center">Qty</th>
											<th class="text-center">Uom</th>
											<th class="text-center">Status</th>
											<th class="text-center">Driver ID</th>
                                            <th class="text-center">Delivery Date</th>
											<th class="text-center">Action</th>
										</tr>
									</thead>
									<input type="text" name="user_id" id="user_id" value="<?= $user['Id']; ?>" hidden>
									<tbody id="table-body"></tbody>
								</table>
							</div>
						</div>
					</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<div class="spinner-container" id="spinner-container">
	<div class="spinner-grow text-success" role="status">
		<span class="visually-hidden">Loading...</span>
	</div>
	<div class="spinner-grow text-success" role="status">
		<span class="visually-hidden">Loading...</span>
	</div>
	<div class="spinner-grow text-success" role="status">
		<span class="visually-hidden">Loading...</span>
	</div>
</div>

<!-- Edit Modal -->

<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <?= form_open_multipart('driver/EditDeliveryStatus'); ?>

      <div class="modal-header">
        <h5 class="modal-title">Edit Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <input type="hidden" class="form-control" id="material_id" name="material_id">
        <div class="row ps-2">
          <div class="col-6">
            <label for="ProductNameEdit" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="ProductNameEdit" name="ProductNameEdit" readonly>
          </div>
          <div class="col-6">
            <label for="StatusEdit" class="form-label">Status</label>
            <select name="StatusEdit" id="StatusEdit" class="form-select" required>
              <option value="">-- Select Status --</option>
              <option value="Outgoing">Outgoing</option>
              <option value="Pending">Pending</option>
              <option value="Delivered">Delivered</option>
            </select>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>

      </form> 

    </div>
  </div>
</div>

<script src="<?= base_url('assets'); ?>/js/functions.js"></script>
<script>
	$(document).ready(function () {
	$.ajax({
		url: '<?= base_url('driver/load_monitoring_delivery'); ?>',
		type: 'get',
		dataType: 'json',
		success: function (res) {
			var $tbody = $('#table-body'); // sesuai dengan ID tbody kamu

			$.each(res, function (index, item) {
				var row = `<tr>
					<td class="text-center">${index + 1}</td>
					<td class="text-center">${item.Product_no}</td>
					<td class="text-center">${item.Product_name}</td>
					<td class="text-center">${item.Qty}</td>
					<td class="text-center">${item.Unit}</td>
					<td class="text-center">${item.Status}</td>
					<td class="text-center">${item.Driver_id}</td>
					<td class="text-center">${item.Delivery_date}</td>
					<td>
						<button type="button" class="btn btn-success edit-data" data-bs-toggle="modal" data-bs-target="#editModal"
							data-id="${item.Id}" 
							data-name="${item.Product_name}" 
							data-status="${item.Status}">
							<i class="bx bxs-edit" style="color: white;"></i>
						</button>
					</td>
				</tr>`;
				$tbody.append(row);
			});

			$('.edit-data').on('click', function () {
			const id = $(this).data('id');
			const name = $(this).data('name');
			const status = $(this).data('status');
			
			$('#editModal').modal('show');
			$('#material_id').val(id);
			$('#ProductNameEdit').val(name);
			$('#StatusEdit').val(status);
		});

		},
		error: function (xhr) {
			console.error(xhr.responseText);
		}
	});
});
</script>
