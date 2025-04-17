<aside id="sidebar" class="sidebar">
	<ul class="sidebar-nav" id="sidebar-nav">

		<!-- QUERY MENU -->
		<?php
		$role_id = $this->session->userdata('role_id');
		$queryMenu = "SELECT `user_menu`.`Id`, `Name`
            FROM `user_menu` JOIN `user_access_menu`
            ON `user_menu`.`Id` = `user_access_menu`.`Menu_id`
            WHERE `user_access_menu`.`Role_id` = $role_id
            AND `Name` != 'User'
            ORDER BY `user_access_menu`.`Menu_id` ASC";
		$menu = $this->db->query($queryMenu)->result_array();
		?>

		<!-- LOOPING MENU -->
		<?php foreach ($menu as $m) : ?>
			<hr>
			<li class="nav-heading"><?= $m['Name']; ?></li>

			<!-- SIAPKAN SUB-MENU SESUAI MENU -->
			<?php
			$menuId = $m['Id'];
			$querySubMenu = "SELECT um.Id as menu_id, um.Name as menu_name, usm.Id, usm.Name, usm.Menu_id, usm.Url, usm.Icon, usm.Active FROM `user_sub_menu` AS usm
								JOIN `user_menu` AS um ON usm.Menu_id = um.Id
								JOIN `user_access_submenu` ON usm.Id = `user_access_submenu`.`Submenu_id`
									WHERE usm.Menu_id = $menuId
									AND `user_access_submenu`.`Role_id` = $role_id
									AND usm.Active = 1";
			$subMenu = $this->db->query($querySubMenu)->result_array();
			?>

			<?php foreach ($subMenu as $sm) : ?>
				<li class="nav-item">
					<a class="nav-link collapsed" href="<?= base_url($sm['Url']); ?>">
						<i class="<?= $sm['Icon']; ?>"></i>
						<span>&nbsp;&nbsp;&nbsp;<?= $sm['Name']; ?></span>
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
					<?php
						// Get the request path and trim leading/trailing slashes
						$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
						$segments = explode('/', trim($path, '/'));

						// Extract the last two segments of the URL dynamically.
						$desiredSegments = array_slice($segments, -2);

						// Optional: check if you indeed have two segments
						$module = isset($desiredSegments[0]) ? ucfirst(strtolower($desiredSegments[0])) : '';
						$page   = isset($desiredSegments[1]) ? ucfirst(strtolower($desiredSegments[1])) : '';
					?>
					<a><?= $module; ?></a>
				</li>
				<li class="breadcrumb-item active"><?= $title; ?></li>
			</ol>
		</nav>
	</div><!-- End Page Title -->
