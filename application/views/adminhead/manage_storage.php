<section>
    <div class="card">
        <div class="card-body">
            <div class="row mt-3">
                <div class="col-md">
                    <table class="table" id="tbl-data">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Rack</th>
                                <th class="text-center">SLoc</th>
                                <th class="text-center">Current space</th>
                                <th class="text-center">Max space</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="tbody-data">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function (){
        function insertData(){
            var data = <?= json_encode($storage); ?>;
            var tbody = '';
            for(var i = 0; i < data.length; i++){
                var status_storage = '';
                if (parseInt(data[i].space_now) == 0 && parseInt(data[i].space_now) < 1) {
                    status_storage = '<i class="bx bxs-battery-full" style="font-size: 22px; color: #002e63"></i>';
                } 
                else if (parseInt(data[i].space_now) ==  parseInt(data[i].space_max)) {
                    status_storage = '<i class="bx bxs-battery" style="font-size: 22px; color: #002e63"></i>';
                } 
                else if (parseInt(data[i].space_now) < parseInt(data[i].space_max)) {
                    status_storage = '<i class="bx bxs-battery-low" style="font-size: 22px; color: #002e63"></i>';
                }

                tbody +=
                `
                    <tr>
                        <td class="text-center">${i + 1}</td>
                        <td class="text-center">${data[i].Rack}</td>
                        <td class="text-center">${data[i].SLoc}</td>
                        <td class="text-center">${data[i].space_now}</td>
                        <td class="text-center">${data[i].space_max}</td>
                        <td class="text-center">${status_storage}</td>
                        <td class="text-center">
                            <button class="btn btn-primary show-detail-btn" data-bs-toggle="modal" data-bs-target="#showDetailModal${data[i].Id_storage}" data-id="${data[i].Id_storage}" data-sloc=${data[i].SLoc}>
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }

            $('.tbody-data').append(tbody);
            $('#tbl-data').DataTable();

            var modalDetail = '';
            for (var i = 0; i < data.length; i++) {
                modalDetail += `
                    <div class="modal fade" id="showDetailModal${data[i].Id_storage}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail storage</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row ps-2">
                                        <div class="col-2">
                                            <label for="rack" class="form-label"><b>Rack</b></label>
                                            <input type="text" class="form-control" id="rack" name="rack" value="${data[i].Rack}" readonly> 
                                        </div>
                                        <div class="col-4">
                                            <label for="sloc" class="form-label"><b>SLoc</b></label>
                                            <input type="text" class="form-control" id="sloc" name="sloc" value="${data[i].SLoc}" readonly> 
                                        </div>
                                    </div>
                                    <div class="row ps-2 mt-4">
                                        <div class="col-12">
                                            <table class="table datatable table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">Box No</th>
                                                        <th class="text-center">Box Type</th>
                                                        <th class="text-center">Box Weight</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="box-details${data[i].Id_storage}">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            $('body').append(modalDetail);

            $(document).on('click', '.show-detail-btn', function() {
                var idStorage = $(this).data('id');
                var sloc = $(this).data('sloc');
                
                $.ajax({
                    url: '<?=base_url('admin/getBoxBySloc')?>',
                    method: 'POST',
                    data: { idStorage },
                    success: function(res) {
                        var response = JSON.parse(res).result;
                        var response_length = JSON.parse(res).result_length;
                        var boxDetails = '';
                        for(var i = 0; i < response_length; i++){
                            boxDetails += `
                                <tr>
                                    <td class="text-center">${i+1}</td>
                                    <td class="text-center">${response.no_box || 'N/A'}</td>
                                    <td class="text-center">${response.box_type || ''}</td>
                                    <td class="text-center">${response.weight || 'N/A'}</td>
                                </tr>
                            `;
                            $('#box-details' + idStorage).html(boxDetails);
                        }

                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });
        }

        insertData();
    });
</script>