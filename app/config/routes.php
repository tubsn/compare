<?php

//Homepage
$routes->get('/', 'Stats@dashboard');


// Teststuff
$routes->get('/subs', 'Import@import_subscribers');
$routes->get('/cards', 'Lists@cards');
$routes->get('/kilkaya', 'Livedata@kilkaya');
$routes->get('/emo', 'Stats@test');
$routes->get('/freecharts', 'Stats@freecharts');
$routes->get('/print/local', 'Orders@map_print_local');
$routes->get('/print/germany', 'Orders@map_print_germany');
$routes->get('/print/local/cancelled', 'Orders@map_print_local_cancelled');
$routes->get('/print/germany/cancelled', 'Orders@map_print_germany_cancelled');


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
$routes->get('/filter', 'Lists@filter');
$routes->post('/filter', 'Lists@filter');

// ePaper
$routes->get('/epaper', 'Epaper@list');
$routes->get('/epaper/ressort/{ressort}', 'Epaper@ressort');
$routes->get('/epaper/stats', 'Epaper@stats');
$routes->get('/epaper/artikel/{id:\d+}', 'Epaper@detail');
$routes->get('/epaper/import', 'Epaper@import');

// Valueables
$routes->get('/valueable', 'Stats@value_articles');
$routes->get('/valueable/{type}', 'Lists@valueables');

// Orders
$routes->get('/orders', 'Orders@stats');
$routes->get('/orders/list', 'Orders@list');
$routes->get('/orders/map/local', 'Orders@map_local');
$routes->get('/orders/map/germany', 'Orders@map_germany');
$routes->get('/orders/map/local/cancelled', 'Orders@map_local_cancelled');
$routes->get('/orders/map/germany/cancelled', 'Orders@map_germany_cancelled');
$routes->get('/orders/campaigns', 'Orders@campaigns');
$routes->get('/orders/import/{date:[\d]{4}-[\d]{2}-[\d]{2}?}', 'Import@order_import');

$routes->get('/orders/live', 'Livedata@index');
$routes->get('/orders/yesterday', 'Livedata@orders_yesterday');
$routes->get('/orders/today', 'Livedata@orders_today');
$routes->get('/orders/cancellations', 'Orders@cancellations');
$routes->get('/orders/payguys', 'LongtermAnalysis@started_payment');

$routes->get('/orders/{id:\d+}', 'Livedata@order');
$routes->get('/orders/{date}', 'Livedata@orders_date');
$routes->get('/orders/customer/{id:\d+}', 'Livedata@customer');
$routes->get('/orders/subscription/{id:\d+}', 'Livedata@subscription');

$routes->post('/orders/set_client', 'Livedata@set_client');
$routes->post('/orders/set_date', 'Livedata@set_date');
$routes->post('/orders/set_paid_filter', 'Livedata@set_paid_filter');

// Stats
$routes->get('/stats', 'Stats@dashboard');
$routes->get('/stats/ressort', 'Stats@ressorts');
$routes->get('/stats/thema', 'Stats@themen');
$routes->get('/stats/tag', 'Stats@tags');
$routes->get('/stats/audience', 'Stats@audiences');
$routes->get('/stats/audience-by-ressort', 'Stats@audience_by_ressorts');
$routes->get('/stats/artikel', 'Stats@artikel');

// Portal Compare
$routes->get('/portals', 'LongtermAnalysis@all_portals');

// Longterm Analysis
$routes->get('/longterm', 'LongtermAnalysis@overview');

// campaigns
$routes->get('/export/campaigns', 'Exports@campaigns');
$routes->get('/export/campaigns/{days:\d+}', 'Exports@ga_campaigns');

$routes->get('/export/campaigns/shop', 'Campaigns@fb_accelerator');
$routes->get('/export/campaigns/shop/30', 'Campaigns@fb_accelerator');

$routes->get('/campaigns/fbaccelerator', 'Campaigns@fb_accelerator');
$routes->get('/campaigns/filter/{filter}', 'Campaigns@all');
$routes->get('/campaigns', 'Campaigns@all');

// Apis
$routes->get('/churncalc[/{product}]', 'LongtermAnalysis@churnAPI');
$routes->get('/api/orders', 'LongtermAnalysis@provide_portal_orders');
$routes->get('/api/kpis', 'LongtermAnalysis@provide_portal_kpis');
$routes->get('/api/portals', 'LongtermAnalysis@provide_combined_kpis');
$routes->get('/api/yesterday', 'Exports@yesterday_stats');
$routes->get('/api/orders-today', 'Livedata@api_orders_today');
$routes->get('/api/articles-today', 'Livedata@api_articles_today');
$routes->get('/api/stats-today[/{resolution:\d+}]', 'Livedata@api_stats_today');
$routes->get('/api/active-users', 'Livedata@api_active_users');
$routes->get('/api/live', 'Livedata@api_live');

// Readers
$routes->get('/readers/{id:[\d]{12}?}', 'Readers@detail');
$routes->get('/readers/list[/{segment}]', 'Readers@list');
$routes->get('/readers/import', 'Import@import_readers');

// Article Details
$routes->get('/artikel/{id:\d+}', 'Articles@detail');
$routes->get('/artikel/{id:\d+}/edit', 'Articles@edit');
$routes->post('/artikel/{id:\d+}/edit', 'Articles@save');
$routes->get('/artikel/{id:\d+}/medium', 'Articles@medium');
$routes->get('/artikel/{id:\d+}/refresh', 'Articles@refresh');
$routes->get('/artikel/{id:\d+}/delete', 'Articles@delete');
$routes->post('/artikel/{id:\d+}', 'Articles@set_type');

// Pages
$routes->get('/search', 'Search@show');
$routes->get('/favoriten', 'Articles@favilink');
$routes->get('/favicon', 'Articles@favilink');
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
$routes->get('/export/value', 'Exports@value_articles');
$routes->get('/export/linkpulse/current', 'Exports@linkpulse_current');
$routes->get('/export/linkpulse/halftime', 'Exports@linkpulse_halftime');

// Newsletter
$routes->get('/newsletter/chefredaktion', 'Newsletter@chefredaktion');
$routes->get('/newsletter/sport', 'Newsletter@sport_newsletter');
$routes->get('/newsletter/nachdrehalert', 'Newsletter@nachdreh_alert');
$routes->get('/newsletter/nachdrehalert-score', 'Newsletter@nachdreh_alert_score');

// Incentives
$routes->get('/incentives', 'Incentives@incentives');

// Admin - Config Area
$routes->get('/admin', 'Admin@index');
$routes->post('/admin', 'Admin@save_config');
$routes->get('/admin/import', 'Import@feeds');
$routes->get('/admin/discover', 'Admin@discover_upload');
$routes->post('/admin/discover', 'Admin@discover_upload');
$routes->get('/admin/subscribers', 'Import@import_subscribers');
$routes->get('/admin/orders', 'Import@order_import_form');
$routes->get('/admin/topics', 'Warmup@topic_clusters');
$routes->get('/admin/warmup', 'Warmup@daterange');
$routes->get('/admin/warmup_conversions[/{daysago}]', 'Warmup@conversions');
$routes->get('/admin/warmup/subscribers', 'Warmup@subscribers');
$routes->get('/admin/warmup/sources[/{daysago}]', 'Warmup@enrich_conversions_with_ga');
$routes->get('/admin/warmup/buyintents[/{daysago}]', 'Warmup@enrich_article_with_buy_intents');
$routes->get('/admin/warmup/{weeksago}', 'Warmup@weeks_ago');
$routes->get('/admin/mailsend', 'Newsletter@trigger_newsletter_sends');

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
