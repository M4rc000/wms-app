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
								<th class="text-center">Qty Predict</th>
								<th class="text-center">Date</th>
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

<script src="<?= base_url('assets'); ?>/js/functions.js"></script>
<script>
	$(document).ready(function() {
		$('#spinner-container').show();

		$.ajax({
			url: '<?= base_url('management/load_demand_stock'); ?>',
			type: 'get',
			dataType: 'json',
			data: {},
			success: function(res) {
				$('#spinner-container').hide();

				var $tbody = $('.tbody-report-usage');

				// Loop over the data and build table rows
				$.each(res, function(index, material) {
					var row = '<tr>' +
						'<td class="text-center">' + (index + 1) + '</td>' +
						'<td class="text-start">' + material.Material_no + '</td>' +
						'<td class="text-left">' + material.Material_name + '</td>' +
						'<td class="text-center">' + (material.Qty_predict) + '</td>' +
						'<td class="text-center">' + material.Date + '</td>' +
						'</tr>';
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
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.error(xhr.statusText);
			}
		});

	});
</script>