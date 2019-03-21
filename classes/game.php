<?php
ob_start();
if(!defined('PATH')) die;

require_once CLASS_PATH.'map.php';
require_once CLASS_PATH.'messages.php';
class WebGame 
{
	protected $map;
	protected $messanger;
	protected $gameId;

	const MD5_REGEX = '/^[0-9a-f]{32}$/i';
	public function __construct($gameId = null) {
		if(!$this->gameExists($gameId)) {
			return $this->createNewGame();
		}

		$this->gameId = $gameId;
		$this->map = $this->loadMap($gameId);
		$this->messanger = new Messanger($this->map, true);
	}
	public function getMessage($state) {
		return $this->messanger->get($state);
	}
	public function gameExists($gameId) {
		if(!preg_match(self::MD5_REGEX, $gameId) || !file_exists($this->getGameFile($gameId)))
			return false;

		return true;
	}
	public function loadMap($gameId) {
		$data = file_get_contents($this->getGameFile($gameId));
		return unserialize($data);
	}
	public function createNewGame() {
		$gameId = $this->findAvailableGameId();
		$this->saveToFile($gameId, new Map());
		$this->redirectToGame($gameId);
	}

	public function findAvailableGameId() {
		while(true) {
			$gameId = md5(rand(0,99999999));
			if(!$this->gameExists($gameId)) {
				return $gameId;
			}
		}
	}
	public function getGameFile($gameId) {
		return DATA_PATH.$gameId;
	}

	public function saveToFile($gameId, $map) {
		return file_put_contents($this->getGameFile($gameId), serialize($map));
	}

	public function redirectToGame($gameId) {
		header('Location: //'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?gameId='.$gameId);
		exit;
	}

	public function getMatrixVisible() {
		$mapMatrix = $this->map->getMatrix();
		$outputMatrix = array();
		foreach($mapMatrix as $y => $row) {
			foreach($row as $x => $cellValue) {
				$value = $cellValue === 1 ? 0 : $cellValue;
				$outputMatrix[$y][$x] = $value;
			}
		}

		return $outputMatrix;
	}
	public function getMatrixHidden() {
		$mapMatrix = $this->map->getMatrix();
		$outputMatrix = array();
		foreach($mapMatrix as $y => $row) {
			foreach($row as $x => $cellValue) {
				$value = 0;
				if($cellValue === 1 || $cellValue === 3) {
					$value = 1;
				}

				$outputMatrix[$y][$x] = $value;
			}
		}

		return $outputMatrix;
	}
	public function shoot($y, $x) {
		$state = $this->map->shoot($y, $x);
		$this->saveToFile($this->gameId, $this->map);
		return $state;
	}
	public function getShots() {
		return $this->map->getShotCount();
	}

	public function getGameState() {
		return 0 == $this->map->getTargetCount() ? 1 : 0;
	}

}