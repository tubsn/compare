<?php

//Homepage
$routes->get('/', 'Stats@dashboard');

// Teststuff
$routes->get('/cards', 'Lists@cards');
$routes->get('/test', 'Test@test');
$routes->get('/import/topics', 'Import@topics');


// Article Lists
$routes->get('/unclassified/types', 'Lists@unset_only');
$routes->get('/unclassified/audiences', 'Lists@unset_audience_only');
$routes->get('/author/{author}', 'Lists@author');
$routes->get('/author-fuzzy/{author}', 'Lists@author_fuzzy');
$routes->get('/list', 'Lists@index');
$routes->get('/ressort/{ressort}', 'Lists@ressort');
$routes->get('/ressort', 'Lists@ressort');
$routes->get('/type/{type}', 'Lists@type');
$routes->get('/type', 'Lists@type');
$routes->get('/userneed/{userneed}', 'Lists@userneed');
$routes->get('/userneed', 'Lists@userneed');
$routes->get('/audience/{audience}', 'Lists@audience');
$routes->get('/audience', 'Lists@audience');
$routes->get('/tag/{tag}', 'Lists@tag');
$routes->get('/tag', 'Lists@tag');
$routes->get('/discover', 'Lists@discover');

// KPIs
$routes->get('/top5', 'Lists@top5');
$routes->get('/score', 'Lists@scores');
$routes->get('/conversions', 'Lists@conversions');
$routes->get('/pageviews', 'Lists@pageviews');
$routes->get('/mediatime', 'Lists@mediatime');
$routes->get('/subscribers', 'Lists@subscribers');

// ePaper
$routes->get('/epaper', 'Epaper@list');
$routes->get('/epaper/ressort/{ressort}', 'Epaper@ressort');
$routes->get('/epaper/stats', 'Epaper@stats');
$routes->get('/epaper/artikel/{id:\d+}', 'Epaper@detail');
$routes->get('/epaper/import', 'Epaper@import');

// Valueables
$routes->get('/valueable', 'Stats@value_articles');
$routes->get('/valueable/audience', 'Stats@value_articles_audience');
$routes->get('/valueable/type', 'Stats@value_articles_thema');
$routes->get('/valueable/{type}', 'Lists@valueables');

// Print Maps
$routes->get('/print/local', 'Orders@map_print_local');
$routes->get('/print/germany', 'Orders@map_print_germany');
$routes->get('/print/local/cancelled', 'Orders@map_print_local_cancelled');
$routes->get('/print/germany/cancelled', 'Orders@map_print_germany_cancelled');

// Maps
$routes->get('/orders/map/local', 'Maps@map_local');
$routes->get('/orders/map/germany', 'Maps@map_germany');
$routes->get('/orders/map/local/cancelled', 'Maps@map_local_cancelled');
$routes->get('/orders/map/germany/cancelled', 'Maps@map_germany_cancelled');

// Orders
$routes->get('/orders', 'Orders@list');
$routes->get('/orders/list-cancellations', 'Orders@list_cancellations');
$routes->get('/orders/list-daily', 'Orders@list_by_day');
$routes->get('/orders/behavior', 'Orders@customer_behavior');
$routes->get('/orders/clustered', 'Orders@clustered');
$routes->get('/orders/utm[/{field}/{campaign}]', 'Orders@utm');
$routes->get('/orders/import/{date:[\d]{4}-[\d]{2}-[\d]{2}?}', 'Import@order_import');

$routes->get('/orders/live', 'Livedata@index');
$routes->get('/orders/yesterday', 'Livedata@orders_yesterday');
$routes->get('/orders/today', 'Livedata@orders_today');

$routes->get('/orders/app', 'Orders@list_app_orders');
$routes->get('/orders/payguys', 'LongtermAnalysis@started_payment');
$routes->get('/orders/sources', 'Livedata@compared_conversion_sources');
$routes->get('/orders/chain/{id:\d+}', 'Orders@subscription_chain');

$routes->get('/orders/{id:\d{7}}', 'Livedata@order');
$routes->get('/orders/{date:[\d]{4}-[\d]{2}-[\d]{2}?}', 'Livedata@orders_date');
$routes->get('/orders/customer/{id:\d+}', 'Livedata@customer');
$routes->get('/orders/subscription/{id:\d+}', 'Livedata@subscription');

$routes->post('/orders/set_date', 'Livedata@set_date');
$routes->post('/orders/set_paid_filter', 'Livedata@set_paid_filter');
$routes->get('/orders/yearlyconverters', 'Orders@yearly_converters');
$routes->get('/orders/active-customers', 'Orders@active_customers');


// Invoices & Transactions
$routes->get('/invoices[/{month}]', 'Transactions@invoice_download');
$routes->get('/transactions', 'Transactions@index');
$routes->post('/transactions', 'Transactions@index');

// Stats
$routes->get('/stats', 'Stats@dashboard');
$routes->get('/stats/weekly', 'Stats@weekly_review');
$routes->get('/stats/ressort', 'Stats@ressorts');
$routes->get('/stats/userneed', 'Stats@userneeds');
$routes->get('/stats/thema', 'Stats@themen');
$routes->get('/stats/tag', 'Stats@tags');
$routes->get('/stats/audience', 'Stats@audiences');
$routes->get('/stats/audience-by-ressort', 'Stats@audience_by_ressorts');

$routes->get('/stats/cluster/audiences', 'Stats@cluster_audiences');
$routes->get('/stats/cluster/types', 'Stats@cluster_types');
$routes->get('/stats/cluster/tags', 'Stats@cluster_tags');

$routes->get('/stats/artikel', 'Stats@artikel');
$routes->get('/stats/segments', 'Stats@segments');
$routes->get('/stats/mediatime', 'Stats@avg_mediatime');
$routes->get('/stats/pubtime[/{audience}]', 'Stats@publications');


// Portal Compare
$routes->get('/portals', 'LongtermAnalysis@all_portals');
$routes->get('/bbboard', 'LongtermAnalysis@brandenburg');
$routes->get('/weekly', 'Stats@weekly_review');

// Longterm KPI Overview
$routes->get('/longterm', 'LongtermAnalysis@overview');

// campaigns
$routes->get('/export/campaigns', 'Exports@campaigns');
$routes->get('/export/campaigns/{days:\d+}', 'Exports@ga_campaigns');

$routes->get('/export/campaigns/shop', 'Campaigns@fb_accelerator');
$routes->get('/export/campaigns/shop/30', 'Campaigns@fb_accelerator');

$routes->get('/campaigns/fbaccelerator', 'Campaigns@fb_accelerator');
$routes->get('/campaigns/filter/{filter}', 'Campaigns@all');
$routes->get('/campaigns', 'Campaigns@all');

// Churn explorer
$routes->get('/orders/explorer', 'ChurnExplorer@index');
$routes->get('/api/explorer', 'ChurnExplorer@api');

// Apis
$routes->get('/api/orders', 'API@provide_portal_orders');
$routes->get('/api/kpis', 'API@provide_portal_kpis');
$routes->get('/api/sales', 'API@provide_portal_sales');
$routes->get('/api/portals', 'API@provide_combined_kpis');
$routes->get('/api/weekly/{from:[\d]{4}-[\d]{2}-[\d]{2}?}/{to:[\d]{4}-[\d]{2}-[\d]{2}?}', 'API@weekly');
$routes->get('/api/yesterday', 'Exports@yesterday_stats');
$routes->get('/api/orders-today', 'Livedata@api_orders_today');
$routes->get('/api/articles-today', 'Livedata@api_articles_today');
$routes->get('/api/article/{id}', 'Livedata@api_article');
$routes->get('/api/article/{id}/live', 'Livedata@live_article');
$routes->get('/api/stats-today[/{resolution:\d+}]', 'Livedata@api_stats_today');
$routes->get('/api/active-users', 'Livedata@api_active_users');
//$routes->get('/api/reader/{id}', 'API@get_reader');
$routes->get('/api/teaser/{date}/{hour}', 'Teaser@api_positions');
$routes->get('/showip', 'API@showip');

// Readers
$routes->get('/readers/{id:[\d]{12}?}', 'Readers@detail');
$routes->get('/readers/test/{id}', 'Test@reader');
$routes->get('/readers/list[/{segment}]', 'Readers@list');
$routes->get('/readers/sessionlist', 'Readers@session_list');
$routes->get('/readers/audiences', 'Readers@audience_sizes');
$routes->get('/readers/multiple-orders', 'Readers@with_multiple_orders');
$routes->get('/readers/engagement', 'Readers@engagement_alert');
$routes->get('/readers/map/local', 'Maps@users_local');
$routes->get('/readers/map/germany', 'Maps@users_germany');

$routes->get('/readers/import', 'Import@import_readers');

// Article Details
$routes->get('/artikel/{id:\d+}', 'Articles@detail');
$routes->get('/artikel/{id:\d+}/edit', 'Articles@edit');
$routes->post('/artikel/{id:\d+}/edit', 'Articles@save');
$routes->get('/artikel/{id:\d+}/medium', 'Articles@medium');
$routes->get('/artikel/{id:\d+}/refresh', 'Articles@refresh');
$routes->get('/artikel/{id:\d+}/delete', 'Articles@delete');
$routes->post('/artikel/{id:\d+}', 'Articles@set_type');
$routes->get('/artikel/production-per-day', 'Articles@production_per_day');
$routes->get('/artikel/compare/{swpID:\d+}/{mozID:\d+}/{lrID:\d+}', 'Livedata@article_compare');

// Push
$routes->get('/push', 'Push@today');
$routes->get('/push/app', 'Push@today_app');
$routes->get('/push/app/archiv', 'Push@list_app');
$routes->get('/push/archiv', 'Push@list');
$routes->get('/push/stats', 'Push@stats');
$routes->get('/push/stats/development', 'Push@development');
$routes->get('/push/stats/time', 'Push@time_stats');
$routes->get('/push/stats/{column}', 'Push@stats_by');
$routes->get('/push/import', 'Push@import');
$routes->post('/push/import', 'Push@import');
$routes->get('/push/{id}', 'Push@detail');

// Pages
$routes->get('/search', 'Search@show');
$routes->get('/favoriten', 'StaticPages@favilink');
$routes->get('/favicon', 'StaticPages@favilink');
$routes->get('/faq', 'StaticPages@faq');
$routes->get('/changelog', 'StaticPages@changelog');
$routes->post('/settimeframe', 'Articles@set_timeframe');
$routes->get('/switch-portal', 'Articles@switch_portal');
$routes->get('/live', 'Livedata@live_dashboard');
$routes->get('/retresco/{id:\d+}', 'Articles@retresco');

// Exports
$routes->get('/export/articles', 'Exports@articles');
$routes->get('/export/conversions', 'Exports@conversions');
$routes->get('/export/kpis', 'Exports@KPIs');
$routes->get('/export/json', 'Exports@full_json');
$routes->get('/export/ressorts', 'Exports@ressort_stats');
$routes->get('/export/readers', 'Exports@readers');
$routes->get('/export/sales', 'Exports@sales_data');
$routes->get('/export/push', 'Exports@push_data');
$routes->get('/export/value', 'Exports@value_articles');
$routes->get('/export/valueaudience', 'Exports@value_articles_by_audience');
$routes->get('/export/linkpulse/current', 'Exports@linkpulse_current');
$routes->get('/export/linkpulse/halftime', 'Exports@linkpulse_halftime');

// Newsletter
$routes->get('/newsletter/chefredaktion', 'Newsletter@chefredaktion');
$routes->get('/newsletter/sport', 'Newsletter@sport_newsletter');
$routes->get('/newsletter/nachdrehalert', 'Newsletter@nachdreh_alert');
$routes->get('/newsletter/nachdrehalert/{region}', 'Newsletter@nachdreh_alert_filtered');
$routes->get('/newsletter/nachdrehalert-score', 'Newsletter@nachdreh_alert_score');

// Incentives
$routes->get('/incentives', 'Incentives@incentives');

// Teasers
$routes->get('/teasers[/{date}]', 'Teaser@index');

// Admin - Config Area
$routes->get('/admin', 'Admin@index');
$routes->post('/admin', 'Admin@save_config');
$routes->get('/admin/import', 'Import@feeds');
$routes->get('/admin/push', 'Admin@push_import');
$routes->get('/admin/discover', 'Admin@discover_upload');
$routes->post('/admin/discover', 'Admin@discover_upload');
$routes->get('/admin/experimentdata', 'Import@experiment_data');
$routes->get('/admin/kpisegments', 'Import@import_dailyKPI_segments');
$routes->get('/admin/subscribers', 'Import@import_subscribers');
$routes->get('/admin/orders', 'Import@order_import_form');
$routes->get('/admin/topics', 'Warmup@topic_clusters');
$routes->get('/admin/warmup', 'Warmup@daterange');
$routes->get('/admin/warmup_conversions[/{daysago}]', 'Warmup@conversions');
$routes->get('/admin/warmup/subscribers', 'Warmup@subscribers');
$routes->get('/admin/warmup/readers', 'Warmup@readers');
$routes->get('/admin/warmup/assign-sources', 'Warmup@assign_sources');
$routes->get('/admin/warmup/buyintentions', 'Warmup@buy_intentions');
$routes->get('/admin/warmup/sources[/{daysago}]', 'Warmup@enrich_conversions_with_ga');
$routes->get('/admin/warmup/buyintents[/{daysago}]', 'Warmup@enrich_article_with_buy_intents');
$routes->get('/admin/warmup/{weeksago}', 'Warmup@weeks_ago');
$routes->get('/admin/mailsend', 'Newsletter@trigger_newsletter_sends');
$routes->get('/admin/mailsend/weekly', 'Newsletter@trigger_weekly_newsletter_sends');

// Manual Segment imports
$routes->get('/segments', 'Import@segments');

// Admin - Typemanager
$routes->get('/admin/cluster', 'Admin@cluster_manager');
$routes->post('/admin/cluster', 'Admin@set_clusters');

// Admin - Usermanagement
$routes->get('/admin/users', 'Usermanagement@index');
$routes->get('/admin/users/new', 'Usermanagement@new');
$routes->post('/admin/users', 'Usermanagement@create');
$routes->get('/admin/users/{id:\d+}', 'Usermanagement@show');
$routes->get('/admin/users/{id:\d+}/delete/{token}', 'Usermanagement@delete');
$routes->post('/admin/users/{id:\d+}', 'Usermanagement@update');


// Authentication Routes
$routes->get('/login', 'Authentication@login');
$routes->post('/login', 'Authentication@login');
$routes->get('/logout', 'Authentication@logout');
$routes->get('/profile', 'Authentication@profile');
$routes->get('/password-reset', 'Authentication@password_reset_form');
$routes->post('/password-reset', 'Authentication@password_reset_send_mail');
$routes->get('/password-change[/{resetToken}]', 'Authentication@password_change_form');
$routes->post('/password-change[/{resetToken}]', 'Authentication@password_change_process');
$routes->get('/profile/edit', 'Authentication@edit_profile');
$routes->post('/profile/edit', 'Authentication@edit_profile');
