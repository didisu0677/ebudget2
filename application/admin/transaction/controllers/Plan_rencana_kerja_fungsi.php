<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plan_rencana_kerja_fungsi extends BE_Controller {
    var $path       = 'transaction/budget_planner/kantor_pusat/';
    var $sub_menu   = 'transaction/budget_planner/sub_menu';
    function __construct() {
        parent::__construct();
    }
    
    function index($p1="") { 
        $data = data_cabang();
        $data['path']     = $this->path;
        $data['sub_menu'] = $this->sub_menu;
        render($data,'view:'.$this->path.'rencana_kerja_fungsi/index');
    }

    function get_kebijakan_umum($type="echo"){
        $ls             = get_data('tbl_kebijakan_umum a',[
            'where'     => [
                'a.is_active' => 1,
            ]
        ])->result();
        $data           = '<option value=""></option>';
        foreach($ls as $e2) {
            $data       .= '<option value="'.$e2->id.'">'.$e2->nama.'</option>';
        }

        if($type == 'echo') echo $data;
        else return $data;  
    }
    function get_perspektif($type="echo"){
        $ls             = get_data('tbl_perspektif a',[
            'where'     => [
                'a.is_active' => 1,
            ]
        ])->result();
        $data           = '<option value=""></option>';
        foreach($ls as $e2) {
            $data       .= '<option value="'.$e2->id.'">'.$e2->nama.'</option>';
        }

        if($type == 'echo') echo $data;
        else return $data;  
    }
    function get_skala_program($type="echo"){
        $ls             = get_data('tbl_skala_program a',[
            'where'     => [
                'a.is_active' => 1,
            ]
        ])->result();
        $data           = '<option value=""></option>';
        foreach($ls as $e2) {
            $data       .= '<option value="'.$e2->id.'">'.$e2->nama.'</option>';
        }

        if($type == 'echo') echo $data;
        else return $data;
    }

    function save(){
        $kode_cabang = post('kode_cabang');
        $ckode_anggaran = user('kode_anggaran');

        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$ckode_anggaran)->row();
        $cabang   = get_data('tbl_m_cabang','kode_cabang',user('kode_cabang'))->row();
        $tahun    = $anggaran->tahun_anggaran;


        $dt_id      = post('dt_id');
        $dt_key     = post('dt_key');
        $kebijakan_umum = post('kebijakan_umum');
        $program_kerja  = post('program_kerja');
        $perspektif     = post('perspektif');
        $status_program = post('status_program');
        $skala_program  = post('skala_program');
        $tujuan = post('tujuan');
        $output = post('output');

        $arrID = array();
        foreach ($dt_key as $k => $v) {
            $key    = $v;
            $produk = 0;
            $anggaran_select = "0";
            $x = post('produk'.$key);
            if(isset($x[0])): $produk = $x[0]; endif;
            $x = post('anggaran'.$key);
            if(isset($x[0])): if($x[0]): $anggaran_select = $x[0]; endif; endif;
            $c = [
                'kode_anggaran' => $ckode_anggaran,
                'keterangan_anggaran' => $anggaran->keterangan,
                'tahun'         => $anggaran->tahun_anggaran,
                'kode_cabang'   => $kode_cabang,
                'cabang'        => $cabang->nama_cabang,
                'username'      => user('username'),
                'id_kebijakan_umum'  => $kebijakan_umum[$k],
                'id_perspektif'      => $perspektif[$k],
                'id_skala_program'   => $skala_program[$k],
                'program_kerja'      => $program_kerja[$k],
                'produk'             => $produk,
                'status_program'     => $status_program[$k],
                'anggaran'           => $anggaran_select,
                'tujuan'   => $tujuan[$k],
                'output'   => $output[$k],
            ];
            $cek = get_data('tbl_input_rkf',[
                'where'         => [
                    'kode_anggaran'   => $ckode_anggaran,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $tahun,
                    'id' => $dt_id[$k],
                ],
            ])->row();

            if(!isset($cek->id)) {
                $dt_insert = insert_data('tbl_input_rkf',$c);
                array_push($arrID, $dt_insert);
            }else{
                update_data('tbl_input_rkf',$c,['kode_anggaran'   => $ckode_anggaran,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $tahun,
                    'id' => $dt_id[$k]]);
                array_push($arrID, $dt_id[$k]);
            }
        }

        if(count($arrID)>0 && post('id')):
            delete_data('tbl_input_rkf',['kode_anggaran'=>$ckode_anggaran,'id not'=>$arrID,'kode_cabang'=>$kode_cabang,'tahun'=>$tahun]);
        endif;

        render([
            'status'    => 'success',
            'message'   => lang('data_berhasil_disimpan'),
        ],'json');
    }

    function save_perubahan() {       
        $data   = json_decode(post('json'),true);
        foreach($data as $id => $record) {          
            update_data('tbl_input_rkf',$record,'id',$id); }
    }

    function data($anggaran="", $cabang="", $tipe = 'table'){
        $menu = menu();
        $ckode_anggaran = $anggaran;
        $ckode_cabang = $cabang;

        $a = get_access('plan_rencana_kerja_fungsi');
        $data['akses_ubah'] = $a['access_edit'];

        $arr = ['select'    => '
                    a.*,
                    b.nama as kebijakan_umum,
                    c.nama as perspektif,
                    d.nama as skala_program,
                ',];
        if($anggaran) {
            $arr['where']['a.kode_anggaran']  = $ckode_anggaran;
        }
        if($cabang) {
            $arr['where']['a.kode_cabang']  = $ckode_cabang;
        }

        $arr['join'][] = 'tbl_kebijakan_umum b on b.id = a.id_kebijakan_umum';
        $arr['join'][] = 'tbl_perspektif c on c.id = a.id_perspektif';
        $arr['join'][] = 'tbl_skala_program d on d.id = a.id_skala_program';
        $list = get_data('tbl_input_rkf a',$arr)->result();
        $data['list']     = $list;
        $data['current_cabang'] = $cabang;
 
        $response   = array(
            'table' => $this->load->view($this->path.'rencana_kerja_fungsi/table',$data,true),
        );
       
        render($response,'json');
    }

    function get_data(){
        $d = get_data('tbl_input_rkf',[
            'where'         => [
                'id' => post('id'),
            ],
        ])->row();

        $list = get_data('tbl_input_rkf',[
            'where'         => [
                'kode_anggaran'   => $d->kode_anggaran,
                'kode_cabang'     => $d->kode_cabang,
                'tahun'           => $d->tahun,
            ]
        ])->result();

        render([
            'status'    => 'success',
            'data'      => $list,
            'detail'    => $d,
        ],'json');
    }
}