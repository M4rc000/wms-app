<section>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header"> <a href="<?=base_url('master/add_raw_material')?>"
						style="text-decoration: none"><button class="btn btn-primary">New Material</button></a> </div>
				<div class="card-body pt-3">
					<table class="table" id="tbl-report-raw">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-left">Material No</th>
								<th class="text-left">Material Name</th>
								<th class="text-center">Uom</th>
								<th class="text-center">Active</th>
							</tr>
						</thead>
						<tbody class="tbody-report-raw"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<form id="form-delete" method="post" style="display:none;">
    	<input type="hidden" name="id">
	</form>
</section> 

<!-- SPINNER LOADING -->
<div class="spinner-container" id="spinner-container">
	<div class="spinner-grow text-success" role="status"> <span class="visually-hidden">Loading...</span> </div>
	<div class="spinner-grow text-success" role="status"> <span class="visually-hidden">Loading...</span> </div>
	<div class="spinner-grow text-success" role="status"> <span class="visually-hidden">Loading...</span> </div>
</div>

<script src="<?= base_url('assets'); ?>/js/functions.js"></script>
<script>
    $(document).ready(function() {
        $('#spinner-container').show();

        $.ajax({
            url: '<?= base_url('master/load_raw_material'); ?>',
            type: 'get',
            dataType: 'json',
            data: {},
            success: function(res) {
                $('#spinner-container').hide();

                var $tbody = $('.tbody-report-raw');

                // Loop over the data and build table rows
                $.each(res, function(index, material) {
                    var row = `<tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-start">${material.Material_no}</td>
                        <td class="text-left">${material.Material_name}</td>
                        <td class="text-center">${material.Unit}</td>
                        <td class="text-center">
                            <span class="badge bg-warning">
                                <a class="text-white" href="<?=base_url('master/edit_raw_material/');?>${material.id}" style="text-decoration: none"><i class="bx bxs-edit"></i></a>
                            </span>
                            <span class="badge bg-danger">
                                <!-- Perhatikan atribut data: gunakan data-materialname tanpa underscore -->
                                <a class="text-white" id="btn-delete-raw" href="<?=base_url('master/delete_raw_material/');?>${material.id}" style="text-decoration: none" data-id="${material.id}" data-materialname="${material.Material_name}">
                                    <i class="bx bxs-trash-alt"></i>
                                </a>
                            </span>
                        </td>
                    </tr>`;
                    $tbody.append(row);
                });

                // Transform the table into a DataTable
                $('#tbl-report-raw').DataTable({
                    columnDefs: [{
                        targets: 1,
                        className: 'text-start'
                    }],
                    layout: {
                        topStart: {
                            buttons: [{
                                extend: 'excel',
                                text: '<i class="bx bx-table"></i> Excel',
                                className: 'btn-custom-excel'
                            }]
                        }
                    }
                });
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.error(xhr.statusText);
            }
        });

        // Event handler untuk tombol delete
        $(document).on('click', '#btn-delete-raw', function(e){
            e.preventDefault(); // Hindari redirect default
            var id = $(this).data('id');
            var material_name = $(this).data('materialname'); // sesuaikan nama data attribute

            Swal.fire({
                title: `Do you want to delete this material "${material_name}"?`,
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Yes",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Isi form delete dengan id dan sesuaikan action URL
                    $("#form-delete input[name='id']").val(id);
                    $("#form-delete").attr('action', '<?= base_url("master/delete_raw_material"); ?>/' + id);
                    $("#form-delete").submit();
                }
            });
        });
    });
</script>
