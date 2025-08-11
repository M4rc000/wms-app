<style>
	.navitem-active{
		border-bottom: 3px solid #4154f1; 
		box-shadow: 0 4px 8px rgba(65, 84, 241, 0.3); 
		border-radius: 5px;
	}

	.submenu-active{
		color:#4154f1
	}

	.sidebar-nav .nav-link {
	transition: all 0.3s ease;
	position: relative;
	overflow: hidden;
	}

	.sidebar-nav .nav-link:before {
	content: '';
	position: absolute;
	left: 0;
	bottom: 0;
	height: 3px;
	width: 0;
	background-color: #4154f1;
	transition: width 0.3s ease;
	}

	.sidebar-nav .nav-link:hover:before,
	.sidebar-nav .nav-link.active:before {
	width: 100%;
	}

	.sidebar-nav .nav-link:hover,
	.sidebar-nav .nav-link.active {
	color: #4154f1 !important;
	transform: translateY(-2px);
	box-shadow: 0 4px 8px rgba(65, 84, 241, 0.3);
	}

	.sidebar-nav .nav-link:hover i,
	.sidebar-nav .nav-link.active i {
	color: #4154f1 !important;
	transform: scale(1.2);
	}

</style>
<aside id="sidebar" class="sidebar">
	<ul class="sidebar-nav" id="sidebar-nav">

		<!-- QUERY MENU -->
		<?php
		$role_id = $this->session->userdata('role_id'); 
		$queryMenu = "SELECT user_menu.Id, Name
            FROM user_menu JOIN user_access_menu
            ON user_menu.Id = user_access_menu.Menu_id
            WHERE user_access_menu.Role_id = $role_id
            AND Name != 'User'
            ORDER BY user_access_menu.Menu_id ASC";
		$menu = $this->db->query($queryMenu)->result_array();
		?>

		<!-- LOOPING MENU -->
		<?php foreach ($menu as $m) : ?>
			<hr>
			<li class="nav-heading"><?= $m['Name']; ?></li>

			<!-- SIAPKAN SUB-MENU SESUAI MENU -->
			<?php
			$menuId = $m['Id'];
			$querySubMenu = "SELECT um.Id as menu_id, um.Name as menu_name, usm.Id, usm.Name, usm.Menu_id, usm.Url, usm.Icon, usm.Active FROM user_sub_menu AS usm
								JOIN user_menu AS um ON usm.Menu_id = um.Id
								JOIN user_access_submenu ON usm.Id = user_access_submenu.Submenu_id
									WHERE usm.Menu_id = $menuId
									AND user_access_submenu.Role_id = $role_id
									AND usm.Active = 1";
			$subMenu = $this->db->query($querySubMenu)->result_array();
			?>

			<?php foreach ($subMenu as $sm) : ?>
				<li class="nav-item">
					<a class="nav-link collapsed <?= $title == $sm['Name'] ? 'navitem-active' : ''; ?>" href="<?= base_url($sm['Url']); ?>">
						<i class="<?= $sm['Icon']; ?>" class="<?= $title == $sm['Name'] ? 'submenu-active' : ''; ?>"></i>
						<span class="<?= $title == $sm['Name'] ? 'submenu-active' : ''; ?>"><?= $sm['Name']; ?></span>
					</a>
				</li><!-- End Profile Page Nav -->
			<?php endforeach; ?>
		<?php endforeach; ?>
	</ul>
</aside><!-- End Sidebar-->

<main id="main" class="main">
	<div class="pagetitle">
		<h1><?= $title; ?></h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a><?= ucfirst(strtolower(explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'))[2])); ?></a>
				</li>
				<li class="breadcrumb-item active"><?= $title; ?></li>
			</ol>
		</nav>
	</div><!-- End Page Title -->