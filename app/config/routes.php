<?php

//Homepage
$routes->get('/', 'Lists@index');

// Pages
$routes->get('/unset', 'Lists@unset_only');
$routes->get('/author/{author}', 'Lists@author');
$routes->get('/author-fuzzy/{author}', 'Lists@author_fuzzy');
$routes->get('/ressort/{ressort}', 'Lists@ressort');
$routes->get('/ressort', 'Lists@ressort');
$routes->get('/type/{type}', 'Lists@type');
$routes->get('/type', 'Lists@type');
$routes->get('/tag/{tag}', 'Lists@tag');
$routes->get('/tag', 'Lists@tag');
$routes->get('/plus', 'Lists@plus');
$routes->get('/top5', 'Lists@top5');
$routes->get('/conversions', 'Lists@conversions');
$routes->get('/pageviews', 'Lists@pageviews');
$routes->get('/subscribers', 'Lists@subscribers');
$routes->post('/settimeframe', 'Articles@set_timeframe');

// Orders
$routes->get('/orders', 'Orders@stats');
$routes->get('/orders/list', 'Orders@list');
$routes->get('/orders/import/{date:[\d]{4}-[\d]{2}-[\d]{2}?}', 'Import@order_import');

$routes->get('/orders/live', 'Livedata@index');
$routes->get('/orders/yesterday', 'Livedata@orders_yesterday');
$routes->get('/orders/today', 'Livedata@orders_today');
$routes->get('/orders/{id:\d+}', 'Livedata@order');
$routes->get('/orders/{date}', 'Livedata@orders_date');
$routes->get('/orders/customer/{id:\d+}', 'Livedata@customer');
$routes->get('/orders/subscription/{id:\d+}', 'Livedata@subscription');

$routes->post('/orders/set_client', 'Livedata@set_client');
$routes->post('/orders/set_date', 'Livedata@set_date');
$routes->post('/orders/set_paid_filter', 'Livedata@set_paid_filter');

// Stats
$routes->get('/stats', 'Stats@index');
// $routes->get('/stats/cancellations', 'Stats@cancellations');

// Article Details
$routes->get('/artikel/{id:\d+}', 'Articles@detail');
$routes->get('/artikel/{id:\d+}/edit', 'Articles@edit');
$routes->post('/artikel/{id:\d+}/edit', 'Articles@save');
$routes->get('/artikel/{id:\d+}/medium', 'Articles@medium');
$routes->get('/artikel/{id:\d+}/refresh', 'Articles@refresh');
$routes->get('/artikel/{id:\d+}/delete', 'Articles@delete');
$routes->post('/artikel/{id:\d+}', 'Articles@set_type');

// Search
$routes->get('/search', 'Search@show');
$routes->get('/favoriten', 'Articles@favilink');
$routes->get('/favicon', 'Articles@favilink');

// Teststuff
$routes->get('/leser', 'Readers@index');
$routes->get('/leser/{plenigoID}', 'Readers@detail');
$routes->get('/lp/{articleID:\d+}', 'Articles@linkpulse');
$routes->get('/retresco/{id:\d+}', 'Articles@retresco');
$routes->get('/cards', 'Lists@cards');
$routes->get('/test/{id:\d+}', 'Articles@test');


// Exports
$routes->get('/export/articles', 'Exports@articles');
$routes->get('/export/conversions', 'Exports@conversions');
$routes->get('/export/json', 'Exports@full_json');
$routes->get('/export/ressorts', 'Exports@ressort_stats');

// Newsletter
$routes->get('/newsletter/chefredaktion', 'Newsletter@chefredaktion');
$routes->get('/newsletter/sport', 'Newsletter@sport_newsletter');
$routes->get('/newsletter/test', 'Newsletter@test');


// Admin - Config Area
$routes->get('/admin', 'Admin@index');
$routes->post('/admin', 'Admin@save_config');
$routes->get('/admin/import', 'Import@feeds');
$routes->get('/admin/orders', 'Import@order_import_form');
$routes->get('/admin/warmup', 'Warmup@daterange');
$routes->get('/admin/warmup_conversions', 'Warmup@conversions');
$routes->get('/admin/warmup_subscribers', 'Warmup@subscribers');
$routes->get('/admin/warmup/{weeksago}', 'Warmup@weeks_ago');

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
