<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plan_by_divisi_rutin extends BE_Controller {
    var $path       = 'transaction/budget_planner/kantor_pusat/';
    var $sub_menu   = 'transaction/budget_planner/sub_menu';
    var $detail_tahun;
    var $kode_anggaran;
    function __construct() {
        parent::__construct();
    }
    
    function index($p1="") { 
        $data = data_cabang();
        $data['path']     = $this->path;
        $data['sub_menu'] = $this->sub_menu;
        $data['coa']      = $this->get_coa('data');
        render($data,'view:'.$this->path.'by_divisi_rutin/index');
    }

    function get_coa($type = 'echo'){
        $ls             = get_data('tbl_m_coa a',[
            'where'     => "a.is_active = 1 and a.kantor_pusat = 1",
        ])->result();
        $data           = '<option value=""></option>';
        foreach($ls as $e2) {
            $data       .= '<option value="'.$e2->glwnco.'">'.$e2->glwnco.' - '.$e2->glwdes.'</option>';
        }

        if($type == 'echo') echo $data;
        else return $data;
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
                b.glwnco,
                b.glwdes,
            ',
        ];

        if($anggaran) {
            $arr['where']['a.kode_anggaran']  = $ckode_anggaran;
        }
        
        if($cabang) {
            $arr['where']['a.kode_cabang']  = $ckode_cabang;
        }
        $arr['join']     = 'tbl_m_coa b on b.glwnco = a.coa';
        $arr['orderby']  = 'a.id';
        $list = get_data('tbl_divisi_rutin a',$arr)->result();
        $data['list'] = $list;

        // header
        $arrHeader = array();
        foreach ($list as $k => $v) {
            $name = str_replace(' ', '_', $v->kegiatan);
            if(isset($data['count_'.$name])):
                $data['count_'.$name] += 1;
            else:
                $data['count_'.$name] = 1;
            endif;

            if(!in_array($v->kegiatan,$arrHeader)):
                array_push($arrHeader,$v->kegiatan);
            endif;
        }
        $data['header']     = $arrHeader;        
        $response   = array(
            'table'     => $this->load->view($this->path.'by_divisi_rutin/table',$data,true),
        );
       
        render($response,'json');
    }

    function save(){
        $data = post();
        $kode_cabang = post('kode_cabang');
        $ckode_anggaran = user('kode_anggaran');

        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$ckode_anggaran)->row();

        $tahun  = $anggaran->tahun_anggaran;
        $kegiatan    = post('kegiatan');
        $dt_index    = post('dt_index');

        $cabang      = get_data('tbl_m_cabang','kode_cabang',user('kode_cabang'))->row();
        $status      = false;
        if($kegiatan):
            foreach ($kegiatan as $i => $h) {
                $status      = true;
                $arrID = array();
                $key = $dt_index[$i];
                $dt_id  = post('dt_id'.$key);
                $coa    = post('coa'.$key);
                $c = [];
                if(post('id')):
                    $dt = get_data('tbl_divisi_rutin','id',post('id'))->row();
                endif;
                foreach($dt_id as $k => $v) {
                    $c = [
                        'kode_anggaran' => $ckode_anggaran,
                        'keterangan_anggaran' => $anggaran->keterangan,
                        'tahun'  => $anggaran->tahun_anggaran,
                        'kode_cabang' => $kode_cabang,
                        'cabang' => $cabang->nama_cabang,
                        'username' => user('username'),
                        'coa' => $coa[$k],
                        'kegiatan' => $kegiatan[$i]

                    ];

                    $cek        = get_data('tbl_divisi_rutin',[
                        'where'         => [
                            'kode_anggaran'   => $ckode_anggaran,
                            'kode_cabang'     => $kode_cabang,
                            'tahun'           => $anggaran->tahun_anggaran,
                            'id'              => $dt_id[$k]
                            ],
                    ])->row();

                    
                    if(!isset($cek->id)) {
                        $id = insert_data('tbl_divisi_rutin',$c);
                    }else{
                        $id = $dt_id[$k];
                        update_data('tbl_divisi_rutin',$c,[
                            'kode_anggaran'   => $ckode_anggaran,
                            'keterangan_anggaran' => $anggaran->keterangan,
                            'kode_cabang'     => $kode_cabang,
                            'tahun'           => $anggaran->tahun_anggaran,
                            'id'              => $dt_id[$k]
                        ]);
                    }

                    array_push($arrID, $id);
                }

                if(count($arrID)>0 && post('id')):
                    delete_data('tbl_divisi_rutin',['kode_anggaran'=>$ckode_anggaran,'id not'=>$arrID,'kode_cabang'=>$kode_cabang,'tahun'=>$tahun, 'kegiatan' => $dt->kegiatan]);
                endif;
            }
        endif;

        if(!$status && post('id')):
            $dt = get_data('tbl_divisi_rutin','id',post('id'))->row();
            delete_data('tbl_divisi_rutin',['kode_anggaran'=>$ckode_anggaran,'kode_cabang'=>$kode_cabang,'tahun'=>$tahun, 'kegiatan' => $dt->kegiatan]);
        endif;

        render([
            'status'    => 'success',
            'message'   => lang('data_berhasil_disimpan')
        ],'json');
    }

    function save_perubahan() {       
        $data   = json_decode(post('json'),true);
        foreach($data as $id => $record) {          
            update_data('tbl_divisi_rutin',$record,'id',$id); }
    }

    function get_data() {
        $dt = get_data('tbl_divisi_rutin','id',post('id'))->row();
        $list = get_data('tbl_divisi_rutin',[
            'where' => [
                'kode_anggaran' => $dt->kode_anggaran,    
                'tahun' => $dt->tahun,
                'kode_cabang' => $dt->kode_cabang,
                'kegiatan'  => $dt->kegiatan
            ],
        ])->result_array();
        $data['detail'] = $dt;
        $data['data'] = $list;
        render($data,'json');

    }
}