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
	 * @param locale
	 */
	function setTitle($title, $locale) {
		return $this->setData('title', $title, $locale);
	}

	/**
	 * Get entry title
	 * @param locale
	 * @return string
	 */
	function getTitle($locale) {
		return $this->getData('title', $locale);
	}

	/**
	 * Get Localized entry title
	 * @return string
	 */
	function getLocalizedTitle() {
		return $this->getLocalizedData('title');
	}

	/**
	 * Set entry content
	 * @param $content string
	 * @param locale
	 */
	function setContent($content, $locale) {
		return $this->setData('content', $content, $locale);
	}

	/**
	 * Get entry content
	 * @param locale
	 * @return string
	 */
	function getContent($locale) {
		return $this->getData('content', $locale);
	}

	/**
	 * Get "localized" content
	 * @return string
	 */
	function getEntryContent() {
		return $this->getLocalizedData('content');
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


	/**
	 * Get "localized" content
	 * @return string
	 */
	function getLocalizedContent() {
		return $this->getLocalizedData('content');
	}		

}

