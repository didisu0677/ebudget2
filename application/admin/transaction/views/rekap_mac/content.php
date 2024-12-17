<div class="main-container">
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="card-header text-center">
					MONITORING ANGGARAN BANK JATENG TAHUN <?= $tahun->tahun_anggaran ?><br>
					BERDASAR KANTOR CABANG DAN CAPEM <br>
					<red><?=  $coa->glwnco.' - '.$coa->glwdes ?></red>
				</div>
				<div class="card-body">
					<div class="table-responsive tab-pane fade active show">
						<table class="table table-striped table-bordered table-app table-hover">
							<thead>
								<th width="30" class="text-center align-middle"><?= lang('no') ?></th>
								<th class="mw-100 text-center align-middle"><?= lang('coa_baru') ?></th>
								<th class="mw-100 text-center align-middle"><?= lang('jenis_kantor') ?></th>
								<th colspan="2" class="mw-250 text-center align-middle"><?= lang('nama_kantor') ?></th>
								<th class="mw-150 text-center align-middle"><?= month_lang($bulan).' '.($tahun->tahun_anggaran-1) ?> <br>Real</th>
								<th class="mw-150 text-center align-middle"><?= month_lang(12).' '.($tahun->tahun_anggaran-1) ?><br>Real</th>
								<th class="mw-150 text-center align-middle">- <br>Real</th>
								<th class="mw-150 text-center align-middle"><?= month_lang($bulan).' '.($tahun->tahun_anggaran) ?> <br>Renc</th>
								<th class="mw-150 text-center align-middle"><?= month_lang($bulan).' '.($tahun->tahun_anggaran) ?> <br>Real</th>
								<th class="mw-150 text-center align-middle">Penc <br>(%)</th>
								<th class="mw-150 text-center align-middle">Pert <br>(%)</th>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>