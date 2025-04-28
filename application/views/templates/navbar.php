<style>
	.hover:hover {
		cursor: pointer;
	}
</style>

<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">

	<div class="d-flex align-items-center justify-content-between">
		<a href="" class="logo d-flex align-items-center">
			<img src="<?= base_url('assets') ?>/img/valeo.png" alt="">
		</a>
		<i class="bi bi-list toggle-sidebar-btn"></i>
	</div><!-- End Logo -->


	<nav class="header-nav ms-auto">
		<ul class="d-flex align-items-center">
			<li class="nav-item dropdown px-2">

				<span class="nav-link nav-icon hover" data-bs-toggle="dropdown">
					<i class="bi bi-bell"></i>
					<span class="badge bg-primary badge-number">
						<?php
						$query = "SELECT DISTINCT Material_no, Material_name, Qty, Unit, Updated_at, Updated_by
							FROM 
								storage
							WHERE
								Material_no LIKE '%RW%'
							GROUP BY 
								material_no, material_name
							HAVING 
								(SUM(CASE WHEN transaction_type = 'IN' THEN Qty ELSE 0 END) - 
								SUM(CASE WHEN transaction_type = 'OUT' THEN Qty ELSE 0 END)) <= 25";
						$lowNotifCount = $this->db->query($query)->num_rows();
						$lowNotifStock = $this->db->query($query)->result_array();
						echo $lowNotifCount;
						?>
					</span>
				</span><!-- End Notification Icon -->

				<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications" style="max-height: 300px; overflow-y: auto; width: 350px">
					<li class="dropdown-header">
						<i class="bx bxs-error mx-2" style="color: red; font-size: 25px"></i>
						<span>Low Stock Raw Material</span>
					</li>
					<li>
						<hr class="dropdown-divider">
					</li>
					<?php foreach ($lowNotifStock as $ln) : ?>
						<li class="notification-item" style="padding: 8px">
							<i class="bx bx-file text-warning"></i>
							<div>
								<h6 style="font-size: 15px"><?= $ln['Material_no']; ?></h6>
								<p><?= $ln['Material_name']; ?></p>
								<p><?= $ln['Qty']; ?> <?= $ln['Unit']; ?></p>
								<div class="row">
									<p>
										<?= $ln['Updated_by']; ?>
										| <?= date_format(new DateTime($ln['Updated_at']), 'Y-m-d H:i:s') ?>
									</p>
								</div>
							</div>
						</li>

						<li>
							<hr class="dropdown-divider">
						</li>
					<?php endforeach; ?>
			</li>
		</ul><!-- End Notification Dropdown Items -->
		</li><!-- End Notification Nav -->

		<li class="nav-item dropdown pe-4">
			<span class="nav-link nav-profile d-flex align-items-center pe-0 hover" data-bs-toggle="dropdown">
				<img src="<?= base_url('assets'); ?>/img/Man.png" alt="Profile" class="rounded-circle">
				<span class="d-none d-md-block dropdown-toggle ps-2"><?= $this->session->userdata('username'); ?></span>
			</span><!-- End Profile Image Icon -->

			<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
				<li class="dropdown-header">
					<h6>
						<?= isset($name['Name']) ? $name['Name'] : $this->session->userdata('Email'); ?>
					</h6>
					<span>
						<?php
						$role_query = $this->db->get('user_role');
						$role_mapping = [];

						foreach ($role_query->result_array() as $role) {
							$role_mapping[$role['Id']] = $role['Name'];
						}

						if (isset($name['Name'])) {
							$role_id = $name['Role_id'];
							echo isset($role_mapping[$role_id]) ? $role_mapping[$role_id] : 'Unknown Role';
						} else {
							echo 'Unknown';
						}
						?>
					</span>
				</li>

				<li>
					<hr class="dropdown-divider">
				</li>

				<li>
					<a class="dropdown-item d-flex align-items-center" href="<?= base_url('user'); ?>">
						<i class="bi bi-person"></i>
						<span>My Profile</span>
					</a>
				</li>

				<li>
					<hr class="dropdown-divider">
				</li>

				<li>
					<hr class="dropdown-divider">
				</li>

				<li>
					<a class="dropdown-item d-flex align-items-center" href="<?= base_url('auth/logout'); ?>">
						<i class="bi bi-box-arrow-right"></i>
						<span>Sign Out</span>
					</a>
				</li>

			</ul><!-- End Profile Dropdown Items -->
		</li><!-- End Profile Nav -->

		</ul>
	</nav><!-- End Icons Navigation -->

</header><!-- End Header -->
