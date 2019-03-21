<?php
if(!defined('PATH')) die;

class Map
{
	protected $map = array();
	protected $totalTargets = 0;
	protected $shots = 0;
	protected $shipPositions = array();
	
	static $ships = array(5, 4, 4);
	static $orientation = array('horizontal', 'vertical');
	static $verticalDirection = array('left', 'right');
	static $horizontalDirection = array('up', 'down');

	const MAP_X = 10;
	const MAP_Y = 10;

	const LEFT = 'left';
	const RIGHT = 'right';
	const HORIZONTAL = 'horizontal';
	const VERTICAL = 'vertical';
	const UP = 'up';
	const DOWN = 'down';
	public function __construct() {
		$this
			->calculateMaxTargets()
			->generateMap()
			->populateMap();
	}

	public function getMatrix() {
		return $this->map;
	}

	public function getTargetCount() {
		return $this->totalTargets;
	}

	public function calculateMaxTargets() {
		foreach(self::$ships as $shipSize)
			$this->totalTargets += $shipSize;

		return $this;
	}
	public function generateMap() {
		for($y = 0; $y < static::MAP_Y; $y++) {
			for($x = 0; $x < static::MAP_X; $x++) {
				$this->map[$y][$x] = 0;
			}
		}

		return $this;
	}
	public function populateMap() {
		foreach(static::$ships as $shipSize)
			$this->addToMap($shipSize);

		return $this;
	}

	public function addToMap($shipSize) {
		while(true) {
			$position = $this->getRandomEmptyCoordinate();
			$orientation = $this->getRandomOrientation();
			$direction = $this->getRandomDirection($orientation);

			if($this->canPositionShipAt($shipSize, $position, $direction)) {
				$this->positionShipAt($shipSize, $position, $direction);
				return true;
			}
		}
	}

	public function canPositionShipAt($shipSize, Array $position, $direction) {
		list($y, $x) = $position;

		for($i = 0; $i < $shipSize; $i++) {
			switch($direction) {
				case self::LEFT:
					if($this->isOccupiedCoordinate($y, $x - $i)) return false;
					break;

				case self::RIGHT:
					if($this->isOccupiedCoordinate($y, $x + $i)) return false;
					break;

				case self::UP:
					if($this->isOccupiedCoordinate($y - $i, $x)) return false;
					break;

				case self::DOWN:
					if($this->isOccupiedCoordinate($y + $i, $x)) return false;
			}
		}

		return true;
	}
	public function positionShipAt($shipSize, Array $position, $direction) {
		list($y, $x) = $position;
		$shipId = sizeof($this->shipPositions);

		for($i = 0; $i < $shipSize; $i++) {
			switch($direction) {
				case self::LEFT:
					$newY = $y; $newX = $x - $i;
					break;

				case self::RIGHT:
					$newY = $y; $newX = $x + $i;
					break;

				case self::UP:
					$newY = $y - $i; $newX = $x;
					break;

				case self::DOWN:
					$newY = $y + $i; $newX = $x;
			}

			$this->fillCoordinate($newY, $newX);
			$this->shipPositions[$shipId][] = array($newY, $newX);
		}

		return true;
	}

	public function isOccupiedCoordinate($y, $x) {
		if($x < 0 || $x > self::MAP_X - 1 || $y < 0 || $y > self::MAP_Y - 1)
			return true;

		return 0 !== $this->map[$y][$x];
	}

	public function fillCoordinate($y, $x) {
		return $this->map[$y][$x] = 1;
	}
	public function getRandomDirection($orientation)
	{
		if(self::HORIZONTAL === $orientation)
			return self::$horizontalDirection[rand(0,1)];

		return self::$verticalDirection[rand(0,1)];
	}
	public function getRandomOrientation() {
		return self::$orientation[rand(0,1)];
	}
	public function getRandomEmptyCoordinate() {
		while(true) {
			$randX = rand(0, static::MAP_X - 1);
			$randY = rand(0, static::MAP_Y - 1);
			if(0 === $this->map[$randX][$randY]) {
				return array($randY, $randX);
			}
		}
	}
	public function shoot($y, $x) {
		$state = (int) $this->map[$y][$x];
		$shipSunk = false;

		switch($state) {
			case 0:
				$this->shots++;
				break;
			case 1:
				$this->shots++;
				$this->totalTargets--;
				$shipSunk = $this->sunkShip($y, $x);
				break;
			case 2:
			case 3:
				return false;
		}

		$newState = $this->map[$y][$x] += 2;
		$newState = true === $shipSunk ? 4 : $newState;

		if(0 === $this->totalTargets)
			return true;

		return $newState;
	}

	public function sunkShip($y, $x) {
		foreach($this->shipPositions as $shipId => $shipPosition) {
			if(0 === sizeof($shipPosition))
				continue;

			foreach($shipPosition as $positionId => $position) {
				list($shipY, $shipX) = $position;
				if($shipY == $y && $shipX == $x) {
					unset($this->shipPositions[$shipId][$positionId]);
					return 0 === sizeof($this->shipPositions[$shipId]);
				}
			}
		}

		return false;
	}

	public function getShotCount() {
		return $this->shots;
	}
}