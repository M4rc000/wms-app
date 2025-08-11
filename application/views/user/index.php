<section class="section profile">
	<div class="row">
		<?= $this->session->flashdata('message'); ?>
		<div class="col-xl-4">

			<div class="card">
				<div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

					<img src="<?= !empty($user['Image']) ? base_url('assets/img/profiles/' . $user['Image']) : base_url('assets/img/Man.png'); ?>" alt="Profile" class="rounded-circle">
					<h2><?= ($user['Name']); ?></h2>
					<h3>
						<?php
						$role_query = $this->db->get('user_role');
						$role_mapping = [];

						foreach ($role_query->result_array() as $role) {
							$role_mapping[$role['Id']] = $role['Name'];
						}

						echo 'Admin Head';
						?>
					</h3>
					<div class="social-links mt-2">
					</div>
				</div>
			</div>

		</div>

		<div class="col-xl-8">

			<div class="card">
				<div class="card-body pt-3">
					<!-- Bordered Tabs -->
					<ul class="nav nav-tabs nav-tabs-bordered">

						<li class="nav-item">
							<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
						</li>

						<li class="nav-item">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
						</li>

					</ul>
					<div class="tab-content pt-2">

						<div class="tab-pane fade show active profile-overview" id="profile-overview">

							<h5 class="card-title">Profile Details</h5>

							<div class="row">
								<div class="col-lg-3 col-md-4 label ">Name</div>
								<div class="col-lg-9 col-md-8"><?= ($user['Name']); ?></div>
							</div>
							
							<div class="row">
								<div class="col-lg-3 col-md-4 label">Company</div>
								<div class="col-lg-9 col-md-8">Cahaya Karomah Cemerlang</div>
							</div>

							<div class="row">
								<div class="col-lg-3 col-md-4 label">Email</div>
								<div class="col-lg-9 col-md-8"><?= ($user['Email']); ?></div>
							</div>

						</div>

						<div class="tab-pane fade profile-edit pt-3" id="profile-edit">

							<!-- Profile Edit Form -->
							<form method="post" action="<?= base_url('user/update_profile'); ?>" enctype="multipart/form-data">
								<div class="row">
									<div class="col-md-6">
										<h5 class="card-title">Profile Image</h5>
										<div class="text-center mb-4">
											<img id="profilePreview" src="<?= !empty($user['Image']) ? base_url('assets/img/profiles/' . $user['Image']) : base_url('assets/img/Man.png'); ?>" alt="Profile" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
											<div class="pt-2">
												<input type="file" id="profileImage" name="profileImage" accept="image/*" style="display: none;">
												<button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('profileImage').click();">
													<i class="bi bi-upload"></i>
												</button>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<h5 class="card-title">Full Name</h5>
										<div class="mb-3">
											<input name="fullName" type="text" class="form-control" id="fullName" value="<?= ($user['Name']); ?>" placeholder="Enter your full name">
										</div>
									</div>
								</div>


								<div class="text-center mt-4">
									<button type="submit" class="btn btn-primary">Save Changes</button>
								</div>
							</form><!-- End Profile Edit Form -->

							<script>
							document.getElementById('profileImage').addEventListener('change', function(event) {
								const file = event.target.files[0];
								if (file) {
									// Validate file type
									const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
									if (!allowedTypes.includes(file.type)) {
										alert('Please select a valid image file (JPEG, JPG, PNG, or GIF)');
										this.value = '';
										return;
									}
									
									// Validate file size (2MB)
									if (file.size > 2 * 1024 * 1024) {
										alert('File size must be less than 2MB');
										this.value = '';
										return;
									}
									
									// Show preview
									const reader = new FileReader();
									reader.onload = function(e) {
										document.getElementById('profilePreview').src = e.target.result;
									};
									reader.readAsDataURL(file);
								}
							});
							</script>

						</div>

						<div class="tab-pane fade pt-3" id="profile-settings">

							<!-- Settings Form -->
							<form>

								<div class="row mb-3">
									<label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email Notifications</label>
									<div class="col-md-8 col-lg-9">
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="changesMade" checked>
											<label class="form-check-label" for="changesMade">
												Changes made to your account
											</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="newProducts" checked>
											<label class="form-check-label" for="newProducts">
												Information on new products and services
											</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="proOffers">
											<label class="form-check-label" for="proOffers">
												Marketing and promo offers
											</label>
										</div>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" id="securityNotify" checked disabled>
											<label class="form-check-label" for="securityNotify">
												Security alerts
											</label>
										</div>
									</div>
								</div>

								<div class="text-center">
									<button type="submit" class="btn btn-primary">Save Changes</button>
								</div>
							</form><!-- End settings Form -->

						</div>

						<div class="tab-pane fade pt-3" id="profile-change-password">
							<!-- Change Password Form -->
							<form>

								<div class="row mb-3">
									<label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
									<div class="col-md-8 col-lg-9">
										<input name="password" type="password" class="form-control" id="currentPassword">
									</div>
								</div>

								<div class="row mb-3">
									<label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
									<div class="col-md-8 col-lg-9">
										<input name="newpassword" type="password" class="form-control" id="newPassword">
									</div>
								</div>

								<div class="row mb-3">
									<label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
									<div class="col-md-8 col-lg-9">
										<input name="renewpassword" type="password" class="form-control" id="renewPassword">
									</div>
								</div>

								<div class="text-center">
									<button type="submit" class="btn btn-primary">Change Password</button>
								</div>
							</form><!-- End Change Password Form -->

						</div>

					</div><!-- End Bordered Tabs -->

				</div>
			</div>

		</div>
	</div>
</section