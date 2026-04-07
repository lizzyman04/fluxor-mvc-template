<?php

use Fluxor\Flow;
use Source\Controllers\AuthController;

Flow::GET()->to(AuthController::class, 'showRegister');
Flow::POST()->to(AuthController::class, 'register');