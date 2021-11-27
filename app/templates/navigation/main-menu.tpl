<header>

<!--
<div style="text-align:center; color:white; background-color:#3e6693; font-size:14px; "><small>Achtung: seit der Plenigo Umstellung werden die Google Analytics Conversions leider mehrfach getrackt. Ich hoffe auf baldige Lösung.</small></div>
-->

<nav class="main-nav">
	<ul>
		<li class="compare-logo-li"><a href="/"><span class="compare-logo"></span></a>
			<ul class="dropdown" aria-label="submenu">
				<li><a href="/favoriten">Favicon einrichten</a></li>
			</ul>
		</li>
		<li><a href="/list" aria-haspopup="true">Artikel-Listen</a>
			<ul class="dropdown" aria-label="submenu">
				<li><a href="/ressort">nach Ressorts</a></li>
				<li><a href="/type">nach Themen</a></li>
				<li><a href="/tag">nach #-Tags</a></li>
				<li><a href="/audience">nach Audiences</a></li>
				<li><a href="/discover">Google Discover Artikel</a></li>
			</ul>
		</li>

		<li><a href="/score" aria-haspopup="true">Toplisten / KPIs</a>
			<ul class="dropdown" aria-label="submenu">
				<li><a href="/pageviews">Pageviews</a></li>
				<li><a href="/subscribers">Subscribers</a></li>
				<li><a href="/mediatime">Mediatime</a></li>
				<li><a href="/conversions">Conversions</a></li>
				<li><a href="/score">Artikel-Score</a></li>
				<li><a href="/top5">Top5</a></li>
				<li><a href="/filter">Eigene Filter</a></li>
			</ul>
		</li>


		<li><a href="/valueable" aria-haspopup="true">Wertschöpfende Artikel</a>
			<ul class="dropdown" aria-label="submenu">
				<li><a href="/valueable/geister">Geister</a></li>
				<li><a href="/valueable/abwehr">Abwehr</a></li>
				<li><a href="/valueable/stuermer">Stürmer</a></li>
				<li><a href="/valueable/spielmacher">Spielmacher</a></li>
			</ul>
		</li>

		<li><a href="/stats/ressort" aria-haspopup="true">Inhalts-Statistiken</a>
			<ul class="dropdown" aria-label="submenu">
				<li><a href="/stats/ressort">nach Ressort</a></li>
				<li><a href="/stats/thema">nach Themen</a></li>
				<li><a href="/stats/tag">nach #-Tag</a></li>
				<li><a href="/stats/audience">nach Audiences</a></li>
				<li><a href="/stats/audience-by-ressort">Audiences nach Ressort</a></li>
				<li><a href="/stats/artikel">Publikationszeiten</a></li>
				<?php if (auth_rights('incentive')): ?>
				<li class="hightlight"><a href="/incentives">Halbjahresvergleich</a></li>
				<?php endif; ?>
			</ul>
		</li>


		<li><a href="/orders" aria-haspopup="true">Bestell- und Kündigerdaten</a>
			<ul class="dropdown" aria-label="submenu">
				<li><a href="/orders/list">Bestelldaten als Tabelle</a></li>
				<li><a href="/orders/map/local">Bestelldaten als Karte</a></li>
				<!--<li><a href="/readers/list">Bestellungen mit Usergroup (Beta)</a></li>-->
				<li><a href="/orders/cancellations">Kündigerverhalten</a></li>
				<!--<li><a href="/stats/compared">Kündigerverhalten (Longterm)</a></li>-->
				<li><a href="/orders/campaigns">UTM-Kampagnen</a></li>
				<li><a href="/orders/today">Echtzeit Bestelleingang</a></li>
			</ul>
		</li>

		<?php if (auth_rights('type, audience')): ?>
		<li><a href="/unclassified/types">Artikel zuordnen</a>
			<ul class="dropdown" aria-label="submenu">
				<li><a href="/unclassified/types">Unbestimmte Themen</a></li>
				<li><a href="/unclassified/audiences">Unbestimmte Audiences</a></li>
			</ul>
		</li>
		<?php endif; ?>

	</ul>

	<ul>
		<li>
			<form class="searchbox" method="get" action="/search">
				<input type="text" name="q" placeholder="Suchen..." value="<?=$query ?? ''?>">
				<button type="submit"></button>
			</form>
		</li>
		<?php if (auth_rights('type, audience')): ?>
		<li><a href="/admin" title="Einstellungen">Einstellungen</a>
			<ul class="dropdown rightmenu" aria-label="submenu">
				<li><a href="/admin/users">Nutzerverwaltung</a></li>
				<li><a href="/admin/cluster">Cluster-Manager</a></li>
				<li><a href="/admin/discover">Discover-Import</a></li>
				<li><a href="/admin/orders">Conversion-Import</a></li>
			</ul>

		</li>
		<?php endif; ?>
		<li class="login-icon">
			<a href="/profile" title="Nutzer">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 width="15px" height="18px" viewBox="0 0 15 18" enable-background="new 0 0 15 18" xml:space="preserve">
				<path id="loginHeadIcon" fill="#ffffff" d="M7.5,10.017c-2.772,0-5.018-2.242-5.018-5.009S4.728,0,7.5,0c2.772,0,5.017,2.241,5.017,5.008
					S10.272,10.017,7.5,10.017 M10.954,11.163c0,0-1.644,0.923-3.455,0.923c-1.812,0-3.453-0.923-3.454-0.923
					C4.042,11.161,0.043,12.288,0,16.389c0,0.82,0.665,1.485,1.485,1.485h12.029c0.819,0,1.485-0.665,1.485-1.485
					C14.956,12.283,10.958,11.166,10.954,11.163"/>
				</svg>
			</a>
		</li>
	</ul>


</nav>

<!--<a href="/" title="Max die Datenkrake lässt sich nicht gern kitzeln!"><img class="main-logo" src="/styles/img/datenkrake.svg"></a>-->

<?php if (isset($navigation)): ?>
<?php include tpl($navigation) ?>
<?php endif; ?>

</header>
