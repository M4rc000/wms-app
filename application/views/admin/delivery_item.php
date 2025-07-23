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
				<div class="row mb-3 mx-2 mt-4">
					<div class="col-12 col-md-3">
						<a href="<?=base_url('admin/add_delivery_item');?>">
							<button type="button" class="btn btn-primary w-40" id="add-row-btn">
								New Delivery Item
							</button>
						</a>
					</div>
				</div>
					<div class="row mt-2 mx-2">
						<div class="col-12">
							<div class="table-responsive">
								<table id="delivery-item-table" class="table table-bordered">
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
									<tbody id="tbody-delivery-item"></tbody>
								</table>
							</div>
						</div>
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

<script src="<?= base_url('assets'); ?>/js/functions.js"></script>
<script>
	$(document).ready(function() {
		$('#spinner-container').show();

		$.ajax({
			url: '<?= base_url('admin/load_delivery_item'); ?>',
			type: 'get',
			dataType: 'json',
			data: {},
			success: function(res) {
				$('#spinner-container').hide();

				var $tbody = $('#tbody-delivery-item');

				// Loop over the data and build table rows
				$.each(res, function(index, delivery) {
					var row = '<tr>' +
						'<td class="text-center">' + (index + 1) + '</td>' +
						'<td class="text-center">' + delivery.Product_no + '</td>' +
						'<td class="text-left">' + delivery.Product_name + '</td>' +
						'<td class="text-center">' + delivery.Qty + '</td>' +
						'<td class="text-center">' + delivery.Unit + '</td>' +
						'<td class="text-center">' + delivery.Status + '</td>' +
						'<td class="text-center">' + delivery.Driver_id + '</td>' +
						'<td class="text-center">' + formatDateToLong(delivery.Delivery_date) + '</td>' +
						'<td class="text-center">' +
							'<button class="btn btn-sm btn-danger me-2 generate-pdf-btn" data-id="' + delivery.Id + '">' +
								'<i class="bi bi-file-earmark-pdf"></i>' +
							'</button>' +
							'<button class="btn btn-sm btn-outline-danger btn-delete-delivery" data-id="' + delivery.Id + '">' +
								'<i class="bi bi-trash"></i>' +
							'</button>' +
						'</td>'
						'</tr>';
					$tbody.append(row);
				});

				// Transform the table into a DataTable
				$('#tbl-report-raw').DataTable({
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
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.error(xhr.statusText);
			}
		});

		$(document).on('click', '.generate-pdf-btn', function() {
			const id = $(this).data('id');
			window.open('<?= base_url("admin/print_delivery_pdf/"); ?>' + id, '_blank');
		});

		$(document).on('click', '.btn-delete-delivery', function() {
			const id = $(this).data('id');
			Swal.fire({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = '<?= base_url("admin/delete_delivery_item/"); ?>' + id;
				}
			});
		});
	});
</script>

<?php if ($this->session->flashdata('SUCCESS_ADD_DELIVERY_ITEM')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Success",
				html: `<?= $this->session->flashdata('SUCCESS_ADD_DELIVERY_ITEM'); ?>`,
				icon: "success"
			});
		});
	</script>
<?php endif; ?>

<?php if ($this->session->flashdata('FAILED_ADD_DELIVERY_ITEM')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Failed",
				html: `<?= $this->session->flashdata('FAILED_ADD_DELIVERY_ITEM'); ?>`,
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

<?php if ($this->session->flashdata('SUCCESS_DELETE')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Terhapus",
				html: `<?= $this->session->flashdata('SUCCESS_DELETE'); ?>`,
				icon: "success"
			});
		});
	</script>
<?php endif; ?>