<?php

use Fluxor\Flow;
use Source\Controllers\PostController;

Flow::POST()->to(PostController::class, 'delete');