<?php
	error_reporting(0);

	    require_once 'config/config.php';
	    require_once 'classes/map.php';
		require_once 'classes/game.php';
		require_once 'classes/controller.php';

		$params = array_merge($_GET, $_POST);
		$game = new WebGame($params['gameId']);
		$controller = new WebController($game);
		$controller->routeAction($params['action'], $params);

