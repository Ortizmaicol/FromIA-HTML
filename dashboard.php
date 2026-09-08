<?php
require_once __DIR__ . '/controllers/DashboardController.php';
$dash = new DashboardController();
$dash->index();
