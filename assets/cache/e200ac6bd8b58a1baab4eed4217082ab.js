
var rencana = '';
var status = '';
var tahapan1 = '';
var jenis_kantor = '';
var jadwal1 = '';
var index = 0;
function formOpen() {
	$('#result2 tbody').html('');
	var cabang = $('#filter_cabang option:selected').val();
	$('#kode_cabang').val(cabang).trigger('change');
	get_rencana();
	get_jenis_kantor();
	get_tahapan();
	get_jadwal();
	get_status();
	var response = response_edit;
	if(typeof response.id != 'undefined') {
		$('#id').val(response.id);
		$.each(response.detail,function(x,y){
			add_item();
			var f = $('#result2 tbody tr').last();
			f.find('.renc').val(y.id_rencana).trigger('change');
			f.find('.tah').val(y.id_tahapan).trigger('change');
			f.find('.jenis').val(y.id_kategori_kantor).trigger('change');
			f.find('.jadwal').val(y.jadwal).trigger('change');
			f.find('.status_ket').val(y.id_status_kantor).trigger('change');
		});
	}
}


$('#filter_cabang').change(function(){
	getData();
});

$(document).ready(function () {
	resize_window();
	$('#result2 tbody').html('');	
	getData(); 
	get_rencana();
	get_jenis_kantor();
	get_tahapan();
	get_jadwal();
	get_status();
});	


function getData() {
	cLoader.open(lang.memuat_data + '...');
	var page = base_url + 'transaction/usulan_kantor/data';
	page 	+= '/'+$('#filter_anggaran').val();
	page 	+= '/'+$('#filter_cabang').val();

	$.ajax({
		url 	: page,
		data 	: {},
		type	: 'get',
		dataType: 'json',
		success	: function(response) {
			$('.table-app tbody').html(response.table);
			$('#parent_id').html(response.option);
			var kode_cabang;
			var cabang ;

			kode_cabang = $('#user_cabang').val();
			cabang = $('#filter_cabang').val();


			if(!response.edit) {	
				$(".btn-add").prop("disabled", true);
				$(".btn-input").prop("disabled", true);
				$(".btn-save").prop("disabled", true);	
			}else{
				$(".btn-add").prop("disabled", false);
				$(".btn-input").prop("disabled", false);
				$(".btn-save").prop("disabled", false);	
			}

			cLoader.close();
			cek_autocode();
			fixedTable();
			var item_act	= {};
			if($('.table-app tbody .btn-input').length > 0) {
				item_act['edit'] 		= {name : lang.ubah, icon : "edit"};					
			}

			var act_count = 0;
			for (var c in item_act) {
				act_count = act_count + 1;
			}
			if(act_count > 0) {
				$.contextMenu({
			        selector: '.table-app tbody tr', 
			        callback: function(key, options) {
			        	if($(this).find('[data-key="'+key+'"]').length > 0) {
				        	if(typeof $(this).find('[data-key="'+key+'"]').attr('href') != 'undefined') {
				        		window.location = $(this).find('[data-key="'+key+'"]').attr('href');
				        	} else {
					        	$(this).find('[data-key="'+key+'"]').trigger('click');
					        }
					    } 
			        },
			        items: item_act
			    });
			}
		}
	});
}
$(function(){
    getData();
});

$(document).on('dblclick','.table-app tbody td .badge',function(){
	if($(this).closest('tr').find('.btn-input').length == 1) {
		var badge_status 	= '0';
		var data_id 		= $(this).closest('tr').find('.btn-input').attr('data-id');
		if( $(this).hasClass('badge-danger') ) {
			badge_status = '1';
		}
		active_inactive(data_id,badge_status);
	}
});


$(document).on('focus','.edit-value',function(){
	$(this).parent().removeClass('edited');
});
$(document).on('blur','.edit-value, .edit-text',function(){
	var tr = $(this).closest('tr');
	if($(this).text() != $(this).attr('data-value')) {
		$(this).addClass('edited');
	}
	if(tr.find('td.edited').length > 0) {
		tr.addClass('edited-row');
	} else {
		tr.removeClass('edited-row');
	}
});
$(document).on('keyup','.edit-value',function(e){
	var wh 			= e.which;
	if((48 <= wh && wh <= 57) || (96 <= wh && wh <= 105) || wh == 8) {
		if($(this).text() == '') {
			$(this).text('');
		} else {
			var n = parseInt($(this).text().replace(/[^0-9\-]/g,''),10);
		    $(this).text(n.toLocaleString());
		    var selection = window.getSelection();
			var range = document.createRange();
			selection.removeAllRanges();
			range.selectNodeContents($(this)[0]);
			range.collapse(false);
			selection.addRange(range);
			$(this)[0].focus();
		}
	}
});
$(document).on('keypress','.edit-value',function(e){
	var wh 			= e.which;
	if (e.shiftKey) {
		if(wh == 0) return true;
	}
	if(e.metaKey || e.ctrlKey) {
		if(wh == 86 || wh == 118) {
			$(this)[0].onchange = function(){
				$(this)[0].innerHTML = $(this)[0].innerHTML.replace(/[^0-9\-]/g, '');
			}
		}
		return true;
	}
	if(wh == 0 || wh == 8 || wh == 45 || (48 <= wh && wh <= 57) || (96 <= wh && wh <= 105)) 
		return true;
	return false;
});


$(document).on('click','.btn-save',function(){
	var i = 0;
	$('.edited').each(function(){
		i++;
	});
	if(i == 0) {
		cAlert.open('tidak ada data yang di ubah');
	} else {
		var msg 	= lang.anda_yakin_menyetujui;
		if( i == 0) msg = lang.anda_yakin_menolak;
		cConfirm.open(msg,'save_perubahan');        
	}

});

function save_perubahan() {
	var data_edit = {};
	var i = 0;
	
	$('.edited').each(function(){
		var content = $(this).children('div');
		if(typeof data_edit[$(this).attr('data-id')] == 'undefined') {
			data_edit[$(this).attr('data-id')] = {};
		}

		var vfield = ['nama_kantor','kecamatan','keterangan','penjelasan'];

		if (jQuery.inArray($(this).attr('data-name'),vfield) != -1) {
			data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text();
		}else{
			data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text().replace(/[^0-9\-]/g,'');
		}

		i++;
	});
	
	var jsonString = JSON.stringify(data_edit);		
	$.ajax({
		url : base_url + 'transaction/usulan_kantor/save_perubahan',
		data 	: {
			'json' : jsonString,
			verifikasi : i
		},
		type : 'post',
		success : function(response) {
			cAlert.open(response,'success','refreshData');
		}
	})
}


$(document).on('click','.btn-add-item',function(){
	add_item();
});
$(document).on('click','.btn-remove',function(){
	$(this).closest('tr').remove();
});

function add_item() {
	var konten = '<tr>'
			+ '<td width="350"><select class="form-control pilihan renc" name="rencana[]" aria-label="" data-validation="required">'+rencana+'</select></td>';
				konten += '<td width="350"><select class="form-control pilihan tah" width ="200" name="tahapan[]" aria-label="" data-validation="required">'+tahapan1+'</select></td>';
				konten += '<td width="350"><select class="form-control pilihan jenis" name="jenis_kantor[]" aria-label="" data-validation="required">'+jenis_kantor+'</select></td>';
				konten += '<td width="350"><select class="form-control pilihan jadwal" name="jadwal[]" aria-label="" data-validation="required">'+jadwal1+'</select></td>';
				konten += '<td width="350"><select class="form-control pilihan status_ket" name="status_ket[]" aria-label="" data-validation="required">'+status+'</select></td>';
				konten += '<td><button type="button" class="btn btn-sm btn-icon-only btn-danger btn-remove"><i class="fa-times"></i></button></td>';
		+ '</tr>';
	$('#result2 tbody').append(konten);
	var $t = $('#result2 .pilihan:last-child');
	$.each($t,function(k,o){
		var $o = $(o);
		$o.select2({
			dropdownParent : $o.parent(),
			placeholder : ''
		});
	})
	index++;
}

function get_rencana() {
	if(proccess) {
	//	readonly_ajax = false;
		$.ajax({
			url : base_url + 'transaction/usulan_kantor/get_rencana',
			data : {},
			type : 'POST',
			success	: function(response) {
				rencana = response;
	//			readonly_ajax = true;				
			}
		});
	}
}

function get_status() {
	if(proccess) {
	//	readonly_ajax = false;
		$.ajax({
			url : base_url + 'transaction/usulan_kantor/get_status',
			data : {},
			type : 'POST',
			success	: function(response) {
				status = response;
	//			readonly_ajax = true;				
			}
		});
	}
}

function get_tahapan() {
	if(proccess) {
	//	readonly_ajax = false;
		$.ajax({
			url : base_url + 'transaction/usulan_kantor/get_tahapan',
			data : {},
			type : 'POST',
			success	: function(response) {
				tahapan1 = response;
	//			readonly_ajax = true;				
			}
		});
	}
}

function get_jenis_kantor() {
	if(proccess) {
	//	readonly_ajax = false;
		$.ajax({
			url : base_url + 'transaction/usulan_kantor/get_jenis_kantor',
			data : {},
			type : 'POST',
			success	: function(response) {
				jenis_kantor = response;
	//			readonly_ajax = true;				
			}
		});
	}
}

function get_jadwal() {
	if(proccess) {
	//	readonly_ajax = false;
		$.ajax({
			url : base_url + 'transaction/usulan_kantor/get_jadwal',
			data : {},
			type : 'POST',
			success	: function(response) {
				jadwal1 = response;
	//			readonly_ajax = true;				
			}
		});
	}
}

