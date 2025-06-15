<section>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div class="row mb-3 mx-2 mt-4">
						<div class="col-12 col-md-3">
							<a href="<?=base_url();?>master/add_raw_material">
								<button type="button" class="btn btn-primary w-40" id="add-row-btn">
									New Raw Material
								</button>
							</a>
						</div>
					</div>
				</div>
				<div class="card-body pt-3">
					<table class="table" id="tbl-report-raw">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-left">Material No</th>
								<th class="text-left">Material Name</th>
								<th class="text-center">Uom</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody class="tbody-report-raw">
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
			url: '<?= base_url('master/load_raw_material'); ?>',
			type: 'get',
			dataType: 'json',
			data: {},
			success: function(res) {
				$('#spinner-container').hide();

				var $tbody = $('.tbody-report-raw');

				// Loop over the data and build table rows
				$.each(res, function(index, material) {
					var row = `<tr>
						<td class="text-center">${(index + 1)}</td>
						<td class="text-start">${material.Material_no}</td>
						<td class="text-left">${material.Material_name}</td>
						<td class="text-center">${material.Unit}</td>
						<td class="text-center">
							<a href="<?=base_url('master/');?>edit_raw_material/${material.Id}">
								<span class="badge bg-warning badge-hover" style=":hover{cursor: pointer;}">
									<i class="bx bxs-edit" style="color: white;"></i>
								</span>
							</a>
							<a href="<?=base_url('master/');?>delete_raw_material/${material.Id}">
								<span class="badge bg-danger badge-hover" style=":hover{cursor: pointer;}">
									<i class="bx bxs-trash"></i>
								</span>
							</a>
						</td>
						</tr>`;
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
	});
</script>

<?php if ($this->session->flashdata('SUCCESS_ADD_RAW_MATERIAL')): ?>
	<script>
		Swal.fire({
			title: "Success",
			html: `<?= $this->session->flashdata('SUCCESS_ADD_RAW_MATERIAL'); ?>`,
			icon: "success"
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_ADD_RAW_MATERIAL')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: `<?= $this->session->flashdata('FAILED_ADD_RAW_MATERIAL'); ?>`,
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('SUCCESS_EDIT_RAW_MATERIAL')): ?>
	<script>
		Swal.fire({
			title: "Success",
			html: `<?= $this->session->flashdata('SUCCESS_EDIT_RAW_MATERIAL'); ?>`,
			icon: "success"
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_EDIT_RAW_MATERIAL')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: `<?= $this->session->flashdata('FAILED_EDIT_RAW_MATERIAL'); ?>`,
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('SUCCESS_DELETE_RAW_MATERIAL')): ?>
	<script>
		Swal.fire({
			title: "Success",
			html: `<?= $this->session->flashdata('SUCCESS_DELETE_RAW_MATERIAL'); ?>`,
			icon: "success"
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_EDIT_RAW_MATERIAL')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: `<?= $this->session->flashdata('FAILED_EDIT_RAW_MATERIAL'); ?>`,
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