<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usulan_kantor extends BE_Controller {

    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $cabang_user  = get_data('tbl_user',[
            'where' => [
                'is_active' => 1,
                'id_group'  => id_group_access('usulan_kantor')
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

        $data['opt_jaringan']  = get_data('tbl_status_jaringan_kantor',[
            'where' => [
            'is_active' => 1,
        ],
        ])->result_array();

        $data['opt_kategori']  = get_data('tbl_kategori_kantor',[
            'where' => [
            'is_active' => 1,
        ],
        ])->result_array();

        $data['opt_status']  = get_data('tbl_status_ket_kantor',[
            'where' => [
            'is_active' => 1,
        ],
        ])->result_array();
        render($data);
    }


    function get_status($type ='echo') {
        $barang             = get_data('tbl_status_ket_kantor a',[
            'where'     => [
                'a.is_active' => 1,
            ]
        ])->result();
        $data           = '<option value=""></option>';
        foreach($barang as $e1) {
            $data       .= '<option value="'.$e1->id.'">'.$e1->status_ket.'</option>';
        }

        if($type == 'echo') echo $data;
        else return $data;       
    }


    function get_rencana($type ='echo') {
        $barang             = get_data('tbl_status_jaringan_kantor a',[
            'where'     => [
                'a.is_active' => 1,
            ]
        ])->result();
        $data           = '<option value=""></option>';
        foreach($barang as $e1) {
            $data       .= '<option value="'.$e1->id.'">'.$e1->status_jaringan.'</option>';
        }

        if($type == 'echo') echo $data;
        else return $data;       
    }

    function get_tahapan($type ='echo') {
        $barang             = get_data('tbl_tahapan_pengembangan a',[
            'where'     => [
                'a.is_active' => 1,
            ]
        ])->result();
        $data           = '<option value=""></option>';
        foreach($barang as $e2) {
            $data       .= '<option value="'.$e2->id.'">'.$e2->tahapan.'</option>';
        }

        if($type == 'echo') echo $data;
        else return $data;       
    }

    function get_jenis_kantor($type ='echo') {
        $barang             = get_data('tbl_kategori_kantor a',[
            'where'     => [
                'a.is_active' => 1,
            ]
        ])->result();
        $data           = '<option value=""></option>';
        foreach($barang as $e3) {
            $data       .= '<option value="'.$e3->id.'">'.$e3->kategori.'</option>';
        }

        if($type == 'echo') echo $data;
        else return $data;       
    }

    function get_jadwal($type ='echo') {
        $data           = '<option value=""></option>';
        for($i = 1; $i <= 12; $i++){
            $data       .= '<option value="'.$i.'">'.month_lang($i).'</option>';
        }

        if($type == 'echo') echo $data;
        else return $data;       

    }

    
    
    function data($anggaran="", $cabang="", $tipe = 'table') {
        $menu = menu();
        $ckode_anggaran = $anggaran;
        $ckode_cabang = $cabang;

        $a = get_access('usulan_kantor');
        $data['akses_ubah'] = $a['access_edit'];

        $data['current_cabang'] = $cabang;

        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$ckode_anggaran)->row();

        
	   	    $arr            = [
                'select'	=> 'a.*',
            ];
            
            if($anggaran) {
                $arr['where']['a.kode_anggaran']  = $ckode_anggaran;
            }
            
            if($cabang) {
                $arr['where']['a.kode_cabang']  = $ckode_cabang;
            }

            $produk 	= get_data('tbl_rencana_pjaringan a',$arr)->result();

            $nama_cabang ='';
            foreach ($produk as $m1) {

                $cabang = get_data('tbl_m_cabang','kode_cabang',$ckode_cabang)->row();
                
                if(isset($cabang->nama_cabang)) $nama_cabang = $cabang->nama_cabang;

            	$data2 = array(
                    'kode_anggaran' => $ckode_anggaran,
                    'keterangan_anggaran' => $anggaran->keterangan,
	                'tahun'  => $anggaran->tahun_anggaran,
	                'kode_cabang'   => $ckode_cabang,
                    'cabang'        => $nama_cabang,
                    'username'      => user('username'),
                    'id_rencana' => '',
                    'rencana_jarkan' => '',
                    'id_kategori_kantor' => '',
                    'kategori_kantor' => '',
                    'nama_lokasi' => '',
                    'jadwal' => $m1->jadwal,
                    'id_status_kantor' => '',
                    'status_ket_kantor' => ''
	            );

	            $cek		= get_data('tbl_rencana_pjaringan',[
	                'where'			=> [
                        'kode_anggaran'   => $ckode_anggaran,
	                    'kode_cabang'	  => $ckode_cabang,
	                    'tahun'           => $anggaran->tahun_anggaran,
                        'id_rencana'  => $m1->id_rencana,  
	                    'id_kategori_kantor'	  => $m1->id_kategori_kantor
	                    ],
	            ])->row();
	            
	            if(!isset($cek->id)) {
	                $response = 			insert_data('tbl_rencana_pjaringan',$data2);
	            }
            }      

        	$arr            = [
                'select'	=> 'a.*',
            ];

            if($anggaran) {
                $arr['where']['a.kode_anggaran']  = $ckode_anggaran;
            }
            
            if($cabang) {
                $arr['where']['a.kode_cabang']  = $ckode_cabang;
            }

            
            $data['produk'] 	= get_data('tbl_rencana_pjaringan a',$arr)->result();     
        	            
 
        $response	= array(
            'table'		=> $this->load->view('transaction/usulan_kantor/table',$data,true),
        );
	   
	    render($response,'json');
	}


	function get_data() {
        $dt = get_data('tbl_rencana_pjaringan','id',post('id'))->row();

		$data = get_data('tbl_rencana_pjaringan',[
            'where' => [
            'kode_anggaran' => $dt->kode_anggaran,    
            'kode_cabang' => $dt->kode_cabang
        ],
        ])->row_array();

        $data['detail'] = get_data('tbl_rencana_pjaringan',[
            'where' => [
            'kode_anggaran' => $dt->kode_anggaran,    
            'tahun' => $dt->tahun,
            'kode_cabang' => $dt->kode_cabang,
        ],
        ])->result_array();

		render($data,'json');
	}	

    function save_perubahan() {       
        $data   = json_decode(post('json'),true);
        foreach($data as $id => $record) {
        //    $result = insert_view_report_arr($record);
            update_data('tbl_rencana_pjaringan', $record,'id',$id);
        } 
    }

    function save() {
        $data = post();
        $kode_cabang = post('kode_cabang');

        $ckode_anggaran = user('kode_anggaran');

        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$ckode_anggaran)->row();

        $tahun         = $anggaran->tahun_anggaran;

        $rencana = post('rencana');
        $tahapan = post('tahapan');
        $kategori = post('jenis_kantor');
        $jadwal = post('jadwal');
        $status_ket =post('status_ket');
   //     $nama_lokasi  = post('nama_lokasi');
   //     $bulan = post('bulan');


        $cabang      = get_data('tbl_m_cabang','kode_cabang',user('kode_cabang'))->row();


        $c = [];
        foreach($tahapan as $i => $v) {
            $jaringan_kantor = '';
            $kategori_kantor = '';
            $status_kantor = '';

            $jaringan = get_data('tbl_status_jaringan_kantor','id',$rencana[$i])->row();
            if(isset($jaringan->id)) $jaringan_kantor = $jaringan->status_jaringan;

            $kat = get_data('tbl_kategori_kantor','id',$kategori[$i])->row();
            if(isset($kat->id)) $kategori_kantor = $kat->kategori;

            $st = get_data('tbl_status_ket_kantor','id',$status_ket[$i])->row();
            if(isset($st->id)) $status_kantor = $st->status_ket;

            $tah = get_data('tbl_tahapan_pengembangan','id',$tahapan[$i])->row();
            if(isset($tah->id)) $tahapan_pengembangan = $tah->tahapan;

            $c = [
                'kode_anggaran' => $ckode_anggaran,
                'keterangan_anggaran' => $anggaran->keterangan,
                'tahun'  => $anggaran->tahun_anggaran,
                'kode_cabang' => $kode_cabang,
                'cabang' => $cabang->nama_cabang,
                'username' => user('username'),
                'id_rencana' => $rencana[$i],
                'rencana_jarkan' => $jaringan_kantor,
                'id_tahapan' => $tahapan[$i],
                'tahapan_pengembangan' => $tahapan_pengembangan,
                'id_kategori_kantor' => $kategori[$i],
                'kategori_kantor' => $kategori_kantor,
                'jadwal'    => $jadwal[$i],
             //   'nama_lokasi' => $nama_lokasi[$i],
                'id_status_kantor' => $status_ket[$i],
             //   'bulan'           => $bulan[$i],  
                'status_ket_kantor' => $status_kantor,
            ];

            $cek        = get_data('tbl_rencana_pjaringan',[
                'where'         => [
                    'kode_anggaran'   => $ckode_anggaran,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    'id_rencana' => $rencana[$i],
                    'id_kategori_kantor' => $kategori[$i],
               //     'id_status_kantor' => $status_ket[$i]
                    ],
            ])->row();
            
            if(!isset($cek->id)) {
                insert_data('tbl_rencana_pjaringan',$c);
            }else{
                update_data('tbl_rencana_pjaringan',$c,
                    [
                    'kode_anggaran'   => $ckode_anggaran,
                    'keterangan_anggaran' => $anggaran->keterangan,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    'id_rencana'      => $rencana[$i],
                    'id_kategori_kantor' => $kategori[$i],
                    'kategori_kantor' => $kategori_kantor,
                    'id_tahapan'  => $tahapan[$i],
                    'tahapan_pengembangan' => $tahapan_pengembangan,
                    'jadwal' => $jadwal[$i],
            //        'id_status_kantor' => $status_ket[$i]
                ]);
            }

    
        }

      if(post('id')):
       delete_data('tbl_rencana_pjaringan',['kode_anggaran'=>$ckode_anggaran,'id_tahapan not'=>$tahapan,'kode_cabang'=>$kode_cabang,'tahun'=>$anggaran->tahun_anggaran,'id_status_jaringan'=>$status_jaringan_kantor]);    
   endif;
    //    if(count($c) > 0) insert_batch('tbl_rencana_aset',$c);

    
 
        render([
            'status'    => 'success',
            'message'   => lang('data_berhasil_disimpan')
        ],'json');
    }
}