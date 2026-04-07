<?php

use Fluxor\Flow;
use Source\Controllers\PostController;

Flow::GET()->to(PostController::class, 'edit');
Flow::POST()->to(PostController::class, 'update');