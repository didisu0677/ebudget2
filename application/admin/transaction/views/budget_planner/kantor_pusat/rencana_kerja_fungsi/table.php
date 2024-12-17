<?php
	if(count($list)>0):
     	$item = '';
     	foreach ($list as $k => $v) {
     		$produk = 'Tidak'; if($v->produk == 1) $produk = 'Ya';
     		$anggaran = 'Tidak'; if($v->anggaran == 1) $anggaran = 'Ya';
			$item .= '<tr>';
			$item .= '<td>'.($k+1).'</td>';
			$item .= '<td>'.$v->kebijakan_umum.'</td>';
			$item .= '<td>'.$v->program_kerja.'</td>';
			$item .= '<td>'.$produk.'</td>';
			$item .= '<td>'.$v->perspektif.'</td>';
			$item .= '<td>'.$v->status_program.'</td>';
			$item .= '<td>'.$v->skala_program.'</td>';
			$item .= '<td>'.$v->tujuan.'</td>';
			$item .= '<td>'.$v->output.'</td>';
			$item .= '<td>'.$anggaran.'</td>';
			$item .= '<td class="button"><button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="'.$v->id.'" title="'.lang('ubah').'"><i class="fa-edit"></i></button></td>';
			$item .= '</tr>';
		}
		echo $item;
	else:
		echo '<tr><th colspan="11" class="text-center">Data Not Found</th></tr>';
	endif;
?>