<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plan_formula_kantor_pusat extends BE_Controller {
    var $path = 'transaction/budget_planner/';
    var $controller = 'kantor_pusat/formula_kantor_pusat/';
    var $detail_tahun;
    var $kode_anggaran;
    function __construct() {
        parent::__construct();
        $this->kode_anggaran  = user('kode_anggaran');
        $this->detail_tahun   = get_data('tbl_detail_tahun_anggaran a',[
            'select'    => 'a.bulan,a.tahun,a.sumber_data,b.singkatan',
            'join'      => 'tbl_m_data_budget b on b.id = a.sumber_data',
            'where'     => [
                'a.kode_anggaran' => $this->kode_anggaran,
                'a.sumber_data'   => array(2,3)
            ],
            'order_by' => 'tahun,bulan'
        ])->result();
    }

    private function data_cabang(){
        $cabang_user  = get_data('tbl_user',[
            'where' => [
                'is_active' => 1,
                'id_group'  => id_group_access('plan_input_akt')
            ]
        ])->result();
        
        $kode_cabang          = [];
        foreach($cabang_user as $c) $kode_cabang[] = $c->kode_cabang;

        $id = user('id_struktur');
        if($id){
            $cab = get_data('tbl_m_cabang','id',$id)->row();
        }else{
            $id = user('kode_cabang');
            $cab = get_data('tbl_m_cabang','kode_cabang',$id)->row();
        }

        $x ='';
        for ($i = 1; $i <= 4; $i++) { 
            $field = 'level' . $i ;

            if($cab->id == $cab->$field) {
                $x = $field ; 
            }    
        }    

        $data['cabang']            = get_data('tbl_m_cabang a',[
            'select'    => 'distinct a.kode_cabang,a.nama_cabang,a.level_cabang',
            'where'     => [
                'a.is_active' => 1,
                'a.'.$x => $cab->id,
                'a.kode_cabang' => $kode_cabang
            ]
        ])->result_array();

        $data['cabang_input'] = get_data('tbl_m_cabang a',[
            'select'    => 'distinct a.kode_cabang,a.nama_cabang,a.level_cabang',
            'where'     => [
                'a.is_active' => 1,
                'a.kode_cabang' => user('kode_cabang')
            ]
        ])->result_array();

        $data['tahun'] = get_data('tbl_tahun_anggaran','kode_anggaran',user('kode_anggaran'))->result();
        $data['path'] = $this->path;
        $data['detail_tahun'] = $this->detail_tahun;
        return $data;
    }
    
    function index($p1="") { 

        $data = $this->data_cabang();
        render($data,'view:'.$this->path.$this->controller.'index');
    }

    function data($anggaran="", $cabang=""){

        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$anggaran)->row();

        $bln_trakhir = $anggaran->bulan_terakhir_realisasi;
        $getMinBulan = $anggaran->bulan_terakhir_realisasi - 1;
        $thn_trakhir = $anggaran->tahun_terakhir_realisasi;
        $tbl_history = 'tbl_history_'.$thn_trakhir;

        $dataA['detail_tahun'] = $this->detail_tahun;
        $dataB['detail_tahun'] = $this->detail_tahun;
        $dataC['detail_tahun'] = $this->detail_tahun;
        $dataD['detail_tahun'] = $this->detail_tahun;

        $ck = $this->db->field_exists("TOT_".$cabang, $tbl_history);
        if(!$ck):
            render(['status'=>false,'message' => str_replace('{$table}', "TOT_".$cabang, lang('table_not_found'))], 'json');
            exit();
        endif;
        
        $select     = 'a.glwsbi,a.glwnco,a.glwdes';

        $select2    = "coalesce(sum(case when b.bulan = '".$bln_trakhir."' then b.TOT_".$cabang." end), 0) as hasil, coalesce(sum(case when b.bulan = '".$getMinBulan."'  then b.TOT_".$cabang." end), 0) as hasil2";
        
        $dataA['A'] = get_data('tbl_m_coa a',[
            'select' => $select.','.$select2,
            'order_by' => 'a.id',

            'join' => "$tbl_history b on b.bulan = '$bln_trakhir' and a.glwnco = b.glwnco type left",

            'where'     => " a.is_active = '1' and a.glwnco like '1621013%' or a.glwnco like '1622013%' or a.glwnco like '5621011%' group by a.glwdes",

        ])->result();

        $dataA['valA'] = get_data('tbl_m_coa a',[
            'select' => $select.','.$select2,
            'order_by' => 'a.id',

            'join' => "$tbl_history b on b.bulan = '$bln_trakhir' and a.glwnco = b.glwnco type left",

            'where'     => " a.is_active = '1' and a.glwnco like '1621013%' group by a.glwdes",

        ])->result_array();


        $dataA['E2'] = get_data('tbl_rencana_aset a',[
            'select' => 'sum(harga * jumlah) as total, bulan',
            'where'     => " is_active = '1' and grup = 'E.2' and kode_cabang = '".$cabang."'",
        ])->result();

         $dataB['B'] = get_data('tbl_m_coa a',[
            'select' => $select.','.$select2,
            'order_by' => 'a.id',

            'join' => "$tbl_history b on b.bulan = '$bln_trakhir' and a.glwnco = b.glwnco type left",

            'where'     => " a.is_active = '1' and a.glwnco like '1621014%' or a.glwnco like '1622014%' or a.glwnco like '5621014%' group by a.glwdes",

        ])->result();


        $dataB['valB'] = get_data('tbl_m_coa a',[
            'select' => $select.','.$select2,
            'order_by' => 'a.id',

            'join' => "$tbl_history b on b.bulan = '$bln_trakhir' and a.glwnco = b.glwnco type left",

            'where'     => " a.is_active = '1' and a.glwnco like '1621013%' group by a.glwdes",

        ])->result_array();


        $dataB['E4'] = get_data('tbl_rencana_aset a',[
            'select' => 'sum(harga * jumlah) as total, bulan',
            'where'     => " is_active = '1' and grup = 'E.4' and kode_cabang = '".$cabang."'",
        ])->result();


        $dataC['C'] = get_data('tbl_m_coa a',[
            'select' => $select.','.$select2,
            'order_by' => 'a.id',

            'join' => "$tbl_history b on b.bulan = '$bln_trakhir' and a.glwnco = b.glwnco type left",

            'where'     => " a.is_active = '1' and a.glwnco like '1621015%' or a.glwnco like '1622015%' or a.glwnco like '5621015%' group by a.glwdes",

        ])->result();

        $dataC['valC'] = get_data('tbl_m_coa a',[
            'select' => $select.','.$select2,
            'order_by' => 'a.id',

            'join' => "$tbl_history b on b.bulan = '$bln_trakhir' and a.glwnco = b.glwnco type left",

            'where'     => " a.is_active = '1' and a.glwnco like '1621013%' group by a.glwdes",

        ])->result_array();


        $dataC['E5'] = get_data('tbl_rencana_aset a',[
            'select' => 'sum(harga * jumlah) as total, bulan',
            'where'     => " is_active = '1' and grup = 'E.5' and kode_cabang = '".$cabang."'",
        ])->result();

        $dataC['D'] = get_data('tbl_m_coa a',[
            'select' => $select.','.$select2,
            'order_by' => 'a.id',

            'join' => "$tbl_history b on b.bulan = '$bln_trakhir' and a.glwnco = b.glwnco type left",

            'where'     => " a.is_active = '1' and a.glwnco like '1621016%' or a.glwnco like '1622016%' or a.glwnco like '5621012%' group by a.glwdes",

        ])->result();

        $dataD['valD'] = get_data('tbl_m_coa a',[
            'select' => $select.','.$select2,
            'order_by' => 'a.id',

            'join' => "$tbl_history b on b.bulan = '$bln_trakhir' and a.glwnco = b.glwnco type left",

            'where'     => " a.is_active = '1' and a.glwnco like '1621013%' group by a.glwdes",

        ])->result_array();


        $dataD['E3'] = get_data('tbl_rencana_aset a',[
            'select' => 'sum(harga * jumlah) as total, bulan',
            'where'     => " is_active = '1' and grup = 'E.3' and kode_cabang = '".$cabang."'",
        ])->result();

        $view = '';

        $view .= $this->load->view($this->path.$this->controller.'table',$dataA,true);
        $view .= $this->load->view($this->path.$this->controller.'tableB',$dataB,true);
        $view .= $this->load->view($this->path.$this->controller.'tableC',$dataC,true);
        $view .= $this->load->view($this->path.$this->controller.'tableD',$dataD,true);


        // echo json_encode($dataB);    

        $response   = array(
            'table'     => $view,
            'status'    => true,
        );
        render($response,'json');
    }

    

}