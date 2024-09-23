<?php


namespace PGMB\Google;


class LocalPostEditMask {
	private $livePostFlat, $updatedPostFlat = [];
	private $mask;

	private $readOnly = [
		'name',
		'state',
		'updateTime',
		'createTime',
		'searchUrl',
	];

	public function __construct($livePost, LocalPost $updatedPost) {
		$this->walk((array)$livePost, $this->livePostFlat);
		$this->walk($updatedPost->getArray(), $this->updatedPostFlat);

		$updated_fields = array_diff_assoc($this->updatedPostFlat, $this->livePostFlat);

		// Check whether specific fields have been removed, such as call to action or image
		$deleted_fields = array_diff_assoc(array_diff_key($this->livePostFlat, array_flip($this->readOnly)), $this->updatedPostFlat);

		$this->mask = implode(',', array_keys(array_merge($updated_fields, $deleted_fields)));
	}

	private function walk($array, &$output, $parent = ''){
		foreach($array as $key => $value){
			if(is_array($value) || is_object($value)){
				$this->walk((array)$value, $output, $parent.$key.".");
				continue;
			}
			$output[$parent.$key] = $value;
		}
	}

	public function getMask(){
		return $this->mask;
	}
}
