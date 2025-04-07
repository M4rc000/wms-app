<section class="section">
	<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addModal" style="color: white">
		New menu
	</button>
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-body table-responsive mt-2">
					<table class="table datatable">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-center">ID</th>
								<th class="text-center">Menu</th>
								<th class="text-center">Created Date</th>
								<th class="text-center">Update Date</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php $number = 0;
							foreach ($menus as $menu) : $number++ ?>
								<tr class="text-center">
									<td><?= $number; ?></td>
									<td><?= $menu['Id']; ?></td>
									<td><?= $menu['Name']; ?></td>
									<td><?= date('d M Y H:i', strtotime($menu['Created_at'])); ?></td>
									<td><?= date('d M Y H:i', strtotime($menu['Updated_at'])); ?></td>
									<td>
										<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editModal<?= $menu['Id']; ?>">
											<i class="bx bxs-edit" style="color: white;"></i>
										</button>
										<button class="btn btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $menu['Id']; ?>">
											<i class="bx bxs-trash"></i>
										</button>
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
			<?= form_open_multipart('adminhead/AddMenu'); ?>
			<div class="modal-header">
				<h5 class="modal-title">Add Menu</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<!-- GET USER ID -->
				<input type="text" class="form-control" id="user_id" name="user_id" value="<?= $user['Id']; ?>" hidden>
				<div class="row ps-2">
					<div class="col-4">
						<label for="id" class="form-label">Menu ID</label>
						<input type="text" class="form-control" id="id" name="id" readonly value="<?= intval($lastMenuId['Id'] ?? 0) + 1; ?>">
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

<!-- EDIT MODAL-->
<?php foreach ($menus as $menu) : ?>
	<div class="modal fade" id="editModal<?=$menu['Id'];?>" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<?= form_open_multipart('adminhead/EditMenu'); ?>
				<div class="modal-header">
					<h5 class="modal-title">Edit Menu</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<!-- GET USER -->
					<input type="text" class="form-control" id="user_id" name="user_id" value="<?= $user['Id']; ?>" hidden>
					<div class="row ps-2">
						<div class="col-4">
							<label for="menu" class="form-label">ID</label>
							<input type="text" class="form-control" id="id" name="id" value="<?= $menu['Id']; ?>">
						</div>
						<div class="col-4">
							<label for="menu" class="form-label">Menu</label>
							<input type="text" class="form-control" id="menu" name="menu" value="<?= $menu['Name']; ?>">
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
<?php foreach ($menus as $menu) : ?>
	<?= form_open_multipart('adminhead/DeleteMenu'); ?>
	<div class="modal fade" id="deleteModal<?= $menu['Id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title pb-0 mb-0" id="exampleModalLabel">Confirm to delete ?</h4>
				</div>
				<div class="modal-body">
					<input type="text" name="user_id" id="user_id" value="<?= $user['Id']; ?>" style="display: none">
					<input type="text" name="id" id="id" value="<?= $menu['Id']; ?>" style="display: none">
					<p><b>ID</b> : <?= $menu['Id']; ?></p>
					<p><b>Menu</b> : <?= $menu['Name']; ?></p>
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



<!-- SWEET ALERT -->
<?php if ($this->session->flashdata('SUCCESS_AddMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Success",
				html: "<?= $this->session->flashdata('SUCCESS_AddMenu'); ?>",
				icon: "success"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_AddMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: "<?= $this->session->flashdata('FAILED_AddMenu'); ?>",
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('SUCCESS_editMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Success",
				html: "<?= $this->session->flashdata('SUCCESS_editMenu'); ?>",
				icon: "success"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_editMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: "<?= $this->session->flashdata('FAILED_editMenu'); ?>",
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('SUCCESS_deleteMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Success",
				html: "<?= $this->session->flashdata('SUCCESS_deleteMenu'); ?>",
				icon: "success"
			});
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_deleteMenu')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: "<?= $this->session->flashdata('FAILED_deleteMenu'); ?>",
				icon: "error"
			});
		});
	</script>
<?php endif; ?>
