<?php

/**
 * @file classes/blogEntryDAO.inc.php
 *
 *
 * @package plugins.generic.blog
 * @class blogEntryDAO
 * Operations for retrieving and modifying blog objects.
 */

import('lib.pkp.classes.db.DAO');
import('plugins.generic.blog.classes.BlogEntry');

class blogEntryDAO extends DAO {

	/**
	 * Get a blog entry by ID
	 * @param $blogId int blog ID
	 * @param $contextId int Optional context ID
	 */
	function getById($blogEntryId, $contextId = null) {
		$params = array((int) $blogEntryId);
		if ($contextId) $params[] = $contextId;

		$result = $this->retrieve('SELECT * FROM blog_entries WHERE context_id = ?',
			$params);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $this->_fromRow($result->GetRowAssoc(false));
		}
		$result->Close();
		return $returner;
	}

	/**
	 * Get a set of static pages by context ID
	 * @param $contextId int
	 * @param $rangeInfo Object optional
	 * @return DAOResultFactory
	 */
	function getByContextId($contextId, $rangeInfo = null) {
		$result = $this->retrieveRange(
			'SELECT * FROM blog_entries WHERE context_id = ?',
			(int) $contextId,
			$rangeInfo
		);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Insert a blog.
	 * @param $blog Blog
	 * @return int Inserted blog ID
	 */
	function insertObject($blogEntry) {
		$this->update(
			'INSERT INTO blog (context_id) VALUES (?)',
			array(
				(int) $blog->getContextId()
			)
		);

		$blog->setId($this->getInsertId());
		$this->updateLocaleFields($blog);

		return $blog->getId();
	}

	/**
	 * Update the database with a blog object
	 * @param $blog blog
	 */
	function updateObject($blog) {
		$this->update(
			'UPDATE	blog
			SET	context_id = ?
			WHERE	blog_id = ?',
			array(
				(int) $blog->getContextId(),
				(int) $blog->getId()
			)
		);
		$this->updateLocaleFields($blog);
	}

	/**
	 * Delete a static page by ID.
	 * @param $staticPageId int
	 */
	function deleteById($blogId) {
		$this->update(
			'DELETE FROM blog WHERE blog_id = ?',
			(int) $blogId
		);
		$this->update(
			'DELETE FROM blog_settings WHERE blog_id = ?',
			(int) $blogId
		);
	}

	/**
	 * Delete a static page object.
	 * @param $staticPage StaticPage
	 */
	function deleteObject($blog) {
		$this->deleteById($blog->getId());
	}

	/**
	 * Generate a new static page object.
	 * @return StaticPage
	 */
	function newDataObject() {
		return new BlogEntry();
	}

	/**
	 * Return a new static pages object from a given row.
	 * @return StaticPage
	 */
	function _fromRow($row) {
		$blog = $this->newDataObject();
		$blog->setId($row['blog_id']);
		$blog->setContextId($row['context_id']);
	    $this->getDataObjectSettings('blog_settings', 'blog_id', $row['blog_id'], $blog);
		return $blog;
	}

	/**
	 * Get the insert ID for the last inserted static page.
	 * @return int
	 */
	function getInsertId() {
		return $this->_getInsertId('blog', 'blog_id');
	}

	/**
	 * Get field names for which data is localized.
	 * @return array
	 */
	function getLocaleFieldNames() {
		return array('title', 'content');
	}

	/**
	 * Update the localized data for this object
	 * @param $author object
	 */
	function updateLocaleFields(&$staticPage) {
		$this->updateDataObjectSettings('blog_settings', $blogEntry, array(
			'blog_id' => $blogEntry->getId()
		));
	}

	/**
	 * Get blog entry posted datetime.
	 * @return datetime (YYYY-MM-DD HH:MM:SS)
	 */
	function getDatetimePosted() {
		return $this->getData('datePosted');
	}

	/**
	 * Set blog entry posted datetime.
	 * @param $datetimePosted date (YYYY-MM-DD HH:MM:SS)
	 */
	function setDatetimePosted($datetimePosted) {
		$this->setData('datePosted', $datetimePosted);
	}




}

