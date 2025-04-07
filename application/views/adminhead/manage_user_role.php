<link rel="stylesheet" href="<?=base_url('assets');?>/vendor/datatables/datatables.css">
<section class="section">
	<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addModal" style="color: white">New Role</button>
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-body table-responsive">
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Role</th>
                    <th>CrtDt</th>
                    <th>CrtBy</th>
                    <th>UpdDt</th>
                    <th>UpdBy</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
			<?php $number = 0; foreach($roles as $role) : $number++?>
                  <tr>
                    <td><?=$number;?></td>
                    <td><?=$role['id'];?></td>
                    <td><?=$role['role'];?></td>
                    <td><?=$role['crtdt'];?></td>
                    <td><?=$role['crtby'];?></td>
                    <td><?=$role['upddt'];?></td>
                    <td><?=$role['updby'];?></td>
					<td>
						<a href="<?= base_url('admin/roleAccess/' . $role['id']); ?>">
							<button class="btn btn-warning">
								<i class="bx bxs-wrench" style="color: white;"></i>
							</button>
						</a>
						<button class="btn btn-success ms-1" data-bs-toggle="modal" data-bs-target="#editModal<?=$role['id'];?>">
							<i class="bx bxs-edit" style="color: white;"></i>
						</button>
						<button class="btn btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#DeleteConfirmModal<?=$role['id'];?>">
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
	<?= form_open_multipart('admin/AddRole'); ?>
		<div class="modal-header">
			<h5 class="modal-title">Add Role</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<div class="modal-body">
			<!-- GET USER -->
			<input type="text" class="form-control" id="user" name="user" value="<?=$name['username'];?>" hidden>
			<div class="row ps-2">
				<div class="col-4">
					<label for="id" class="form-label">ID</label>
					<input type="text" class="form-control" id="id" name="id" value="<?=intval($lastRoleId[0]['id'])+1;?>" readonly>
				</div>
				<div class="col-4">
					<label for="role" class="form-label">Role</label>
					<input type="text" class="form-control" id="role" name="role" required>
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

<!-- CONFIG MODAL -->
<?php foreach($roles as $role) : ?>
	<div class="modal fade" id="configModal<?=$role['id'];?>" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<?= form_open_multipart('admin/UpdateConfigRole'); ?>
					<div class="modal-header">
						<h5 class="modal-title"><b>Configuration Role</b> : <?=$role['role'];?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<!-- GET USER -->
						<input type="text" class="form-control" id="user" name="user" value="<?=$name['username'];?>" hidden>
						<!-- GET ROLE -->
						<input type="text" class="form-control" id="role" name="role" value="<?=$role['id'];?>" hidden>
						<div class="row mt-4">
							<div class="col">
								<table class="table" id="table-access-<?=$role['id'];?>">
									<thead>
										<tr>
											<th scope="col">#</th>
											<th scope="col">Menu</th>
											<th scope="col">Submenu</th>
											<th scope="col">Access</th>
										</tr>
									</thead>
									<tbody>
										<?php $number = 0; foreach($mensub as $ms) : $number++ ?>
										<tr>
											<th scope="row"><?= $number; ?></th>
											<td>
												<?= $ms['menu']; ?>
												<input type="hidden" name="menu_ids[]" value="<?= $ms['menu_id']; ?>">
											</td>
											<td>
												<?= $ms['title']; ?>
												<input type="hidden" name="submenu_ids[]" value="<?= $ms['submenu_id']; ?>">
											</td>
											<td class="text-center">
												<?php
													$role_id = $role['id']; 
													$menu_id = $ms['menu_id'];
													$submenu_id = $ms['submenu_id'];
													$checked = check_access_submenu($role_id, $menu_id, $submenu_id);
												?>
												<input type="checkbox" name="sub_menu[<?=$submenu_id;?>]" id="sub_menu_<?=$submenu_id;?>" <?=$checked;?>>
												<input type="hidden" name="all_sub_menus[]" value="<?=$submenu_id;?>">
											</td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>    
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

<!-- EDIT MODAL-->
<?php foreach($roles as $role) : ?>
	<div class="modal fade" id="editModal<?=$role['id'];?>" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
		<?= form_open_multipart('admin/editRole'); ?>
			<!-- GET USER -->
			<input type="text" class="form-control" id="user" name="user" value="<?=$name['username'];?>" hidden>
			<div class="modal-header">
				<h5 class="modal-title">Edit Role</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row ps-2">
					<div class="col-4">
						<label for="id" class="form-label">ID</label>
						<input type="text" class="form-control" id="id" name="id" value="<?=$role['id'];?>" readonly>
					</div>
					<div class="col-4">
						<label for="role" class="form-label">Role</label>
						<input type="text" class="form-control" id="role" name="role" value="<?=$role['role'];?>" required>
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
<?php foreach($roles as $role) : ?>
	<?= form_open_multipart('admin/deleteRole'); ?>
		<div class="modal fade" id="DeleteConfirmModal<?= $role['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<!-- GET USER -->
				<input type="text" class="form-control" id="user" name="user" value="<?=$name['username'];?>" hidden>
				<div class="modal-header">
					<h4 class="modal-title pb-0 mb-0" id="exampleModalLabel">Confirm to delete Role?</h4>
				</div>
				<p class="px-2 mt-2">Role:  <?= $role['role']; ?> </p>
				<input type="text" name="id" id="id" value="<?= $role['id']; ?>" style="display: none;">
				<input type="text" name="role" id="role" value="<?= $role['role']; ?>" style="display: none;">
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary" name="delete_user">Confirm</button>
				</div>
				</div>
			</div>
		</div>
	</form>
<?php endforeach; ?>

<script src="<?=base_url('assets');?>/vendor/datatables/datatables.js"></script>
<script>
    $(document).ready(function (){
        $('.modal').on('shown.bs.modal', function () {
            let modalId = $(this).attr('id');
            $('#table-access-' + modalId.replace('configModal', '')).DataTable({
				"pageLength": 5
			});
        });
    });
</script>



<!-- SWEET ALERT -->
<?php if ($this->session->flashdata('SUCCESS_addRole')): ?>
    <script>
        Swal.fire({
            title: "Success",
            html: `<?=$this->session->flashdata('SUCCESS_addRole');?>`,
            icon: "success"
        });
    </script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_addRole')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: "Error",
                html: `<?=$this->session->flashdata('FAILED_addRole');?>`,
                icon: "error"
            });
        });
    </script>
<?php endif; ?>
<?php if ($this->session->flashdata('SUCCESS_updateRole')): ?>
    <script>
        Swal.fire({
            title: "Success",
            html: `<?=$this->session->flashdata('SUCCESS_updateRole');?>`,
            icon: "success"
        });
    </script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_updateRole')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: "Error",
                html: `<?=$this->session->flashdata('FAILED_updateRole');?>`,
                icon: "error"
            });
        });
    </script>
<?php endif; ?>
<?php if ($this->session->flashdata('SUCCESS_deleteRole')): ?>
    <script>
        Swal.fire({
            title: "Success",
			html: `Role <b><?=$this->session->flashdata('SUCCESS_deleteRole');?></b> has successfully deleted`,
            icon: "success"
        });
    </script>
<?php endif; ?>
<?php if ($this->session->flashdata('FAILED_deleteRole')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: "Error",
                html: `Role <b><?=$this->session->flashdata('FAILED_deleteRole');?></b> has successfully deleted`,
                icon: "error"
            });
        });
    </script>
<?php endif; ?>