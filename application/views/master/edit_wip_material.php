<section>
	<div class="card">
		<div class="card-body">	
			<div class="container mx-2 mt-5">
				<form method="post" action="<?=base_url('master/update_wip_material');?>">
					<div class="row mb-3">
						<!-- <input type="hidden" class="form-control" name="id" id="id" value="<?=$materials[0]['Id'];?>" required readonly> -->
						<label class="col-sm-2 col-form-label"><strong>Material No</strong></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="Material_no" id="Material_no" value="<?=$materials[0]['Material_no'];?>" required>
						</div>
					</div>
					
					<div class="row mb-3">
						<label class="col-sm-2 col-form-label"><strong>Material Name</strong></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="Material_name" id="Material_name" value="<?=$materials[0]['Material_name'];?>" required>
						</div>
					</div>					
					
					<div class="row mb-3">
						<label class="col-sm-2 col-form-label"><strong>Unit</strong></label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="Unit" id="Unit" value="<?=$materials[0]['Unit'];?>" required>
						</div>
					</div>					
					<div class="row mt-2">
						<button type="submit" class="btn btn-primary col-12">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<script>
  $(document).ready(function() {
    // Simpan nilai asli dari input
    var originalValue = $("#Material_no").val();

    // Mencegah user mengubah nilai melalui keyboard atau pengeditan DOM lain
    $("#Material_no").on("input change", function() {
      if ($(this).val() !== originalValue) {
        $(this).val(originalValue);
      }
    });

    // Mengunci atribut readonly kembali jika dihapus
    var observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === "attributes" && mutation.attributeName === "readonly") {
          if (!$("#Material_no").prop("readonly")) {
            $("#Material_no").prop("readonly", true);
          }
        }
      });
    });

    observer.observe(document.getElementById("Material_no"), {
      attributes: true
    });
  });
</script>