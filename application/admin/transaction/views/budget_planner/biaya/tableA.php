<?php
$item = '';
// $item = '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="'.$v_field.'" data-id="'.$m0->glwnco.'" data-value="'.$m0->$v_field.'">'.$content.'</div></td>';
$bg = ' class="text-right  bg-grey"';
$bd = ' class="text-right  border-none bg-white"';
$tr = ' class="text-right "';
foreach ($coa as $k => $v) {
	$item2 = '';
	$dt2 = [];
	$minus = $v->kali_minus;
	if(isset($detail['1'][$v->glwnco])){
		foreach ($detail['1'][$v->glwnco] as $k2 => $v2) {
			$item3 = '';
			$dt3 = [];
			$minus2 = $v2->kali_minus;
			if(isset($detail['2'][$v2->glwnco])){
				foreach ($detail['2'][$v2->glwnco] as $k3 => $v3) {
					$item4 = '';
					$dt4   = [];
					$minus3 = $v3->kali_minus;
					if(isset($detail['3'][$v3->glwnco])){
						foreach ($detail['3'][$v3->glwnco] as $k4 => $v4) {
							$item5 = '';
							$dt5   = [];
							$minus4 = $v4->kali_minus;
							if(isset($detail['4'][$v4->glwnco])){
								foreach ($detail['4'][$v4->glwnco] as $k5 => $v5) {
									$item6 	= '';
									$dt6 	= [];
									$minus5 = $v5->kali_minus;
									if(isset($detail['5'][$v5->glwnco])){
										foreach ($detail['5'][$v5->glwnco] as $k6 => $v6) {
											$item6 .= '<tr>';
											$item6 .= '<td>'.$v6->glwnco.'</td>';
											$item6 .= '<td class="sb-6">'.$v6->glwdes.'</td>';
											$bln_trakhir = $v6->{'TOT_'.$cabang};
											$value = (float) $bln_trakhir/10;
											$minus6 = $v6->kali_minus;
											for ($i=1; $i <=12 ; $i++) {
												$val = $value * $i;
												if(isset($dt6[$i])){ $dt6[$i] += $val; }else{ $dt6[$i] = $val; }
												$item6 .= '<td>'.check_data($val,$minus6).'</td>';
											}
											$item6 .= '<td'.$bd.'></td>';
											if(!empty($v6->biaya_bulan)){
												$valueTxt = $v6->biaya_bulan;
											}
											if(!empty($v6->biaya_tahun)){
												$bln_trakhir = $v6->biaya_tahun;
											}
											$item6 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_bulan" data-id="'.$v6->glwnco.'" data-value="'.$valueTxt.'">'.$valueTxt.'</div></td>';
											$item6 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_tahun" data-id="'.$v6->glwnco.'" data-value="'.$bln_trakhir.'">'.$bln_trakhir.'</div></td>';
											$item6 .= '</tr>';
										}
										$bln_trakhir = '';
										$value = '';
										$valueTxt = '';
									}else{
										$bln_trakhir = $v5->{'TOT_'.$cabang};
										$value 		 = (float) $bln_trakhir/10;
										$valueTxt 	 = check_data($value,$minus5);
										$bln_trakhir = check_data($bln_trakhir,$minus5);
									}

									$item5 .= '<tr>';
									$item5 .= '<td>'.$v5->glwnco.'</td>';
									$item5 .= '<td class="sb-5">'.$v5->glwdes.'</td>';
									for ($i=1; $i <= 12 ; $i++) { 
										if(count($dt6)>0){ $val = $dt6[$i]; }
										else{ $val = $value * $i; }
										$item5 .= '<td>'.check_data($val,$minus5).'</td>';
										if(isset($dt5[$i])){ $dt5[$i] += $val; }else{ $dt5[$i] = $val; }
									}
									$item5 .= '<td'.$bd.'></td>';
									if(!empty($v5->biaya_bulan)){
										$valueTxt = $v5->biaya_bulan;
									}
									if(!empty($v5->biaya_tahun)){
										$bln_trakhir = $v5->biaya_tahun;
									}
									$item5 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_bulan" data-id="'.$v6->glwnco.'" data-value="'.$valueTxt.'">'.$valueTxt.'</div></td>';
									$item5 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_tahun" data-id="'.$v6->glwnco.'" data-value="'.$bln_trakhir.'">'.$bln_trakhir.'</div></td>';
									$item6 .= '</tr>';
									$item5 .= '</tr>';
									$item5 .= $item6;
								}
								$bln_trakhir = '';
								$value = '';
								$valueTxt = '';
							}else{
								$bln_trakhir = $v4->hasil;
								$value 		 = (float) $bln_trakhir/ $bulan_terakhir;
								$valueTxt 	 = check_data($value,$minus4);
								$getTahun	 = check_data($value*12, $minus4);
								$bln_trakhir = check_data($bln_trakhir,$minus4);
							}

							$item4 .= '<tr>';
							$item4 .= '<td>'.$v4->glwnco.'</td>';
							$item4 .= '<td class="sb-4">'.$v4->glwdes.'</td>';
							for ($i=1; $i <= 12 ; $i++) {
								if(count($dt5)>0){ 
									$val = $dt5[$i];
									$bulanb = "bulan_b".$i;
									if(!empty($v4->$bulanb)){
										$val = $v4->$bulanb ;
									}
									$item4 .= '<td '.$tr.'><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_b'.$i.'" data-id="'.$v4->glwnco.'" data-value="'.$val.'">'.check_data($val,$minus4).'</div></td>';
								}
								else{ 
									$val = $value ; 

									$bulanb = "bulan_b".$i;
									if(!empty($v4->$bulanb)){
										$val = $v4->$bulanb ;
									}
									$item4 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_b'.$i.'" data-id="'.$v4->glwnco.'" data-value="'.$val.'">'.check_data($val,$minus4).'</div></td>'; 
								} 
								if(isset($dt4[$i])){ $dt4[$i] += $val; }else{ $dt4[$i] = $val; }
							}
							$item4 .= '<td'.$bd.'></td>';
							if(!empty($v4->biaya_bulan)){
								$valueTxt = $v4->biaya_bulan;
							}
							if(!empty($v4->biaya_tahun)){
								$bln_trakhir = $v4->biaya_tahun;
							}
							$item4 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_bulan" data-id="'.$v4->glwnco.'" data-value="'.$valueTxt.'">'.$valueTxt.'</div></td>';
							$item4 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_tahun" data-id="'.$v4->glwnco.'" data-value="'.$bln_trakhir.'">'.$bln_trakhir.'</div></td>';
							$item4 .= '<td'.$bd.'></td>';
							$item4 .= '<td'.$bg.'>'.check_data($val,$minus4).'</td>';
							$item4 .= '<td'.$bg.'>'.$v4->hasil2.'</td>';
							$item4 .= '<td'.$bg.'>'.$v4->hasil.'</td>';
							$item4 .= '</tr>';
							$item4 .= $item5;
						}
						$bln_trakhir = '';
						$value = '';
						$valueTxt = '';
					}else{
						$bln_trakhir = $v3->hasil;
						$value 		 = (float) $bln_trakhir/$bulan_terakhir;
						$valueTxt 	 = check_data($value,$minus3);
						$getTahun	 = check_data($value*12, $minus3);
						$bln_trakhir = check_data($bln_trakhir,$minus3);
					}

					$item3 .= '<tr>';
					$item3 .= '<td>'.$v3->glwnco.'</td>';
					$item3 .= '<td class="sb-3">'.$v3->glwdes.'</td>';
					for ($i=1; $i <= 12 ; $i++) {
						if(count($dt4)>0){
							$val = $dt4[$i]; 

							$bulanb = "bulan_b".$i;
							if(!empty($v3->$bulanb)){
								$val = $v3->$bulanb;
							}
						
							$item3 .=  '<td '.$tr.'><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_b'.$i.'" data-id="'.$v3->glwnco.'" data-value="'.$val.'">'.check_data($val,$minus3).'</div></td>';
						}
						else{ 
							$val = $value ; 

							$bulanb = "bulan_b".$i;
							if(!empty($v3->$bulanb)){
								$val = $v3->$bulanb ;
							}
							$item3 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_b'.$i.'" data-id="'.$v3->glwnco.'" data-value="'.$val.'">'.check_data($val,$minus3).'</div></td>';
						} 
						
						if(isset($dt3[$i])){ $dt3[$i] += $val; }else{ $dt3[$i] = $val; }
					}
					$item3 .= '<td'.$bd.'></td>';
					if(!empty($v3->biaya_bulan)){
						$valueTxt = $v3->biaya_bulan;
					}
					if(!empty($v3->biaya_tahun)){
						$bln_trakhir = $v3->biaya_tahun;
					}
					$item3 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_bulan" data-id="'.$v3->glwnco.'" data-value="'.$valueTxt.'">'.$valueTxt.'</div></td>';
					$item3 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_tahun" data-id="'.$v3->glwnco.'" data-value="'.$bln_trakhir.'">'.$bln_trakhir.'</div></td>';
					$item3 .= '<td'.$bd.'></td>';
					$item3 .= '<td'.$bg.'>'.check_data($val,$minus3).'</td>';
					$item3 .= '<td'.$bg.'>'.check_data($v3->hasil2,$minus3).'</td>';
					$item3 .= '<td'.$bg.'>'.check_data($v3->hasil,$minus3).'</td>';
					$item3 .= '</tr>';
					$item3 .= $item4;
				}
				$bln_trakhir = '';
				$value = '';
				$valueTxt = '';
			}else{
				$bln_trakhir = $v2->hasil;
				$value 		 = (float) $bln_trakhir/$bulan_terakhir;
				$valueTxt 	 = check_data($value,$minus2);
				$getTahun	 = check_data($value*12, $minus2);
				$bln_trakhir = check_data($bln_trakhir,$minus2);
			}

			$item2 .= '<tr>';
			$item2 .= '<td>'.$v2->glwnco.'</td>';
			$item2 .= '<td class="sb-2">'.$v2->glwdes.'</td>';
			for ($i=1; $i <=12 ; $i++) { 
				if(count($dt3)>0){
					$val = $dt3[$i];
					$bulanb = "bulan_b".$i;
					if(!empty($v2->$bulanb)){
						$val = $v2->$bulanb;
					} 
					$item2 .= '<td '.$tr.'><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_b'.$i.'" data-id="'.$v2->glwnco.'" data-value="'.$val.'">'.check_data($val,$minus2).'</div></td>';
				}
				else{ 
					$val = $value ; 
					$bulanb = "bulan_b".$i;
					if(!empty($v2->$bulanb)){
						$val = $v2->$bulanb ;
					}
					$item2 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_b'.$i.'" data-id="'.$v2->glwnco.'" data-value="'.$val.'">'.check_data($val,$minus2).'</div></td>';
				} 
				if(isset($dt2[$i])){ $dt2[$i] += $val; }else{ $dt2[$i] = $val; }
			}
			$item2 .= '<td'.$bd.'></td>';
			if(!empty($v2->biaya_bulan)){
				$valueTxt = $v2->biaya_bulan;
			}
			if(!empty($v2->biaya_tahun)){
				$bln_trakhir = $v2->biaya_tahun;
			}
			$item2 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_bulan" data-id="'.$v2->glwnco.'" data-value="'.$valueTxt.'">'.$valueTxt.'</div></td>';
			$item2 .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_tahun" data-id="'.$v2->glwnco.'" data-value="'.$bln_trakhir.'">'.$bln_trakhir.'</div></td>';
			$item2 .= '<td'.$bd.'></td>';
			$item2 .= '<td'.$bg.'>'.check_data($val,$minus2).'</td>';
			$item2 .= '<td'.$bg.'>'.check_data($v2->hasil2,$minus2).'</td>';
			$item2 .= '<td'.$bg.'>'.check_data($v2->hasil,$minus2).'</td>';
			$item2 .= '</tr>';
			$item2 .= $item3;
		}
		$bln_trakhir = '';
		$value = '';
		$valueTxt = '';
	}else{
		$bln_trakhir = $v->hasil;
		$value 		 = (float) $bln_trakhir/$bulan_terakhir;
		$valueTxt 	 = check_data($value,$minus);
		$getTahun	 = check_data($value*12, $minus);
		$bln_trakhir = check_data($bln_trakhir,$minus);
	}

	$item .= '<tr>';
	$item .= '<td>'.$v->glwnco.'</td>';
	$item .= '<td>'.$v->glwdes.'</td>';
	for ($i=1; $i <= 12 ; $i++) { 
		if(count($dt2)>0){ 
			$val = $dt2[$i]; 
			$bulanb = "bulan_b".$i;
			if(!empty($v->$bulanb)){
				$val = $v->$bulanb;
			}
		}
		else{ 
			$val = $value; 
			$bulanb = "bulan_b".$i;
			if(!empty($v->$bulanb)){
				$val = $v->$bulanb;
			}
		}
		$item .= '<td '.$tr.'><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="bulan_b'.$i.'" data-id="'.$v->glwnco.'" data-value="'.$val.'">'.check_data($val,$minus).'</div></td>';
	}
	$item .= '<td'.$bd.'></td>';
	if(!empty($v2->biaya_bulan)){
		$val = $v2->biaya_bulan;
	}
	if(!empty($v2->biaya_tahun)){
		$bln_trakhir = $v2->biaya_tahun;
	}else {
		$bln_trakhir = $val * 12;
	}
	$item .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_bulan" data-id="'.$v->glwnco.'" data-value="'.$val.'">'.check_data($bln_trakhir,$minus).'</div></td>';
	$item .= '<td><div style="min-height: 10px; width: 100%; overflow: hidden;" contenteditable="true" class="edit-value text-right edited" data-name="biaya_tahun" data-id="'.$v->glwnco.'" data-value="'.$bln_trakhir.'">'.check_data($bln_trakhir,$minus).'</div></td>';
	$item .= '<td'.$bd.'></td>';
	$item .= '<td'.$bg.'>'.check_data($val,$minus).'</td>';
	$item .= '<td'.$bg.'>'.check_data($v->hasil2,$minus).'</td>';
	$item .= '<td'.$bg.'>'.check_data($v->hasil,$minus).'</td>';
	$item .= '</tr>';
	$item .= $item2;
}
echo $item;
function check_data($v,$x){
	$val = kali_minus($v,$x);
	$val = custom_format($val);
	// $val = custom_format(view_report($val));
	return $val;
}
?>