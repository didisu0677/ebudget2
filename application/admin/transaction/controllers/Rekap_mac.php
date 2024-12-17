<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rekap_mac extends BE_Controller {
	var $controller = 'rekap_mac';
	var $path       = 'transaction/';
	function index() {
	 	$tahun = get_data('tbl_tahun_anggaran','kode_anggaran',user('kode_anggaran'))->result_array();

        $data['controller']     = $this->controller;
        $data['coa'] 			= $this->coa_option();
        $data['tahun']     		= $tahun;
        $data['bulan']     		= $this->month_option();
        render($data);
    }

    private function coa_option(){
    	$data = get_data('tbl_m_budget_control a',[
    		'select' 	=> 'a.coa,b.glwdes as name',
    		'where'		=> 'a.is_active = 1',
    		'join'		=> 'tbl_m_coa b on a.coa = b.glwnco',
    		'order_by'	=> 'a.id',
    	])->result_array();
    	return $data;
    }
    private function month_option(){
    	$data = array();
    	for ($i=1; $i <=12 ; $i++) { 
    		$month = month_lang($i);
    		array_push($data, array('value' => $i,'name' => $month));
    	}
    	return $data;
    }

    function get_content(){
    	$bulan 	= post('bulan');
    	$tahun 	= post('tahun');
    	$coa 	= post('coa');

    	$tahun = get_data('tbl_tahun_anggaran','kode_anggaran',$tahun)->row();
    	$coa   = get_data('tbl_m_coa','glwnco',$coa)->row();

    	$data['bulan'] = $bulan;
    	$data['tahun'] = $tahun;
    	$data['coa'] = $coa;
    	$view 	= $this->load->view($this->path.$this->controller.'/content',$data,true);

    	render([
    		'view' => $view,
    	],'json');
    }

    function data(){
    	$bulan 	= post('bulan');
    	$tahun 	= post('tahun');
    	$coa 	= post('coa');

    	$tahun = get_data('tbl_tahun_anggaran','kode_anggaran',$tahun)->row();

    	$status = true;
    	$tbl_history = 'tbl_history_'.($tahun->tahun_anggaran-1);
    	if(!$this->db->table_exists($tbl_history)):
    		$status = false;
    	endif;

    	$dt_bulan 	 = get_data($tbl_history,['where' => "glwnco = '$coa' and bulan = '$bulan'"])->row();
    	$dt_des 	 = get_data($tbl_history,['where' => "glwnco = '$coa' and bulan = '12'"])->row();
        $dt_mac      = get_data('tbl_control_mac',['where' => "coa = '$coa' and kode_anggaran = '$tahun->kode_anggaran' and bulan = '$bulan'"])->row();

        $dt_column = $this->check_column();
        $tabel  = $dt_column['tabel'];
        $column = $dt_column['column'];
        $where  = $dt_column['where'];

    	$cabang = get_data('tbl_m_cabang a',[
    		'select' 	=> 
                'a.kode_cabang,a.nama_cabang,a.level1,a.level2,a.level3,a.level4,'.
                $column,
    		'where'	 	=> "a.is_active = '1'",
            'join'      => [
                "$tabel c on $where = '$coa' and c.kode_cabang = a.kode_cabang and c.kode_anggaran = '$tahun->kode_anggaran' TYPE LEFT"
            ],
    		'order_by'	=> 'a.kode_cabang',
    	])->result();
    	$cabang = $this->get_cabang($cabang);

    	$data['cabang'] 	= $cabang;
    	$data['dt_bulan'] 	= $dt_bulan;
    	$data['dt_des'] 	= $dt_des;
        $data['dt_mac']     = $dt_mac;
        $data['bulan']      = 'B_'.sprintf("%02d", $bulan);
        $data['bulanx']     = $bulan;
        $data['tahun']      = $tahun;
        $data['coa']        = $coa;
    	$view 	= $this->load->view($this->path.$this->controller.'/table',$data,true);

    	render([
    		'view' 		=> $view,
    		'status' 	=> $status,
    		'cabang' 	=> $cabang,
    	],'json');
    }

    private function get_tabel(){
    	$coa 	= post('coa');
    	$d 		= get_data('tbl_m_budget_control','coa',$coa)->row();
    }

    private function get_cabang($cabang){
    	$data   = [];
        $status = false;
        $fields = [];
    	foreach ($cabang as $k => $v) {
            $tot = 'TOT_'.$v->kode_cabang;
            $arr = [$tot.'_before',$tot.'_12',$tot.'_',$tot,$tot.'_real',$tot.'_penc',$tot.'_pert'];
    		if (!$this->db->field_exists($tot,'tbl_control_mac'))://check filed table
                // $status = true;
                // foreach ($arr as $x) {
                //     $fields[$x] = array(
                //         'type' => 'double',
                //         'null' => TRUE,
                //     );
                // }
            endif;

            if($v->level1 && !$v->level2 && !$v->level3 && !$v->level4): //level 1
    			$data['l1'][] = $v;
    		endif;

    		if($v->level1 && $v->level2 && !$v->level3 && !$v->level4): //level 2
    			$data['l2'][$v->level1][] = $v;
    		endif;

    		if($v->level1 && $v->level2 && $v->level3 && !$v->level4): //level 3
    			$data['l3'][$v->level2][] = $v;
    		endif;

    		if($v->level1 && $v->level2 && $v->level3 && $v->level4): //level 4
    			$data['l4'][$v->level3][] = $v;
    		endif;
    	}

        if($status):
            $this->load->dbforge();
            $this->dbforge->add_column('tbl_control_mac',$fields);
        endif;

    	return $data;
    }

    private function check_column(){
        $coa    = post('coa');
        $bulan  = post('bulan');
        
        $dt  = get_data('tbl_m_budget_control',[
            'select' => 'tabel',
            'where'  => "coa = '$coa' and is_active = '1'" 
        ])->row();
        $column = '';
        $tabel  = '';
        $where  = '';
        if($dt):
            $tabel = $dt->tabel;
            if($dt->tabel == 'tbl_budget_plan_neraca'):
                $c  = 'c.B_'.sprintf("%02d", $bulan);
                $as = 'B_'.sprintf("%02d", $bulan);
                $column .= $c.' as '.$as.', ';
                $where = 'c.coa';
            elseif($dt->tabel == 'tbl_labarugi'):
                $c  = 'c.bulan_'.$bulan;
                $as = 'B_'.sprintf("%02d", $bulan);
                $column .= $c.' as '.$as.', ';
                $where = 'c.glwnco';
            endif;
        endif;

        $data = [
            'column'    => $column,
            'tabel'     => $tabel,
            'where'     => $where,
        ];

        return $data;
    }
}