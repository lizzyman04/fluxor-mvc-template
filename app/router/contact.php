<?php

use Fluxor\Flow;
use Source\Controllers\HomeController;

Flow::GET()->to(HomeController::class, 'contact');