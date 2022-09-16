<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$page['title']?></h1>

<div style="float:right; position:relative; top:-13px;" class="text-right">
	<a class="button " href="/readers/map/local">Lokale Karte</a>
	-
	<a class="button light" href="/readers/map/germany">Deutschlandkarte</a>
</div>


<div style="width:100%; max-width:1200px; margin:0 auto; position:relative;">
	<div class="map-holder map-germany"></div>
</div>

<?php if (PORTAL == 'LR' || PORTAL == 'MOZ'): ?>
<div class="col-2" style="grid-template-columns: 1.2fr .8fr;">
	<div style="width:100%; margin:0 auto;">
		<div class="map-holder map-brandenburg"></div>
	</div>

	<div style="width:100%; margin:0 auto;">
		<div class="map-holder map-berlin"></div>
	</div>

	<div style="width:100%; margin:0 auto; margin-top:-120px; margin-right:-80px;">
		<div class="map-holder map-sachsen"></div>
	</div>

</div>
<?php endif; ?>


<?php if (PORTAL == 'SWP'): ?>
	<div style="width:100%; max-width:1200px; margin:0 auto;">
		<div class="map-holder map-bawue"></div>
	</div>
<?php endif; ?>



<script>

<?php if (PORTAL == 'LR' || PORTAL == 'MOZ'): ?>
loadMap('.map-brandenburg', '/maps/brandenburg.svg')
loadMap('.map-berlin', '/maps/berlin.svg')
loadMap('.map-sachsen', '/maps/sachsen.svg')
<?php endif; ?>

<?php if (PORTAL == 'SWP'): ?>
loadMap('.map-bawue', '/maps/bawue.svg')
<?php endif; ?>

function loadMap(container, map) {

	let el = document.querySelector(container);

	fetch(map)
	    .then(response => response.text())
	    .then(svgContent => {
	        el.innerHTML = svgContent;
			paintSVG();
			interactiveSVG(container);
	    })
	    .catch(console.error.bind(console));
}


function paintSVG() {

	let plz;
	<?php foreach ($PLZs as $plz => $data): ?>
	<?php if (!is_null($data['orderColor'])): ?>
	plz = document.querySelector('#plz<?=$plz?>');
	if (plz) {
		plz.style.fill = '<?=$data['orderColor']?>';
		plz.setAttribute('data-orders', <?=$data['orders']?>);

		<?php if ($showCancelled ?? false): ?>
		plz.style.fill = '<?=$data['cancellationQuoteColor']?>';
		plz.setAttribute('data-orders', <?=$data['cancellationQuote']?>);
		<?php endif; ?>
	}
	<?php endif; ?>
	<?php endforeach; ?>


}


function createToolTipElement() {

	let tooltip = document.createElement('div');

	tooltip.classList.add('tooltip');
	tooltip.style.background = 'black';
	tooltip.style.color = 'white';
	tooltip.style.display = 'none';
	tooltip.style.position = 'absolute';
	tooltip.style.padding = '0.2em .6em';
	tooltip.style.borderRadius = '0.3em';

	return tooltip;

}

function interactiveSVG(container) {
	let counter = document.querySelector('.tooltip');
	let svg = document.querySelector(container + ' svg');
	let tooltip = createToolTipElement();
	let canvas = document.querySelector('main');
	canvas.appendChild(tooltip);

	svg.addEventListener('mouseover', (e) => {

		let element = e.target;
		if (element.tagName != 'path') {return;}

		element.style.opacity = 0.6;

		let orders = element.getAttribute('data-orders');
		let plz = element.getAttribute('id');
		plz = plz.substr(3);

		if (orders) {
			tooltip.style.display = 'block';
			tooltip.innerHTML = `<b>PLZ:</b> ${plz}<br/><b>Users:</b> ${orders}`;

			<?php if ($showCancelled ?? false): ?>
			tooltip.innerHTML = `<b>PLZ:</b> ${plz}<br/><b>Kündigerquote:</b> ${orders}%`;
			<?php endif; ?>
		}

	});

	svg.addEventListener('mouseout', (e) => {
		let element = e.target;
		if (element.tagName != 'path') {return;}
		element.style.opacity = 1;

		tooltip.style.display = 'none';
	});

	svg.addEventListener('mousemove', function(event) {
		moveTooltip(event, tooltip);
	},false);

}

function moveTooltip(e,tooltip) {

	tooltip.style.left =
	  (e.pageX + tooltip.clientWidth + 15 < document.body.clientWidth)
	      ? (e.pageX + 15 + "px")
	      : (document.body.clientWidth + 10 - tooltip.clientWidth + "px");
	tooltip.style.top =
	  (e.pageY + tooltip.clientHeight + 15 < document.body.clientHeight)
	      ? (e.pageY + 15 + "px")
	      : (document.body.clientHeight + 10 - tooltip.clientHeight + "px");
}



function interactiveSVG2() {
	let counter = document.querySelector('.tooltip');
	let svgpaths = document.querySelectorAll('svg path');

	Array.from(svgpaths).forEach(path => {

		let count = path.getAttribute('data-orders');
		let plz = path.getAttribute('id');
		plz = plz.substr(3);

	    path.addEventListener('mouseover', (e) => {

			path.style.opacity = 0.6;

			if (count) {
				counter.innerHTML = `<b>PLZ:</b> ${plz} <b>Users:</b> ${count}`;
				path.addEventListener('mousemove', showTooltip);
			}
	    });

		path.addEventListener('mouseout', (e) => {
			path.style.opacity = 1;
			path.removeEventListener("mousemove", showTooltip);
			counter.style.display = 'none';
	    });

	});
}





</script>








</main>
