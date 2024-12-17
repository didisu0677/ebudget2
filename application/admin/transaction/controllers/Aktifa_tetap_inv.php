<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aktifa_tetap_inv extends BE_Controller {
    var $path = 'transaction/budget_planner/';
    function __construct() {
        parent::__construct();
    }

    private function data_cabang(){
        $cabang_user  = get_data('tbl_user',[
            'where' => [
                'is_active' => 1,
                'id_group'  => id_group_access('Aktifa_tetap_inv')
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
        $data['path'] = $this->path;
        return $data;
    }
    
    function index($p1="") { 
        $data = $this->data_cabang();
        $data['opt_grup']  = get_data('tbl_grup_asetinventaris',[
            'where' => [
            'is_active' => 1,
            'kode' => ['E.1','E.2','E.3','E.6','E.7']
        ],
        ])->result_array();

        $data['opt_inv1']  = get_data('tbl_kode_inventaris',[
            'where' => [
            'is_active' => 1,
            'grup'      => 'E.4'
        ],
        ])->result_array();
        $data['opt_inv2']  = get_data('tbl_kode_inventaris',[
            'where' => [
            'is_active' => 1,
            'grup'      => 'E.5'
        ],
        ])->result_array();
        render($data,'view:'.$this->path.'akt_inv/index');
    }

    function data($anggaran="", $cabang="", $tipe = 'table') {
        $menu = menu();
        $ckode_anggaran = $anggaran;
        $ckode_cabang = $cabang;
        
        $a = get_access('usulan_aset');
        $data['akses_ubah'] = $a['access_edit'];

        $data['current_cabang'] = $cabang;

        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$ckode_anggaran)->row();
                  
        $arr            = [
            'select'    => 'a.*',
            'where'     => [
                'a.is_active' => 1,
            ],
            'sort_by'   => 'a.kode',
        ];
        
    
        $data['grup'][0]= get_data('tbl_grup_asetinventaris a',$arr)->result();
        

        foreach($data['grup'][0] as $m0) {         

            $arr            = [
                'select'    => 'a.*',
                'where'     => [
                    'a.grup' => $m0->kode,
                ],
            ];
            
            if($anggaran) {
                $arr['where']['a.kode_anggaran']  = $ckode_anggaran;
            }
            
            if($cabang) {
                $arr['where']['a.kode_cabang']  = $ckode_cabang;
            }

            $produk     = get_data('tbl_rencana_aset a',$arr)->result();

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
                    'kode_inventaris' => $m1->kode_inventaris,
                    'nama_inventaris' => $m1->nama_inventaris,
                    'grup'      => $m1->grup,
                    'nama_grup' => $m1->nama_grup,
                );

                $cek        = get_data('tbl_rencana_aset',[
                    'where'         => [
                        'kode_anggaran'   => $ckode_anggaran,  
                        'kode_cabang'     => $ckode_cabang,
                        'tahun'           => $anggaran->tahun_anggaran,
                        'kode_inventaris' => $m1->kode_inventaris,  
                        'grup'            => $m1->grup,
                        ],
                ])->row();
                
                if(!isset($cek->id)) {
                    $response =             insert_data('tbl_rencana_aset',$data2);
                }
            }      

            $arr            = [
                'select'    => 'a.*',
                'where'     => [
                    'a.grup' => $m0->kode,
                ],
            ];

            if($anggaran) {
                $arr['where']['a.kode_anggaran']  = $ckode_anggaran;
            }
            
            if($cabang) {
                $arr['where']['a.kode_cabang']  = $ckode_cabang;
            }

            
            $data['produk'][$m0->kode]  = get_data('tbl_rencana_aset a',$arr)->result();     
                        
        }           
   
        $data['cabang_user'] = user('kode_cabang');

        $response   = array(
            'table'     => $this->load->view($this->path.'akt_inv/table',$data,true),
        );
       
        render($response,'json');
    }

    function getKodeInventaris(){
        $get = get_data('tbl_rencana_aset a',[
            'select' => 'a.kode_inventaris',
            'where' => [
                'kode_cabang'   => user("kode_cabang"),
                'kode_inventaris like' => 'H%'
            ],
            'order_by' => 'id',
            'sort' => 'DESC',
            'limit' => '1'
        ])->result();

        if(!empty($get)){
            $data = $get;
        }else {
            $test['kode_inventaris'] = "H-00";
            $data[] = $test;
        }

        render($data,'json');
    }


    function getKodeInventaris2(){
        $get = get_data('tbl_rencana_aset a',[
            'select' => 'a.kode_inventaris',
            'where' => [
                'kode_cabang'   => user("kode_cabang"),
                'kode_inventaris like' => 'M%'
            ],
            'order_by' => 'id',
            'sort' => 'DESC',
            'limit' => '1'
        ])->result();

        if(!empty($get)){
            $data = $get;
        }else {
            $test['kode_inventaris'] = "M 0";
            $data[] = $test;
        }

        render($data,'json');
    }


    function get_data() {
        $dt = get_data('tbl_rencana_aset','id',post('id'))->row();
        $data = get_data('tbl_rencana_aset',[
            'where' => [
            'kode_anggaran' => $dt->kode_anggaran,    
            'tahun' => $dt->tahun,
            'kode_cabang' => $dt->kode_cabang
        ],
        ])->row_array();

        $data['detail_ket'] = get_data('tbl_rencana_aset',[
            'where' => [
            'kode_anggaran' => $dt->kode_anggaran,    
            'tahun' => $dt->tahun,
            'kode_cabang' => $dt->kode_cabang,
            'grup' => ['E.1','E.2','E.3','E.6','E.7']
        ],
        ])->result_array();


        $data['detail_invk1'] =  get_data('tbl_rencana_aset a',[
            'select' => 'a.*',
            'join' => 'tbl_kode_inventaris b on b.kode_inventaris = a.kode_inventaris',
            'where' => [
            'a.kode_anggaran' => $dt->kode_anggaran, 
            'a.tahun' => $dt->tahun,
            'a.kode_cabang' => $dt->kode_cabang,
            'a.grup' => 'E.4'
        ],
        ])->result_array();

        $data['detail_invk2'] = get_data('tbl_rencana_aset a',[
            'select' => 'a.*',
            'join' => 'tbl_kode_inventaris b on b.kode_inventaris = a.kode_inventaris',
            'where' => [
            'a.kode_anggaran' => $dt->kode_anggaran, 
            'a.tahun' => $dt->tahun,
            'a.kode_cabang' => $dt->kode_cabang,
            'a.grup' => 'E.5'
        ],
        ])->result_array();


        $data['detail_tambahan1'] = get_data('tbl_rencana_aset a',[
            'select' => 'a.*',
            'join' => 'tbl_kode_inventaris b on b.kode_inventaris = a.kode_inventaris TYPE left',
            'where' => [
            'a.kode_anggaran' => $dt->kode_anggaran, 
            'a.tahun' => $dt->tahun,
            'a.kode_cabang' => $dt->kode_cabang,
            'a.grup' => 'E.4',
            'b.kode_inventaris' => null
        ],
        ])->result_array();

        $data['detail_tambahan2'] = get_data('tbl_rencana_aset',[
            'where' => [
            'kode_anggaran' => $dt->kode_anggaran, 
            'tahun' => $dt->tahun,
            'kode_cabang' => $dt->kode_cabang,
            'grup' => 'E.5',
            'kode_inventaris' => '' 
        ],
        ])->result_array();

        render($data,'json');
    }   

    function save_perubahan() {       
        $data   = json_decode(post('json'),true);
        foreach($data as $id => $record) {          
            update_data('tbl_rencana_aset',$record,'id',$id); }
    }

    function save() {
        $data = post();
        $kode_cabang = post('kode_cabang');
        $ckode_anggaran = user('kode_anggaran');

        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$ckode_anggaran)->row();

        $tahun         = $anggaran->tahun_anggaran;
        
        $keterangan          = post('keterangan');
        $catatan             = post('catatan');
        $catatanInv1         = post('catatanInv1');
        $catatanInv2         = post('catatanInv2');
        $catatanInvKel1      = post('catatanInvKel1');
        $catatanInvkel2      = post('catatanInvkel2');
        $grup_aset           = post('grup_aset');
        $inv_kel1            = post('inv_kel1');
        $inv_kel2            = post('inv_kel2');
        $bulan_aset          = post('bulan_aset');
        $bulan_kel1          = post('bulan_kel1');
        $bulan_kel2          = post('bulan_kel2');
        $bulan_kel3          = post('bulan_kel3');
        $bulan_kel4          = post('bulan_kel4');

        $kodeInventarisEdit  = post('kodeinventaris');

        $keterangan1          = post('keterangan1');
        $keterangan2          = post('keterangan2');
        $inp_inv_kel1         = post('kodeinventaris1');
        $inp_inv_kel2         = post('kodeinventaris2');

        $cabang      = get_data('tbl_m_cabang','kode_cabang',user('kode_cabang'))->row();


        $c = [];
        foreach($keterangan as $i => $v) {
            $nama_grup = '';
            $prefiks = '';

              $grup = get_data('tbl_grup_asetinventaris','kode',$grup_aset[$i])->row();

            if(isset($grup->nama_grup)) $nama_grup = $grup->nama_grup;  
            if(!empty($grup->kode)){
                $prefiks = $grup->prefiks;
            }
            
          
          

            $getKodeInventaris = get_data('tbl_rencana_aset a',[
                'select' => 'a.kode_inventaris',
                'where' => [
                    'kode_cabang'   => user("kode_cabang"),
                    'kode_inventaris like' => $prefiks.' %'
                ],
                'order_by' => 'id',
                'sort' => 'DESC',
                'limit' => '1'
            ])->result_array();

            if(!empty($getKodeInventaris)){
                $coun   = explode(" ", $getKodeInventaris[0]['kode_inventaris']);

                $fCoun  = $coun[1] + 1;

                $kode_inventaris = $prefiks." ".$fCoun;  
            }else {
                $kode_inventaris = $prefiks." 1"; 
            }
            


            $c = [
                'kode_anggaran' => $ckode_anggaran,
                'keterangan_anggaran' => $anggaran->keterangan,
                'tahun'  => $anggaran->tahun_anggaran,
                'kode_cabang' => $kode_cabang,
                'cabang' => $cabang->nama_cabang,
                'username' => user('username'),
                'nama_inventaris' => $keterangan[$i],
                'catatan'         => $catatan[$i],
                'grup'  => $grup_aset[$i],
                'nama_grup' => $nama_grup,
                'bulan' => $bulan_aset[$i]
            ];

            $cek        = get_data('tbl_rencana_aset',[
                'where'         => [
                    'kode_anggaran'   => $ckode_anggaran,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    // 'kode_inventaris' => $kode_inventaris,  
                    'nama_inventaris' => $keterangan[$i],
                    'grup'            => $grup_aset[$i],
                    ],
            ])->row();
            
            if(!isset($cek->id) && !empty($kodeInventarisEdit)) {
                $c['kode_inventaris'] = $kode_inventaris;
                insert_data('tbl_rencana_aset',$c);
            }else{
                update_data('tbl_rencana_aset',$c,[
                    'kode_anggaran'   => $ckode_anggaran,
                    'keterangan_anggaran' => $anggaran->keterangan,  
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    // 'kode_inventaris' => $kodeInventarisEdit[$i],  
                    'nama_inventaris' => $keterangan[$i],
                    'grup'            => $grup_aset[$i],
                ]);
            }
    
        }

        delete_data('tbl_rencana_aset',['kode_anggaran'=>$ckode_anggaran,'nama_inventaris not' =>$keterangan,'kode_cabang'=>$kode_cabang,'tahun'=>$anggaran->tahun_anggaran,'grup'=>$grup_aset]);    

    //    if(count($c) > 0) insert_batch('tbl_rencana_aset',$c);

        $d = [];
        foreach($inv_kel1 as $i => $v) {            


            $nama_invkel1 = '';
            $grup_aset = '';
            $nama_grup = '';
            $harga = 0;
            $inv = get_data('tbl_kode_inventaris','kode_inventaris',$inv_kel1[$i])->row();

            if(isset($inv->nama_inventaris)) {
                $nama_invkel1 = $inv->nama_inventaris;
                $grup_aset = $inv->grup;
                $nama_grup = $inv->nama_grup_aset;
                $harga = $inv->harga;
            }    

            $d = [
                'kode_anggaran' => $ckode_anggaran,
                'keterangan_anggaran' => $anggaran->keterangan,
                'tahun'  => $anggaran->tahun_anggaran,
                'kode_cabang' => $kode_cabang,
                'cabang' => $cabang->nama_cabang,
                'username' => user('username'),
                'kode_inventaris' => $inv_kel1[$i],
                'nama_inventaris' => $nama_invkel1,
                'catatan' => $catatanInvKel1[$i],
                'grup'  => $grup_aset,
                'nama_grup' => $nama_grup,
                'bulan' => $bulan_kel1[$i],
                'harga' => $harga
            ];

            $cek        = get_data('tbl_rencana_aset',[
                'where'         => [
                    'kode_anggaran'   => $ckode_anggaran,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    // 'kode_inventaris' => $inv_kel1[$i],
                    'nama_inventaris' => $nama_invkel1,
                    'grup'            => $grup_aset,
                    ],
            ])->row();
            
            if(empty($cek->id)) {
                insert_data('tbl_rencana_aset',$d);
            }else{
                update_data('tbl_rencana_aset',$d,[
                    'kode_anggaran'   => $ckode_anggaran,
                    'keterangan_anggaran' => $anggaran->keterangan,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    // 'kode_inventaris' => $inv_kel1[$i],
                    'nama_inventaris' => $nama_invkel1,
                    'grup'            => $grup_aset,
                ]);
            }
    
        }

        delete_data('tbl_rencana_aset',['kode_anggaran'=>$ckode_anggaran,'kode_inventaris not'=>$inv_kel1,'kode_cabang'=>$kode_cabang,'tahun'=>$anggaran->tahun_anggaran,'grup'=>$grup_aset]);    
    //    if(count($c) > 0) insert_batch('tbl_rencana_aset',$c);

        $e = [];
        foreach($inv_kel2 as $i => $v) {
            $nama_invkel2 = '';
            $grup_aset2 = '';
            $nama_grup2 = '';
            $harga = 0;
            $inv2 = get_data('tbl_kode_inventaris','kode_inventaris',$inv_kel2[$i])->row();
            if(isset($inv2->nama_inventaris)) {
                $nama_invkel2 = $inv2->nama_inventaris;
                $grup_aset2 = $inv2->grup;
                $nama_grup2 = $inv2->nama_grup_aset;
                $harga = $inv2->harga;
            }    

            $e = [
                'kode_anggaran' => $ckode_anggaran,
                'keterangan_anggaran' => $anggaran->keterangan,
                'tahun'  => $anggaran->tahun_anggaran,
                'kode_cabang' => $kode_cabang,
                'cabang' => $cabang->nama_cabang,
                'username' => user('username'),
                // 'catatan' => $catatanInvkel2[$i],
                'kode_inventaris' => $inv_kel2[$i],
                'nama_inventaris' => $nama_invkel2,
                'grup'  => $grup_aset2,
                'nama_grup' => $nama_grup2,
                'bulan' => $bulan_kel2[$i],
                'harga' => $harga
            ];

            $cek        = get_data('tbl_rencana_aset',[
                'where'         => [
                    'kode_anggaran'   => $ckode_anggaran,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    // 'kode_inventaris' => $inv_kel2[$i],
                    'nama_inventaris' => $nama_invkel2,
                    'grup'            => $grup_aset2,
                    ],
            ])->row();
            
            if(!isset($cek->id)) {
                insert_data('tbl_rencana_aset',$e);
            }else{
                update_data('tbl_rencana_aset',$e,[
                    'kode_anggaran' => $ckode_anggaran,
                    'keterangan_anggaran' => $anggaran->keterangan,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'  => $anggaran->tahun_anggaran,
                    'kode_inventaris' => $inv_kel2[$i],
                    'nama_inventaris' => $nama_invkel2,
                    'grup'            => $grup_aset2,
                ]);
            }
    
        }

        delete_data('tbl_rencana_aset',['kode_anggaran'=>$ckode_anggaran,'kode_inventaris not'=>$inv_kel2,'kode_cabang'=>$kode_cabang,'tahun'=>$anggaran->tahun_anggaran,'grup'=>$grup_aset2]);    


     //   if(count($d) > 0) insert_batch('tbl_rencana_aset',$d);
        if($keterangan1) {
        $f = [];
        foreach($keterangan1 as $i => $v) {
            $grup_aset3 = 'E.4';
            $nama_grup3 = 'PENAMBAHAN INVENTARIS KEL.1';
            $harga = 0;

            $grup1 = get_data('tbl_grup_asetinventaris','kode',$grup_aset3)->row();

            $prefiks1 = $grup1->prefiks;
              $getKodeInventaris1 = get_data('tbl_rencana_aset a',[
                'select' => 'a.kode_inventaris',
                'where' => [
                    'kode_cabang'   => user("kode_cabang"),
                    'kode_inventaris like' => $prefiks1.' %'
                ],
                'order_by' => 'id',
                'sort' => 'DESC',
                'limit' => '1'
            ])->result_array();


            if(!empty($getKodeInventaris1)){
                $coun1  = explode(" ", $getKodeInventaris1[0]['kode_inventaris']);

                $fCoun1     = $coun1[1] + 1;

                $kode_inventaris1 = $prefiks1." ".$fCoun1; 
            }else {
                $kode_inventaris1 = $prefiks1." 1"; 
            }
             
             


            $f = [
                'kode_anggaran' => $ckode_anggaran,
                'keterangan_anggaran' => $anggaran->keterangan,
                'tahun'  => $anggaran->tahun_anggaran,
                'kode_cabang' => $kode_cabang,
                'cabang' => $cabang->nama_cabang,
                'username' => user('username'),
                // 'catatan'    => $catatanInv1[$i],
                'kode_inventaris' => $kode_inventaris1,
                'nama_inventaris' => $keterangan1[$i],
                'grup'  => $grup_aset3,
                'nama_grup' => $nama_grup3,
                'bulan' => $bulan_kel3[$i],
                'harga' => $harga
            ];

            $cek        = get_data('tbl_rencana_aset',[
                'where'         => [
                    'kode_anggaran'   => $ckode_anggaran,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    // 'kode_inventaris' => $inp_inv_kel1[$i],
                    'nama_inventaris' => $keterangan1[$i],
                    'grup'            => $grup_aset3,
                    ],
            ])->row();
            
            if(!isset($cek->id) && !empty($inp_inv_kel1)) {
                $f['kode_inventaris'] = $kode_inventaris1;
                insert_data('tbl_rencana_aset',$f);
            }else{
                update_data('tbl_rencana_aset',$f,[
                    'kode_anggaran'   => $ckode_anggaran,
                    'keterangan_anggaran' => $anggaran->keterangan,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    // 'kode_inventaris' => $inp_inv_kel1[$i],
                    'nama_inventaris' => $keterangan1[$i],
                    'grup'            => $grup_aset3,
                ]);
            }
    
        }

        delete_data('tbl_rencana_aset',['kode_anggaran'=>$ckode_anggaran,'nama_inventaris not'=>$keterangan1,'kode_cabang'=>$kode_cabang,'tahun'=>$anggaran->tahun_anggaran,'grup'=>$grup_aset3,'kode_inventaris'=>'']);
        }
        ////
        
        if($keterangan2) {
        $g = [];
        foreach($keterangan2 as $i => $v) {
            $grup_aset4 = 'E.5';
            $nama_grup4 = 'PENAMBAHAN INVENTARIS KEL.2';
            $harga = 0;


            $grup2 = get_data('tbl_grup_asetinventaris','kode',$grup_aset3)->row();

            $prefiks2 = $grup2->prefiks;
              $getKodeInventaris2 = get_data('tbl_rencana_aset a',[
                'select' => 'a.kode_inventaris',
                'where' => [
                    'kode_cabang'   => user("kode_cabang"),
                    'kode_inventaris like' => $prefiks2.' %'
                ],
                'order_by' => 'id',
                'sort' => 'DESC',
                'limit' => '1'
            ])->result_array();


            if(!empty($getKodeInventaris2)){
                $coun2  = explode(" ", $getKodeInventaris2[0]['kode_inventaris']);

                $fCoun2     = $coun2[1] + 1;

                $kode_inventaris2 = $prefiks2." ".$fCoun2; 
            }else {
                $kode_inventaris2 = $prefiks2." 1"; 
            }




            $g = [
                'kode_anggaran' => $ckode_anggaran,
                'keterangan_anggaran' => $anggaran->keterangan,
                'tahun'  => $anggaran->tahun_anggaran,
                'kode_cabang' => $kode_cabang,
                'cabang' => $cabang->nama_cabang,
                'username' => user('username'),
                // 'kode_inventaris' => $inp_inv_kel2[$i],
                'nama_inventaris' => $keterangan2[$i],
                'catatan' => $catatanInv2[$i],
                'grup'  => $grup_aset4,
                'nama_grup' => $nama_grup4,
                'bulan' => $bulan_kel4[$i],
                'harga' => $harga
            ];

            $cek        = get_data('tbl_rencana_aset',[
                'where'         => [
                    'kode_anggaran'   => $ckode_anggaran,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    // 'kode_inventaris' => $inp_inv_kel2[$i],
                    'nama_inventaris' => $keterangan2[$i],
                    'grup'            => $grup_aset4,
                    ],
            ])->row();
            
           if(!isset($cek->id) && !empty($inp_inv_kel2)) {
                $g['kode_inventaris'] = $kode_inventaris2;
                insert_data('tbl_rencana_aset',$c);
            }else{
                update_data('tbl_rencana_aset',$g,[
                    'kode_anggaran'   => $ckode_anggaran,
                    'keterangan_anggaran' => $anggaran->keterangan,
                    'kode_cabang'     => $kode_cabang,
                    'tahun'           => $anggaran->tahun_anggaran,
                    // 'kode_inventaris' => $inp_inv_kel2[$i],
                    'nama_inventaris' => $keterangan2[$i],
                    'grup'            => $grup_aset4,
                ]);
            }
    
        }

        delete_data('tbl_rencana_aset',['kode_anggaran'=>$ckode_anggaran,'nama_inventaris not'=>$keterangan2,'kode_cabang'=>$kode_cabang,'tahun'=>$anggaran->tahun_anggaran,'grup'=>$grup_aset4,'kode_inventaris'=>'']);
        }
        ////
        render([
            'status'    => 'success',
            'message'   => lang('data_berhasil_disimpan')
        ],'json');
    }
}