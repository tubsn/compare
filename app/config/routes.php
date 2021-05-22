<?php

//Homepage
$routes->get('/', 'Lists@index');

// Pages
$routes->get('/unset', 'Lists@unset_only');
$routes->get('/author/{author}', 'Lists@author');
$routes->get('/ressort/{ressort}', 'Lists@ressort');
$routes->get('/ressort', 'Lists@ressort');
$routes->get('/type/{type}', 'Lists@type');
$routes->get('/type', 'Lists@type');
$routes->get('/tag/{tag}', 'Lists@tag');
$routes->get('/tag', 'Lists@tag');
$routes->get('/plus', 'Lists@plus');
$routes->get('/conversions', 'Lists@conversions');
$routes->get('/pageviews', 'Lists@pageviews');
$routes->post('/settimeframe', 'Articles@set_timeframe');

// Stats
$routes->get('/stats', 'Stats@index');
$routes->get('/stats/cancellations', 'Stats@cancellations');

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
$routes->get('/art/{articleID:\d+}', 'Articles@quickview');
$routes->get('/test/{id:\d+}', 'Articles@test');


// Exports
$routes->get('/export/articles', 'Exports@articles');
$routes->get('/export/conversions', 'Exports@conversions');
$routes->get('/export/json', 'Exports@full_json');
$routes->get('/export/combined', 'Exports@daily');
$routes->get('/export/ressorts', 'Exports@ressort_stats');
$routes->get('/mp', 'MassPlenigo@export');


// Admin - Config Area
$routes->get('/admin', 'Admin@index');
$routes->post('/admin', 'Admin@save_config');
$routes->get('/admin/import', 'Import@feeds');
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
