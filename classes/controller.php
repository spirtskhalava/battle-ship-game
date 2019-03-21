<?php
if(!defined('PATH')) die;

class WebController
{
	protected $game;
	const MSG_STATE_SHOOT = 0;
	public function __construct(WebGame $game) {
		$this->game = $game;
	}
	public function routeAction($action, $params) {
		switch($action) {
			case 'shoot':
				return $this->shoot($params);
			case 'getMapVisible':
				return $this->actionGetMapVisible();
			case 'getMapHidden':
				return $this->actionGetMapHidden();
			default:
				require TEMPLATE_PATH.'layout.php';
				return true;
		}
	}

	public function shoot($params) {
		$x = $params['x'];
		$y = $params['y'];

		$data = array();
		$data['messageState'] = $this->game->shoot($y, $x);
		$data['message'] = $this->game->getMessage($data['messageState']);
		$data['coords'] = $this->game->getMatrixVisible();
		$data['shots'] = $this->game->getShots();
		$data['gameState'] = $this->game->getGameState();

		return $this->sendJson($data);
	}

	public function actionGetMapVisible() {
		$data = array();
		$data['coords'] = $this->game->getMatrixVisible();
		$data['shots'] = $this->game->getShots();
		$data['gameState'] = $this->game->getGameState();
		if($data['gameState']) {
			$data['message'] = $this->game->getMessage(true);
			$data['messageState'] = true;
		} 

		return $this->sendJson($data);
	}
	public function actionGetMapHidden() {
		$data = array();
		$data['coords'] = $this->game->getMatrixHidden();
		$data['shots'] = $this->game->getShots();
		$data['gameState'] = $this->game->getGameState();

		return $this->sendJson($data);
	}

	public function sendJson($data) {
		header('Content-type: application/json');
		echo json_encode($data);
		exit;		
	}
}