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

<section class="section">
	<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addModal" style="color: white">
		New user
	</button>
	<div class="row">

		<div class="col-lg-12">
			<div class="card">
				<div class="card-body table-responsive mt-2">
					<table class="table datatable">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-center">Name</th>
								<th class="text-center">Email</th>
								<th class="text-center">Role</th>
								<th class="text-center">Active</th>
								<th class="text-center">Update</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $number = 0;
							foreach ($users as $usr) : $number++ ?>
								<tr>
									<td class="text-center"><?= $number; ?></td>
									<td class="text-center"><?= $usr['Name']; ?></td>
									<td class="text-center"><?= $usr['Email']; ?></td>
									<td class="text-center">
										<?php
										foreach ($roles as $role) {
											if ($usr['Role_id'] == $role['Id']) {
												echo $role['Name'];
												break;
											}
										}
										?>
									</td>
									<td class="text-center"><?= $usr['Active'] == 1 ? '<i class="bx bxs-check-circle ps-4" style="color: #012970"></i>' : '<i class="bx bxs-x-circle ps-4" style="color: #012970"></i>'; ?></td>
									<td class="text-center"><?= $usr['Updated_at'] != '' ? date('d M Y H:i', strtotime($usr['Updated_at'])) : '-'; ?></td>
									<td class="text-center">
										<span class="badge bg-success badge-hover" data-bs-toggle="modal" data-bs-target="#editModal<?= $usr['Id']; ?>" style=":hover{cursor: pointer;}">
											<i class="bx bxs-edit" style="color: white;"></i>
										</span>
										<span class="badge bg-danger badge-hover" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $usr['Id']; ?>" style=":hover{cursor: pointer;}">
											<i class="bx bxs-trash"></i>
										</span>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
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
			<?= form_open_multipart('adminhead/AddUser'); ?>
			<div class="modal-header">
				<h5 class="modal-title">Add User</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<input type="text" class="form-control" id="user_id" name="user_id" style="display: none" value="<?=$user['Id'];?>">
				<div class="row ps-2">
					<div class="col-4">
						<label for="name" class="form-label">Name</label>
						<input type="text" class="form-control" id="name" name="name" required>
					</div>
					<div class="col-4">
						<label for="email" class="form-label">Email</label>
						<input type="text" class="form-control" id="email" name="email" required>
					</div>
					<div class="col-4">
						<label for="password" class="form-label">Password</label>
						<input type="password" class="form-control" id="password" name="password" required>
					</div>
				</div>
				<div class="row ps-2 mt-3">
					<div class="col-4">
						<label for="role" class="form-label">Role</label>
						<select id="role" class="form-select" required name="role">
							<?php foreach ($roles as $role): ?>
								<option value="<?= $role['Id']; ?>"><?= $role['Name']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-4">
						<label for="active" class="form-label">Active</label>
						<select id="active" class="form-select" required name="active">
							<option value="1">Active</option>
							<option value="0">Not active</option>
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

<!-- EDIT MODAL-->
<?php foreach ($users as $usr) : ?>
	<div class="modal fade editModal" id="editModal<?= $usr['Id']; ?>" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<?= form_open_multipart('adminhead/EditUser'); ?>
				<div class="modal-header">
					<h5 class="modal-title">Edit User</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<input type="text" class="form-control" id="user_id" name="user_id" value="<?= $user['Id']; ?>" hidden>
					<div class="row ps-2">
						<div class="col-4">
							<label for="name" class="form-label">Name</label>
							<input type="text" class="form-control" id="name" name="name" value="<?= $usr['Name']; ?>">
							<input type="text" class="form-control" id="id" name="id" value="<?= $usr['Id']; ?>" hidden>
						</div>
						<div class="col-4">
							<label for="email" class="form-label">Email</label>
							<input type="text" class="form-control" id="email" name="email" value="<?= $usr['Email']; ?>">
						</div>
						<div class="col-4">
							<label for="password" class="form-label">Password</label>
							<input type="password" class="form-control" id="password" name="password">
						</div>
					</div>
					<div class="row ps-2 mt-3">
						<div class="col-4">
							<label for="role" class="form-label">Role</label>
							<select id="role" class="form-select" name="role">
								<?php foreach ($roles as $role): ?>
									<option value="<?= $role['Id']; ?>" <?= $usr['Role_id'] == $role['Id'] ? 'selected' : ''; ?>><?= $role['Name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-4">
							<label for="active" class="form-label">Active</label>
							<select id="active" class="form-select" name="active">
								<option value="1" <?= $usr['Active'] == '1' ? 'selected' : ''; ?>>Active</option>
								<option value="0" <?= $usr['Active'] == '0' ? 'selected' : ''; ?>>Not active</option>
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
<?php endforeach; ?>

<!-- DELETE CONFIRM MODAL-->
<?php foreach ($users as $usr) : ?>
	<?= form_open_multipart('adminhead/deleteUser'); ?>
	<div class="modal fade" id="deleteModal<?= $usr['Id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title pb-0 mb-0" id="exampleModalLabel">Confirm to delete ?</h4>
				</div>
				<div class="modal-body">
					<input type="text" name="id" id="id" value="<?= $usr['Id']; ?>" style="display: none;">
					<p><b>Email</b> : <?= $usr['Email']; ?></p>
					<p><b>Name</b> : <?= $usr['Name']; ?></p>
					<p><b>Role</b> :
						<?php
						foreach ($roles as $role) {
							if ($usr['Role_id'] == $role['Id']) {
								echo $role['Name'];
								break;
							}
						}
						?>
					</p>
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

		// ADD MODAL
		$('#addModal').on('shown.bs.modal', function() {
			// SET DEFAULT IS EMPTY
			$(this).find('#name').val('');
			$(this).find('#username').val('');
			$(this).find('#password').val('');
			$(this).find('#role').val('');
			$(this).find('#active').val('');

			$(this).find('#role').select2({
				dropdownParent: $('#addModal'),
			});
			$(this).find('#active').select2({
				dropdownParent: $('#addModal'),
			});
		});

		// EDIT MODAL
		$('.editModal').on('shown.bs.modal', function() {
			$(this).find('#role').select2({
				dropdownParent: $(this),
			});
			$(this).find('#active').select2({
				dropdownParent: $(this),
			});
		});
	});
</script>

<!-- SWEET ALERT  -->
<?php if ($this->session->flashdata('SUCCESS_AddUser')): ?>
	<script>
		Swal.fire({
			title: "Success",
			html: `<?= $this->session->flashdata('SUCCESS_AddUser'); ?>`,
			icon: "success"
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_AddUser')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: `<?= $this->session->flashdata('FAILED_AddUser'); ?>`,
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('SUCCESS_EditUser')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Success",
				html: "<?= $this->session->flashdata('SUCCESS_EditUser'); ?>",
				icon: "success"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_EditUser')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: "<?= $this->session->flashdata('FAILED_EditUser'); ?>",
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('SUCCESS_deleteUser')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Success",
				html: "<?= $this->session->flashdata('SUCCESS_deleteUser'); ?>",
				icon: "success"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_deleteUser')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: "<?= $this->session->flashdata('FAILED_deleteUser'); ?>",
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
