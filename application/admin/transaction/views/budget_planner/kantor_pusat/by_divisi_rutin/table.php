<?php
	$item = 0;
	$i  = 0;
	if(count($list)>0):
		foreach ($header as $h) {
			$rowspan = 0;
			$no = 0;
			$i += 1;
			foreach ($list as $k => $v) {
				
				if($v->kegiatan == $h):
					$no += 1;
					$bgedit ="";
					$contentedit ="false" ;
					$id = 'keterangan';
					if($current_cabang == user('kode_cabang') && $akses_ubah == 1) {
						$bgedit ="#ffbb33";
						$contentedit ="true" ;
						$id = 'id' ;
					}
					$item .= '<tr>';
					if($no == 1):
						$name = str_replace(' ', '_', $h);
						$item .= '<td rowspan='.${'count_'.$name}.'>'.$i.'</td>';
						$item .= '<td rowspan='.${'count_'.$name}.'>'.$v->kegiatan.'</td>';
					endif;
					$item .= '<td>'.$v->glwnco.'</td>';
					$item .= '<td>'.$v->glwdes.'</td>';
					for ($i = 1; $i <= 12; $i++) { 
						$v_field  = 'T_' . sprintf("%02d", $i);
						$value = $v->{$v_field};

						$item .= '<td style="background:'.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;"  contenteditable="'.$contentedit.'" class="edit-value text-right" data-name="'.$v_field.'" data-id="'.$v->id.'" data-value="'.number_format($value).'">'.number_format($value).'</div></td>';
					}
					$item .= '<td class="button"><button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="'.$v->id.'" title="'.lang('ubah').'"><i class="fa-edit"></i></button></td>';
					$item .= '</tr>';
				endif;
			}
		}
	else:
		$item .= '<tr><th colspan="'.(12+5).'" class="text-center">Data Not Found</th></tr>';
	endif;
	echo $item;
?>