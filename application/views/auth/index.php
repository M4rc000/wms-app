<style>
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        height: 100% !important;
        overflow-x: hidden !important;
        width: 100% !important;
    }
    .container {
        max-width: none !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .card {
         max-width: 400px !important;
         width: 100% !important;
     }
    .card-body {
        padding: 0.75rem !important;
    }
</style>
<section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4" style="background-image: url('<?=base_url('assets');?>/img/Background-CKC.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed; position: absolute; top: 0; left: 0; width: 100vw; height: 100vh; margin: 0; padding: 0;">
	<div class="container">
		<div class="row justify-content-center">
		<div class="col-lg-4 col-md-5 d-flex flex-column align-items-center justify-content-center">

<div class="card mb-3">
				<div class="card-body">
					<div class="d-flex justify-content-center pt-2 pb-1">
						<img src="<?=base_url('assets');?>/img/CKC.png" alt="CKC Logo" style="width: 140px !important; height: 60px !important; object-fit: contain; filter: contrast(1.1) brightness(1.05);">
					</div>
					<div class="pt-0 pb-2">
					<h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
					<p class="text-center small">Enter your email & password</p>
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
							<div class="position-relative">
								<input type="password" name="password" class="form-control" id="yourPassword" required style="padding-right: 40px;">
								<i class="bi bi-eye position-absolute" id="yourPassword-icon" onclick="togglePassword('yourPassword')" style="right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d;"></i>
							</div>
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

<script>
document.addEventListener('DOMContentLoaded', function(){
	const icon = document.getElementById('yourPassword-icon');
	const input = document.getElementById('yourPassword');

	if (!icon || !input) return;

	icon.addEventListener('click', function(){
		const isPwd = input.type === 'password';
		input.type = isPwd ? 'text' : 'password';
		icon.classList.toggle('bi-eye');
		icon.classList.toggle('bi-eye-slash');
	});
});
</script>

