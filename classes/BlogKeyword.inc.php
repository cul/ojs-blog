<?php

/**
 * @file classes/BlogKeyword.inc.php
 *
 *
 * @package plugins.generic.blog
 * @class BlogKeyword
 * Data object representing a blog entry.
 */

class BlogKeyword extends DataObject {

	/**
	 * Set entry keyword
	 * @param string string
	 */
	function setkeyword($keyword) {
		return $this->setData('keyword', $keyword);
	}

	/**
	 * Get entry keyword
	 * @return string
	 */
	function getkeyword() {
		return $this->getData('keyword');
	}

}

