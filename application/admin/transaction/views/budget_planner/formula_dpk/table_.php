<?php
$item = "<center>";
    
       foreach ($A1 as $val) {
        $item .= '<tr>';
        $item .= '<td>'.$val->glwnco.'</td>';
        $item .= '<td>'.strtoupper(remove_spaces($val->glwdes)).'</td>';
       
            foreach ($detail_tahun as $v) {
                $T = 'T_' . $v->tahun . sprintf("%02d", $v->bulan);
                $field = 'P_' . sprintf("%02d", $v->bulan); 
                $$T = 0;
                foreach ($A1_detail as $k => $Ad) {
                    if($v->tahun == $Ad->tahun_core && $Ad->coa == $val->glwnco) {
                        $$T = $Ad->$field ;
                    }
                }


                $item .= '<td class="text-right">'.custom_format(view_report($$T)).'</td>';
            }

        $item .= '</tr>';
        if($val->glwnco == '5131011'){
            $item .= '<tr>';
            $item .= '<td></td>';
            $item .= '<td> EFECTIVE RATE</td>';
            foreach ($detail_tahun as $v) {
                $item .= '<td class="text-right">'.custom_format($rate_nonkasda['rate'],false,2).'</td>';
            } 

            $item .= '</tr>';
            $item .= '<tr>';
            $item .= '<td></td>';
            $item .= '<td> JASA GIRO PIHAK III PD BULAN</td>';
            foreach ($detail_tahun as $v) {
                $item .= '<td></td>';
            }

            $item .= '</tr>';
        }
    }

    $item .= "<tr><td></td></tr>";

        foreach ($A2 as $val) {
        $item .= '<tr>';
        $item .= '<td>'.$val->glwnco.'</td>';
        $item .= '<td>'.strtoupper(remove_spaces($val->glwdes)).'</td>';
       
            foreach ($detail_tahun as $v) {
                $T = 'T_' . $v->tahun . sprintf("%02d", $v->bulan);
                $field = 'P_' . sprintf("%02d", $v->bulan); 
                $$T = 0;
                foreach ($A2_detail as $k => $Ad) {
                    if($v->tahun == $Ad->tahun_core && $Ad->coa == $val->glwnco) {
                        $$T = $Ad->$field ;
                    }
                }


                $item .= '<td class="text-right">'.custom_format(view_report($$T)).'</td>';
            }


        $item .= '</tr>';
        if($val->glwnco == '5131012'){
            $item .= '<tr>';
            $item .= '<td></td>';
            $item .= '<td> EFECTIVE RATE</td>';
            foreach ($detail_tahun as $v) {
                $item .= '<td class="text-right">'.custom_format($rate_kasda['rate'],false,2).'</td>';
            }

            $item .= '</tr>';
            $item .= '<tr>';
            $item .= '<td></td>';
            $item .= '<td> JASA GIRO PIHAK III PD BULAN</td>';
            foreach ($detail_tahun as $v) {
                $item .= '<td></td>';
            }

            $item .= '</tr>';
        }

    }

    $item .= "</table>";
    $item .= "<br>";
    $item .= "<br>";
    $item .= "<table>";
    $item .= '<tr  style = "background:#f0f0f0;">';
    $item .= "<th>Coa</th>";
    $item .= "<th>Keterangan</th>";
    foreach ($detail_tahun as $v) {
                    $column = month_lang($v->bulan).' '.$v->tahun;
                    $column .= '<br> ('.$v->singkatan.')';
                    $item .= '<th><center>'.$column.'</center></th>';
            }
    $item .= "</tr>";


    foreach ($B as $val) {
        $item .= '<tr>';
        $item .= '<td>'.$val->glwnco.'</td>';
        $item .= '<td>'.strtoupper(remove_spaces($val->glwdes)).'</td>';
        foreach ($detail_tahun as $v) {
             $vfield = 'P_'. sprintf("%02d", $v->bulan);
             $T = 'TOTAL_' . $v->tahun . sprintf("%02d", $v->bulan);
             $$T = 0;
            foreach ($rinc_tab as $r) {
                if($v->tahun == $r->tahun_core && $r->coa == $val->glwnco){
                    $$T   = $r->$vfield;
                }
            }
            $item .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_'.$v->bulan.'" data-id="'.$v->sumber_data.'-'.$val->glwnco.'" data-value="'.$$T.'">'.custom_format(view_report($$T)).'</div></td>';
        }
        $item .= '<tr>';
        $item .= '<td>'.$val->biaya_bunga.'</td>';
        $item .= '<td>BUNGA '.strtoupper(remove_spaces($val->acct_bunga)).'</td>';
        $i_bln = 0;
        foreach ($detail_tahun as $v) {
             $i_bln++;
             $vfield = 'P_'. sprintf("%02d", $v->bulan);
             $T = 'TOTAL_' . $v->tahun . sprintf("%02d", $v->bulan);
             $R = 'RATE_' . $v->tahun . sprintf("%02d", $v->bulan);
             $RD = 'TR_' . $v->tahun . sprintf("%02d", $v->bulan);
             $$T = 0;
             $$R = 0;
             $$RD = 0;
            foreach ($rinc_tab as $r) {
                if($v->tahun == $r->tahun_core && $r->coa == $val->glwnco){
                    $$T   = $r->$vfield;
                    $$R   = ($r->$vfield * $r->rate) / (100 * 12);
                }
                    
                if($i_bln == 1 || $v->bulan == 1 ) {
                    $$RD = $$R;
                }

                if($i_bln > 1 && $v->bulan != 1 ) {
                    $rbefore = 'TR_' . $v->tahun . sprintf("%02d", ($v->bulan - 1));
                    $$RD = $$rbefore + $$R;
                }

            }
            $item .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_'.$v->bulan.'" data-id="'.$v->sumber_data.'-'.$val->glwnco.'" data-value="'.$$RD.'">'.custom_format(view_report($$RD)).'</div></td>';
        }

        $item .= '</tr>';
        $item .= '<tr>';
        $item .= '<td></td>';
        $item .= '<td> EFECTIVE RATE</td>';
        foreach ($detail_tahun as $v) {            
            $R = 'RATE_' . $v->tahun . sprintf("%02d", $v->bulan);
            $$R = 0;
            foreach ($rinc_tab as $r) {
                if($v->tahun == $r->tahun_core && $r->coa == $val->glwnco){
                    $$R   = $r->rate;
                }
            }    

            $item .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_'.$v->bulan.'" data-id="'.$v->sumber_data.'-'.$val->glwnco.'" data-value="'.$$R.'">'.custom_format($$R,false,2).'</div></td>';
        }

        $item .= '</tr>';
         $item .= '<tr>';
        $item .= '<td></td>';
        $item .= '<td>  BUNGA PD BULAN</td>';
        foreach ($detail_tahun as $v) {
            $vfield = 'P_'. sprintf("%02d", $v->bulan);
            $T = 'TOTAL_' . $v->tahun . sprintf("%02d", $v->bulan);
            $$T = 0;
            foreach ($rinc_tab as $r) {
                if($v->tahun == $r->tahun_core && $r->coa == $val->glwnco){
                    $$T   = ($r->$vfield * $r->rate) / (100 * 12);
                }
            }     

            $item .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_'.$v->bulan.'" data-id="'.$v->sumber_data.'-'.$val->glwnco.'" data-value="'.$$T.'">'.custom_format(view_report($$T)).'</div></td>';
        }

        $item .= '</tr>';
         $item .= '<tr>';
        $item .= '<td></td>';
        $item .= '<td>BIAYA HADIAH PD BULAN</td>';
        foreach ($detail_tahun as $v) {
            $item .= '<td></td>';
        }

        $item .= '</tr>';

    }

    $item .= "</table>";
    $item .= "<br>";
    $item .= "<br>";
    $item .= "<table>";
    $item .= '<tr  style = "background:#f0f0f0;">';
    $item .= "<th>Coa</th>";
    $item .= "<th>Keterangan</th>";
    $item .= "<th>Rate</th>";

    foreach ($detail_tahun as $v) {
                 $column = month_lang($v->bulan).' '.$v->tahun;
                 $column .= '<br> ('.$v->singkatan.')';
                 $item .= '<th><center>'.$column.'</center></th>';
            }
    $item .= "</tr>";

    foreach ($C as $val) {
        $item .= '<tr>';
        $item .= '<td></td>';
        $item .= '<td>'.$val->nama.'</td>';
        $item .= '<td class="text-right">'.custom_format($val->rate,false,2).'</td>';
            foreach ($detail_tahun as $v) {
                $vfield = 'P_'. sprintf("%02d", $v->bulan);
                $T = 'TOTAL_' . $v->tahun . sprintf("%02d", $v->bulan);
                $$T = 0;
                foreach ($rinc_dep as $r) {
                    if($v->tahun == $r->tahun_core && $r->coa == $val->coa){
                        $$T   = $r->$vfield;
                    }
                }
                $item .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_'.$v->bulan.'" data-id="'.$v->sumber_data.'-'.$val->coa.'" data-value="'.$$T.'">'.custom_format(view_report($$T)).'</td>';
            }


        $item .= '</tr>';
    }

        foreach ($detail_tahun as $v) {   
            $Jumlah = 'JUMLAH_' . $v->tahun . sprintf("%02d", $v->bulan);      
            $$Jumlah = 0;
        }

  $item .= "</table>";
    $item .= "<br>";
    $item .= "<br>";
    $item .= "<table>";
    $item .= '<tr  style = "background:#f0f0f0;">';
    $item .= "<th>Coa</th>";
    $item .= "<th>Keterangan1</th>";
    // for ($i = $realisasi['bulan_terakhir_realisasi'] -1; $i <= $realisasi['bulan_terakhir_realisasi']; $i++) { 
    //     $x = 'Real';
    //     $column = month_lang($i).' '.$realisasi['tahun_terakhir_realisasi'];
    //     $column .= '<br> ('.$x.')';
    //     $item .= '<th><center>'.$column.'</center></th>';                  
    // }

    foreach ($detail_tahun as $v) {
                    $column = month_lang($v->bulan).' '.$v->tahun;
                    $column .= '<br> ('.$v->singkatan.')';
                 $item .= '<th><center>'.$column.'</center></th>';
            }
    $item .= "</tr>";

    foreach ($C as $val) {
        $item .= '<tr>';
        $item .= '<td></td>';
        $item .= '<td>'.$val->nama.'</td>';
        // $item .= '<td></td>';
        // $item .= '<td></td>';
            foreach ($detail_tahun as $v) {
                $vfield = 'P_'. sprintf("%02d", $v->bulan);
                $T = 'TOTAL_' . $v->tahun . sprintf("%02d", $v->bulan);
                $$T = 0;
                $Jumlah = 'JUMLAH_' . $v->tahun . sprintf("%02d", $v->bulan); 
                foreach ($rinc_dep as $r) {
                    if($v->tahun == $r->tahun_core && $r->coa == $val->coa){
                        $$T   = ($r->$vfield * $r->rate) / 1200;

                        $$Jumlah += $$T ;
                    }
  
                }
                $item .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_'.$v->bulan.'" data-id="'.$v->sumber_data.'-'.$val->coa.'" data-value="'.$$T.'">'.custom_format(view_report($$T)).'</td>';
            }
        $item .= '</tr>';
    }


        $item .= '<tr>';   
        $item .= '<td style="background:#ffeb3b"></td>'; 
        $item .= '<td style="background:#ffeb3b">BUNGA PD BULAN</td>';
        // $item .= '<td style="background:#ffeb3b"></td>';
        // $item .= '<td style="background:#ffeb3b"></td>';
        foreach ($detail_tahun as $v) {   
            $Jumlah = 'JUMLAH_' . $v->tahun . sprintf("%02d", $v->bulan);      

           // if($v->tahun==user('tahun_anggaran')) {

                 //   if($v->bulan == 1){
                 //        $Jumlah = 'JUMLAH_' . ($v->tahun-1) . sprintf("%02d", '12'); 
                 //   }else{
                 //        $Jumlah = 'JUMLAH_' . $v->tahun . sprintf("%02d", $v->bulan-1); 
                 //   }

               $item .= '<td class="text-right" style="background:#ffeb3b">'.custom_format(view_report($$Jumlah)).'</td>';
          //  }else{
          //      $item .= '<td class="text-right" style="background:#ffeb3b"></td>';                
          //  }
        }
        $item .= '</tr>';

    $item .= '<tr>';   
    $item .= '<td style="background:#ffeb3b">'.$Bunga['glwnco'].'</td>';
    $item .= '<td style="background:#ffeb3b">'.$Bunga['account_name'].'</td>';
    // $item .= '<td class = "text-right" style="background:#ffeb3b">'.custom_format(view_report($Bunga['hasil9'] *-1)).'</td>';
    // $item .= '<td class  = "text-right" style="background:#ffeb3b">'.custom_format(view_report($Bunga['hasil10'] *-1)).'</td>';
   


    $x = $Bunga['hasil10'] - $Bunga['hasil9'];
    $default = $x ;
    $default0 = $Bunga['hasil10'];

    $n = 0 ;
    $v = '' ;
    foreach ($detail_tahun as $k2 => $v2) {
        $n++;
   
        // if($v2->bulan == 1 && $v2->tahun == user('tahun_anggaran')){
        //     //$Jumlah = 'JUMLAH_' . $v2->tahun . sprintf("%02d", $v2->bulan); 
        //      $Jumlah = 'JUMLAH_' . ($v2->tahun-1) . sprintf("%02d", '12'); 

        //     $$v = $$Jumlah ;  
        //     $nv = custom_format(view_report($$v));
        // }elseif ($v2->bulan > 1 && $v2->tahun == user('tahun_anggaran')) {
        //     $Jumlah = 'JUMLAH_' . $v2->tahun . sprintf("%02d", ($v2->bulan-1)); 
            
        //     $$v += $$Jumlah ;  

        //     $nv = custom_format(view_report($$v));
        // }else
        //     {
        //     $nv = '';
        // }

$nv = '';
        $item .= '<td class="text-right" style="background:#ffeb3b">'.$nv.'</td>';
    }
    $item .= '</tr>'; 

    $item .="</center>";
    echo $item;

?>