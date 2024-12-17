<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rekap_rasio extends BE_Controller {
    var $path = 'transaction/budget_planner/';
    function __construct() {
        parent::__construct();
    }

    private function data_cabang(){
        $cabang_user  = get_data('tbl_user',[
            'where' => [
                'is_active' => 1,
                'id_group'  => id_group_access('rekap_rasio')
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
        render($data,'view:'.$this->path.'rekap_rasio/index');
    }

    function data ($anggaran1="", $cabang=""){
        $anggaran = get_data('tbl_tahun_anggaran','kode_anggaran',$anggaran1)->row();
        $thn_trakhir = $anggaran->tahun_anggaran;

        $selectDpk = "";
        $selectkredit = "";
        $selectLoan = "a.glwnco,";
        $selectRoa = "";
        $selectCasa = "a.coa,";
        for($a = 1;$a <= 12;$a++){
            if($a >= 10){
                $selectkredit .= "a.B_".$a.",";

                $selectDpk .= "((b.bulan_".$a."/a.b_".$a.") * 100) as hasil".$a.",";
                $selectDpk .= "a.B_".$a.",";
                $selectDpk .= "b.bulan_".$a.",";

                $selectRoa .= "((b.bulan_".$a."/".$a.") * 12) as hasil".$a.",";
                $selectRoa .= "a.B_".$a.",";
                $selectRoa .= "b.bulan_".$a.",";

                $selectLoan .= "a.bulan_".$a.",";

                $selectCasa .= "a.B_".$a.",";

            }else {
                $selectkredit .= "a.B_0".$a.",";

                $selectDpk .= "((b.bulan_".$a."/a.b_0".$a.") * 100) as hasil".$a.",";
                $selectDpk .= "a.B_0".$a.",";
                $selectDpk .= "b.bulan_".$a.",";

                $selectRoa .= "((b.bulan_".$a."/".$a.") * 12) as hasil".$a.",";
                $selectRoa .= "a.B_0".$a.",";
                $selectRoa .= "b.bulan_".$a.",";

                $selectLoan .= "a.bulan_".$a.",";

                $selectCasa .= "a.B_0".$a.",";
            }
            $selectDpk .= "b.bulan_".$a.",";
           

        }

         $data['rateKredit'] = get_data('tbl_budget_plan_neraca a',[
            'select' => $selectDpk,
            'join'   => "tbl_labarugi b on a.kode_cabang = b.kode_cabang type left",
            'where'  => "a.kode_cabang = '".$cabang."' and a.coa = '1450000' AND b.glwnco = '4150000'"
         ])->result_array();

         $data['rateDpk'] = get_data('tbl_budget_plan_neraca a',[
            'select' => $selectDpk,
            'join'   => "tbl_labarugi b on a.kode_cabang = b.kode_cabang type left",
            'where'  => "a.kode_cabang = '".$cabang."' and a.coa = '602' AND b.glwnco = '5130000'"
         ])->result_array();

         $data['portofolioKredit'] = get_data('tbl_budget_plan_neraca a',[
            'select' => 'a.coa, '.$selectkredit,
            'where'  => "a.kode_cabang = '".$cabang."' and (a.coa = '122502' or a.coa = '122501' or a.coa = '122506')"
         ])->result_array();

         $data['kolektabilitasNpl'] = get_data('tbl_kolektibilitas_npl a',[
            'where'  => "a.kode_cabang = '".$cabang."' and a.tahun_core = '".$thn_trakhir."'"
         ])->result_array();


          $data['kolektabilitasDetail1'] = get_data('tbl_kolektibilitas a, tbl_kolektibilitas_detail b',[
            'select' => 'b.*',
            'where'  => " a.id = b.id_kolektibilitas and coa_produk_kredit  = '122502' and a.kode_cabang = '".$cabang."'  and b.tahun_core = '".$thn_trakhir."' "
         ])->result_array();

          $data['kolektabilitasDetail2'] = get_data('tbl_kolektibilitas a, tbl_kolektibilitas_detail b',[
            'select' => 'b.*',
            'where'  => " a.id = b.id_kolektibilitas and coa_produk_kredit  = '122506' and a.kode_cabang = '".$cabang."'  and b.tahun_core = '".$thn_trakhir."' "
         ])->result_array();



          $data['loan'] = get_data('tbl_labarugi a',[
            'select' => $selectLoan,
            'where'  => "a.kode_cabang = '".$cabang."' and (a.glwnco = '4100000' or a.glwnco = '5500000' or a.glwnco = '5100000') "
         ])->result_array();


         $data['roa'] = get_data('tbl_budget_plan_neraca a',[
            'select' => $selectRoa,
            'join'   => "tbl_labarugi b on a.kode_cabang = b.kode_cabang type left",
            'where'  => "a.kode_cabang = '".$cabang."' and a.coa = '1000000' AND b.glwnco = '59999'"
         ])->result_array();


         $data['nim'] = get_data('tbl_labarugi b',[
            'where'  => "b.kode_cabang = '".$cabang."' and (b.glwnco = '5100000' or b.glwnco = '4195011')"
         ])->result_array();

          $data['nimAktifa'] = get_data('tbl_budget_plan_neraca a',[
            'where'  => "a.kode_cabang = '".$cabang."' and a.coa = '1000000' "
         ])->result_array();


         $data['casa'] = get_data('tbl_budget_plan_neraca a',[
            'select' => 'distinct '.$selectCasa,
            'where'  => "a.kode_cabang = '".$cabang."' and  a.kode_anggaran = '".$anggaran1."' and (a.coa = '602' or a.coa = '2130000')"
         ])->result_array();

         $data['rasiofee'] = get_data('tbl_labarugi a',[
            'select' => $selectLoan,
            'where'  => "a.kode_cabang = '".$cabang."' and (a.glwnco = '4590000' or a.glwnco = '4500000' or a.glwnco = '4100000') "
         ])->result_array();



        // echo json_encode($data['nim']);   

        $response   = array(
            'table'     => $this->load->view('transaction/budget_planner/rekap_rasio/table',$data,true),
        );
        render($response,'json');
    }

    function save_perubahan($anggaran="",$cabang="") {       

        $data   = json_decode(post('json'),true);

        // echo post('json');
        foreach($data as $getId => $record) {
            $cekId = $getId;

            $record = insert_view_report_arr($record);
            // echo $id." - ".$cekId[1]."<br>";
            $cek  = get_data('tbl_rekap_rasio a',[
                'select'    => 'a.id',
                'where'     => [
                    'a.kode'             => $cekId,
                    'a.kode_anggaran'   => $anggaran,
                    'a.kode_cabang'   => $cabang,
                ]
            ])->result_array();
     
            if(count($cek) > 0){
                update_data('tbl_rekap_rasio', $record,'id',$cek[0]['id']);
            }else {
                    $record['kode'] = $cekId;
                    $record['kode_anggaran'] = $anggaran;
                    $record['kode_cabang'] = $cabang;
                    insert_data('tbl_rekap_rasio',$record);
            } 
         } 
    }
} 

