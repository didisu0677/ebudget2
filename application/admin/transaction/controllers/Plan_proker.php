<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plan_proker extends BE_Controller {
    var $path       = 'transaction/budget_planner/kantor_pusat/';
    var $sub_menu   = 'transaction/budget_planner/sub_menu';
    var $detail_tahun;
    var $kode_anggaran;
    var $arr_sumber_data = array();
    function __construct() {
        parent::__construct();
        $this->kode_anggaran  = user('kode_anggaran');
    }
    
    function index($p1="") { 
        $data = data_cabang();
        $data['path']     = $this->path;
        $data['sub_menu'] = $this->sub_menu;
        render($data,'view:'.$this->path.'proker/index');
    }

    private  function check_sumber_data($sumber_data){
        $key = array_search($sumber_data, array_map(function($element){return $element->sumber_data;}, $this->detail_tahun));
        if(strlen($key)>0):
            array_push($this->arr_sumber_data,$sumber_data);
        endif;
    }

    function get_coa(){
        $ls             = get_data('tbl_m_coa a',[
            'where'     => "a.is_active = 1 and a.kantor_pusat = 1"
        ])->result();
        return $ls;
    }

    function data($anggaran="", $cabang="", $tipe = 'table') {
        $menu = menu();
        $ckode_anggaran = $anggaran;
        $ckode_cabang = $cabang;

        $a = get_access('plan_proker');
        $data['akses_ubah'] = $a['access_edit'];

        $data['current_cabang'] = $cabang;

        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$ckode_anggaran)->row();
              
        $arr            = [
            'select'    => '
                a.*,
                b.nama as kebijakan_umum,
                c.glwnco,
                c.glwdes
            ',
        ];

        if($anggaran) {
            $arr['where']['a.kode_anggaran']  = $ckode_anggaran;
        }
        
        if($cabang) {
            $arr['where']['a.kode_cabang']  = $ckode_cabang;
        }
        $arr['join'][]= 'tbl_kebijakan_umum b on b.id = a.id_kebijakan_umum';
        $arr['join'][]= 'tbl_m_coa c on c.glwnco = a.coa type left';
        $arr['order_by']= 'a.id';
        $data['list'] = get_data('tbl_input_rkf a',$arr)->result();          
        $data['coa_list']  = $this->get_coa();
        $response   = array(
            'table'     => $this->load->view($this->path.'proker/table',$data,true),
        );
       
        render($response,'json');
    }

    function save_perubahan() {       
        $data   = json_decode(post('json'),true);
        foreach($data as $id => $record) {          
            update_data('tbl_input_rkf',$record,'id',$id); }
    }
}