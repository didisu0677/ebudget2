<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Laba_rugi_new extends BE_Controller {
    var $path = 'transaction/budget_planner/';
    function __construct() {
        parent::__construct();
    }

    private function data_cabang(){
        $cabang_user  = get_data('tbl_user',[
            'where' => [
                'is_active' => 1,
                'id_group'  => id_group_access('laba_rugi_new')
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
            'select'    => 'distinct a.kode_cabang,a.nama_cabang,level_cabang',
            'where'     => [
                'a.is_active' => 1,
                'a.'.$x => $cab->id,
                'a.kode_cabang' => $kode_cabang
            ]
        ])->result_array();

        $data['cabang_input'] = get_data('tbl_m_cabang a',[
            'select'    => 'distinct a.kode_cabang,a.nama_cabang,level_cabang',
            'where'     => [
                'a.is_active' => 1,
                'a.kode_cabang' => user('kode_cabang')
            ]
        ])->result_array();

        $data['tahun'] = get_data('tbl_tahun_anggaran','kode_anggaran',user('kode_anggaran'))->result();
        $data['path'] = $this->path;
        return $data;
    }
    
    function index($p1="") { 
        $data = $this->data_cabang();
        $data['bulan_terakhir'] = month_lang($data['tahun'][0]->bulan_terakhir_realisasi);
        render($data,'view:'.$this->path.'laba_rugi_new/index');
    }

     function data ($anggaran="", $cabang=""){
        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$anggaran)->row();

        $bln_trakhir = $anggaran->bulan_terakhir_realisasi;
        $thn_trakhir = $anggaran->tahun_terakhir_realisasi;
        $tbl_history = 'tbl_history_'.$thn_trakhir;

        $or_neraca  = "(a.glwnco like '4%' or a.glwnco like '5%')";
        $select     = 'distinct level1,level2,level3,level4,level5,
                    a.glwsbi,a.glwnob,a.glwcoa,a.glwnco,a.glwdes,a.kali_minus';
        $coa = get_data('tbl_m_coa a',[
            'select' => $select.',b.TOT_'.$cabang,
            'where' => "
                a.is_active = '1' and $or_neraca
                ",
            'order_by' => 'a.id',
            'join' => "$tbl_history b on b.bulan = '$bln_trakhir' and a.glwnco = b.glwnco type left"
        ])->result();
        $coa = $this->get_coa($coa);


        $query_result     = $this->db->query("CALL stored_laba_rugi_nett('$cabang','$anggaran->kode_anggaran','$anggaran->tahun_anggaran')");
        // $query_result = $this->get_data_net($anggaran->kode_anggaran,$cabang,$anggaran->tahun_anggaran);
        $data['stored']           = $query_result->result_array();

        //add this two line 
        $query_result->next_result(); 
        $query_result->free_result(); 
        //end of new code

        // $coa = $this->get_list_coa($coa,$detail);

        $data['coa']    = $coa['coa'];
        $data['detail'] = $coa['detail'];
        $data['cabang'] = $cabang;
        $data['bulan_terakhir'] = $bln_trakhir;

        // $data['biaya'] = get_data("tbl_biaya",[
        //     'kode_cabang'   => $cabang,
        // ])->result_array();


        // echo json_encode($data);
        $response   = array(
            'table'     => $this->load->view($this->path.'laba_rugi_new/table',$data,true),
            'data' => $data,
        );
        render($response,'json');
    }

     private function get_list_coa($coa,$detail){
        $data = [];
        foreach ($coa as $k => $v) {
            // level 0
            if(!$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
                $key = multidimensional_search($detail, array(
                    'coa' => $v->glwnco,
                ));
                $h = $v;
                if(strlen($key)>0):
                    $h = array_merge($h,$detail[$key]);
                endif;
                $data['coa'][] = $h;
            endif;

            // level 1
            if($v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
                $key = multidimensional_search($detail, array(
                    'level1' => $v->level1,
                    'coa'    => $v->glwnco,
                ));
                $h = (array) $v;
                if(strlen($key)>0):
                    $h = array_merge($h,$detail[$key]);
                endif;
                $data['detail']['1'][$v->level1][] = $h;
            endif;

            // level 2
            if(!$v->level1 && $v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
                $key = multidimensional_search($detail, array(
                    'level2' => $v->level2,
                    'coa'    => $v->glwnco,
                ));
                $h = (array) $v;
                if(strlen($key)>0):
                    $h = array_merge($h,$detail[$key]);
                endif;
                $data['detail']['2'][$v->level2][] = $h;
            endif;

            // level 3
            if(!$v->level1 && !$v->level2 && $v->level3 && !$v->level4 && !$v->level5):
                $key = multidimensional_search($detail, array(
                    'level3' => $v->level3,
                    'coa'    => $v->glwnco,
                ));
                $h = (array) $v;
                if(strlen($key)>0):
                    $h = array_merge($h,$detail[$key]);
                endif;
                $data['detail']['3'][$v->level3][] = $h;
            endif;

            // level 4
            if(!$v->level1 && !$v->level2 && !$v->level3 && $v->level4 && !$v->level5):
                $key = multidimensional_search($detail, array(
                    'level4' => $v->level4,
                    'coa'    => $v->glwnco,
                ));
                $h = (array) $v;
                if(strlen($key)>0):
                    $h = array_merge($h,$detail[$key]);
                endif;
                $data['detail']['4'][$v->level4][] = $h;
            endif;

            // level 5
            if(!$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && $v->level5):
                $key = multidimensional_search($detail, array(
                    'level5' => $v->level5,
                    'coa'    => $v->glwnco,
                ));
                $h = (array) $v;
                if(strlen($key)>0):
                    $h = array_merge($h,$detail[$key]);
                endif;
                $data['detail']['5'][$v->level5][] = $h;
            endif;
        }
        return $data;
    }

    private function get_coa($coa){
        $data = [];
        foreach ($coa as $k => $v) {
            // level 0
            if(!$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
                $data['coa'][] = $v;
            endif;

            // level 1
            if($v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
                $data['detail']['1'][$v->level1][] = $v;
            endif;

            // level 2
            if(!$v->level1 && $v->level2 && !$v->level3 && !$v->level4 && !$v->level5):
                $data['detail']['2'][$v->level2][] = $v;
            endif;

            // level 3
            if(!$v->level1 && !$v->level2 && $v->level3 && !$v->level4 && !$v->level5):
                $data['detail']['3'][$v->level3][] = $v;
            endif;

            // level 4
            if(!$v->level1 && !$v->level2 && !$v->level3 && $v->level4 && !$v->level5):
                $data['detail']['4'][$v->level4][] = $v;
            endif;

            // level 5
            if(!$v->level1 && !$v->level2 && !$v->level3 && !$v->level4 && $v->level5):
                $data['detail']['5'][$v->level5][] = $v;
            endif;
        }
        return $data;
    }

    function save_perubahan($anggaran="",$cabang="") {       

        $data   = json_decode(post('json'),true);

        // echo post('json');
        foreach($data as $getId => $record) {
            $cekId = $getId;

            $data = insert_view_report_arr($record);

            $cek  = get_data('tbl_labarugi a',[
                'select'    => 'a.id',
                'where'     => [
                    'a.glwnco'             => $cekId,
                    'a.kode_anggaran'   => $anggaran,
                    'a.kode_cabang'   => $cabang,
                ]
            ])->result_array();
     
            if(count($cek) > 0){
                update_data('tbl_labarugi', $data,'id',$cek[0]['id']);
            }else {
                    $record['glwnco'] = $cekId;
                    $record['kode_anggaran'] = $anggaran;
                    $record['kode_cabang'] = $cabang;
                    insert_data('tbl_labarugi',$data);
            } 
         } 
    }


}