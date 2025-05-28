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
	<div class="row">
		<div class="col-md-12">
			<div class="card border">
				<?= form_open_multipart('driver/editDeliveryStatus'); ?>
					<div class="row mt-2 mx-2">
						<div class="col-12">
							<div class="table-responsive">
								<table id="bomTable" class="table table-bordered">
									<thead>
										<tr>
											<th class="text-center">#</th>
											<th class="text-center">Product No</th>
											<th class="text-center">Product Name</th>
											<th class="text-center">Qty</th>
											<th class="text-center">Uom</th>
											<th class="text-center">Status</th>
											<th class="text-center">Driver ID</th>
                                            <th class="text-center">Delivery Date</th>
											<th class="text-center">Action</th>
										</tr>
									</thead>
									<input type="text" name="user_id" id="user_id" value="<?= $user['Id']; ?>" hidden>
									<tbody id="table-body"></tbody>
								</table>
							</div>
						</div>
					</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>



