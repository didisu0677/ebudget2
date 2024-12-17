<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Laba_rugi_op extends BE_Controller {
    var $path = 'transaction/budget_planner/';
    function __construct() {
        parent::__construct();
    }

    private function data_cabang(){
        $cabang_user  = get_data('tbl_user',[
            'where' => [
                'is_active' => 1,
                'id_group'  => id_group_access('Neraca_laba_rugi')
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
        return $data;
    }
    
    function index($p1="") { 
        $data = $this->data_cabang();
        render($data,'view:'.$this->path.'laba_rugi/index');
    }

     function data ($anggaran="", $cabang=""){

         $tahun = 'tbl_history_'.substr($anggaran, 0,4);     
         $select = 'TOT_'.$cabang;

         $data['data'] = get_data($tahun.' as a',[
            'select'    =>
                    "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
                    coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
                    coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
                    coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
                    coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
                    coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
                    coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
                    coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
                    coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
                    coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
                    coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
                    coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
                    b.glwdes,
                    b.glwnob,
                    b.glwsbi,
                    b.glwnco,
                    b.kali_minus",
            'join'  => 'tbl_m_coa b on a.glwnco = b.glwnco type LEFT',  
            'where'     => " a.bulan not in(0) and b.glwnco like '4%' or b.glwnco like '5%' group by b.glwnco order by b.id"     

         ])->result_array();

        //  $data['nameA'] =get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       

        //     'where'     => " a.bulan not in(0) and b.glwdes like '%PENDAPATAN DAN BEBAN BUNGA%' group by b.glwdes"
        // ])->result_array();  

        //   $data['nameB'] =get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       
        //     'where'     => " a.bulan not in(0) and b.glwdes like '% PENDAPATAN DAN BEBAN OPERASIONAL LAIN%' group by b.glwdes"
           
        // ])->result_array();  

        //   $data['nameC'] = get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       

        //     'where'     => " a.bulan not in(0) and b.glwdes like '% LABA RUGI OPERASIONAL%' group by b.glwdes"
        // ])->result_array();  


        //     $data['nameD'] = get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       


        //     'where'     => " a.bulan not in(0) and b.glwdes like '% PENDAPATAN NON-OPERASIONAL%' group by b.glwdes"
            
        // ])->result_array();  


        //  $data['nameE'] =get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       


        //     'where'     => " a.bulan not in(0) and b.glwdes like '%BEBAN NON-OPERASIONAL%' group by b.glwdes"
        // ])->result_array();  


        //     $data['nameF'] = get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       

        //     'where'     => " a.bulan not in(0) and b.glwdes like '% LABA RUGI NON-OPERASIONAL (D + E)%' group by b.glwdes"
        // ])->result_array();  


        //  $data['nameG'] =get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       
        //     'where'     => " a.bulan not in(0) and b.glwdes like '% TOTAL PENDAPATAN%' group by b.glwdes"
        // ])->result_array();  


        //     $data['nameH'] = get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       

        //     'where'     => " a.bulan not in(0) and b.glwdes like '% TOTAL BIAYA%' group by b.glwdes"
        // ])->result_array();  


        //        $data['nameK'] = get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       

        //     'where'     => " a.bulan not in(0) and b.glwdes like '% LABA RUGI BERSIH (G - J)%' group by b.glwdes"
        // ])->result_array();  


        //  $data['A'] = get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       

        //     'where'     => " a.bulan not in(0) and b.glwnco like '41%' or b.glwnco like '51%' group by b.glwdes order by SUBSTRING(b.glwnco,1,5)"
        // ])->result();  



        // $data['B'] = get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       

        //     'where'     => " a.bulan not in(0) and b.glwnco like '45%' or b.glwnco like '55%' or b.glwnco like '56%' or b.glwnco like '57%' group by b.glwdes order by SUBSTRING(b.glwnco,1,5)"
        // ])->result();  


        // $data['D'] =get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       

        //     'where'     => " a.bulan not in(0) and b.glwnco like '48%' group by b.glwdes order by SUBSTRING(b.glwnco,1,5)"
        // ])->result();  


        //  $data['E'] =get_data($tahun.' as a',[

        //     'select'    => 
        //            "coalesce(sum(case when a.bulan = '1' then a.".$select." end), 0) as b_1,
        //             coalesce(sum(case when a.bulan = '2' then a.".$select." end), 0) as b_2,
        //             coalesce(sum(case when a.bulan = '3' then a.".$select." end), 0) as b_3,
        //             coalesce(sum(case when a.bulan = '4' then a.".$select." end), 0) as b_4,
        //             coalesce(sum(case when a.bulan = '5' then a.".$select." end), 0) as b_5,
        //             coalesce(sum(case when a.bulan = '6' then a.".$select." end), 0) as b_6,
        //             coalesce(sum(case when a.bulan = '7' then a.".$select." end), 0) as b_7,
        //             coalesce(sum(case when a.bulan = '8' then a.".$select." end), 0) as b_8,
        //             coalesce(sum(case when a.bulan = '9' then a.".$select." end), 0) as b_9,
        //             coalesce(sum(case when a.bulan = '10' then a.".$select." end), 0) as b_10,
        //             coalesce(sum(case when a.bulan = '11' then a.".$select." end), 0) as b_11,
        //             coalesce(sum(case when a.bulan = '12' then a.".$select." end), 0) as b_12,
        //             b.glwdes,
        //             b.glwnob,
        //             b.glwsbi,
        //             b.glwnco,
        //             b.kali_minus",
            
        //     'join'  => ['tbl_m_coa b on a.glwnco = b.glwnco type LEFT',
        //     ],       

        //     'where'     => " a.bulan not in(0) and b.glwnco like '58%' group by b.glwdes order by SUBSTRING(b.glwnco,1,5)"
        // ])->result();  

        $response   = array(
            'table'     => $this->load->view('transaction/budget_planner/laba_rugi/table',$data,true),
        );
        render($response,'json');
    }

}