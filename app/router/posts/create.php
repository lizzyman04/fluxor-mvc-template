<?php

use Fluxor\Flow;
use Source\Controllers\PostController;

Flow::GET()->to(PostController::class, 'create');
Flow::POST()->to(PostController::class, 'store');