<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Stats extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Conversions,Orders,Plenigo,DailyKPIs,Linkpulse,Charts,ArticlesMeta,Analytics,Longterm,Portals');
	}

	public function dashboard() {

		Session::set('referer', '/');

		$viewData['articles'] = $this->Articles->count('*');
		$viewData['subscriberviews'] = $this->DailyKPIs->sum('subscriberviews') ?? 0;
		$viewData['avgmediatime'] = $this->Articles->average('avgmediatime') ?? 0;
		$viewData['pageviews'] = $this->DailyKPIs->sum('pageviews') ?? 0;

		// Mediatime
		$viewData['mediatime'] = $this->Articles->sum('mediatime');
		$viewData['mtDays'] = floor($viewData['mediatime'] / 60 / 60 / 24);
		$viewData['mtHours'] = date('G', mktime($viewData['mediatime'] / 60 / 60));
		$viewData['mtMinutes'] = date('i', mktime(0,$viewData['mediatime'] / 60));
		$viewData['mtSeconds'] = date('s', mktime(0,0,$viewData['mediatime']));

		$viewData['orders'] = $this->Orders->list();
		$viewData['numberOfOrders'] = count($viewData['orders'] ?? []);
		$viewData['numberOfCancelled'] = count($this->Orders->filter_cancelled($viewData['orders']));

		if ($viewData['orders']) {
			$viewData['cancelQuote'] = round(($viewData['numberOfCancelled'] / $viewData['numberOfOrders']) * 100, 1);
		} else {$viewData['cancelQuote'] = null;}

		$viewData['plusOnly'] = count($this->Orders->filter_plus_only($viewData['orders']));
		$viewData['aboshopOnly'] = count($this->Orders->filter_aboshop($viewData['orders']));
		$viewData['externalOnly'] = count($this->Orders->filter_external($viewData['orders']));

		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['orders']),'retention');

		$premiumUsers = $this->DailyKPIs->premium_users();
		$this->view->premiumAvg = null;
		$this->view->usersAvg = null;
		$this->view->premiumUsers = null;

		if ($premiumUsers) {
			$this->view->premiumUsers = $this->Charts->convert($premiumUsers,1);

			$registeredUsers = array_column($premiumUsers,'subscribers');
			$allUsers = array_column($premiumUsers,'users');
			$this->view->usersAvg = round(array_sum($allUsers)/count($allUsers));
			$this->view->premiumAvg = round(array_sum($registeredUsers)/count($registeredUsers));
		}

		// Charts
		$viewData['charts'] = $this->Charts;
		$this->view->title = 'Compare - Dashboard';
		$this->view->render('pages/dashboard', $viewData);

	}

	public function ressorts() {

		$viewData['articles'] = $this->Articles->count('*', 'ressort');
		$viewData['plusarticles'] = $this->Articles->sum('plus', 'ressort');
		$viewData['pageviews'] = $this->Articles->sum('pageviews', 'ressort');
		$viewData['subscriberviews'] = $this->Articles->sum('subscriberviews', 'ressort');
		$viewData['mediatime'] = $this->Articles->sum('mediatime', 'ressort');
		$viewData['sessions'] = $this->Articles->sum('sessions', 'ressort');
		$viewData['conversions'] = $this->Articles->sum('conversions', 'ressort');
		$viewData['buyintents'] = $this->Articles->sum('buyintent', 'ressort');
		$viewData['cancelled'] = $this->Articles->sum('cancelled', 'ressort');

		$viewData['groupedStats'] = $this->Articles->stats_grouped_by($column = 'ressort', $order = 'conversions DESC, ressort ASC');
		$viewData['chartOne'] = $this->Charts->get('avg_subscriberviews_by', ['ressort', 'DESC']);
		$viewData['chartOneTitle'] = 'Durchschnittliche Subscriber nach Ressort';
		$viewData['chartTwo'] = $this->Charts->get('avg_pageviews_by', ['ressort', 'DESC']);
		$viewData['chartTwoTitle'] = 'Durchschnittliche Pageviews nach Ressort';

		Session::set('referer', '/stats/ressort');
		$this->view->title = 'Statistiken nach Ressort';
		$this->view->class = 'Ressort';
		$this->view->urlPrefix = '/ressort/';
		$this->view->render('stats/content-stats', $viewData);

	}

	public function themen() {

		$viewData['articles'] = $this->Articles->count('*', 'type');
		$viewData['plusarticles'] = $this->Articles->sum('plus', 'type');
		$viewData['pageviews'] = $this->Articles->sum('pageviews', 'type');
		$viewData['subscriberviews'] = $this->Articles->sum('subscriberviews', 'type');
		$viewData['mediatime'] = $this->Articles->sum('mediatime', 'type');
		$viewData['sessions'] = $this->Articles->sum('sessions', 'type');
		$viewData['conversions'] = $this->Articles->sum('conversions', 'type');
		$viewData['buyintents'] = $this->Articles->sum('buyintent', 'type');
		$viewData['cancelled'] = $this->Articles->sum('cancelled', 'type');

		$viewData['groupedStats'] = $this->Articles->stats_grouped_by($column = 'type', $order = 'conversions DESC');

		$viewData['chartOne'] = $this->Charts->get('avg_subscriberviews_by', ['type', 'DESC']);
		$viewData['chartOneTitle'] = 'Durchschnittliche Subscriber nach Inhaltstyp';
		$viewData['chartTwo'] = $this->Charts->get('avg_pageviews_by', ['type', 'DESC']);
		$viewData['chartTwoTitle'] = 'Durchschnittliche Pageviews nach Inhaltstyp';

		Session::set('referer', '/stats/thema');
		$this->view->title = 'Statistiken nach Inhaltstyp / Thema';
		$this->view->class = 'Thema';
		$this->view->urlPrefix = '/type/';
		$this->view->render('stats/content-stats', $viewData);

	}

	public function audiences() {

		$viewData['articles'] = $this->Articles->count('*', 'audience');
		$viewData['plusarticles'] = $this->Articles->sum('plus', 'audience');
		$viewData['pageviews'] = $this->Articles->sum('pageviews', 'audience');
		$viewData['subscriberviews'] = $this->Articles->sum('subscriberviews', 'audience');
		$viewData['mediatime'] = $this->Articles->sum('mediatime', 'audience');
		$viewData['sessions'] = $this->Articles->sum('sessions', 'audience');
		$viewData['conversions'] = $this->Articles->sum('conversions', 'audience');
		$viewData['buyintents'] = $this->Articles->sum('buyintent', 'audience');
		$viewData['cancelled'] = $this->Articles->sum('cancelled', 'audience');

		$viewData['groupedStats'] = $this->Articles->stats_grouped_by($column = 'audience', $order = 'conversions DESC');

		$viewData['chartOne'] = $this->Charts->get('avg_subscriberviews_by', ['audience', 'DESC']);
		$viewData['chartOneTitle'] = 'Durchschnittliche Subscriber nach Audience';
		$viewData['chartTwo'] = $this->Charts->get('avg_pageviews_by', ['audience', 'DESC']);
		$viewData['chartTwoTitle'] = 'Durchschnittliche Pageviews nach Audience';

		Session::set('referer', '/stats/audience');
		$this->view->title = 'Statistiken nach Audiences';
		$this->view->class = 'Audience';
		$this->view->urlPrefix = '/audience/';
		$this->view->render('stats/content-stats', $viewData);

	}

	public function userneeds() {

		$viewData['articles'] = $this->Articles->count('*', 'userneed');
		$viewData['plusarticles'] = $this->Articles->sum('plus', 'userneed');
		$viewData['pageviews'] = $this->Articles->sum('pageviews', 'userneed');
		$viewData['subscriberviews'] = $this->Articles->sum('subscriberviews', 'userneed');
		$viewData['mediatime'] = $this->Articles->sum('mediatime', 'userneed');
		$viewData['sessions'] = $this->Articles->sum('sessions', 'userneed');
		$viewData['conversions'] = $this->Articles->sum('conversions', 'userneed');
		$viewData['buyintents'] = $this->Articles->sum('buyintent', 'userneed');
		$viewData['cancelled'] = $this->Articles->sum('cancelled', 'userneed');

		$viewData['groupedStats'] = $this->Articles->stats_grouped_by($column = 'userneed', $order = 'conversions DESC');

		$viewData['chartOne'] = $this->Charts->get('avg_subscriberviews_by', ['userneed', 'DESC']);
		$viewData['chartOneTitle'] = 'Durchschnittliche Subscriber nach Bedürfniskategorie';
		$viewData['chartTwo'] = $this->Charts->get('avg_pageviews_by', ['userneed', 'DESC']);
		$viewData['chartTwoTitle'] = 'Durchschnittliche Pageviews nach Bedürfniskategorie';

		Session::set('referer', '/stats/userneed');
		$this->view->title = 'Statistiken nach Bedürfniskategorie';
		$this->view->class = 'Bendürfniskategorie';
		$this->view->urlPrefix = '/userneed/';
		$this->view->render('stats/content-stats', $viewData);

	}

	public function tags() {

		$viewData['articles'] = $this->Articles->count('*', 'tag');
		$viewData['plusarticles'] = $this->Articles->sum('plus', 'tag');
		$viewData['pageviews'] = $this->Articles->sum('pageviews', 'tag');
		$viewData['subscriberviews'] = $this->Articles->sum('subscriberviews', 'tag');
		$viewData['mediatime'] = $this->Articles->sum('mediatime', 'tag');
		$viewData['sessions'] = $this->Articles->sum('sessions', 'tag');
		$viewData['conversions'] = $this->Articles->sum('conversions', 'tag');
		$viewData['buyintents'] = $this->Articles->sum('buyintent', 'tag');
		$viewData['cancelled'] = $this->Articles->sum('cancelled', 'tag');

		$viewData['groupedStats'] = $this->Articles->stats_grouped_by($column = 'tag', $order = 'conversions DESC');
		$viewData['chartOne'] = $this->Charts->get('avg_subscriberviews_by', ['tag', 'DESC']);
		$viewData['chartOneTitle'] = 'Durchschnittliche Subscriber nach #Tag';
		$viewData['chartTwo'] = $this->Charts->get('avg_pageviews_by', ['tag', 'DESC']);
		$viewData['chartTwoTitle'] = 'Durchschnittliche Pageviews nach #Tag';

		Session::set('referer', '/stats/tag');
		$this->view->title = 'Statistiken nach #-Tag';
		$this->view->class = 'Tag';
		$this->view->urlPrefix = '/tag/';
		$this->view->render('stats/content-stats', $viewData);

	}

	public function value_articles($groupedBy = 'ressort') {

		Session::set('referer', '/valueable');

		$this->view->wertschoepfend = $this->Articles->value_articles($groupedBy) ?? [];

		$artikel = array_column($this->view->wertschoepfend,'artikel');
		$spielmacher = array_column($this->view->wertschoepfend,'spielmacher');
		$stuermer = array_column($this->view->wertschoepfend,'stuermer');
		$abwehr = array_column($this->view->wertschoepfend,'abwehr');
		$geister = array_column($this->view->wertschoepfend,'geister');

		$this->view->artikelCount = array_sum($artikel);
		$this->view->spielmacherCount = array_sum($spielmacher);
		$this->view->stuermerCount = array_sum($stuermer);
		$this->view->abwehrCount = array_sum($abwehr);
		$this->view->geisterCount = array_sum($geister);

		$this->view->artikel = $artikel;
		$this->view->spielmacher = $spielmacher;
		$this->view->stuermer = $stuermer;
		$this->view->abwehr = $abwehr;
		$this->view->geister = $geister;

		$this->view->quoteKeepers = percentage(($this->view->spielmacherCount + $this->view->abwehrCount), $this->view->artikelCount,1);
		$this->view->quoteNew = percentage(($this->view->spielmacherCount + $this->view->stuermerCount), $this->view->artikelCount,1);

		$this->view->group = $groupedBy;
		$this->view->title = 'Wertschöpfende Artikel';
		$this->view->render('articles/wertschoepfend');

	}

	public function value_articles_audience() {
		return $this->value_articles('audience');
	}

	public function value_articles_thema() {
		return $this->value_articles('type');
	}

	public function audience_by_ressorts() {

		Session::set('referer', '/stats/audience-by-ressort');
		$this->view->days = $this->Charts->timeframe();


		$filteredRessorts = [];
		if (PORTAL == 'LR') {$filteredRessorts = ['bilder','ratgeber','blaulicht','unbekannt','leser_service'];}
		if (PORTAL == 'MOZ') {
			$filteredRessorts = [
				'nachrichten','politik','bilder','panorama','themen','wissen','anzeigen','lokales','unbekannt',
				'bad-belzig', 'brandenburg-havel', 'rathenow', 'falkensee', 'berlin', 'wirtschaft', 'brandenburg',
				'seelow', 'bad-freienwalde'
			];
		}

		$ressortList = $this->Articles->list_distinct('ressort');
		$audiencesByRessort = $this->Articles->audiences_by_ressort();

		$summedRessorts = $this->Articles->kpi_grouped_by('audience','ressort','count');
		$summedAudiences = $this->Articles->kpi_grouped_by('ressort','audience','count');

		if (PORTAL == 'MOZ') {
			// Combine and Remove Hack
			$audiencesByRessort = array_map(function ($set) {

				if (isset($set['seelow']) || isset($set['bad-freienwalde']) ) {
					$set['seelow+bad-freienwalde'] = $set['seelow'] ?? 0 + $set['bad-freienwalde'] ?? 0;
					unset($set['seelow']);
					unset($set['bad-freienwalde']);
				}

				return $set;

			}, $audiencesByRessort);

			$audiencesByRessort = array_map(function ($set) use ($seeba){

				if (isset($set['brandenburg']) || isset($set['wirtschaft']) || isset($set['berlin']) ) {
					$set['brandenburg-kombiniert'] = $set['brandenburg'] ?? 0 + $set['wirtschaft'] ?? 0 + $set['berlin'] ?? 0;
					unset($set['brandenburg']);
					unset($set['wirtschaft']);
					unset($set['berlin']);
				}

				return $set;

			}, $audiencesByRessort);

			$seeba = 0;
			foreach ($audiencesByRessort as $ressorts) {
				$seeba = $seeba + $ressorts['seelow+bad-freienwalde'] ?? 0;
			}

			$brbko = 0;
			foreach ($audiencesByRessort as $ressorts) {
				$brbko = $brbko + $ressorts['brandenburg-kombiniert'] ?? 0;
			}

			array_push($ressortList, 'seelow+bad-freienwalde');
			array_push($ressortList, 'brandenburg-kombiniert');

			array_push($summedRessorts, ['ressort' => 'seelow+bad-freienwalde', 'audience' => $seeba]);
			array_push($summedRessorts, ['ressort' => 'brandenburg-kombiniert', 'audience' => $brbko]);
		}


		$ressortList = array_filter($ressortList, function($ressort) use ($filteredRessorts) {
			if (in_array($ressort, $filteredRessorts)) {return null;}
			return $ressort;
		});

		$summedRessorts = array_filter($summedRessorts, function($ressort) use ($filteredRessorts){
			if (in_array($ressort['ressort'], $filteredRessorts)) {return null;}
			return $ressort;
		});

		$audienceList = array_column($summedAudiences, 'audience');
		$audienceValues = array_column($summedAudiences, 'ressort');
		$summedAudiences = array_combine($audienceList, $audienceValues);

		$this->view->ressortList = $ressortList;
		$this->view->audiencesByRessort = $audiencesByRessort;
		$this->view->summedAudiences = $summedAudiences;
		$this->view->summedRessorts = $summedRessorts;

		$this->view->title = 'Audience Artikel nach Ressort';
		$this->view->info = 'Hinweis: Die Summe der produzierten Audience Artikel (letzte Spalte) beinhaltet Artikel über alle Ressorts (auch ausgeblendete wie z.B. Politik).';
		$this->view->render('pages/audiences-by-ressort');

	}

	public function artikel() {
		Session::set('referer', '/stats/artikel');
		$viewData['charts'] = $this->Charts;
		$this->view->title = 'Artikel Produktions Statistiken';
		$this->view->render('articles/artikel-entwicklung', $viewData);
	}

	public function cluster_audiences() {
		$this->view->cluster = $this->Articles->cluster_by_ressort('audience');
		$this->view->title = 'Audience-Cluster nach Ressort';
		$this->view->render('stats/cluster-stats');
	}

	public function cluster_types() {
		$this->view->cluster = $this->Articles->cluster_by_ressort('type');
		$this->view->title = 'Themen-Cluster nach Ressort';
		$this->view->render('stats/cluster-stats');
	}

	public function cluster_tags() {
		$this->view->cluster = $this->Articles->cluster_by_ressort('tag');
		$this->view->title = 'Tag-Cluster nach Ressort';
		$this->view->render('stats/cluster-stats');
	}

	public function publications($audience = ARTICLE_AUDIENCES[0]) {

		$this->view->audienceList = ARTICLE_AUDIENCES;

		$articles = $this->Articles->audience_by_time($audience);
		$orders = $this->Orders->audience_by_time($audience);
		$sessions = $this->Analytics->use_timeframe_by_audience(ucfirst($audience));

		$data = [];
		foreach (range(0,23) as $key => $void) {
			$data[$key]['articles'] = $articles[$key]['articles'] ?? 0;
			$data[$key]['orders'] = $orders[$key]['orders'] ?? 0;
			$data[$key]['sessions'] = $sessions[$key]['Sessions'] ?? 0;
		}

		$this->view->audience = ucFirst($audience);
		$this->view->chart = $this->Charts->convert($data);
		$this->view->charts = $this->Charts;

		$this->view->title = 'Zeitliche Interessensverteilung: ' . ucFirst($audience);
		$this->view->render('pages/audiences-by-time');

	}

	public function avg_mediatime() {

		$this->DailyKPIs->switch_db(LR_DB_SETTINGS);

		$data = $this->DailyKPIs->mediatime();
		$chartDataRaw = array_column($data,'avgmediatime','date');
		$out = [];

		foreach ($data as $set) {
			$out[$set['date']]['mediatime'] = round($set['avgmediatime'],2);
			$out[$set['date']]['buddy_mediatime'] = 0;
			$out[$set['date']]['buddy2_mediatime'] = 0;
		}

		$this->DailyKPIs->switch_db(MOZ_DB_SETTINGS);
		$data = $this->DailyKPIs->mediatime();
		$chartDataRaw = array_column($data,'avgmediatime','date');
		foreach ($data as $set) {
			if (isset($set['date'])) {
				$out[$set['date']]['buddy_mediatime'] = round($set['avgmediatime'] ?? 0,2);
			}
			else {
				$out[$set['date']]['buddy_mediatime'] = 0;
			}
		}

		$this->DailyKPIs->switch_db(SWP_DB_SETTINGS);
		$data = $this->DailyKPIs->mediatime();
		$chartDataRaw = array_column($data,'avgmediatime','date');
		foreach ($data as $set) {
			$out[$set['date']]['buddy2_mediatime'] = round($set['avgmediatime'],2);
		}

		$this->view->charts = $this->Charts;
		$this->view->chart = $this->Charts->convert($out);

		$this->view->title = 'AVG Mediatime laut GA';
		$this->view->render('stats/avg-mediatime');

	}


	public function segments() {
		Session::set('referer', '/stats/segments');

		$segments = $this->DailyKPIs->segments();
		$premiumUsers = $this->DailyKPIs->premium_users();
		$premiumUsersQuote = $this->DailyKPIs->quote_of_premium_users();

		$this->view->maxChartHeight = 400;
		if ($premiumUsersQuote) {
			$this->view->maxChartHeight = ceil(max(array_column($premiumUsersQuote,'reg_quote')));
		}

		$this->view->charts = $this->Charts;
		$this->view->segments = $this->Charts->convert($segments,1);
		$this->view->premiumUsers = $this->Charts->convert($premiumUsers,1);
		$this->view->premiumUsersQuote = $this->Charts->convert($premiumUsersQuote,1);

		$this->view->title = 'Abonennten Entwicklung';
		$this->view->render('stats/segments');

	}


	public function weekly_review() {

		$data = $this->Portals->weekly();
		$this->view->title = 'Weekly-Review der Mediengruppe Brandenburg';
		$this->view->templates['footer'] = null;
		$this->view->render('stats/weekly-review', $data);

	}


	public function avg_orders_by_day() {

		$premiumUsers = $this->DailyKPIs->premium_users();
		$this->view->premiumAvg = null;
		$this->view->usersAvg = null;
		$this->view->premiumUsers = null;

		if ($premiumUsers) {
			$this->view->premiumUsers = $this->Charts->convert($premiumUsers,1);

			$registeredUsers = array_column($premiumUsers,'subscribers');
			$allUsers = array_column($premiumUsers,'users');
			$this->view->usersAvg = round(array_sum($allUsers)/count($allUsers));
			$this->view->premiumAvg = round(array_sum($registeredUsers)/count($registeredUsers));
		}


		dd($this->view->usersAvg);

	}



}
