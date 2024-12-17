<?php defined('BASEPATH') OR exit('No direct script access allowed');
// Create by MW 20201201

function check_min_value($v,$x){
	$val = kali_minus($v,$x);
	// $val = custom_format($val);
	$val = custom_format(view_report($val));
	return $val;
}
function check_value($v){
	// $val = kali_minus($v,$x);
	$val = custom_format(view_report($v));
	return $val;
}

function remove_spaces($val){
	return preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $val);
}

function checkRealisasiKolektibilitas($p1,$data){
	if($p1['sumber_data'] == 2):
		$key = multidimensional_search($data, array(
			'sumber_data' => $p1['sumber_data'],
			'id_kolektibilitas' => $p1['id'],
			'parent_index' => $p1['cabang'],
		));
		$d = $data[$key];
	else:
		$key = multidimensional_search($data, array(
			'sumber_data' => $p1['sumber_data'],
			'id_kolektibilitas' => $p1['id'],
			'parent_index' => '0'
		));
		$d = $data[$key];
	endif;
	return $d;
}

function checkMonthAnggaran($anggaran){
	$bulan 	= sprintf('%02d', $anggaran->bulan_terakhir_realisasi);
	$date 	= "01-".$bulan.'-'.$anggaran->tahun_terakhir_realisasi;
	return minusMonth($date,1);
}

function minusMonth($date,$minus){
	$date = date("m-Y", strtotime($date." -".$minus." months"));
	return $date;
}

function insert_formula_kolektibilitas($data,$anggaran){
	$d=[];
	$table = 'tbl_formula_kolektibilitas';
	foreach ($data as $k => $v) {
		$x 		= explode("-", $k);
		$coa 	= $x[0];
		$thn 	= $x[1];
		$sumber_data = $x[2];
		$cabang = $x[3];

		$h = [
			'coa' => $coa,
		];
		$h['kode_anggaran'] 		= $anggaran->kode_anggaran;
		$h['tahun_anggaran'] 		= $anggaran->tahun_anggaran;
		$h['keterangan_anggaran'] 	= $anggaran->keterangan;
		$h['kode_cabang']			= $cabang;
		$h['tahun_core'] 			= $thn;
		$h['sumber_data'] 			= $sumber_data;
		foreach ($v as $k2 => $v2) {
			$h[$k2] 					= $v2;
		}
		insert_data($table,$h);
		$d[] = $h;
	}
	// render($d,'json');
}

function update_formula_kolektibilitas($data,$anggaran){
	$kode_anggaran 		= $anggaran->kode_anggaran;
	$tahun_anggaran 	= $anggaran->tahun_anggaran;
	$keterangan_anggaran 	= $anggaran->keterangan;
	$table = 'tbl_formula_kolektibilitas';
	foreach ($data as $k => $v) {
		$x 		= explode('-', $k);
		$id 	= $x[0];
		$coa 	= $x[1];
		$thn 	= $x[2];
		$sumber_data = $x[3];
		$cabang = $x[4];
		if(strlen(strpos($coa,'sumkol123'))>0):
			$ck = get_data($table,[
				'select'	=> 'id',
				'where' 	=> "coa = '$coa' and kode_cabang = '$cabang' and sumber_data = '$sumber_data' and kode_anggaran = '$kode_anggaran' and tahun_core = '$thn'",
			])->result();
			if(count($ck)>0):
				update_data($table,$v,['coa','tahun_core','sumber_data','kode_cabang','kode_anggaran'],[$coa,$thn,$sumber_data,$cabang,$kode_anggaran]);
			else:
				$h = $v;
				$h['coa'] 					= $coa;
				$h['kode_anggaran'] 		= $anggaran->kode_anggaran;
				$h['tahun_anggaran'] 		= $anggaran->tahun_anggaran;
				$h['keterangan_anggaran'] 	= $anggaran->keterangan;
				$h['kode_cabang']			= $cabang;
				$h['tahun_core'] 			= $thn;
				$h['sumber_data'] 			= $sumber_data;
				insert_data($table,$h);
			endif;
		elseif(strlen(strpos($coa, '_total'))>0):
			update_data($table,$v,['coa','tahun_core','sumber_data','kode_cabang','kode_anggaran'],[$coa,$thn,$sumber_data,$cabang,$kode_anggaran]);
		else:
			update_data($table,$v,'id',$id);
		endif;
	}
}

function filter_money($val){
 	$value = str_replace('.', '', $val);
    $value = str_replace(',', '.', $value);
    if(strlen(strpos($value, '('))>0):
    	$value = str_replace('(', '', $value);
    	$value = str_replace(')', '', $value);
    	$value = '-'.$value;
    endif;
    return $value;
}

function rate_icon_budget_nett($val){
	$icon = '';
	if($val<80):
		$icon = ' <div class="float-left"><img height="11" src="'.base_url('assets/images/close.png').'"/></div>';
	elseif($val>=80 && $val<90):
		$icon = ' <div class="float-left r-45"><img height="11" src="'.base_url('assets/images/right-arrow.png').'"/></div>';
	elseif($val>=90 && $val<100):
		$icon = ' <div class="float-left"><img height="11" src="'.base_url('assets/images/right-arrow2.png').'"/></div>';
	elseif($val>=100 && $val<115):
		$icon = ' <div class="float-left r-45-"><img height="11" src="'.base_url('assets/images/right-arrow.png').'"/></div>';
	elseif($val>115):
		$icon = ' <div class="float-left"><img height="11" src="'.base_url('assets/images/star.png').'"/></div>';
	endif;

	return $icon;
}