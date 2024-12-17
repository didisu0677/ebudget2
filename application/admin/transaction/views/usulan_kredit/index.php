<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		
		<div class="float-right">
			<?php
			input('hidden',lang('user'),'user_cabang','',user('kode_cabang'));
			?>
			<label class=""><?php echo lang('anggaran'); ?>  &nbsp</label>

			<select class="select2 infinity custom-select" id="filter_anggaran">
				<?php foreach ($tahun as $tahun) { ?>
                <option value="<?php echo $tahun->kode_anggaran; ?>"<?php if($tahun->kode_anggaran == user('kode_anggaran')) echo ' selected'; ?>><?php echo $tahun->keterangan; ?></option>
                <?php } ?>
			</select>						
			               		
			
			<label class=""><?php echo lang('cabang'); ?>  &nbsp</label>
				
			<select class="select2 custom-select" id="filter_cabang">
                <?php foreach($cabang as $b){ ?>
                <option value="<?php echo $b['kode_cabang']; ?>" <?php if($b['kode_cabang'] == user('kode_cabang')) echo ' selected'; ?>><?php echo $b['nama_cabang']; ?></option>
                <?php } ?>
			</select>   	
    		
    		<?php 
			if (in_array(user('id_group'), id_group_access('usulan_besaran'), TRUE)){
				echo '<button class="btn btn-success btn-save" href="javascript:;" > Save <span class="fa-save"></span></button>';
				$arr = [];
					$arr = [
						// ['btn-save','Save Data','fa-save'],
					    ['btn-export','Export Data','fa-upload'],
					    ['btn-import','Import Data','fa-download'],
					    ['btn-template','Template Import','fa-reg-file-alt']
					];
				echo access_button('',$arr); 
			}
			?>
    		</div>
			<div class="clearfix"></div>
			
		</div>
	</div>

<div class="content-body">
	<?php
	$thn_sebelumnya = user('tahun_anggaran') -1;
	table_open('',true,'','','data-table="tbl_m_produk"');
		thead();
			tr();
				th(lang('no'),'','width="60" class="text-center align-middle"');
				th(lang('keterangan'),'','width="260" rowspan="2" class="text-center align-middle"');
				th('Jan '.user('tahun_anggaran'),'','class="text-center"');
				th('Feb '.user('tahun_anggaran'),'','class="text-center"');
				th('Mar '.user('tahun_anggaran'),'','class="text-center"');
				th('Apr '.user('tahun_anggaran'),'','class="text-center"');
				th('May '.user('tahun_anggaran'),'','class="text-center"');
				th('Jun '.user('tahun_anggaran'),'','class="text-center"');
				th('Jul '.user('tahun_anggaran'),'','class="text-center"');
				th('Aug '.user('tahun_anggaran'),'','class="text-center"');
				th('Sep '.user('tahun_anggaran'),'','class="text-center"');
				th('Oct '.user('tahun_anggaran'),'','class="text-center"');
				th('Nov '.user('tahun_anggaran'),'','class="text-center"');
				th('Dec '.user('tahun_anggaran'),'','class="text-center"');
				th('&nbsp;','','width="30" class="text-center align-middle"');
		tbody();
	table_close();
	?>
</div>
<?php 

modal_open('modal-form','','modal-lg','data-openCallback="formOpen"');
	modal_body();
		form_open(base_url('transaction/usulan_kredit/save'),'post','form'); 
				col_init(2,4);
				input('hidden','id','id');

			input('text',lang('tahun'),'tahun_anggaran','',user('tahun_anggaran'),'disabled');
				col_init(2,9);
			?>
	

			<div class="form-group row">
				<label class="col-form-label col-md-2"><?php echo lang('cabang'); ?>  &nbsp</label>
				<div class="col-md-4 col-9 mb-1 mb-md-0">	
					<select class="select2 infinity custom-select" id="kode_cabang" name="kode_cabang">
		                <?php foreach($cabang_input as $b){ ?>
		                <option value="<?php echo $b['kode_cabang']; ?>" <?php if($b['kode_cabang'] == user('kode_cabang')) echo ' selected'; ?>><?php echo $b['nama_cabang']; ?></option>
		                <?php } ?>
					</select>    
				</div>
			</div>

			<div class="card mb-2">
				<div class="card-header"><?php echo lang('target_kredit'); ?></div>
				<div class="card-body">
		            <div class="form-group row">
						<div class="col-md-7 col-9 mb-1 mb-md-0">
							<input type="text" name="keterangan[]" autocomplete="off" class="form-control keterangan" data-validation="required|max-length:255" placeholder="<?php echo lang('keterangan'); ?>" aria-label="<?php echo lang('keterangan'); ?>" id="keterangan">
						</div>

						<div class="col-md-4 col-9 mb-1 mb-md-0">
		                    <select id="grup_aset" class="form-control col-md-9 col-xs-9 grup_aset select2" name="grup_aset[]" data-validation="required" aria-label="<?php echo lang('grup_aset'); ?>">
								<option value=""></option>
								<?php foreach($opt_grup as $u) {
									echo '<option value="'.$u['keterangan'].'">'.$u['keterangan'].'</option>';
								} ?>
		                    </select>
		                </div>


						<div class="col-md-1 col-3 mb-1 mb-md-0">
							<button type="button" class="btn btn-block btn-success btn-icon-only btn-add-anggota"><i class="fa-plus"></i></button>
							<button type="button" class="btn btn-block btn-danger btn-icon-only btn-remove-anggota"><i class="fa-times"></i></button>
						</div>
					</div>	
					<div id="additional-anggota" class="mb-2"></div>
				</div>	
			</div>		



	<?php

				form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close(); ?>

?>

<script type="text/javascript">

$('#filter_tahun').change(function(){
	getData();
});

$('#filter_cabang').change(function(){
	getData();
});

$(document).ready(function () {

	getData();
	select_value = $('#grup_aset').html();
    $(document).on('keyup', '.calculate', function (e) {
        calculate();
    });
});	

$('#filter_tahun').change(function(){
	getData();
});

function getData() {
	cLoader.open(lang.memuat_data + '...');
	var page = base_url + 'transaction/usulan_kredit/data';
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
			cLoader.close();
			cek_autocode();
			fixedTable();
			var item_act	= {};
			if($('.table-app tbody .btn-input').length > 0) {
				item_act['edit'] 		= {name : lang.ubah, icon : "edit"};					
			}

			var kode_cabang;
			var cabang ;

			kode_cabang = $('#user_cabang').val();
			cabang = $('#filter_cabang').val();


			if(kode_cabang != cabang) {	
				$(".btn-add").prop("disabled", true);
				$(".btn-input").prop("disabled", true);
				$(".btn-save").prop("disabled", true);	
			}else{
				$(".btn-add").prop("disabled", false);
				$(".btn-input").prop("disabled", false);
				$(".btn-save").prop("disabled", false);	
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
$(document).on('blur','.edit-value',function(){
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

function calculate() {
	var total_budget = 0;

	$('#result tbody tr').each(function(){
		if($(this).find('.budget').length == 1) {
			var subtotal_budget = moneyToNumber($(this).find('.budget').val());
			total_budget += subtotal_budget;
		}


	});

	$('#total_budget').val(total_budget);
}

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
		data_edit[$(this).attr('data-id')][$(this).attr('data-name')] = $(this).text().replace(/[^0-9\-]/g,'');
		i++;
	});
	
	var jsonString = JSON.stringify(data_edit);		
	$.ajax({
		url : base_url + 'transaction/usulan_kredit/save_perubahan',
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


$('.btn-import').click(function(){
	$('#form-import')[0].reset();
	$('#tahun').val($('#filter_tahun').val()).trigger("change")
	$('#kode_harga').val($('#filter_harga').val()).trigger("change");
	$('#bisunit').val($('#filter_divisi').val()).trigger("change");

    $('#modal-import .alert').hide();
    $('#modal-import').modal('show');

});


$(document).on('click','.btn-export',function(){
	var currentdate = new Date(); 
	var datetime = currentdate.getDate() + "/"
	                + (currentdate.getMonth()+1)  + "/" 
	                + currentdate.getFullYear() + " @ "  
	                + currentdate.getHours() + ":"  
	                + currentdate.getMinutes() + ":" 
	                + currentdate.getSeconds();
	
	$('.bg-grey-2').each(function(){
		$(this).attr('bgcolor','#f4f4f4');
	});
	$('.bg-grey-2').each(function(){
		$(this).attr('bgcolor','#dddddd');
	});
	$('.bg-grey-2-1').each(function(){
		$(this).attr('bgcolor','#b4b4b4');
	});
	$('.bg-grey-2-2').each(function(){
		$(this).attr('bgcolor','#aaaaaa');
	});
	$('.bg-grey-2').each(function(){
		$(this).attr('bgcolor','#888888');
	});
	var table	= '<table>';
	table += '<tr><td colspan="1">Bank Jateng</td></tr>';
	table += '<tr><td colspan="1"> Usulan Bottom Up Besaran Tertentu </td><td colspan="25">: '+$('#filter_tahun option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Cabang </td><td colspan="25">: '+$('#filter_cabang option:selected').text()+'</td></tr>';
	table += '<tr><td colspan="1"> Print date </td><td colspan="25">: '+datetime+'</td></tr>';
	table += '</table><br />';
	table += '<table border="1">';
	table += $('.content-body').html();
	table += '</table>';
	var target = table;
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(target));
	$('.bg-grey-1,.bg-grey-2.bg-grey-2-1,.bg-grey-2-2,.bg-grey-3').each(function(){
		$(this).removeAttr('bgcolor');
	});
});

$(document).on('click','.btn-template',function(){
	var page = base_url + 'pl_sales/target_produk/template'
	   $.ajax({
		      url:page,
		      complete: function (response) {
		    	  window.open(page);
		      },
		  });
});

$('.btn-add-anggota').click(function(){
	add_row_anggota();
});
$(document).on('click','.btn-remove-anggota',function(){
	$(this).closest('.form-group').remove();
});

var select_value = '';
function add_row_anggota() {
	konten = '<div class="form-group row">'
			+ '<div class="col-md-7 col-9 mb-1 mb-md-0">'
			+ '<input type="text" name="keterangan[]" autocomplete="off" class="form-control keterangan" data-validation="required|max-length:255" placeholder="'+$('#keterangan').attr('placeholder')+'" aria-label="'+$('#keterangan').attr('placeholder')+'">'
			+ '</div>'
			+ '<div class="col-md-4 col-9 mb-1 mb-md-0">'
			+ '<select class="form-control grup_aset" name="grup_aset[]" data-validation="required" aria-label="'+$('#grup_aset').attr('aria-label')+'">'+select_value+'</select> '
			+ '</div>'
			+ '<div class="col-md-1 col-3 mb-1 mb-md-0">'
			+ '<button type="button" class="btn btn-block btn-danger btn-icon-only btn-remove-anggota"><i class="fa-times"></i></button>'
			+ '</div>'
			+ '</div>'
			$('#additional-anggota').append(konten);
			var $t = $('#additional-anggota .grup_aset:last-child');
			$t.select2({
				dropdownParent : $t.parent()
			});

			var $t = $('#additional-anggota .bulan_aset:last-child');
			$t.select2({
				dropdownParent : $t.parent()
			});
}

function formOpen() {
	$('#additional-anggota').html('');
	var response = response_edit;
	if(typeof response.id != 'undefined') {
		$('.btn-remove-anggota').show();
		$('.btn-add-anggota').hide();
		$.each(response.detail_ket,function(e,d){
			if(e == '0') {
				$('#keterangan').val(d.keterangan);
				$('#grup_aset').val(d.grup).trigger('change');
			} else {
				add_row_anggota();
				$('#additional-anggota .keterangan').last().val(d.keterangan);
				$('#additional-anggota .grup_aset').last().val(d.grup).trigger('change');	
			}
		});
	}else {

		$('.btn-remove-anggota').hide();
		$('.btn-add-anggota').show();
	}
}
</script>