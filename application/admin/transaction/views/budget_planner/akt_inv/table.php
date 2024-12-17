<?php 
	foreach($grup[0] as $m0) { 

	$no=0;
	$total = 0;
	$jumlah = 0;

    ?>
	<tr>
		<td><?php echo $m0->kode; ?></td>
		<td><?php echo $m0->keterangan; ?></td>
		<!-- <td><?php echo $m0->catatan; ?></td> -->
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<?php foreach($produk[$m0->kode] as $m1) { 
		$no++;
		$total += ($m1->harga * $m1->jumlah);
		$jumlah += $m1->jumlah ;

		$bgedit ="";
		$contentedit ="false" ;
		if(($m1->grup !='E.4' && $m1->grup !='E.5') || $m1->kode_inventaris =='') {
			$bgedit ="#ffbb33";
			$contentedit ="true" ;
		}	

		$id = 'keterangan';
		if($current_cabang == user('kode_cabang') && $akses_ubah == 1) {
			$bgedit ="#ffbb33";
			$contentedit ="true" ;
			$id = 'id' ;
		}else{
			$bgedit ="";
			$contentedit ="false" ;
			$id = 'id' ;
		}


		?> 
		<tr>
			<td><?php echo $no; ?></td>
			<td><?php echo $m1->nama_inventaris; ?></td>
			<td><?php echo $m1->catatan; ?></td>
			
			<td style="background: <?php echo $bgedit; ?>"><div style="background: <?php echo $bgedit; ?>" style="min-height: 10px; width: 50px; overflow: hidden;"  contenteditable="<?php echo $contentedit; ?>" class="edit-value text-right" data-name="harga" data-id="<?php echo $m1->id; ?>" data-value="'.number_format($m1->harga).'"><?php echo number_format($m1->harga); ?></div></td>

			<td style="background: <?php echo $bgedit; ?>"><div style="background: <?php echo $bgedit; ?>" style="min-height: 10px; width: 50px; overflow: hidden;"  contenteditable="<?php echo $contentedit; ?>" class="edit-value text-right" data-name="jumlah" data-id="<?php echo $m1->$id; ?>" data-value="'.number_format($m1->jumlah).'"><?php echo number_format($m1->jumlah); ?></div></td>

			<td style="background: <?php echo $bgedit; ?>"><div style="background: <?php echo $bgedit; ?>" style="min-height: 10px; width: 50px; overflow: hidden;"  contenteditable="<?php echo $contentedit; ?>" class="edit-value text-right" data-name="bulan" data-id="<?php echo $m1->$id; ?>" data-value="'.number_format($m1->bulan).'"><?php echo number_format($m1->bulan); ?></div></td>

			<th style="background: <?php echo $bgedit; ?>"><div style="background: <?php echo $bgedit; ?>" style="min-height: 10px; width: 50px; overflow: hidden;" class="text-right" data-name="bulan" data-id="<?php echo $m1->$id; ?>" data-value="'.number_format($m1->harga * $m1->jumlah).'"><?php echo number_format($m1->harga * $m1->jumlah); ?></div></th>

			<td class="button">
			<button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="<?php echo $m1->id; ?>" title="<?php echo lang('ubah'); ?>"><i class="fa-edit"></i></button>
			<!--
			<button type="button" class="btn btn-danger btn-delete" data-key="delete" data-id="<?php echo $m1->id; ?>" title="<?php echo lang('hapus'); ?>"><i class="fa-trash-alt"></i></button>
			-->
		</td>
		</tr>
	<?php } 
	?>
		<tr>
			<th style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"></th>
			<th style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;">TOTAL <?php echo $m0->kode; ?></th>
			<th style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"></th>
			<th style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"></th>
			<th class="text-right" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><?php echo number_format($jumlah); ?></th>
			<th style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"></th>
			<th class="text-right" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><?php echo number_format($total); ?></th>
			<th style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"></th>
			
		</tr>	

<?php 
$t_jumlah = 0;
} ?>
		
	