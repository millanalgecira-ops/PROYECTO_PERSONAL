<?php
session_start();
require_once __DIR__ . '/../Config/rutas.php';
require_once __DIR__ . '/../Controllers/HomeController.php';

$controller = new HomeController();
$controller->index();
?>