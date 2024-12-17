<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('delete,active,inactive,export,import'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/master_coa/data'),'tbl_m_coa');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('glwsbi'),'','data-content="glwsbi"');
				th(lang('glwnob'),'','data-content="glwnob"');
				th(lang('glwcoa'),'','data-content="glwcoa"');
				th(lang('glwnco'),'','data-content="glwnco"');
				th(lang('glwdes'),'','data-content="glwdes"');
				th(lang('kali minus').'?','text-center','data-content="kali_minus" data-type="boolean"');
				th(lang('kantor_pusat').'?','text-center','data-content="kantor_pusat" data-type="boolean"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/master_coa/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('text',lang('glwsbi'),'glwsbi');
			input('text',lang('glwnob'),'glwnob');
			input('text',lang('glwcoa'),'glwcoa');
			input('text',lang('glwnco'),'glwnco');
			input('text',lang('glwdes'),'glwdes');
			input('text',lang('glwdes'),'glwdes');
			toggle(lang('kali minus').'?','kali_minus');
			toggle(lang('kantor_pusat').'?','kantor_pusat');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/master_coa/import'),'post','form-import');
			col_init(3,9);
			fileupload('File Excel','fileimport','required','data-accept="xls|xlsx"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
?>
