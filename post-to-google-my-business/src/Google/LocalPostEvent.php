<?php


namespace PGMB\Google;



use InvalidArgumentException;

class LocalPostEvent extends AbstractGoogleJsonObject {
	public function __construct($title, TimeInterval $schedule) {
		$this->setTitle($title);
		$this->setSchedule($schedule);
	}

	public function setTitle($title){
		if(empty($title)){
			throw new InvalidArgumentException(__('Event/offer title is required', 'post-to-google-my-business'));
		}
//		if(function_exists('mb_strlen') && mb_strlen($title) >= 80 || strlen($title) >= 80){
//			throw new InvalidArgumentException(__('Event/offer title is too long, max 80 characters', 'post-to-google-my-business'));
//		}
		$this->jsonOutput['title'] = $title;
	}

	public function setSchedule(TimeInterval $schedule){
		$this->jsonOutput['schedule'] = $schedule->getArray();
	}

	public static function fromArray( $array ) {
		return new self($array['title'], TimeInterval::fromArray($array['schedule']));
	}
}
