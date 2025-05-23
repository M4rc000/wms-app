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
<section>
	<div class="card">
		<div class="card-body">	
			<div class="container mx-2 mt-5">
				<form method="post" action="<?=base_url('admin/demand_forecast');?>">
					<div class="row mb-3">
						<label class="col-sm-2 col-form-label"><strong>Sample Month 1</strong></label>
						<div class="col-sm-4">
							<input type="month" class="form-control" name="sample1" id="sample1" required>
						</div>
					</div>

					<div class="row mb-3">
						<label class="col-sm-2 col-form-label"><strong>Sample Month 2</strong></label>
						<div class="col-sm-4">
							<input type="month" class="form-control" name="sample2" id="sample2" required>
						</div>
					</div>
					
					<div class="row mb-3">
						<label class="col-sm-2 col-form-label"><strong>Sample Month 3</strong></label>
						<div class="col-sm-4">
							<input type="month" class="form-control" name="sample3" id="sample3" required>
						</div>
					</div>
					
					<div class="row mb-3">
						<label class="col-sm-2 col-form-label"><strong>Target Month</strong></label>
						<div class="col-sm-4">
							<input type="month" class="form-control" name="target" id="target" required>
						</div>
					</div>
					<div class="row mt-2">
						<button type="submit" class="btn btn-primary col-12">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<script>
	$(document).ready(function(){
	});
</script>

<!-- SWEET ALERT  -->
<?php if ($this->session->flashdata('success_forecasting_stock')): ?>
	<script>
		Swal.fire({
			title: "Success",
			html: `<?= $this->session->flashdata('success_forecasting_stock'); ?>`,
			icon: "success",
		});
	</script>
<?php endif; ?>
<?php if ($this->session->flashdata('error_forecasting_stock')): ?>
	<script>
		Swal.fire({
			title: "Error",
			html: `<?= $this->session->flashdata('error_forecasting_stock'); ?>`,
			icon: "error",
		});
	</script>
<?php endif; ?>
