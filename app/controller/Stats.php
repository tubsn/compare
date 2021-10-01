<?php

namespace app\controller;
use flundr\mvc\Controller;
use flundr\utility\Session;
use flundr\auth\Auth;

class Stats extends Controller {

	public function __construct() {

		if (!Auth::logged_in() && !Auth::valid_ip()) {Auth::loginpage();}

		$this->view('DefaultLayout');
		$this->models('Articles,Conversions,Orders,Plenigo,GlobalKPIs,Linkpulse,Charts,ArticlesMeta');
	}

	public function index() {

		Session::set('referer', '/stats');

		$viewData['articles'] = $this->Articles->count('*');
		$viewData['plusarticles'] = $this->Articles->sum('plus');
		$viewData['pageviews'] = $this->Articles->sum('pageviews');
		$viewData['subscribers'] = $this->Articles->sum('subscribers');
		$viewData['mediatime'] = $this->Articles->sum('mediatime');
		$viewData['sessions'] = $this->Articles->sum('sessions');
		$viewData['conversions'] = $this->Articles->sum('conversions');
		$viewData['buyintents'] = $this->Articles->sum('buyintent');
		$viewData['cancelled'] = $this->Articles->sum('cancelled');

		$viewData['ressortStats'] = $this->Articles->stats_grouped_by($column = 'ressort', $order = 'conversions DESC, ressort ASC');
		$viewData['authorStats'] = $this->Articles->stats_grouped_by($column = 'author', $order = 'author ASC');
		$viewData['typeStats'] = $this->Articles->stats_grouped_by($column = 'type', $order = 'conversions DESC');
		$viewData['tagStats'] = $this->Articles->stats_grouped_by($column = 'tag', $order = 'conversions DESC');

		$viewData['charts'] = $this->Charts;

		Session::set('referer', '/stats');
		$this->view->title = 'Inhaltsbasierte Statistiken';
		$this->view->render('pages/stats', $viewData);
	}


	public function cancellations() {

		/* Cancellations are Displayed in the Orders Controller now */

		$viewData['conversions'] = $this->Conversions->list();
		$viewData['cancelled'] = $this->Conversions->cancelled_orders();
		$viewData['types'] = $this->Conversions->group_by_combined('article_type');
		$viewData['tags'] = $this->Conversions->group_by_combined('article_tag');
		$viewData['ressorts'] = $this->Conversions->group_by_combined('article_ressort');
		$viewData['cities'] = $this->Conversions->group_by_combined('ga_city');
		$viewData['sources'] = $this->Conversions->group_by_combined('ga_source');
		$viewData['payments'] = $this->Conversions->group_by_combined('subscription_payment_method');
		$viewData['gender'] = $this->Conversions->group_by_combined('customer_gender');
		$viewData['authors'] = $this->Conversions->group_by_combined('article_author');

		if (count($viewData['conversions']) > 0) {
			$viewData['quote'] = count($viewData['cancelled']) / count($viewData['conversions']) * 100;
		} else {$viewData['quote'] = 0;}

		$this->view->title = 'Kündiger Statistiken';
		$this->view->render('pages/cancellation-stats', $viewData);

	}


	public function dashboard() {

		Session::set('referer', '/');

		$viewData['articles'] = $this->Articles->count('*');
		$viewData['subscribers'] = $this->Articles->sum('subscribers');
		$viewData['avgmediatime'] = $this->Articles->average('avgmediatime');
		//$viewData['avgmediatime'] = $this->GlobalKPIs->avg('avgmediatime');
		$viewData['pageviews'] = $this->GlobalKPIs->sum('pageviews');

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
		$viewData['externalOnly'] = count($this->Orders->filter_external($viewData['orders']));
		$viewData['averageRetention'] = $this->Orders->average($this->Orders->filter_cancelled($viewData['orders']),'retention');

		// Charts

		$viewData['charts'] = $this->Charts;

		//$viewData['combinedChart'][0] = $this->Articles->mediatime_by_ressort_chart();
		//$viewData['combinedChart'][1] = $this->Articles->pageviews_by_ressort_chart();

		$this->view->title = 'Dashboard';
		$this->view->render('pages/dashboard', $viewData);

	}

	public function value_articles() {

		$this->view->wertschoepfend = $this->Articles->value_articles();

		$this->view->artikel = array_column($this->view->wertschoepfend,'artikel');
		$this->view->spielmacher = array_column($this->view->wertschoepfend,'spielmacher');
		$this->view->stuermer = array_column($this->view->wertschoepfend,'stuermer');
		$this->view->abwehr = array_column($this->view->wertschoepfend,'abwehr');
		$this->view->geister = array_column($this->view->wertschoepfend,'geister');

		$this->view->render('pages/wertschoepfend');

	}

	public function artikel() {

		$viewData['charts'] = $this->Charts;
		//articlesByRessort
		$this->view->render('pages/artikel-entwicklung', $viewData);

	}


}
