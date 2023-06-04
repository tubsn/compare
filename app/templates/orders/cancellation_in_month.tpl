<main>

	<figure class="mb">
		<h3 class="text-center">Bestelleingang und Kündiger pro Monat</h3>
		<p class="nt text-center">Abgänge beziehen sich hier auf die Zugänge des Monats.</p>
		<?=$charts->create([
			'metric' => [$longterm['orders'], $longterm['cancelledNegative']],
			'dimension' => $longterm['dimensions'],
			'color' => ['#314e6f', '#f77474'],
			'height' => 450,
			'area' => true,
			'stacked' => true,
			'showValues' => false,
			'xfont' => '13px',
			'name' => ['Zugänge', 'davon gekündigt'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Netto Bestelleingang</h3>
		<p class="nt text-center">Nutzer die Stand Heute aus dem zu sehenden Monat noch aktiv sind</p>
		<?=$charts->create([
			'metric' => $longterm['active'],
			'dimension' => $longterm['dimensions'],
			'color' => '#314e6f',
			'height' => 300,
			'area' => true,
			'stacked' => true,
			'showValues' => false,
			'xfont' => '13px',
			'name' => 'Nettozugänge',
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


</main>
