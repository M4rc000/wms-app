<section>
	<div class="card">
		<div class="card-body">	
			<div class="container mx-2 mt-5">
				<form method="post" action="<?=base_url('master/new_raw_material');?>">
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
