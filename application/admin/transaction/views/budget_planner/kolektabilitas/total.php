<h5 class="mt-3"><?= $title_[0].$title[0] ?></h5>

<div class="card mt-3">
	<div class="card-header"><?= $title[0] ?></div>
	<div class="card-body">
		<div class="table-responsive tab-pane fade active show" id="result2">
			<table class="table table-striped table-bordered table-app table-hover">
				<thead>
					<tr>
						<th rowspan="2" width="30" class="text-center align-middle"><?= lang('no') ?></th>
						<th rowspan="2" class="text-center align-middle"><?= lang('bulan') ?></th>
						<th rowspan="2" class="text-center align-middle"><?= lang('total_krd') ?></th>
						<th colspan="5" class="text-center align-middle"><?= lang('kolektabilitas') ?></th>
						<th rowspan="2" width="150px" class="text-center align-middle">KRD BERMASALAH</th>
						<th rowspan="2" width="150px" class="text-center align-middle"><?= lang('npl').' (%)' ?></th>
					</tr>
					<tr>
						<th width="150px" class="text-center align-middle">1</th>
						<th width="150px" class="text-center align-middle">2</th>
						<th width="150px" class="text-center align-middle">3</th>
						<th width="150px" class="text-center align-middle">4</th>
						<th width="150px" class="text-center align-middle">5</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($detail_tahun as $k2 => $v2) {
					$v_field  = 'B_' . sprintf("%02d", $v2->bulan);
					$column = month_lang($v2->bulan).' '.$v2->tahun;
					$column .= '('.$v2->singkatan.')';

					$kredit = 0;
					$col_1 	= 0;
					$col_2 	= 0;
					$col_3 	= 0;
					$col_4 	= 0;
					$col_5 	= 0;
					$npl 	= 0;
					foreach ($listTotalKredit as $k => $v) {
						if($v->sumber_data == $v2->sumber_data):
							$kredit += $v->{$v_field};
							$col_1  += $v->{$v_field.'_1'};
							$col_2  += $v->{$v_field.'_2'};
							$col_3  += $v->{$v_field.'_3'};
							$col_4  += $v->{$v_field.'_4'};
							$col_5  += $v->{$v_field.'_5'};
							$npl    += $v->{$v_field.'_bermasalah'};
						endif;
					}
					
					$total_npl = ($kredit!=0)?($npl/$kredit)*100:0;

					$item = '<tr>';
					$item .= '<td>'.($k2+1).'</td>';
					$item .= '<td>'.$column.'</td>';
					$item .= '<td class="text-right">'.custom_format($kredit).'</td>';
					$item .= '<td class="text-right">'.custom_format($col_1).'</td>';
					$item .= '<td class="text-right">'.custom_format($col_2).'</td>';
					$item .= '<td class="text-right">'.custom_format($col_3).'</td>';
					$item .= '<td class="text-right">'.custom_format($col_4).'</td>';
					$item .= '<td class="text-right">'.custom_format($col_5).'</td>';
					$item .= '<td class="text-right">'.custom_format($npl).'</td>';
					$item .= '<td class="text-right">'.custom_format($total_npl,false,2).'</td>';
					$item .= '</tr>';

					if($v2->sumber_data == 3):
						$column = month_lang($v2->bulan);
						$h['npl'][$column] = $total_npl;
						$this->session->set_userdata($h);
					endif;
					$z['npl2'][$v2->sumber_data.'-'.$v_field] = $total_npl;
					$this->session->set_userdata($z);

					echo $item;
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>