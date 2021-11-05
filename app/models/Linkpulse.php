<?php

namespace app\models;
use	\app\importer\LinkpulseImport;
use \flundr\cache\RequestCache;

class Linkpulse
{

	function __construct() {
		$this->api = new LinkpulseImport();
	}

	public function stats_today($id) {
		return $this->api->article_today($id);
	}

	public function subscribers($id, $pubDate) {
		return $this->api->subscribers($id, $pubDate);
	}

	public function today($client = null) {

		if ($client) {
			$this->api->client($client);
		}

		$liveData = $this->api->live();
		$liveData = $this->convert_to_chart_data($liveData);

		return $liveData;
	}

	public function articles_today() {
		return $this->api->articles_today();
	}


	public function ressort_stats_base() {

		$data = $this->ressort_stats('2021-01-01','2021-08-29');
		echo serialize($data);

	}

	public function ressort_stats_cache() {

		if (PORTAL == 'LR') {

			$data = 'a:15:{s:7:"cottbus";a:5:{s:9:"pageviews";i:5108756;s:11:"subscribers";i:413053;s:11:"conversions";i:712;s:9:"mediatime";i:3174621;s:12:"avgmediatime";d:53.60201235389104;}s:12:"finsterwalde";a:5:{s:9:"pageviews";i:2053581;s:11:"subscribers";i:149369;s:11:"conversions";i:252;s:9:"mediatime";i:1298215;s:12:"avgmediatime";d:50.67701440327801;}s:11:"senftenberg";a:5:{s:9:"pageviews";i:3251840;s:11:"subscribers";i:214481;s:11:"conversions";i:501;s:9:"mediatime";i:1961792;s:12:"avgmediatime";d:48.95427255675895;}s:11:"weisswasser";a:5:{s:9:"pageviews";i:2574692;s:11:"subscribers";i:111746;s:11:"conversions";i:199;s:9:"mediatime";i:1321235;s:12:"avgmediatime";d:46.285012645006645;}s:11:"elsterwerda";a:5:{s:9:"pageviews";i:2599748;s:11:"subscribers";i:150031;s:11:"conversions";i:233;s:9:"mediatime";i:1585487;s:12:"avgmediatime";d:47.954132885322906;}s:11:"hoyerswerda";a:5:{s:9:"pageviews";i:1775243;s:11:"subscribers";i:50455;s:11:"conversions";i:97;s:9:"mediatime";i:760424;s:12:"avgmediatime";d:50.67473754414823;}s:8:"herzberg";a:5:{s:9:"pageviews";i:1028336;s:11:"subscribers";i:78561;s:11:"conversions";i:95;s:9:"mediatime";i:709447;s:12:"avgmediatime";d:41.71637927991105;}s:5:"forst";a:5:{s:9:"pageviews";i:1265801;s:11:"subscribers";i:90712;s:11:"conversions";i:113;s:9:"mediatime";i:791044;s:12:"avgmediatime";d:45.317610852303915;}s:9:"spremberg";a:5:{s:9:"pageviews";i:1169546;s:11:"subscribers";i:88159;s:11:"conversions";i:98;s:9:"mediatime";i:726401;s:12:"avgmediatime";d:56.63711401395267;}s:9:"luebbenau";a:5:{s:9:"pageviews";i:1002331;s:11:"subscribers";i:56562;s:11:"conversions";i:103;s:9:"mediatime";i:556974;s:12:"avgmediatime";d:48.85555667441804;}s:5:"guben";a:5:{s:9:"pageviews";i:841385;s:11:"subscribers";i:45784;s:11:"conversions";i:63;s:9:"mediatime";i:478967;s:12:"avgmediatime";d:43.844559827877674;}s:7:"luebben";a:5:{s:9:"pageviews";i:1064624;s:11:"subscribers";i:61668;s:11:"conversions";i:143;s:9:"mediatime";i:575037;s:12:"avgmediatime";d:52.73066759377252;}s:15:"energie-cottbus";a:5:{s:9:"pageviews";i:1490492;s:11:"subscribers";i:261166;s:11:"conversions";i:348;s:9:"mediatime";i:879527;s:12:"avgmediatime";d:52.29283231240697;}s:6:"luckau";a:5:{s:9:"pageviews";i:379969;s:11:"subscribers";i:26123;s:11:"conversions";i:54;s:9:"mediatime";i:258866;s:12:"avgmediatime";d:47.047172277234495;}s:5:"sport";a:5:{s:9:"pageviews";i:177608;s:11:"subscribers";i:15761;s:11:"conversions";i:83;s:9:"mediatime";i:112364;s:12:"avgmediatime";d:56.03633546613855;}}';

		}

		if (PORTAL == 'MOZ') {

			$data = 'a:25:{s:10:"eberswalde";a:5:{s:9:"pageviews";i:4015463;s:11:"subscribers";i:190977;s:11:"conversions";i:477;s:9:"mediatime";i:2120012;s:12:"avgmediatime";d:44.467082232935354;}s:11:"brandenburg";a:5:{s:9:"pageviews";i:5006674;s:11:"subscribers";i:179349;s:11:"conversions";i:555;s:9:"mediatime";i:2714113;s:12:"avgmediatime";d:56.94638843613211;}s:9:"neuruppin";a:5:{s:9:"pageviews";i:2744416;s:11:"subscribers";i:77177;s:11:"conversions";i:281;s:9:"mediatime";i:1330476;s:12:"avgmediatime";d:44.603225624363404;}s:17:"eisenhuettenstadt";a:5:{s:9:"pageviews";i:3185051;s:11:"subscribers";i:174007;s:11:"conversions";i:349;s:9:"mediatime";i:1437048;s:12:"avgmediatime";d:46.58986392442603;}s:14:"frankfurt-oder";a:5:{s:9:"pageviews";i:3866628;s:11:"subscribers";i:198219;s:11:"conversions";i:334;s:9:"mediatime";i:2037847;s:12:"avgmediatime";d:48.408780966710765;}s:11:"oranienburg";a:5:{s:9:"pageviews";i:3035369;s:11:"subscribers";i:129255;s:11:"conversions";i:493;s:9:"mediatime";i:1603334;s:12:"avgmediatime";d:47.45648891822202;}s:13:"fuerstenwalde";a:5:{s:9:"pageviews";i:2302301;s:11:"subscribers";i:189307;s:11:"conversions";i:427;s:9:"mediatime";i:1215569;s:12:"avgmediatime";d:47.508043700654525;}s:7:"schwedt";a:5:{s:9:"pageviews";i:3551759;s:11:"subscribers";i:166329;s:11:"conversions";i:327;s:9:"mediatime";i:1890745;s:12:"avgmediatime";d:45.55812814377714;}s:10:"strausberg";a:5:{s:9:"pageviews";i:2132440;s:11:"subscribers";i:123699;s:11:"conversions";i:462;s:9:"mediatime";i:1183466;s:12:"avgmediatime";d:50.880353253509384;}s:7:"beeskow";a:5:{s:9:"pageviews";i:1502564;s:11:"subscribers";i:107826;s:11:"conversions";i:201;s:9:"mediatime";i:894514;s:12:"avgmediatime";d:44.24364575080108;}s:6:"bernau";a:5:{s:9:"pageviews";i:2218432;s:11:"subscribers";i:126809;s:11:"conversions";i:480;s:9:"mediatime";i:1198718;s:12:"avgmediatime";d:47.89336452470161;}s:7:"gransee";a:5:{s:9:"pageviews";i:768500;s:11:"subscribers";i:29703;s:11:"conversions";i:142;s:9:"mediatime";i:501985;s:12:"avgmediatime";d:46.62574545782991;}s:6:"seelow";a:5:{s:9:"pageviews";i:953559;s:11:"subscribers";i:62071;s:11:"conversions";i:142;s:9:"mediatime";i:718871;s:12:"avgmediatime";d:47.303569456911646;}s:15:"bad-freienwalde";a:5:{s:9:"pageviews";i:876543;s:11:"subscribers";i:52209;s:11:"conversions";i:115;s:9:"mediatime";i:577451;s:12:"avgmediatime";d:47.10259664669866;}s:11:"angermuende";a:5:{s:9:"pageviews";i:929535;s:11:"subscribers";i:60973;s:11:"conversions";i:115;s:9:"mediatime";i:564483;s:12:"avgmediatime";d:44.52294841688126;}s:6:"erkner";a:5:{s:9:"pageviews";i:993616;s:11:"subscribers";i:77025;s:11:"conversions";i:274;s:9:"mediatime";i:585372;s:12:"avgmediatime";d:50.15775310737081;}s:11:"hennigsdorf";a:5:{s:9:"pageviews";i:570104;s:11:"subscribers";i:31129;s:11:"conversions";i:125;s:9:"mediatime";i:337906;s:12:"avgmediatime";d:46.2024618810392;}s:17:"brandenburg-havel";a:5:{s:9:"pageviews";i:589655;s:11:"subscribers";i:11212;s:11:"conversions";i:9;s:9:"mediatime";i:207829;s:12:"avgmediatime";d:55.76373865571804;}s:10:"bad-belzig";a:5:{s:9:"pageviews";i:378981;s:11:"subscribers";i:8100;s:11:"conversions";i:9;s:9:"mediatime";i:144462;s:12:"avgmediatime";d:58.85898468509549;}s:9:"falkensee";a:5:{s:9:"pageviews";i:407281;s:11:"subscribers";i:8442;s:11:"conversions";i:11;s:9:"mediatime";i:178456;s:12:"avgmediatime";d:64.26431293477071;}s:8:"rathenow";a:5:{s:9:"pageviews";i:698771;s:11:"subscribers";i:8898;s:11:"conversions";i:4;s:9:"mediatime";i:226622;s:12:"avgmediatime";d:61.10160330403596;}s:6:"berlin";a:5:{s:9:"pageviews";i:377431;s:11:"subscribers";i:29296;s:11:"conversions";i:20;s:9:"mediatime";i:264977;s:12:"avgmediatime";d:58.72877117351163;}s:10:"wirtschaft";a:5:{s:9:"pageviews";i:271256;s:11:"subscribers";i:18958;s:11:"conversions";i:40;s:9:"mediatime";i:194169;s:12:"avgmediatime";d:64.90878687374061;}s:6:"kultur";a:5:{s:9:"pageviews";i:221188;s:11:"subscribers";i:8766;s:11:"conversions";i:46;s:9:"mediatime";i:131370;s:12:"avgmediatime";d:64.14045194134815;}s:5:"sport";a:5:{s:9:"pageviews";i:238132;s:11:"subscribers";i:19522;s:11:"conversions";i:158;s:9:"mediatime";i:164350;s:12:"avgmediatime";d:50.03636086179176;}}';

		}

		return unserialize($data);

	}


	public function ressort_stats($from,$to) {

		$cache = new RequestCache($from . $to . PORTAL, 60*60);
		$cachedData = $cache->get();
		if ($cachedData) {return $cachedData;}

		$dateRanges = $this->split_date_range($from,$to);

		$completeStats = [];
		foreach ($dateRanges as $range) {
			$allStats = $this->api->ressort_stats($range['from'], $range['to']);

			if (!is_array($allStats)) {
				echo 'Achtung: ' . $range['from'] . ' - ' .  $range['to'] . ' Importfehler!';
				$cache->flush();
				continue;
			}

			$stats = array_filter($allStats, [$this, 'filter_active_ressorts']);
			$stats = array_values($stats);
			array_push($completeStats, $stats);
		}

		$completeStats = $this->sum_splitted_ressort_stats($completeStats);

		$cache->save($completeStats);

		return $completeStats;

	}

	private function sum_splitted_ressort_stats($periods) {

		$ressortSets = [];

		foreach ($periods as $index => $period) {

			foreach ($period as $set) {
				$ressortSets[$index][$set['ressort']] = [
					'pageviews' => $set['pageviews'],
					'subscribers' => $set['subscribers'],
					'conversions' => $set['conversions'],
					'mediatime' => $set['mediatime'],
					'avgmediatime' => $set['avgmediatime'],
				];
			}

		}

		$out = [];
		foreach ($ressortSets as $set) {

			$keys = array_keys($set);

			foreach ($keys as $ressort) {

				if (!isset($out[$ressort])) {
					$out[$ressort] = $set[$ressort];
				}
				else {
					$out[$ressort]['pageviews'] = $out[$ressort]['pageviews'] + $set[$ressort]['pageviews'];
					$out[$ressort]['subscribers'] = $out[$ressort]['subscribers'] + $set[$ressort]['subscribers'];
					$out[$ressort]['conversions'] = $out[$ressort]['conversions'] + $set[$ressort]['conversions'];
					$out[$ressort]['mediatime'] = $out[$ressort]['mediatime'] + $set[$ressort]['mediatime'];
					$out[$ressort]['avgmediatime'] = ($out[$ressort]['avgmediatime'] + $set[$ressort]['avgmediatime']) / 2;
				}

			}

		}

		return $out;

	}


	private function filter_active_ressorts($array) {

		if (PORTAL == 'SWP') {
			return $array;
		}

		if (PORTAL == 'LR') {
			$activeRessorts = [
				'cottbus',
				'senftenberg',
				'elsterwerda',
				//'brandenburg',
				'weisswasser',
				'finsterwalde',
				'energie-cottbus',
				'hoyerswerda',
				'luebbenau',
				'forst',
				'spremberg',
				'guben',
				'herzberg',
				'luebben',
				'luckau',
				'sport',
			];
		}

		if (PORTAL == 'MOZ') {
			$activeRessorts = [
				'brandenburg',
				'oranienburg',
				'bernau',
				'eberswalde',
				'strausberg',
				'fuerstenwalde',
				'eisenhuettenstadt',
				'frankfurt-oder',
				'schwedt',
				'neuruppin',
				'erkner',
				'beeskow',
				'sport',
				'seelow',
				'gransee',
				'hennigsdorf',
				'bad-freienwalde',
				'angermuende',
				'kultur',
				'wirtschaft',
				'berlin',
				'falkensee',
				'bad-belzig',
				'brandenburg-havel',
				'rathenow',
			];
		}

		if (in_array($array['ressort'], $activeRessorts)) {
			return $array;
		}

	}


	private function split_date_range($from, $to, $interval = 7) {

		$period = new \DatePeriod(
			new \DateTime($from),
			new \DateInterval('P1D'),
			new \DateTime($to)
		);

		$splittedDates = [];
		foreach ($period as $counter => $date) {
			if ($counter % $interval == 0) {
				array_push($splittedDates, $date->format('Y-m-d'));
			}
		};

		$helperDate = null;
		$pairs = [];

		// No splitting needed if period is smaller than interval
		if (count($splittedDates) == 1) {
			return [0 => ['from' => $from, 'to' => $to]];
		}

		foreach ($splittedDates as $counter => $nextDate) {

			$nextDatesDay = new \DateTime($nextDate);
			$nextDatesDay->modify('+1 day');
			$nextDatesDay = $nextDatesDay->format('Y-m-d');

			if (is_null($helperDate)) {
				$helperDate = $nextDate;
				continue;
			}
			$pairs[$counter]['from'] = $helperDate;
			$pairs[$counter]['to'] = $nextDate;

			// Add the Rest of the days to the preiod
			if ($helperDate != $to) {
				$pairs[$counter+1]['from'] = $nextDatesDay;
				$pairs[$counter+1]['to'] = $to;
			}

			$helperDate = $nextDatesDay;
		}

		return array_values($pairs);

	}

	private function convert_to_chart_data($data) {

		$pageviews = null;
		$values = null;
		$time = null;
		$counter = 0;

		foreach ($data as $moment) {

			$pageviews += $moment['attributes']['pageviews'];

			$counter++;
			if ($counter % 3 != 0) {continue;}

			$values .= $moment['attributes']['pageviews'] . ',';
			$timestring = date('H:i', strtotime($moment['id']));
			$time .= "'" . $timestring."'" . ',';

		}

		return [
			'values' => $values,
			'timestamps' => $time,
			'pageviews' => $pageviews,
		];

	}

}
