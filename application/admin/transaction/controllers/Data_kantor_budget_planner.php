<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_kantor_budget_planner extends BE_Controller {
    var $path = 'transaction/budget_planner/';
    function __construct() {
        parent::__construct();
    }

    private function data_cabang(){
        $cabang_user  = get_data('tbl_user',[
            'where' => [
                'is_active' => 1,
                'id_group'  => id_group_access('Data_kantor_budget_planner')
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
            'select'    => 'distinct a.kode_cabang,a.nama_cabang',
            'where'     => [
                'a.is_active' => 1,
                'a.'.$x => $cab->id,
                'a.kode_cabang' => $kode_cabang
            ]
        ])->result_array();

        $data['cabang_input'] = get_data('tbl_m_cabang a',[
            'select'    => 'distinct a.kode_cabang,a.nama_cabang',
            'where'     => [
                'a.is_active' => 1,
                'a.kode_cabang' => user('kode_cabang')
            ]
        ])->result_array();

        $data['tahun'] = get_data('tbl_tahun_anggaran','kode_anggaran',user('kode_anggaran'))->result();

        $data['bln_terakhir'] = get_data('tbl_detail_tahun_anggaran',[
            'select' => 'tahun,bulan-1 as bulan',
            'where'  => [
                'kode_anggaran' => user('kode_anggaran'),
                'sumber_data'   => 2,
            ],
            'sort_by' => 'bulan,tahun',
            'sort' => 'ASC',
            'limit' => 1,
        ])->row_array();

        $data['path'] = $this->path;

        return $data;
    }
    
    function index($p1="") { 
        $data = $this->data_cabang();
        render($data,'view:'.$this->path.'data_kantor/index');
    }


    function get_data($kode_anggaran="",$kode_cabang=""){
        $data = array();

        $cabang = get_data('tbl_m_cabang','kode_cabang',$kode_cabang)->row();
        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$kode_anggaran)->row();

        if(isset($cabang->kode_cabang)) {
            $data2 = array(
                'kode_anggaran' => $anggaran->kode_anggaran,
                'keterangan_anggaran' => $anggaran->keterangan,
                'kode_cabang' => $kode_cabang,
                'nama_kantor' => $cabang->nama_cabang,
            ); 
        }

        $cek = get_data('tbl_plan_berita_acara','kode_cabang',$kode_cabang)->row();
        if(!isset($cek->kode_cabang)) {
            insert_data('tbl_plan_berita_acara',$data2);
        }else{
            update_data('tbl_plan_berita_acara',$data2,['kode_cabang'=>$kode_cabang]);

        }

        $data = get_data('tbl_plan_berita_acara',[
            'where' =>[
                'kode_anggaran' => $kode_anggaran,
                'kode_cabang'   => $kode_cabang,
            ],
        ])->row_array();

        if($data){
            $data['tgl_mulai_menjabat'] = date("d-m-Y", strtotime($data['tgl_mulai_menjabat']));
        } else{
            $data = get_data('tbl_m_data_kantor',"kode_cabang",$kode_cabang)->row_array();

            if($data) $data['tgl_mulai_menjabat'] = date("d-m-Y", strtotime($data['tgl_mulai_menjabat']));
            else $data = array();
            
        }
        render($data,'json');
    }

     function data2($kode_anggaran="", $kode_cabang="") {
              
        $data_view['item_ba']  = get_data('tbl_item_plan_ba a',[
            'select' => '*',
            'where'  => [
                'is_active' => 1,
            ],
        ])->result_array();



        $coa=[];
        $grup=[];
        foreach ($data_view['item_ba'] as $a) {
            if($a['coa'] !="") $coa[] = $a['coa'];
            $grup[$a['grup']] = $a['grup'];

        }    

        $data_view['grup']  = $grup;


        $tahun = get_data('tbl_tahun_anggaran','kode_anggaran',user('kode_anggaran'))->row();

        $bln_terakhir = get_data('tbl_tahun_anggaran',[
            'select' => 'tahun_terakhir_realisasi as tahun,bulan_terakhir_realisasi as bulan',
            'where'  => [
                'kode_anggaran' => user('kode_anggaran'),
            ],
            'sort_by' => 'bulan,tahun',
            'sort' => 'ASC',
            'limit' => 1,
        ])->row(); 

        if(isset($bln_terakhir->bulan)) {
            $v_bln01 = $bln_terakhir->bulan;
        }
            
        $TOT_cab = 'TOT_' . $kode_cabang ;    
        $arr            = [
        'select'    => 'glwnco,
            coalesce(sum(case when substr(glwdat,5,2) = "12" then '.$TOT_cab.' end), 0) as C_akhir,
            coalesce(sum(case when substr(glwdat,5,2) = '.$v_bln01.' then '.$TOT_cab.' end), 0) as C_01akhir'
        ];

        $arr_v02 = [
         'where' => [
            'tahun' => $tahun->tahun_anggaran - 2,
            'glwnco' => $coa,
            ],
          'group_by' => 'glwnco',  
        ];

        $arr_v01 = [
         'where' => [
            'tahun' => $tahun->tahun_anggaran - 1,
            'glwnco' => $coa,
            ],
         'group_by' => 'glwnco',  
        ];


        $tabel_02 = 'tbl_history_' . ($tahun->tahun_anggaran-2);
        $tabel_01 = 'tbl_history_' . ($tahun->tahun_anggaran-1);


        $x_v02 = array_merge($arr,$arr_v02); 
        $x_v01 = array_merge($arr,$arr_v01); 

        $arr_sum            = [
            'select'    => 'a.coa, a.tahun, sum(B_01) as P_01,sum(B_02) as P_02,sum(B_03) as P_03,sum(B_04) as P_04,sum(B_05) as P_05,sum(B_06) as P_06,sum(B_07) as P_07,sum(B_08) as P_08,sum(B_09) as P_09,sum(B_10) as P_10,sum(B_11) as P_11,sum(B_12) as P_12',
            'where'     => [
                'a.kode_anggaran' => $kode_anggaran,
                'a.kode_cabang' => $kode_cabang,
                'a.coa'  => $coa, 
                'a.tahun' => user('tahun_anggaran')

            ],
            'group_by' => 'a.coa,a.tahun',
            'sort_by'  => 'a.coa,a.tahun'
        ];


        $data_view['ba_est']  =  get_data('tbl_budget_plan_neraca a',$arr_sum)->result_array();

        $arr_est           = [
            'select'    => 'a.coa, a.tahun, sum(B_01) as P_01,sum(B_02) as P_02,sum(B_03) as P_03,sum(B_04) as P_04,sum(B_05) as P_05,sum(B_06) as P_06,sum(B_07) as P_07,sum(B_08) as P_08,sum(B_09) as P_09,sum(B_10) as P_10,sum(B_11) as P_11,sum(B_12) as P_12',
            'where'     => [
                'a.kode_anggaran' => $kode_anggaran,
                'a.kode_cabang' => $kode_cabang,
                'a.coa'  => $coa, 
                'a.tahun' => user('tahun_anggaran') - 1

            ],
            'group_by' => 'a.coa,a.tahun',
            'sort_by'  => 'a.coa,a.tahun'
        ];


        $data_view['ba_sum']  =  get_data('tbl_budget_plan_neraca a',$arr_sum)->result_array();
        $data_view['ba_est']  =  get_data('tbl_budget_plan_neraca a',$arr_est)->result_array();

        $data_view['v_02'] = get_data($tabel_02,$x_v02)->result_array();
        $data_view['v_01'] = get_data($tabel_01,$x_v01)->result_array();

        $view   = $this->load->view('transaction/budget_planner/data_kantor/data2',$data_view,true);
     
        $data = [
            'data'              => $view,     
        ];

        render($data,'json');
    }

    function save(){
        $data = post();
        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',user('kode_anggaran'))->row();

        $data['kode_anggaran'] = user('kode_anggaran');
        $data['keterangan_anggaran'] = $anggaran->keterangan;   
        $cek = get_data('tbl_plan_berita_acara',[
            'kode_anggaran' => user('kode_anggaran'),
            'kode_cabang'   => $data['kode_cabang']
        ])->row();

        if(!isset($cek->id)) {
            $response = insert_data('tbl_plan_berita_acara',$data,post(':validation'));
        }else{
            $data_update = [
                'kode_anggaran' => user('kode_anggaran'),
                'keterangan_anggaran' => $anggaran->keterangan,
                'kode_cabang'   => $data['kode_cabang'],
                'nama_kantor'   => $data['nama_kantor'],
                'nama_pimpinan' => $data['nama_pimpinan'],
                'tgl_mulai_menjabat'    => $data['tgl_mulai_menjabat'],
                'no_hp_cp'      => $data['no_hp_cp'],
            ];
                
            $response = update_data('tbl_plan_berita_acara',$data_update,[
                'kode_anggaran'=>user('kode_anggaran'),'kode_cabang'=>$data['kode_cabang']]);


        }

        if($response) {
            $response = save_data('tbl_m_data_kantor',$data,post(':validation'));
        }
            
        render($response,'json');
    }
}       