<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Application Web Routes
|--------------------------------------------------------------------------
|
| All routes are self-contained inside their respective domain modules:
| - Modules/Auth/routes/web.php
| - Modules/Crm/routes/web.php
| - Modules/Finance/routes/web.php
| - Modules/Leads/routes/web.php
| - Modules/Dashboards/routes/web.php
|
*/

require_once base_path('Modules/Auth/routes/web.php');
require_once base_path('Modules/Crm/routes/web.php');
require_once base_path('Modules/Finance/routes/web.php');
require_once base_path('Modules/Leads/routes/web.php');
require_once base_path('Modules/Dashboards/routes/web.php');
require_once base_path('Modules/Sms/routes/web.php');
