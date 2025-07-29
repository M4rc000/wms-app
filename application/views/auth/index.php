<section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
	<div class="container">
		<div class="row justify-content-center">
		<div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

			<div class="d-flex justify-content-center py-4">
			<a class="logo d-flex align-items-center w-auto"> 
				<img src="<?=base_url('assets');?>/img/CKC.png">
			</a>
			</div><!-- End Logo -->

			<div class="card mb-3">
				<div class="card-body">

					<div class="pt-3 pb-2">
					<h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
					<p class="text-center small">Enter your email & password</p>
					<br>
					</div>
					<?php if ($this->session->flashdata('logout') != '') { ?>
						<?= $this->session->flashdata('logout'); ?>
					<?php } ?>
					<?php if ($this->session->flashdata('not_active_email') != '') { ?>
						<?= $this->session->flashdata('not_active_email'); ?>
					<?php } ?>
					<?php if ($this->session->flashdata('wrong_password') != '') { ?>
						<?= $this->session->flashdata('wrong_password'); ?>
					<?php } ?>
					<form class="row g-3 needs-validation" method="post" action="<?=base_url('auth/index')?>">
						<div class="col-12">
							<label for="email" class="form-label">Email</label>
							<div class="input-group has-validation">
								<input type="email" name="email" class="form-control" id="email" required>
								<div class="invalid-feedback">Please enter your email.</div>
							</div>
						</div>
						<div class="col-12">
							<label for="yourPassword" class="form-label">Password</label>
							<input type="password" name="password" class="form-control" id="yourPassword" required>
							<div class="invalid-feedback">Please enter your password!</div>
						</div>
						<div class="col-12">
							<button class="btn btn-primary w-100" type="submit">Login</button>
							<p></p>
							<p class="text-center" style="font-size: 13px;">v1.0.0</p>
						</div>
					</form>
				</div>
			</div>
		</div>
		</div>
	</div>

</section>
