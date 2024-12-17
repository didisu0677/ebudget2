<?php
    $item = "<center>";

    $item .= "<tr><td>A1</td><td>Effective Rate DPK</td>";
    $kali = 1;
    for($i = 1;$i<=12;$i++){
        if(!empty($a1[$i])){
            $a = $a1[$i]['isi']*$kali;
            $item .= "<td class='text-right'>".$a."</td>";
        }else{
            $item .= "<td class='text-right'>0</td>";
        }
        
    }
    
    $item .= "</tr>";

    $item .= "<tr><td>A2</td><td>- Biaya Bunga DPK</td>";

    for($i = 1;$i<=12;$i++){
         if(!empty($a2[$i])){

            $a = $a2[$i]['isi']/$i*12;
            $item .= "<td class='text-right'>".custom_format(view_report($a))."</td>";
        }else{
            $item .= "<td class='text-right'>0</td>";
        }
    }
    $item .= "</tr>";

     $item .= "<tr><td>A3</td><td>- DPK</td>";

    for($i = 1;$i<=12;$i++){
        if(!empty($a2[$i])){
            $item .= "<td class='text-right'>".custom_format(view_report($a3[$i]['isi']))."</td>";
        }else{
            $item .= "<td class='text-right'>0</td>";
        }
    }
    $item .= "</tr>";
    


    
    $item .="</center>";
    echo $item;

?>