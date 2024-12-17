

var myChart;    // dpk (%)
var myChart2;   // dpk (value)
var myChart3;   // kredit (%)
var myChart4;   // kredit (value)
var myChart5;   // laba usaha (pendapatan)
var myChart6;   // laba usaha (beban)
var myChart7;   // pendapatan
var myChart8;   // beban
var myChart9;   // biaya barang jasa
var myChart10;  // biaya promosi
var myChart11;   // biaya sewa
var myChart12;  // biaya lainnya
var serialize_color = [
    '#404E67',
    '#22C2DC',
    '#ff6384',
    '#ff9f40',
    '#ffcd56',
    '#4bc0c0',
    '#9966ff',
    '#36a2eb',
    '#848484',
    '#e8b892',
    '#bcefa0',
    '#4dc9f6',
    '#a0e4ef',
    '#c9cbcf',
    '#00A5A8',
    '#10C888',
    '#7d3cff',
    '#f2d53c',
    '#c80e13',
    '#e1b382',
    '#c89666',
    '#2d545e',
    '#12343b',
    '#9bc400',
    '#8076a3',
    '#f9c5bd',
    '#7c677f'
];

var controller = 'mac';
$(document).ready(function () {
//	getContent();
});

$('#filter_tahun').change(function(){getContent();});
$('#filter_cabang').change(function(){getContent();});
$('#filter_bulan').change(function(){getContent();});

function getContent(){
	cLoader.open(lang.memuat_data + '...');

	var page = base_url + 'transaction/'+controller+'/get_content';
	
	var tahun 	= $('#filter_tahun option:selected').val();
	var cabang	= $('#filter_cabang option:selected').val();
	var bulan 	= $('#filter_bulan option:selected').val();

	var classnya = 'd-'+cabang+'-'+bulan;
	var length = $('body').find('.'+classnya).length;
	var length_body = $('body').find('.d-content-body').length;

	if(length_body>0){
		$('body').find('.d-content-body').hide(300);
	}

	if(length<=0){
		$.ajax({
			url 	: page,
			data 	: {
				tahun 	: tahun,
				cabang 	: cabang,
				bulan 	: bulan,
			},
			type	: 'post',
			dataType: 'json',
			success	: function(response) {
				$('.d-content').append('<div class="d-content-body '+classnya+'"></div>');
				$('body').find('.'+classnya).html(response.view);
				cLoader.close();
			//	getData(tahun,bulan,cabang);
				loadData();
			}
		});
	}else{
		$('body').find('.'+classnya).show(300);
		cLoader.close();
	}
}	

function getData(tahun,cabang,bulan){
	cLoader.open(lang.memuat_data + '...');
	var page = base_url + 'transaction/'+controller+'/data';
	var classnya = 'd-'+cabang+'-'+bulan;
	$.ajax({
		url 	: page,
		data 	: {
			tahun 	: tahun,
			cabang 	: cabang,
			bulan 	: bulan,
		},
		type	: 'post',
		dataType: 'json',
		success	: function(response) {
			$('body').find('.'+classnya+' .table-app tbody').html(response.view);
		//	checkSubData2(classnya);
			cLoader.close();
		}
	});
}

var xhr_ajax = null;
function loadData(){

    if( xhr_ajax != null ) {
        xhr_ajax.abort();
        xhr_ajax = null;
    }

    var page = base_url + 'transaction/mac/data2/';
    page += '/'+ $('#filter_tahun').val();
    page += '/'+ $('#filter_cabang').val();
    page += '/'+ $('#filter_bulan').val();

  	xhr_ajax = $.ajax({
        url: page,
        type: 'post',
		data : $('#form-filter').serialize(),
        dataType: 'json',
        success: function(res){
        	xhr_ajax = null;
            $('#result tbody').html(res.data);		

                var data_bar 			= [];
				var label_chart 		= [];
				var color_bar 			= [];
				var i = 0;
				
				$.each(res.coa_dpk,function(k,v){		
					label_chart.push(v.glwdes);
					color_bar.push(serialize_color[i]);
					$.each(res.dpk,function(r,z){
						if(v.glwnco==r){
					 		data_bar.push(z);
						}
					});	

					i++;
				});	

	    		myChart.data = {
			        datasets: [{
			            label: 'Jumlah',
			            data: data_bar,
			            backgroundColor: ['#0099CC','#FF8800'],
			        },
			        ],
					labels: label_chart,
				};

				myChart.update();		

				myChart2.data = {
			        datasets: [{
			     //       label: 'Jumlah',
			            data: data_bar,
			            backgroundColor: ['#0099CC','#FF8800'],
			        },
			        ],
					labels: label_chart,
				};

				myChart2.update();		


                var data_bar2 			= [];
				var label_chart2		= [];
				var color_bar2 			= [];
				var i = 0;

				$.each(res.coa_kredit,function(k1,v1){		
					label_chart2.push(v1.glwdes);
					color_bar2.push(serialize_color[i]);
					$.each(res.kredit,function(r1,z1){
						if(v1.glwnco==r1){
					 		data_bar2.push(z1);
					 //		alert(z)
						}else{
							data_bar2.push(0);
						}
					});	

					i++;
				});	


	    		myChart3.data = {
			        datasets: [{
			            label: 'Jumlah',
			            data: data_bar2,
			            backgroundColor: ['#0099CC','#FF8800'],
			        },
			        ],
					labels: label_chart2,
				};

				myChart3.update();		

				myChart4.data = {
			        datasets: [{
			     //       label: 'Jumlah',
			            data: data_bar2,
			            backgroundColor: ['#0099CC','#FF8800'],
			        },
			        ],
					labels: label_chart2,
				};

				myChart4.update();	

                var data_bar5 			= [];
				var label_chart5		= [];
				var color_bar5 			= [];
				var i = 0;

				$.each(res.coa_pendapatan,function(k1,v1){		
					label_chart5.push(v1);
					color_bar5.push(serialize_color[i]);
					$.each(res.pendapatan_rencana,function(r1,z1){
						if(r1 = "RENC"){
					 		data_bar5.push(5000);
					 //		alert(z)
						}else{
							data_bar5.push(0);
						}
					});	

					i++;
				});	

				myChart5.data = {
			        datasets: [{
			            label: 'PENDAPATAN',
			            data: data_bar5,
			            backgroundColor: ['#0099CC','#FF8800'],
			        },
			        ],
					labels: label_chart5,
				};
				myChart5.update();	

				var data_bar6 			= [];
				var label_chart6		= [];
				var color_bar6 			= [];
				var i = 0;

				$.each(res.coa_beban,function(k1,v1){		
					label_chart6.push(v1);
					color_bar6.push(serialize_color[i]);
					$.each(res.beban_rencana,function(r1,z1){
						if(r1 = "RENC"){
					 		data_bar6.push(5000);
					 //		alert(z)
						}else{
							data_bar6.push(0);
						}
					});	

					i++;
				});	

				myChart6.data = {
			        datasets: [{
			            label: 'BEBAN',
			            data: data_bar6,
			            backgroundColor: ['#0099CC','#FF8800'],
			        },
			        ],
					labels: label_chart6,
				};
				myChart6.update();	


				label_chart7 =[];
				label_chart7 = ['Pend Bunga Net', 'Pend RAK','Pend Opr','Pend Non Opr']

				var data1 = [];
				var data2 = [];
				// $.each(res.area,function(x,y){		
				// 	label_chart5.push(y);
				// 	color_bar.push(serialize_color[i]);
				// 	$.each(res.komtel,function(x1,z1){
				// 		if(x==x1){
				// 			data_komtel.push(z1);
				// 		}	
				// 	});	
				// 	$.each(res.kompro,function(x1,z1){
				// 		if(x==x1){
				// 			data_kompro.push(z1);
				// 		}	
				// 	});	
				// 	$.each(res.idletel,function(x1,z1){
				// 		if(x==x1){
				// 			data_idletel.push(z1);
				// 		}	
				// 	});	
				// 	$.each(res.idlepro,function(x1,z1){
				// 		if(x==x1){
				// 			data_idlepro.push(z1);
				// 		}	
				// 	});	
				// });	

				myChart7.data = {
					datasets: [
			        {
			          label: "Renc",
			          type: "bar",
					  backgroundColor: "#0288d1",
					  data: [1,2,3,4,5],
			        }, 
			        {
			          label: "Real",
			          type: "bar",
					  backgroundColor: "#ef6c00",
					  data: [4,5,6,6,6],
			        }, 
			      ],



			      labels: label_chart7,
				};
				

				myChart7.update();

				label_chart8 = [];
				label_chart8 = ['Beban Bunga Net', 'Beban RAK','Beban Opr','Beban Non Opr']

				var data1 = [];
				var data2 = [];
				// $.each(res.area,function(x,y){		
				// 	label_chart5.push(y);
				// 	color_bar.push(serialize_color[i]);
				// 	$.each(res.komtel,function(x1,z1){
				// 		if(x==x1){
				// 			data_komtel.push(z1);
				// 		}	
				// 	});	
				// 	$.each(res.kompro,function(x1,z1){
				// 		if(x==x1){
				// 			data_kompro.push(z1);
				// 		}	
				// 	});	
				// 	$.each(res.idletel,function(x1,z1){
				// 		if(x==x1){
				// 			data_idletel.push(z1);
				// 		}	
				// 	});	
				// 	$.each(res.idlepro,function(x1,z1){
				// 		if(x==x1){
				// 			data_idlepro.push(z1);
				// 		}	
				// 	});	
				// });	

				myChart8.data = {
					datasets: [
			        {
			          label: "Renc",
			          type: "bar",
					  backgroundColor: "#0288d1",
					  data: [1,2,3,4,5],
			        }, 
			        {
			          label: "Real",
			          type: "bar",
					  backgroundColor: "#ef6c00",
					  data: [4,5,6,6,6],
			        }, 
			      ],



			      labels: label_chart8,
				};
				

				myChart8.update();

				var data_bar9 			= [];
				var label_chart9		= [];
				var color_bar9 			= [];
				var i = 0;

				$.each(res.coa_pendapatan,function(k1,v1){		
					label_chart9.push(v1);
					color_bar9.push(serialize_color[i]);
					$.each(res.pendapatan_rencana,function(r1,z1){
						if(r1 = "RENC"){
					 		data_bar9.push(5000);
					 //		alert(z)
						}else{
							data_bar9.push(0);
						}
					});	

					i++;
				});	

				myChart9.data = {
			        datasets: [{
			            label: 'By barang & jasa',
			            data: data_bar5,
			            backgroundColor: ['#0099CC','#FF8800'],
			        },
			        ],
					labels: label_chart9,
				};
				myChart9.update();	

				var data_bar10 			= [];
				var label_chart10		= [];
				var color_bar10 			= [];
				var i = 0;

				$.each(res.coa_pendapatan,function(k1,v1){		
					label_chart10.push(v1);
					color_bar10.push(serialize_color[i]);
					$.each(res.pendapatan_rencana,function(r1,z1){
						if(r1 = "RENC"){
					 		data_bar10.push(5000);
					 //		alert(z)
						}else{
							data_bar10.push(0);
						}
					});	

					i++;
				});	

				myChart10.data = {
			        datasets: [{
			            label: 'Biya promosi',
			            data: data_bar10,
			            backgroundColor: ['#0099CC','#FF8800'],
			        },
			        ],
					labels: label_chart10,
				};
				myChart10.update();	

				var data_bar11 			= [];
				var label_chart11		= [];
				var color_bar11 			= [];
				var i = 0;

				$.each(res.coa_pendapatan,function(k1,v1){		
					label_chart11.push(v1);
					color_bar11.push(serialize_color[i]);
					$.each(res.pendapatan_rencana,function(r1,z1){
						if(r1 = "RENC"){
					 		data_bar11.push(5000);
					 //		alert(z)
						}else{
							data_bar11.push(0);
						}
					});	

					i++;
				});	

				myChart11.data = {
			        datasets: [{
			            label: 'Biya sewa',
			            data: data_bar11,
			            backgroundColor: ['#0099CC','#FF8800'],
			        },
			        ],
					labels: label_chart11,
				};
				myChart11.update();	

				var data_bar12 			= [];
				var label_chart12		= [];
				var color_bar12 			= [];
				var i = 0;

				$.each(res.coa_pendapatan,function(k1,v1){		
					label_chart12.push(v1);
					color_bar12.push(serialize_color[i]);
					$.each(res.pendapatan_rencana,function(r1,z1){
						if(r1 = "RENC"){
					 		data_bar12.push(5000);
					 //		alert(z)
						}else{
							data_bar12.push(0);
						}
					});	

					i++;
				});	

				myChart12.data = {
			        datasets: [{
			            label: 'Biya lainnya',
			            data: data_bar12,
			            backgroundColor: ['#0099CC','#FF8800'],
			        },
			        ],
					labels: label_chart12,
				};
				myChart12.update();	
        }
    });
}



$(document).ready(function(){
	var ctxPie = document.getElementById('chart').getContext('2d');
	myChart = new Chart(ctxPie, {
		type: 'pie',
		options: {

			title: {
                display: false,
                text: 'PROGRESS (%)',
                fontSize: 14,
                padding: 10
            },

			maintainAspectRatio: false,
			responsive: true,
			legend: {
				display: true,
				position: 'bottom',
				labels: {
					boxWidth: 15,
					generateLabels: function(chart) {
						var data = chart.data;
						if (data.labels.length && data.datasets.length) {
							return data.labels.map(function(label, i) {
								var meta = chart.getDatasetMeta(0);
								var ds = data.datasets[0];
								var arc = meta.data[i];
								var custom = arc && arc.custom || {};
								var getValueAtIndexOrDefault = Chart.helpers.getValueAtIndexOrDefault;
								var arcOpts = chart.options.elements.arc;
								var fill = custom.backgroundColor ? custom.backgroundColor : getValueAtIndexOrDefault(ds.backgroundColor, i, arcOpts.backgroundColor);
								var stroke = custom.borderColor ? custom.borderColor : getValueAtIndexOrDefault(ds.borderColor, i, arcOpts.borderColor);
								var bw = custom.borderWidth ? custom.borderWidth : getValueAtIndexOrDefault(ds.borderWidth, i, arcOpts.borderWidth);

								var value = chart.config.data.datasets[arc._datasetIndex].data[arc._index];

								return {
									text: label + " : " + value,
									fillStyle: fill,
									strokeStyle: stroke,
									lineWidth: bw,
									hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
									index: i
								};
							});
						} else {
							return [];
						}
					}
				}
			}
		}
	});

	var ctxBar = document.getElementById('chart2').getContext('2d');
	myChart2 = new Chart(ctxBar, {
		type: 'bar',
		options: {
        "hover": {
            "animationDuration": 0
        },
          "hover": {
            "animationDuration": 0
        },
        "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(8, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        data = (parseFloat(data) / 1000);
                        data = toFixedIfNecessary(data,1);
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
        legend: {
            "display": false
        },
        tooltips: {
            "enabled": false
        },

			title: {
                display: false,
                text: 'KREDIT',
                fontSize: 14,
                padding: 10
            },
			maintainAspectRatio: false,
			responsive: true,
		    scales: {
			  xAxes: [{
			      beginAtZero: true,
			      ticks: {
			         autoSkip: false
			      }
			  }],
	            yAxes: [{
	                    display: false,
	                    scaleLabel: {
	                        display: false,
	                        labelString: 'Jumlah'
	                    },
	                    ticks: {
                    	// Abbreviate the millions
                    		callback: function(value, index, values) {
                        	return numberFormat(value / 1,0);
                    		}
                		}
	                }],
	        },

			legend: {
				display: true,
				position: 'bottom',
					labels: {
					boxWidth: 15,
				}
			}
		}
	});

	var ctxPie = document.getElementById('chart3').getContext('2d');
	myChart3 = new Chart(ctxPie, {
		type: 'pie',
		options: {

			title: {
                display: false,
                text: 'PROGRESS (%)',
                fontSize: 14,
                padding: 10
            },

			maintainAspectRatio: false,
			responsive: true,
			legend: {
				display: true,
				position: 'bottom',
				labels: {
					boxWidth: 15,
					generateLabels: function(chart) {
						var data = chart.data;
						if (data.labels.length && data.datasets.length) {
							return data.labels.map(function(label, i) {
								var meta = chart.getDatasetMeta(0);
								var ds = data.datasets[0];
								var arc = meta.data[i];
								var custom = arc && arc.custom || {};
								var getValueAtIndexOrDefault = Chart.helpers.getValueAtIndexOrDefault;
								var arcOpts = chart.options.elements.arc;
								var fill = custom.backgroundColor ? custom.backgroundColor : getValueAtIndexOrDefault(ds.backgroundColor, i, arcOpts.backgroundColor);
								var stroke = custom.borderColor ? custom.borderColor : getValueAtIndexOrDefault(ds.borderColor, i, arcOpts.borderColor);
								var bw = custom.borderWidth ? custom.borderWidth : getValueAtIndexOrDefault(ds.borderWidth, i, arcOpts.borderWidth);

								var value = chart.config.data.datasets[arc._datasetIndex].data[arc._index];

								return {
									text: label + " : " + value,
									fillStyle: fill,
									strokeStyle: stroke,
									lineWidth: bw,
									hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
									index: i
								};
							});
						} else {
							return [];
						}
					}
				}
			}
		}
	});

	var ctxBar4 = document.getElementById('chart4').getContext('2d');
	myChart4 = new Chart(ctxBar4, {
		type: 'bar',
		options: {
        "hover": {
            "animationDuration": 0
        },
          "hover": {
            "animationDuration": 0
        },
        "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(8, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        data = (parseFloat(data) / 1000);
                        data = toFixedIfNecessary(data,1);
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
        legend: {
            "display": false
        },
        tooltips: {
            "enabled": false
        },

			title: {
                display: false,
                text: 'KREDIT',
                fontSize: 14,
                padding: 10
            },
			maintainAspectRatio: false,
			responsive: true,
		    scales: {
			  xAxes: [{
			      beginAtZero: true,
			      ticks: {
			         autoSkip: false
			      }
			  }],
	            yAxes: [{
	                    display: false,
	                    scaleLabel: {
	                        display: false,
	                        labelString: 'Jumlah'
	                    },
	                    ticks: {
                    	// Abbreviate the millions
                    		callback: function(value, index, values) {
                        	return numberFormat(value / 1,0);
                    		}
                		}
	                }],
	        },

			legend: {
				display: true,
				position: 'bottom',
					labels: {
					boxWidth: 15,
				}
			}
		}
	});

	var ctxBar5 = document.getElementById('chart5').getContext('2d');
	myChart5 = new Chart(ctxBar5, {
		type: 'bar',
		options: {
        "hover": {
            "animationDuration": 0
        },
          "hover": {
            "animationDuration": 0
        },
        "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(8, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        data = (parseFloat(data) / 1000);
                        data = toFixedIfNecessary(data,1);
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
        legend: {
            "display": false
        },
        tooltips: {
            "enabled": false
        },

			title: {
                display: false,
                text: 'KREDIT',
                fontSize: 14,
                padding: 10
            },
			maintainAspectRatio: false,
			responsive: true,
		    scales: {
			  xAxes: [{
			      beginAtZero: true,
			      ticks: {
			         autoSkip: false
			      }
			  }],
	            yAxes: [{
	                    display: true,
	                    scaleLabel: {
	                        display: false,
	                        labelString: 'PENDAPATAN (Rp Juta)'
	                    },
	                    ticks: {
                    	// Abbreviate the millions
                    		callback: function(value, index, values) {
                        	return numberFormat(value / 1,0);
                    		}
                		}
	                }],
	        },

			legend: {
				display: true,
				position: 'bottom',
					labels: {
					boxWidth: 15,
				}
			}
		}
	});

	var ctxBar6 = document.getElementById('chart6').getContext('2d');
	myChart6 = new Chart(ctxBar6, {
		type: 'bar',
		options: {
        "hover": {
            "animationDuration": 0
        },
          "hover": {
            "animationDuration": 0
        },
        "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(8, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        data = (parseFloat(data) / 1000);
                        data = toFixedIfNecessary(data,1);
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
        legend: {
            "display": false
        },
        tooltips: {
            "enabled": false
        },

			title: {
                display: false,
                text: 'KREDIT',
                fontSize: 14,
                padding: 10
            },
			maintainAspectRatio: false,
			responsive: true,
		    scales: {
			  xAxes: [{
			      beginAtZero: true,
			      ticks: {
			         autoSkip: false
			      }
			  }],
	            yAxes: [{
	                    display: true,
	                    scaleLabel: {
	                        display: false,
	                        labelString: 'PENDAPATAN (Rp Juta)'
	                    },
	                    ticks: {
                    	// Abbreviate the millions
                    		callback: function(value, index, values) {
                        	return numberFormat(value / 1,0);
                    		}
                		}
	                }],
	        },

			legend: {
				display: true,
				position: 'bottom',
					labels: {
					boxWidth: 15,
				}
			}
		}
	});


	var ctxBar = document.getElementById('chartbar').getContext('2d');
	myChart7 = new Chart(ctxBar, {
		type: 'bar',
		options: {

        "hover": {
            "animationDuration": 0
        },
          "hover": {
            "animationDuration": 0
        },
        "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];                            
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
        legend: {
            "display": false
        },
        tooltips: {
            "enabled": false
        },
			title: {
                display: true,
                text: 'PENDAPATAN',
                fontSize: 14,
                padding: 10
            },
			maintainAspectRatio: false,
			responsive: true,
		    scales: {
	            yAxes: [{
	                    display: true,
	                    scaleLabel: {
	                        display: true,
	                        labelString: 'Jumlah'
	                    },
	                   	ticks: {
			            min: 0,
			            stepSize: 5
			        },
	                }],
	        },
			legend: {
				display: true,
				position: 'bottom',
					labels: {
					boxWidth: 15,
				}
			}
		}
	});


	var ctxBar_beban = document.getElementById('chartbar_beban').getContext('2d');
	myChart8 = new Chart(ctxBar_beban, {
		type: 'bar',
		options: {

        "hover": {
            "animationDuration": 0
        },
          "hover": {
            "animationDuration": 0
        },
        "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];                            
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
        legend: {
            "display": false
        },
        tooltips: {
            "enabled": false
        },
			title: {
                display: true,
                text: 'BEBAN',
                fontSize: 14,
                padding: 10
            },
			maintainAspectRatio: false,
			responsive: true,
		    scales: {
	            yAxes: [{
	                    display: true,
	                    scaleLabel: {
	                        display: true,
	                        labelString: 'Jumlah'
	                    },
	                   	ticks: {
			            min: 0,
			            stepSize: 5
			        },
	                }],
	        },
			legend: {
				display: true,
				position: 'bottom',
					labels: {
					boxWidth: 15,
				}
			}
		}
	});

	var ctxBar9 = document.getElementById('chart_barangjasa').getContext('2d');
	myChart9 = new Chart(ctxBar9, {
		type: 'bar',
		options: {
        "hover": {
            "animationDuration": 0
        },
          "hover": {
            "animationDuration": 0
        },
        "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(8, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        data = (parseFloat(data) / 1000);
                        data = toFixedIfNecessary(data,1);
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
        legend: {
            "display": false
        },
        tooltips: {
            "enabled": false
        },

			title: {
                display: false,
                text: 'KREDIT',
                fontSize: 14,
                padding: 10
            },
			maintainAspectRatio: false,
			responsive: true,
		    scales: {
			  xAxes: [{
			      beginAtZero: true,
			      ticks: {
			         autoSkip: false
			      }
			  }],
	            yAxes: [{
	                    display: true,
	                    scaleLabel: {
	                        display: false,
	                        labelString: 'PENDAPATAN (Rp Juta)'
	                    },
	                    ticks: {
                    	// Abbreviate the millions
                    		callback: function(value, index, values) {
                        	return numberFormat(value / 1,0);
                    		}
                		}
	                }],
	        },

			legend: {
				display: true,
				position: 'bottom',
					labels: {
					boxWidth: 15,
				}
			}
		}
	});

	var ctxBar10 = document.getElementById('chart_bypromosi').getContext('2d');
	myChart10 = new Chart(ctxBar10, {
		type: 'bar',
		options: {
        "hover": {
            "animationDuration": 0
        },
          "hover": {
            "animationDuration": 0
        },
        "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(8, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        data = (parseFloat(data) / 1000);
                        data = toFixedIfNecessary(data,1);
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
        legend: {
            "display": false
        },
        tooltips: {
            "enabled": false
        },

			title: {
                display: false,
                text: 'KREDIT',
                fontSize: 14,
                padding: 10
            },
			maintainAspectRatio: false,
			responsive: true,
		    scales: {
			  xAxes: [{
			      beginAtZero: true,
			      ticks: {
			         autoSkip: false
			      }
			  }],
	            yAxes: [{
	                    display: true,
	                    scaleLabel: {
	                        display: false,
	                        labelString: 'PENDAPATAN (Rp Juta)'
	                    },
	                    ticks: {
                    	// Abbreviate the millions
                    		callback: function(value, index, values) {
                        	return numberFormat(value / 1,0);
                    		}
                		}
	                }],
	        },

			legend: {
				display: true,
				position: 'bottom',
					labels: {
					boxWidth: 15,
				}
			}
		}
	});

	var ctxBar11 = document.getElementById('chart_bysewa').getContext('2d');
	myChart11 = new Chart(ctxBar11, {
		type: 'bar',
		options: {
        "hover": {
            "animationDuration": 0
        },
          "hover": {
            "animationDuration": 0
        },
        "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(8, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        data = (parseFloat(data) / 1000);
                        data = toFixedIfNecessary(data,1);
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
        legend: {
            "display": false
        },
        tooltips: {
            "enabled": false
        },

			title: {
                display: false,
                text: 'KREDIT',
                fontSize: 14,
                padding: 10
            },
			maintainAspectRatio: false,
			responsive: true,
		    scales: {
			  xAxes: [{
			      beginAtZero: true,
			      ticks: {
			         autoSkip: false
			      }
			  }],
	            yAxes: [{
	                    display: true,
	                    scaleLabel: {
	                        display: false,
	                        labelString: 'PENDAPATAN (Rp Juta)'
	                    },
	                    ticks: {
                    	// Abbreviate the millions
                    		callback: function(value, index, values) {
                        	return numberFormat(value / 1,0);
                    		}
                		}
	                }],
	        },

			legend: {
				display: true,
				position: 'bottom',
					labels: {
					boxWidth: 15,
				}
			}
		}
	});

	var ctxBar12 = document.getElementById('chart_bylainnya').getContext('2d');
	myChart12 = new Chart(ctxBar12, {
		type: 'bar',
		options: {
        "hover": {
            "animationDuration": 0
        },
          "hover": {
            "animationDuration": 0
        },
        "animation": {
            "duration": 1,
            "onComplete": function () {
                var chartInstance = this.chart,
                ctx = chartInstance.ctx;

                ctx.font = Chart.helpers.fontString(8, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                ctx.textAlign = 'center';
                ctx.textBaseline = 'bottom';

                this.data.datasets.forEach(function (dataset, i) {
                    var meta = chartInstance.controller.getDatasetMeta(i);
                    meta.data.forEach(function (bar, index) {
                        var data = dataset.data[index];
                        data = (parseFloat(data) / 1000);
                        data = toFixedIfNecessary(data,1);
                        ctx.fillText(data, bar._model.x, bar._model.y - 5);
                    });
                });
            }
        },
        legend: {
            "display": false
        },
        tooltips: {
            "enabled": false
        },

			title: {
                display: false,
                text: 'KREDIT',
                fontSize: 14,
                padding: 10
            },
			maintainAspectRatio: false,
			responsive: true,
		    scales: {
			  xAxes: [{
			      beginAtZero: true,
			      ticks: {
			         autoSkip: false
			      }
			  }],
	            yAxes: [{
	                    display: true,
	                    scaleLabel: {
	                        display: false,
	                        labelString: 'PENDAPATAN (Rp Juta)'
	                    },
	                    ticks: {
                    	// Abbreviate the millions
                    		callback: function(value, index, values) {
                        	return numberFormat(value / 1,0);
                    		}
                		}
	                }],
	        },

			legend: {
				display: true,
				position: 'bottom',
					labels: {
					boxWidth: 15,
				}
			}
		}
	});


});
