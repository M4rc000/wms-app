<style>
	.select2-container {
		z-index: 9999;
	}

	.select2-selection {
		padding-top: 4px !important;
		height: 38px !important;
	}
</style>
<div class="content-wrapper">
	<div class="card p-4">
		<div class="card-body">
			<div class="row mb-5">
				<div class="col-md">
					<a href="<?= base_url('adminhead/manage_role'); ?>">
						<button class="btn btn-primary">
							<i class="bi bi-arrow-left"></i>
						</button>
					</a>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<h5 class="mb-5"><strong>Role :</strong> <span style="font-size: 17px"><?= $role['Name']; ?></span></h5>
					<div class="row mt-2">
						<div class="col">
							<span class="mt-5"><strong>Manage Role Menu</strong></span>
						</div>
					</div>
					<hr>
					<div class="row mt-1 px-2">
						<div class="col">
							<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleMenuModal">
								<i class="bi bi-plus-circle"></i>
							</button>
						</div>
					</div>
					<table class="table datatable mb-5" id="tbl-user-role">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-center">Role ID</th>
								<th class="text-center">Role</th>
								<th class="text-center">Menu</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1;
							foreach ($accessmenu as $acm) : ?>
								<tr>
									<td class="text-center"><?= $i; ?></td>
									<td class="text-center"><?= $acm['Role_id']; ?></td>
									<td class="text-center"><?= $acm['Name']; ?></td>
									<td class="text-center"><?= $acm['Name']; ?></td>
									<td class="text-center">
										<a href="#" data-bs-toggle="modal" data-bs-target="#deleteRoleMenuModal<?= $acm['Id']; ?>">
											<span class="badge bg-danger"><i class="bi bi-trash"></i></span>
										</a>
									</td>
								</tr>
							<?php $i++;
							endforeach; ?>
						</tbody>
					</table>


					<div class="row mt-5">
						<div class="col">
							<span class="mt-5"><strong>Manage Role SubMenu</strong></span>
						</div>
					</div>
					<hr>
					<div class="row mt-1 px-2">
						<div class="col">
							<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleSubMenuModal">
								<i class="bi bi-plus-circle"></i>
							</button>
						</div>
					</div>
					<table class="table datatable" id="tbl-user-role">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-center">Role ID</th>
								<th class="text-center">Role</th>
								<th class="text-center">Menu</th>
								<th class="text-center">SubMenu</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1;
							foreach ($accesssubmenu as $acsm) : ?>
								<tr>
									<td class="text-center"><?= $i; ?></td>
									<td class="text-center"><?= $acsm['Role_id']; ?></td>
									<td class="text-center"><?= $acsm['Role_Name']; ?></td>
									<td class="text-center"><?= $acsm['Menu_Name']; ?></td>
									<td class="text-center"><?= $acsm['Submenu_Name']; ?></td>
									<td class="text-center">
										<a href="#" data-bs-toggle="modal" data-bs-target="#deleteRoleSubMenuModal<?= $acsm['Id']; ?>">
											<span class="badge bg-danger"><i class="bi bi-trash"></i></span>
										</a>
									</td>
								</tr>
							<?php $i++;
							endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="row mt-5 px-5">
			<ul style="list-style-type: none">
				<?php foreach ($accessmenu as $acm) : ?>
					<?php if ($acm['Name'] != 'User') : ?>
						<li>
							<strong><i class="bx bx-window"></i> <?= strtoupper($acm['Name']); ?></strong>
							<ul style="list-style-type: none">
								<?php foreach ($accesssubmenu as $acsm) : ?>
									<?php if ($acsm['Menu_Name'] === $acm['Name']) : ?>
										<li><i class="<?= $acsm['Icon']; ?>" style="color: #002e63"></i> <?= $acsm['Submenu_Name']; ?></li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						</li>
						<br>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>

	</div>
</div>

<script>
	$(document).ready(function() {
		$('#addRoleMenuModal').on('shown.bs.modal', function() {
			$('#menu_id').select2({
				dropdownParent: $('#addRoleMenuModal'),
				width: '100%'
			});
		});

		$('#addRoleSubMenuModal').on('shown.bs.modal', function() {
			$('#meenu_id').select2({
				dropdownParent: $('#addRoleSubMenuModal'),
				width: '100%'
			});
			$('#submenu_id').select2({
				dropdownParent: $('#addRoleSubMenuModal'),
				width: '100%'
			});
		});

		$('#meenu_id').on('change', function() {
			var menu_id = $(this).val();
			var role_id = <?= $role['Id']; ?>;

			$.ajax({
				url: '<?= base_url('adminhead/getSubMenuBasedOnMenu'); ?>',
				type: 'post',
				dataType: 'json',
				data: {
					menu_id: menu_id,
					role_id: role_id
				},
				success: function(res) {
					// console.log(res);

					// Clear the existing options
					$('#submenu_id').empty();

					// Check if response is not empty
					if (res.length > 0) {
						// Append new options
						$.each(res, function(index, item) {
							$('#submenu_id').append($('<option>', {
								value: item.Id,
								text: item.Name
							}));
						});
					} else {
						// Handle no results case
						$('#submenu_id').append($('<option>', {
							value: '',
							text: 'All Submenu accessed'
						}));
					}
				}
			});
		});


		$('.checkbox').change(function() {
			var role_id = $(this).data('role');
			var menu_id = $(this).data('menu');
			var checked = $(this).prop('checked') ? 1 : 0;

			$.ajax({
				url: 'admin/changeaccess', // Replace with the actual URL to update access
				type: 'POST',
				data: {
					role_id: role_id,
					menu_id: menu_id,
					checked: checked
				},
				success: function(response) {}
			});
		});
	});
</script>



<!-- ADD ROLE MENU MODAL -->
<div class="modal fade" id="addRoleMenuModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<?= form_open_multipart('adminhead/addRoleAccessMenu'); ?>
			<div class="modal-header">
				<h5 class="modal-title">Add Role Access Menu</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<!-- GET USER -->
				<input type="text" class="form-control" id="user_id" name="user_id" value="<?= $user['Id']; ?>" hidden>
				<div class="row ps-2">
					<div class="col-4">
						<label for="role_id" class="form-label">Role</label>
						<input type="text" class="form-control" id="role_id" name="role_id" value="<?= $role['Id']; ?>" readonly>
					</div>
					<div class="col-4">
						<label for="menu_id" class="form-label">Menu</label>
						<select class="form-select" id="menu_id" name="menu_id" required>
							<option value="">Select Menu</option>
							<?php foreach ($menus as $mn): ?>
								<option value="<?= $mn['Id'] ?>"><?= $mn['Id']; ?> | <?= $mn['Name']; ?></option>
							<?php endforeach; ?>
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

<!-- DELETE ROLE MENU MODAL -->
<?php $i = 1;
foreach ($accessmenu as $acm) : ?>
	<div class="modal fade" id="deleteRoleMenuModal<?= $acm['Id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<?= form_open_multipart('adminhead/DeleteRoleAccessMenu'); ?>
				<div class="modal-header">
					<h4 class="modal-title pb-0 mb-0" id="exampleModalLabel">Confirm to delete?</h4>
				</div>
				<div class="modal-body">
					<input type="text" name="id" id="id" value="<?= $acm['Id']; ?>" hidden>
					<input type="text" name="user_id" id="user_id" value="<?= $user['Id']; ?>" hidden>
					<input type="text" name="role_id" id="role_id" value="<?= $acm['Role_id']; ?>" hidden>
					<p><b>Role </b>: <?= $acm['Name']; ?></p>
					<p><b>Menu</b>: <?= $acm['Name']; ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary" name="delete_user">Confirm</button>
				</div>
				</form>
			</div>
		</div>
	</div>
<?php endforeach; ?>

<!-- ADD ROLE SUBMENU MODAL -->
<div class="modal fade" id="addRoleSubMenuModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<?= form_open_multipart('adminhead/addRoleAccessSubMenu'); ?>
			<div class="modal-header">
				<h5 class="modal-title">Add Role Access Sub Menu</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<!-- GET USER ID -->
				<input type="text" class="form-control" id="user_id" name="user_id" value="<?= $user['Id']; ?>" hidden>
				<div class="row ps-2">
					<div class="col-4">
						<label for="role_id" class="form-label">Role</label>
						<input type="text" class="form-control" id="role_id" name="role_id" value="<?= $role['Id']; ?>" readonly>
					</div>
					<div class="col-4">
						<label for="meenu_id" class="form-label">Menu</label>
						<select class="form-select" id="meenu_id" name="meenu_id" required>
							<option value="">Select Menu</option>
							<?php foreach ($menu as $mn): ?>
								<option value="<?= $mn['Id'] ?>"><?= $mn['Id']; ?> | <?= $mn['Name']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-4">
						<label for="submenu_id" class="form-label">SubMenu</label>
						<select class="form-select" id="submenu_id" name="submenu_id" required>
							<option value="">Select Sub Menu</option>
							<?php foreach ($submenus as $smn): ?>
								<option value="<?= $smn['Id'] ?>"><?= $smn['Id']; ?> | <?= $smn['Name']; ?></option>
							<?php endforeach; ?>
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

<!-- DELETE ROLE SUBMENU MODAL -->
<?php $i = 1;
foreach ($accesssubmenu as $acsm) : ?>
	<div class="modal fade" id="deleteRoleSubMenuModal<?= $acsm['Id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<?= form_open_multipart('adminhead/DeleteRoleAccessSubMenu'); ?>
				<div class="modal-header">
					<h4 class="modal-title pb-0 mb-0" id="exampleModalLabel">Confirm to delete?</h4>
				</div>
				<div class="modal-body">
					<input type="text" name="id" id="id" value="<?= $acsm['Id']; ?>" hidden>
					<input type="text" name="user_id" id="user_id" value="<?= $user['Id']; ?>" hidden>
					<input type="text" name="role_id" id="role_id" value="<?= $acsm['Role_id']; ?>" hidden>
					<p><b>Role </b>: <?= $acsm['Role_Name']; ?></p>
					<p><b>Menu</b>: <?= $acsm['Menu_Name']; ?></p>
					<p><b>SubMenu</b>: <?= $acsm['Submenu_Name']; ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary" name="delete_user">Confirm</button>
				</div>
				</form>
			</div>
		</div>
	</div>
<?php endforeach; ?>

<script src="<?= base_url('assets'); ?>/vendor/sweet-alert/sweet-alert.js"></script>
<script>
	$(document).ready(function() {
		$('#addRoleMenuModal').on('shown.bs.modal', function() {
			$(this).find('#menu_id').select2({
				dropdownParent: $('#addRoleMenuModal'),
			});
		});
	});
</script>

<?php if ($this->session->flashdata('SUCCESS_ADD_ROLE_ACCESS_MENU')): ?>
	<script>
		Swal.fire({
			title: "Success",
			text: "<?= $this->session->flashdata('SUCCESS_ADD_ROLE_ACCESS_MENU'); ?>",
			icon: "success"
		});
	</script>
<?php endif; ?>

<?php if ($this->session->flashdata('FAILED_ADD_ROLE_ACCESS_MENU')): ?>
	<script>
		Swal.fire({
			title: "Error",
			text: "<?= $this->session->flashdata('FAILED_ADD_ROLE_ACCESS_MENU'); ?>",
			icon: "error"
		});
	</script>
<?php endif; ?>

<?php if ($this->session->flashdata('SUCCESS_DELETE_ROLE_ACCESS_MENU')): ?>
	<script>
		Swal.fire({
			title: "Success",
			text: "<?= $this->session->flashdata('SUCCESS_DELETE_ROLE_ACCESS_MENU'); ?>",
			icon: "success"
		});
	</script>
<?php endif; ?>

<?php if ($this->session->flashdata('FAILED_DELETE_ROLE_ACCESS_MENU')): ?>
	<script>
		Swal.fire({
			title: "Error",
			text: "<?= $this->session->flashdata('FAILED_DELETE_ROLE_ACCESS_MENU'); ?>",
			icon: "error"
		});
	</script>
<?php endif; ?>

<?php if ($this->session->flashdata('SUCCESS_ADD_ROLE_ACCESS_SUBMENU')): ?>
	<script>
		Swal.fire({
			title: "Success",
			text: "<?= $this->session->flashdata('SUCCESS_ADD_ROLE_ACCESS_SUBMENU'); ?>",
			icon: "success"
		});
	</script>
<?php endif; ?>

<?php if ($this->session->flashdata('FAILED_ADD_ROLE_ACCESS_SUBMENU')): ?>
	<script>
		Swal.fire({
			title: "Error",
			text: "<?= $this->session->flashdata('FAILED_ADD_ROLE_ACCESS_SUBMENU'); ?>",
			icon: "error"
		});
	</script>
<?php endif; ?>

<?php if ($this->session->flashdata('success')): ?>
	<script>
		Swal.fire({
			title: "Success",
			text: "<?= $this->session->flashdata('success'); ?>",
			icon: "success"
		});
	</script>
<?php endif; ?>
