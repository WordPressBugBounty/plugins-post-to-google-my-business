<?php

namespace PGMB\Admin;

interface AjaxCallbackInterface {
	/**
	 * @return array ajax_hook => callable
	 */
	public function ajax_callbacks();
}
