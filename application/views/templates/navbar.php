<style>
	.hover:hover{
		cursor: pointer;
	}
</style>

<!-- ======= Header ======= -->
 <header id="header" class="header fixed-top d-flex align-items-center">

<div class="d-flex align-items-center justify-content-between">
  <a href="" class="logo d-flex align-items-center">
	<img src="<?=base_url('assets')?>/img/valeo.png" alt="">
  </a>
  <i class="bi bi-list toggle-sidebar-btn"></i>
</div><!-- End Logo -->


<nav class="header-nav ms-auto">
  <ul class="d-flex align-items-center">
	<li class="nav-item dropdown">

	  <span class="nav-link nav-icon hover" data-bs-toggle="dropdown">
		<i class="bi bi-bell"></i>
		<span class="badge bg-primary badge-number">
			<!-- Count Notification -->
		</span>
	  </span><!-- End Notification Icon -->

	  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications" style="width: 350px">
	  	<li class="dropdown-header">
		  Material Request
		</li>
	  	
	  </ul><!-- End Notification Dropdown Items -->
	</li><!-- End Notification Nav -->

	<li class="nav-item dropdown">

	  <span class="nav-link nav-icon hover" data-bs-toggle="dropdown">
		<i class="bi bi-calendar2"></i>
		<span class="badge bg-primary badge-number">
		</span>
	  </span><!-- End Notification Icon -->

	  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications" style="width: 350px">
	  	<li class="dropdown-header">
		  Return Request
		</li>
	  	
	  </ul><!-- End Notification Dropdown Items -->
	</li><!-- End Notification Nav -->

	<li class="nav-item dropdown">

	  <span class="nav-link nav-icon hover" data-bs-toggle="dropdown">
		<i class="bi bi-chat-left-text"></i>
		<span class="badge bg-success badge-number"></span>
	  </span><!-- End Messages Icon -->

	  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
		<!-- <li class="dropdown-header">
		  You have 3 new messages
		  <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
		</li>
		<li>
		  <hr class="dropdown-divider">
		</li>

		<li class="message-item">
		  <a href="#">
			<img src="<?=base_url('assets');?>/img/messages-1.jpg" alt="" class="rounded-circle">
			<div>
			  <h4>Maria Hudson</h4>
			  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
			  <p>4 hrs. ago</p>
			</div>
		  </a>
		</li>
		<li>
		  <hr class="dropdown-divider">
		</li>

		<li class="message-item">
		  <a href="#">
			<img src="<?=base_url('assets');?>/img/messages-2.jpg" alt="" class="rounded-circle">
			<div>
			  <h4>Anna Nelson</h4>
			  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
			  <p>6 hrs. ago</p>
			</div>
		  </a>
		</li>
		<li>
		  <hr class="dropdown-divider">
		</li>

		<li class="message-item">
		  <a href="#">
			<img src="<?=base_url('assets');?>/img/messages-3.jpg" alt="" class="rounded-circle">
			<div>
			  <h4>David Muldon</h4>
			  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
			  <p>8 hrs. ago</p>
			</div>
		  </a>
		</li>
		<li>
		  <hr class="dropdown-divider">
		</li>

		<li class="dropdown-footer">
		  <a href="#">Show all messages</a>
		</li> -->

	  </ul><!-- End Messages Dropdown Items -->

	</li><!-- End Messages Nav -->

	<li class="nav-item dropdown pe-4">

	  <span class="nav-link nav-profile d-flex align-items-center pe-0 hover" data-bs-toggle="dropdown">
		<img src="<?=base_url('assets');?>/img/Man.png" alt="Profile" class="rounded-circle">
		<span class="d-none d-md-block dropdown-toggle ps-2"><?=$this->session->userdata('username');?></span>
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
		  <a class="dropdown-item d-flex align-items-center" href="<?=base_url('user');?>">
			<i class="bi bi-person"></i>
			<span>My Profile</span>
		  </a>
		</li>
		
		<li>
		  <hr class="dropdown-divider">
		</li>

		<li>
		  <a class="dropdown-item d-flex align-items-center" href="<?=base_url('user/change_password');?>">
			<i class="bx bx-lock"></i>
			<span>Change password</span>
		  </a>
		</li>

		<li>
		  <hr class="dropdown-divider">
		</li>
		
		<li>
		  <a class="dropdown-item d-flex align-items-center" href="<?=base_url('auth/logout');?>">
			<i class="bi bi-box-arrow-right"></i>
			<span>Sign Out</span>
		  </a>
		</li>

	  </ul><!-- End Profile Dropdown Items -->
	</li><!-- End Profile Nav -->

  </ul>
</nav><!-- End Icons Navigation -->

</header><!-- End Header -->
