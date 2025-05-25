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
										</tr>
									</thead>
									<input type="text" name="user_id" id="user_id" value="<?= $user['Id']; ?>" hidden>
									<tbody id="table-body"></tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="row mt-3 mx-2 mb-3">
						<div class="col-12 text-end">
							<button type="submit" class="btn btn-success">Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<script>
	
	$(document).ready(function() {
		// Add new row on button click
		let rowIndex = 1;

		$('#add-row-btn').click(function() {
			addRow();
		});

		$(document).on('click', '.btn-remove-row', function() {
			$(this).closest('tr').remove();
			updateRowIndices();
		});

		function addRow() {
			var material_list = <?= json_encode($materials); ?>;
			let materialOptions = '<option value="" selected>Select Material No</option>';
			material_list.forEach(ml => {
				materialOptions += `<option value="${ml.Material_no}">${ml.Material_no}</option>`;
			});

			const newRow = `
                <tr>
                    <td class="py-3"><b>${rowIndex}</b></td>
                    <td>
						<select class="form-select material-select w-full" name="materials[${(rowIndex-1)}][Material_no]" required>
                            ${materialOptions}
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control material-name w-full" name="materials[${rowIndex}][Material_name]" aria-label="Material Description" readonly>
                    </td>
                    <td>
						<select class="form-select transaction-type w-full text-center" name="materials[${(rowIndex-1)}][Transaction_type]" required>
							<option value="">Choose Type</option>
							<option value="In">In</option>
							<option value="Out">Out</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control material-qty w-full text-center" name="materials[${rowIndex}][Qty]" aria-label="Quantity" required>
                    </td>
                    <td>
                        <input type="text" class="form-control material-unit text-center w-full" name="materials[${rowIndex}][Unit]" aria-label="Unit of Measure" readonly>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-remove-row w-full" type="button" aria-label="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
			$('#table-body').append(newRow);
			updateRowIndices();

			$('.material-select').select2({
				width: '100%'
			});
			$('.transaction-type').select2({
				width: '100%'
			});


			$('.material-select').last().change(function() {
				const selectedMaterialId = $(this).val();
				const selectedMaterial = material_list.find(ml => ml.Material_no == selectedMaterialId);

				if (selectedMaterial) {
					$(this).closest('tr').find('.material-name').val(selectedMaterial.Material_name);
					$(this).closest('tr').find('.material-unit').val(selectedMaterial.Unit);
				}
			});
			rowIndex++;
		}

		function updateRowIndices() {
			$('#table-body tr').each(function(index) {
				$(this).find('td:first-child b').text(index + 1);
				$(this).find('input').each(function() {
					const name = $(this).attr('name');
					const newName = name.replace(/\[\d+\]/, `[${index}]`);
					$(this).attr('name', newName);
				});
			});
			rowIndex = $('#table-body tr').length;
		}
	});
</script>


<?php if ($this->session->flashdata('SUCCESS_ADD_RECEIVING_RAW')): ?>
	<script>
		Swal.fire({
			title: "Success",
			html: `<?= $this->session->flashdata('SUCCESS_ADD_RECEIVING_RAW'); ?>`,
			icon: "success"
		});
	</script>
<?php endif; ?>
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
