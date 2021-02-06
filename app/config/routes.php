<?php
/*
Complete Documentation on: https://github.com/nikic/FastRoute
Example Routes:
$routes->get('/urlpath[/{optionalparameter}]', 'Controller@Action');
$routes->post('/article/{id:\d+}', 'Controller@Action'); With ID-Parameter (Numeric)
*/

$routes->get('/', 'Lists@index');
$routes->get('/unset', 'Lists@unset_only');
$routes->get('/author/{author}', 'Lists@author');
$routes->get('/ressort/{ressort}', 'Lists@ressort');
$routes->get('/ressort', 'Lists@ressort');
$routes->get('/type/{type}', 'Lists@type');
$routes->get('/type', 'Lists@type');
$routes->get('/plus', 'Lists@plus');
$routes->get('/conversions', 'Lists@conversions');
$routes->get('/pageviews', 'Lists@pageviews');
$routes->get('/stats', 'Stats@index');
$routes->post('/settimeframe', 'Articles@set_timeframe');

$routes->get('/retresco/{id:\d+}', 'Articles@retresco');

$routes->get('/cards', 'Lists@cards');

$routes->get('/artikel/{id:\d+}', 'Articles@detail');
$routes->get('/artikel/{id:\d+}/refresh', 'Articles@refresh');
$routes->get('/artikel/{id:\d+}/delete', 'Articles@delete');
$routes->post('/artikel/{id:\d+}', 'Articles@edit');

$routes->get('/search', 'Search@show');

$routes->get('/leser', 'Readers@index');
$routes->get('/leser/{plenigoID}', 'Readers@detail');

$routes->get('/export', 'Exports@full');
$routes->get('/export/json', 'Exports@full_json');
$routes->get('/export/combined', 'Exports@daily');
$routes->get('/export/ressorts', 'Exports@ressort_stats');

$routes->get('/admin', 'Admin@index');
$routes->post('/admin', 'Admin@save_config');
$routes->get('/admin/import', 'Import@feeds');
$routes->get('/admin/warmup', 'Analytics@warmup_daterange');
$routes->get('/admin/warmup/{weeksago}', 'Analytics@warmup_weeks_ago');

$routes->get('/login', 'Admin@login');
$routes->get('/logout', 'Admin@logout');
$routes->get('/profil', 'Admin@profile');
$routes->post('/login', 'Admin@check_login');
