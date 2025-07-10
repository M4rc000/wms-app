<section>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body pt-3">
					<table class="table" id="tbl-report-usage">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-left">Material No</th>
								<th class="text-left">Material Name</th>
								<th class="text-center">Qty</th>
								<th class="text-center">Uom</th>
                                <th class="text-left">Transaction Type</th>
								<th class="text-left">Update At</th>
								<th class="text-left">Action</th>
							</tr>
						</thead>
						<tbody class="tbody-report-usage">
							<!-- Table rows will be appended here by JavaScript -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>


<!-- SPINNER LOADING -->
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

<!-- MODAL EDIT -->

<div class="modal fade" id="editModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<?= form_open_multipart('admin/EditReceivingMaterial'); ?>
			<div class="modal-header">
				<h5 class="modal-title">Add Menu</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<!-- GET USER ID -->
				<input type="text" class="form-control" id="user_id" name="user_id" value="<?= $user['Id']; ?>" hidden>
				<div class="row ps-2">
					<div class="col-4">
						<label for="NameEditModal" class="form-label">Name</label>
						<input type="text" class="form-control" id="NameEditModal" name="NameEditModal" >
					</div>
					<div class="col-4">
						<label for="menu" class="form-label">Menu</label>
						<input type="text" class="form-control" id="menu" name="menu" required>
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
	$(document).ready(function() {
		$('#spinner-container').show();

		$.ajax({
			url: '<?= base_url('admin/load_manage_storage'); ?>',
			type: 'get',
			dataType: 'json',
			data: {},
			success: function(res) {
				$('#spinner-container').hide();

				var $tbody = $('.tbody-report-usage');

				// Loop over the data and build table rows
				console.log("Res: ", res);
				$.each(res, function(index, material) {
					var row = `<tr>
						<td class="text-center">${(index + 1)} </td>
						<td class="text-start">${material.Material_no} </td>
						<td class="text-left">${material.Material_name} </td> +
						<td class="text-center">${formatQuantity(material.Qty, material.Unit)}</td> 
						<td class="text-center">${material.Unit}</td>
                        <td class="text-center">${material.Transaction_type} </td> 
						<td class="text-center">${material.Updated_at == '' ? "-" : material.Updated_at}</td> 
						<td>
							<button class="btn btn-success edit-data" data-bs-toggle="modal" data-bs-target="#editModal${material.id}" data-id=${material.id} data-name=${material.Material_name}>
								<i class="bx bxs-edit" style="color: white;"></i>
							</button>
							<button class="btn btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteModal${material.id}">
								<i class="bx bxs-trash"></i>
							</button>
						</td>
						</tr>`;
					$tbody.append(row);
				});

				// Transform the table into a DataTable
				$('#tbl-report-usage').DataTable({
					columnDefs: [{
							targets: 1,
							className: 'text-start'
						} // Force left alignment on column 1
					],
					layout: {
						topStart: {
							buttons: [{
								extend: 'excel',
								text: '<i class="bx bx-table"></i> Excel',
								className: 'btn-custom-excel'
							}]
						}
					}
				});


				$('.edit-data').on('click', function(){
					var name = $(this).data('name') // data-name
					$('#editModal').modal('show');
					$('#NameEditModal').val(name);
				});
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.error(xhr.statusText);
			}
		});

	});
</script>

<?php if ($this->session->flashdata('FAILED_ADD_RECEIVING_RAW')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: `<?= $this->session->flashdata('FAILED_ADD_RECEIVING_RAW'); ?>`,
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('ERROR')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: `<?= $this->session->flashdata('ERROR'); ?>`,
				icon: "error"
			});
		});
	</script>
<?php endif; ?>