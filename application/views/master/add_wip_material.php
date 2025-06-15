<style>
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
				<form method="post" action="<?=base_url('master/new_wip_material');?>">
					<div class="row mb-3">
						<label class="col-sm-2 col-form-label"><strong>Material Name</strong></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="Material_name" id="Material_name" required>
						</div>
					</div>					
					
					<div class="row mb-3">
						<label class="col-sm-2 col-form-label"><strong>Unit</strong></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="Unit" id="Unit" required>
						</div>
					</div>	

					<div class="row mb-3">
						<label class="col-sm-2 col-form-label"><strong>Client</strong></label>
						<div class="col-sm-4">
							<select id="Client" class="form-select" required name="Client">
								<?php foreach ($clients as $client) : ?>
									<option value="<?= $client['Id']; ?>">
										<?= $client['Name']; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>					
					<div class="d-block row mt-2">
						<div class="col-md-2 ms-auto">
							<button type="submit" class="btn btn-primary col-12">Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<script>
	$(document).ready(function(){
		$('#Client').select2();
	});
</script>