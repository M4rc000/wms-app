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
						<button type="button" class="btn btn-primary w-40" id="add-row-btn">
							<i class="bi bi-plus-circle"></i>
						</button>
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
						'<td class="text-start">' + delivery.Product_no + '</td>' +
						'<td class="text-left">' + delivery.Product_name + '</td>' +
						'<td class="text-left">' + delivery.Qty + '</td>' +
						'<td class="text-left">' + delivery.Unit + '</td>' +
						'<td class="text-left">' + delivery.Status + '</td>' +
						'<td class="text-left">' + delivery.Driver_id + '</td>' +
						'<td class="text-left">' + delivery.Delivery_date + '</td>' +
						'<td class="text-center">' +
							'<button class="btn btn-sm btn-danger generate-pdf-btn" data-id="' + delivery.Id + '">' +
								'<i class="bi bi-file-earmark-pdf"></i>' +
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
	});
</script>

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
