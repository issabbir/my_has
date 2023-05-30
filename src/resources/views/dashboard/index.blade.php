@extends('layouts.default')

@section('title')
	Dashboard
@endsection

@section('header-style')
	<style type="text/css">
		.swiper-container {
			padding: 0px 130px;
		}
		.swiper-slide {
			width: 60%;
			min-height: 200px;
		}
		.swiper-slide:nth-child(2n) {
			width: 40%;
		}
		.swiper-slide:nth-child(3n) {
			width: 20%;
		}
		.card.cardSlide {
			min-height: 180px;
		}
		.first_row{
			min-height: 530px;
		}
		.second_row{
			min-height: 275px;
		}
		.third_row{
			min-height: 500px;
		}
		.forth_row{
			min-height: 410px;
		}

		@media only screen and (max-width: 1400px) {
			.swiper-container {
				padding: 0px !important;
			}
			.swiper-slide {
				width: 50%;
				min-height: 200px;
			}
			.swiper-slide:nth-child(2n) {
				width: 50%;
			}
			.swiper-slide:nth-child(3n) {
				width: 50%;
			}
			.swiper-slide h5{
				font-size: 15px !important;
			}
			#dashboard-analytics h2{
				font-size: 22px !important;
			}
			#dashboard-analytics h4, #dashboard-analytics h6{
				font-size: 15px !important;
			}
			#dashboard-analytics span{
				font-size:12px;
			}
			#dashboard-analytics table tr th{
				font-size:13px;
			}
		}
		@media only screen and (max-width: 640px) {
			.swiper-container {
				padding: 0px !important;
			}
			.swiper-slide {
				width: 100% !important;
				min-height: 200px;
			}
			.swiper-slide:nth-child(2n) {
				width: 100% !important;
			}
			.swiper-slide:nth-child(3n) {
				width: 100% !important;
			}

			.shadow-lg.p-2 {
				padding: 0!important;
			}
		}
	</style>
@endsection

@section('content')
	<section id="dashboard-analytics">

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-content">
					<div class="card-body">
						<div class="text-center">
							<h3>Welcome To House Allotment System</h3>
						</div>
					</div>
				</div>
			</div>
		</div>
		{{--<div class="col-md-12">
			<div class="card">
				<div class="card-header bg-rgba-primary p-1 mb-1">
					<h4 class="card-title">Statistical report </h4>
				</div>
				<div class="card-content">
					<div class="card-body" >
						<div class="d-flex justify-content-center" v-if="grantAccess">
							<div id="chart"></div>
						</div>
					</div>
				</div>
			</div>
		</div>--}}
	</div>

	</section>

@endsection

@section('footer-script')
	<script type="text/javascript">
		var options = {
			series: [{
				name: "STOCK ABC",
				data: [100,200,300,400]
			}],
			chart: {
				type: 'area',
				height: 350,
				zoom: {
					enabled: false
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				curve: 'straight'
			},

			title: {
				text: 'Fundamental Analysis of Stocks',
				align: 'left'
			},
			subtitle: {
				text: 'Price Movements',
				align: 'left'
			},
			labels: ['01/01/2019','02/02/2019','03/03/2019','04/04/2019'],
			xaxis: {
				type: 'datetime',
			},
			yaxis: {
				opposite: true
			},
			legend: {
				horizontalAlign: 'left'
			}
		};

		/*var chart = new ApexCharts(document.querySelector("#chart"), options);
		chart.render();*/
	</script>
@endsection
