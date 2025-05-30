<style>
	.select2-container {
		z-index: 99;
	}

	.select2-selection {
		padding-top: 4px !important;
		height: 38px !important;
	}
</style>
<section class="section dashboard">
  <div class="row">
    <!-- Sales Card -->
    <div class="col-md-12">
      <div class="card info-card sales-card">
		<div class="card-header">
			<div class="row d-flex justify-content-center mt-2">
				<div class="col-md-3">
                  <label for="Material_name" class="form-label"><strong>Material Name</strong></label>
                  <select id="Material_name" class="form-select" id="multiple-select-field">
					  	<option value="">Choose material</option>
					  	<?php foreach($material_name as $mn): ?>
						<option value="<?=$mn['Material_no'];?>"><?=$mn['Material_name'];?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-3">
                  	<label for="period" class="form-label"><strong>Period</strong></label>
					<select id="period" name="period" class="form-select">
						<?php 
						$currentYear = date('Y');
						for ($year = $currentYear; $year >= 2000; $year--) {
							echo "<option value='$year'>$year</option>";
						}
						?>
					</select>
				</div>
				<div class="col-md-3">
                  <button class="btn btn-primary" id="search_material_data" style="margin-top: 2rem">Search</button>
				</div>
			</div>
		</div>
        <div class="card-body">
			<h3 class="card-title text-center title-material-receiving" style="display: none;">Material Receiving</h3>
			<div id="material_receiving_chart"></div>
			<h3 class="card-title text-center title-material-usage mt-3" style="display: none;">Material Usage</h3>
			<div id="material_usage_chart"></div>
			<h3 class="card-title text-center title-material-usage mt-3" style="display: none;">Demand Forecast</h3>
			<div id="demand_forecast_chart"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
$(document).ready(function(){
	$('#period').select2();
    $('#Material_name').select2();

    function material_usage(){
        var material_no = $('#Material_name').val();
        var period = $('#period').val();

        $.ajax({
			url: '<?= base_url('adminhead/load_material_usage'); ?>',
            type: 'post',
            dataType: 'json',
            data: {
				material_no: material_no,
                period: period
            },
            success: function(res) {
				$('.title-material-usage').css('display', 'block');
				console.log("Material usage: ", res);
                let seriesData = [];
                let categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                res.forEach(function(item) {
                    seriesData.push({
                        name: item.name,
                        data: item.monthly_qty,
                        unit: item.Unit
                    });
                });

                // Clear existing chart
                $("#material_usage_chart").html("");

                new ApexCharts(document.querySelector("#material_usage_chart"), {
                    series: seriesData,
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '60%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: categories
                    },
                    yaxis: {
						title: { text: 'Quantity' },
						labels: {
							formatter: function (val) {
								return Math.round(val);
							}
						}
					},
                    fill: { opacity: 1 },
                    tooltip: {
                        y: {
                            formatter: function(val, opts) {
                                var unit = seriesData[opts.seriesIndex].unit;
                                return Math.round(val) + ' ' + unit;
                            }
                        }
                    }
                }).render();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }

	function material_receiving(){
        var material_no = $('#Material_name').val();
        var period = $('#period').val();

        $.ajax({
			url: '<?= base_url('adminhead/load_material_receiving'); ?>',
            type: 'post',
            dataType: 'json',
            data: {
				material_no: material_no,
                period: period
            },
            success: function(res) {
				$('.title-material-receiving').css('display', 'block');
				console.log("Material receiving: ", res);
                let seriesData = [];
                let categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                res.forEach(function(item) {
                    seriesData.push({
                        name: item.name,
                        data: item.monthly_qty,
                        unit: item.Unit
                    });
                });

                // Clear existing chart
                $("#material_receiving_chart").html("");

                new ApexCharts(document.querySelector("#material_receiving_chart"), {
                    series: seriesData,
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '60%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: categories
                    },
                    yaxis: {
						title: { text: 'Quantity' },
						labels: {
							formatter: function (val) {
								return Math.round(val);
							}
						}
					},
                    fill: { opacity: 1 },
                    tooltip: {
                        y: {
                            formatter: function(val, opts) {
                                var unit = seriesData[opts.seriesIndex].unit;
                                return Math.round(val) + ' ' + unit;
                            }
                        }
                    }
                }).render();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }

	function demand_forecast(){
        var material_no = $('#Material_name').val();
        var period = $('#period').val();

        $.ajax({
			url: '<?= base_url('adminhead/load_demand_forecast'); ?>',
            type: 'post',
            dataType: 'json',
            data: {
				material_no: material_no,
                period: period
            },
            success: function(res) {
				$('.title-demand-forecast').css('display', 'block');
				console.log("Demand Forecast: ", res);
                let seriesData = [];
                let categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                res.forEach(function(item) {
                    seriesData.push({
                        name: item.name,
                        data: item.monthly_qty,
                        unit: item.Unit
                    });
                });

                // Clear existing chart
                $("#demand_forecast_chart").html("");

                new ApexCharts(document.querySelector("#demand_forecast_chart"), {
                    series: seriesData,
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '60%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: categories
                    },
                    yaxis: {
						title: { text: 'Quantity' },
						labels: {
							formatter: function (val) {
								return Math.round(val);
							}
						}
					},
                    fill: { opacity: 1 },
                    tooltip: {
                        y: {
                            formatter: function(val, opts) {
                                var unit = seriesData[opts.seriesIndex].unit;
                                return Math.round(val) + ' ' + unit;
                            }
                        }
                    }
                }).render();
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }

    $('#search_material_data').click(function(){
        material_receiving();
        material_usage();
        demand_forecast();
    });
});
</script>
