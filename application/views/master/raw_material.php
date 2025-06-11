<style>
	.select2-container {
		z-index: 9999;
	}

	.select2-selection {
		padding-top: 4px !important;
		height: 38px !important;
	}
</style>
<section class="section">
	<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addModal" style="color: white">
		New Raw Material
	</button>
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-body table-responsive mt-2">
					<table class="table datatable">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-center">Material No</th>
								<th class="text-center">Material Name</th>
								<th class="text-center">Uom</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<!-- <tbody>
							<?php $number = 0;
							foreach ($submenus as $sb) : $number++ ?>
								<tr class="text-center">
									<td><?= $number; ?></td>
									<td>
										<?php
										foreach ($menus as $menu) {
											if ($sb['Menu_id'] == $menu['Id']) {
												echo $menu['Name'];
											}
										}
										?>
									</td>
									<td><?= $sb['Material_no']; ?></td>
									<td><?= $sb['Material_name']; ?></td>
									<td><?= $sb['Unit']; ?></td>
									<td>
										<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editModal<?= $sb['Id']; ?>">
											<i class="bx bxs-edit" style="color: white;"></i>
										</button>
										<button class="btn btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $sb['Id']; ?>">
											<i class="bx bxs-trash"></i>
										</button>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody> -->
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ADD MODAL-->
<div class="modal fade" id="addModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<?= form_open_multipart('master/AddRawMaterial'); ?>
			<div class="modal-header">
				<h5 class="modal-title">Add Raw Material</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<!-- GET USER ID -->
				<!-- <input type="text" class="form-control" id="user_id" name="user_id" value="<?= $user['Id']; ?>" hidden>
				<div class="row ps-2">
					<div class="col-4">
						<label for="menu_id" class="form-label">Menu ID</label>
						<select id="menu_id" class="form-select" required name="menu_id">
							<?php foreach ($menus as $menu) : ?>
								<option value="<?= $menu['Id']; ?>" data-url="<?= strtolower(str_replace(' ', '', $menu['Name'])); ?>/">
									<?= $menu['Id']; ?> | <?= $menu['Name']; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-4">
						<label for="name" class="form-label">Name</label>
						<input type="text" class="form-control" id="name" name="name" required>
					</div>
					<div class="col-4">
						<label for="url" class="form-label">Url</label>
						<input type="text" class="form-control" id="url" name="url" required>
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
</div> -->

<!-- EDIT MODAL-->
<?php foreach ($submenus as $sb) : ?>
	<div class="modal fade" id="editModal<?= $sb['Id']; ?>" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<?= form_open_multipart('adminhead/EditRawMaterial'); ?>
				<div class="modal-header">
					<h5 class="modal-title">Edit Raw Material</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<!-- GET USER ID -->
					<!-- <input type="text" class="form-control" id="user_id" name="user_id" value="<?= $user['Id']; ?>" hidden>
					<div class="row ps-2">
						<div class="col-4">
							<label for="menu_id" class="form-label">Menu ID</label>
							<select id="menu_id" class="form-select" required name="menu_id">
								<?php foreach ($menus as $menu): ?>
									<option <?= $menu['Id'] == $sb['Menu_id'] ? 'selected' : ''; ?> 
											value="<?= $menu['Id']; ?>" 
											data-url="<?= strtolower(str_replace(' ', '', $menu['Name'])); ?>/">
										<?= $menu['Name'] ?>
									</option>
								<?php endforeach; ?>
							</select>
							<input type="text" class="form-control" id="id" name="id" value="<?= $sb['Id']; ?>" hidden>
						</div>
						<div class="col-4">
							<label for="name" class="form-label">Name</label>
							<input type="text" class="form-control" id="name" name="name" value="<?= $sb['Name']; ?>">
						</div>
						<div class="col-4">
							<label for="url" class="form-label">Url</label>
							<input type="text" class="form-control" id="url" name="url" value="<?= $sb['Url']; ?>">
						</div>
					</div>
					<div class="row ps-2 mt-4">
						<div class="col-4">
							<label for="icon" class="form-label">Icon</label>
							<div class="input-group mb-3">
								<span class="input-group-text icon-show" id="basic-addon1"><i class="<?= $sb['Icon']; ?>"></i></span>
								<input type="text" class="form-control" aria-label="icon" aria-describedby="basic-addon1" id="icon" name="icon" value="<?= $sb['Icon']; ?>">
							</div>
						</div>
						<div class="col-4">
							<label for="active" class="form-label">Active</label>
							<select id="active" class="form-select" required name="active">
								<option <?= $sb['Active'] == 1 ? 'selected' : ''; ?> value="1">Active</option>
								<option <?= $sb['Active'] == 0 ? 'selected' : ''; ?> value="0">Not active</option>
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
<?php endforeach; ?> -->

<!-- DELETE CONFIRM MODAL-->
<?php foreach ($submenus as $sb) : ?>
	<?= form_open_multipart('adminhead/DeleteRawMaterial'); ?>
	<div class="modal fade" id="deleteModal<?= $sb['Id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title pb-0 mb-0" id="exampleModalLabel">Confirm to delete ?</h4>
				</div>
				<div class="modal-body">
					<!-- GET USER ID -->
					<input type="text" class="form-control" id="user_id" name="user_id" value="<?= $user['Id']; ?>" hidden>
					<input type="text" name="id" id="id" value="<?= $sb['Id']; ?>" style="display: none;">
					<p><b>Menu ID</b> : <?= $sb['Menu_id']; ?></p>
					<p><b>Title</b> : <?= $sb['Name']; ?></p>
					<p><b>Url</b> : <?= $sb['Url']; ?></p>
					<p><b>Icon</b> : <i class="<?= $sb['Icon']; ?>"></i></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary" name="delete_user">Confirm</button>
				</div>
			</div>
		</div>
	</div>
	</form>
<?php endforeach; ?>


<script>
	$(document).ready(function() {
		$('#addModal').on('shown.bs.modal', function() {
			$('#menu_id').on('change', function() {
				var selectedOption = $(this).find('option:selected');
				var autoUrl = selectedOption.data('url');
				if (autoUrl) {
					$('#url').val(autoUrl);
				}
			});
		});

		$('#editModal').on('shown.bs.modal', function() {
			$('#menu_id').on('change', function() {
				var selectedOption = $(this).find('option:selected');
				var autoUrl = selectedOption.data('url');
				if (autoUrl) {
					$('#url').val(autoUrl);
				}
			});
		});

		$('.modal').on('shown.bs.modal', function() {
			// Target only the input inside the currently opened modal
			$(this).find('#icon').on('input', function() {
				var iconValue = $(this).val();
				$(this).closest('.modal').find('.icon-show').html('<i class="' + iconValue + '"></i>');
			});

			$(this).find('#icon').on('change', function() {
				var iconValue = $(this).val();
				$(this).closest('.modal').find('.icon-show').html('<i class="' + iconValue + '"></i>');
			});

			// Ensure select2 dropdown works within the current modal
			$(this).find('#menu_id').select2({
				dropdownParent: $(this)
			});

			$(this).find('#active').select2({
				dropdownParent: $(this)
			});

			$('select[name="menu_id"]').on('change', function () {
				var selectedOption = $(this).find('option:selected');
				var url = selectedOption.data('url');
				
				var modal = $(this).closest('.modal');
				modal.find('input[name="url"]').val(url);
			});
		});
	});
</script>



<!-- SWEET ALERT -->
<?php if ($this->session->flashdata('SUCCESS_AddSubMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Success",
				html: "<?= $this->session->flashdata('SUCCESS_AddSubMenu'); ?>",
				icon: "success"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_AddSubMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: "<?= $this->session->flashdata('FAILED_AddSubMenu'); ?>",
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('SUCCESS_editSubMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Success",
				html: "<?= $this->session->flashdata('SUCCESS_editSubMenu'); ?>",
				icon: "success"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_editSubMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: "<?= $this->session->flashdata('FAILED_editSubMenu'); ?>",
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('SUCCESS_DeleteSubMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Success",
				html: "<?= $this->session->flashdata('SUCCESS_DeleteSubMenu'); ?>",
				icon: "success"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_DeleteSubMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: "<?= $this->session->flashdata('FAILED_DeleteSubMenu'); ?>",
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
