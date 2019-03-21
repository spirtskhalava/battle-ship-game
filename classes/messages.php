<?php
if(!defined('PATH')) die;

class Messanger
{
	protected $map;
	protected $htmlMode = true;

	const MSG_SHOOT_MISS = 'missed!';
	const MSG_SHOOT_WRONG = 'you already shot there!';
	const MSG_SHOOT_BULLSEYE = 'You hit a ship!';
	const MSG_SHIP_SANK = 'you sank a ship!';
	const MSG_CONGRATULATIONS = 'you won the game!';
	const MSG_TOTAL_SHOTS = 'Total: ';

	public function __construct(Map $map, $htmlMode = true) {
		$this->map = $map;
		$this->htmlMode = $htmlMode;
	}

	public function get($shot) {

		if(false === $shot)
			return self::MSG_SHOOT_WRONG;

		if(true === $shot) {
			if(true === $this->htmlMode)
				return self::MSG_CONGRATULATIONS;
			$message  = self::MSG_SHOOT_BULLSEYE.' '.self::MSG_CONGRATULATIONS."\n";
			$message .= self::MSG_TOTAL_SHOTS.$this->map->getShotCount();
			return $message;
		}

		if(2 === (int) $shot) {
			return self::MSG_SHOOT_MISS;
		}

		if(3 === (int) $shot) {
			return self::MSG_SHOOT_BULLSEYE;
		}

		if(4 === (int) $shot) {
			return self::MSG_SHIP_SANK;
		}
	}
}