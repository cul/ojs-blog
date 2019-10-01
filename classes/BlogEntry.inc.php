<?php

/**
 * @file classes/BlogEntry.inc.php
 *
 *
 * @package plugins.generic.blog
 * @class BlogEntry
 * Data object representing a blog entry.
 */

class BlogEntry extends DataObject {

	//
	// Get/set methods
	//

	/**
	 * Get context ID
	 * @return string
	 */
	function getContextId(){
		return $this->getData('contextId');
	}

	/**
	 * Set context ID
	 * @param $contextId int
	 */
	function setContextId($contextId) {
		return $this->setData('contextId', $contextId);
	}


	/**
	 * Set entry title
	 * @param string string
	 */
	function setTitle($title) {
		return $this->setData('title', $title);
	}

	/**
	 * Get entry title
	 * @return string
	 */
	function getTitle() {
		return $this->getData('title');
	}


	/**
	 * Set entry content
	 * @param $content string
	 */
	function setContent($content) {
		return $this->setData('content', $content);
	}

	/**
	 * Get entry content
	 * @return string
	 */
	function getContent() {
		return $this->getData('content');
	}

	function getAbbreviatedContent(){
		return implode(' ', array_slice(explode(' ', $this->getData('content')), 0, 25));
	}

	/**
	 * Set entry keywords
	 * @param $keywords array
	 */
	function setKeywords($keywords) {
		return $this->setData('keywords', $keywords);
	}

	/**
	 * Get entry keywords
	 * @return string
	 */
	function getKeywords() {
		return $this->getData('keywords');
	}


	/**
	 * Get blog entry posted date.
	 * @return date (YYYY-MM-DD)
	 */
	function getDatePosted() {
		return date('Y-m-d', strtotime($this->getData('datePosted')));
	}

	/**
	 * Set blog entry posted date.
	 * @param $datePosted date (YYYY-MM-DD)
	 */
	function setDatePosted($datePosted) {
		$this->setData('datePosted', $datePosted);
	}

}

