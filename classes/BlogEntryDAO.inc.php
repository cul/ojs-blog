<?php

/**
 * @file classes/BlogEntryDAO.inc.php
 *
 *
 * @package plugins.generic.blog
 * @class BlogEntryDAO
 * Operations for retrieving and modifying blog objects.
 */

import('lib.pkp.classes.db.DAO');
import('plugins.generic.blog.classes.BlogEntry');
import('plugins.generic.blog.classes.BlogKeywordDAO');

class BlogEntryDAO extends DAO {

	/**
	 * Get a blog entry by ID
	 * @param $blogEntryId int blog ID
	 */
	function getById($blogEntryId) {
		$params = array((int) $blogEntryId);
		$sql = 'SELECT * FROM blog_entries WHERE entry_id = ?';
		$result = $this->retrieve($sql, $params);
                
		$resultFactory = new DAOResultFactory($result, $this, '_fromRow', [], $sql, $params);
		$returner = null;
                if ($resultFactory->getCount() != 0) {
                    $returner = $resultFactory->next();
                }

		return $returner;
	}

	/**
	 * Get a set of blog entries by context ID
	 * @param $contextId int
	 * @return DAOResultFactory 
	 */
	function getEntriesByContextId($contextId, $keyword = null, $year = null, $paging_params = null) {
		$params = array((int) $contextId);
		if ($keyword) $params[] = $keyword;
		if ($year) $params[] = $year;
		$paging_sql = '';
		if ($paging_params) $paging_sql = 'limit '.$paging_params['offset'].', '.$paging_params['count'];
		$sql = 'SELECT distinct e.* FROM blog_entries e'
			. ($keyword?', blog_keywords k, blog_entries_keywords b':'')
			. ' WHERE e.context_id = ? '
			. ($keyword?' AND e.entry_id=b.entry_id AND k.keyword_id=b.keyword_id AND k.keyword = ?':'')
                        . ($year?' AND year(e.date_posted) = ? ':'')
			.' order by e.date_posted desc '
			.$paging_sql;
		$result = $this->retrieve($sql, $params);

		return new DAOResultFactory($result, $this, '_fromRow', [], $sql, $params);
	}


	function getEntryYears($contextId, $keyword) {
                $params = array((int) $contextId);
                if ($keyword) $params[] = $keyword;		
		$sql = 'SELECT distinct year(e.date_posted) as year FROM blog_entries e '
			 . ($keyword?', blog_keywords k, blog_entries_keywords b':'')
			. ' WHERE e.context_id = ? ' 
                        . ($keyword?' AND e.entry_id=b.entry_id AND k.keyword_id=b.keyword_id AND k.keyword = ?':'')
			.' order by year desc';
                $result = $this->retrieve($sql, $params);
                $resultFactory = new DAOResultFactory($result, $this, '_getYear');
                $years = $resultFactory->toArray();
                return $years;
	}


	/**
	 * Insert a blog entry.
	 * @param $blogEntry BlogEntry
	 * @return int Inserted blogEntryID
	 */
	function insertObject($blogEntry) {
		$valArray = [(int) $blogEntry->getContextId(), $blogEntry->getTitle(), $blogEntry->getContent(), $blogEntry->getByline(), $blogEntry->getDatePosted()];

		$this->update(
		   'INSERT INTO blog_entries (context_id, title, content, byline, date_posted) VALUES (?,?,?,?,?)',
			$valArray
		);

		$blogEntry->setId($this->getInsertId());

		return $blogEntry->getId();
	}

	/**
	 * Update the database with a blogEntry object
	 * @param $blogEntry BlogEntry
	 */
	function updateObject($blogEntry) {
		$this->update(
			'UPDATE	blog_entries
			SET	context_id = ?, title = ?, content = ?, byline = ?, date_posted = ?  
			WHERE	entry_id = ?',
			array(
				(int) $blogEntry->getContextId(),
				$blogEntry->getTitle(),
				$blogEntry->getContent(),
				$blogEntry->getByline(),
				$blogEntry->getDatePosted(),
				(int) $blogEntry->getId()
			)
		);
	}

	/**
	 * Delete blog entry by ID.
	 * @param $blogEntry int
	 */
	function deleteById($entryId) {
		$this->update(
			'DELETE FROM blog_entries WHERE entry_id = ?',
			[(int) $entryId]
		);
	}

	/**
	 * Delete a blog entry object.
	 * @param $blogEntry BlogEntry
	 */
	function deleteObject($blogEntry) {
		$this->deleteById($blogEntry->getId());
	}

	/**
	 * Generate a new blog entry object.
	 * @return blogEntry
	 */
	function newDataObject() {
		return new BlogEntry();
	}


	function _getCount($row){
	  return $row['COUNT(*)'];
	}


	function _getYear($row){
	  return $row['year'];
	}

	/**
	 * Return a new blog entry object from a given row.
	 * @return blogEntry
	 */
	function _fromRow($row) {
		$blogEntry = $this->newDataObject();
		$blogEntry->setId($row['entry_id']);
		$blogEntry->setContextId($row['context_id']);
		$blogEntry->setTitle($row['title']);
		$blogEntry->setContent($row['content']);
		$blogEntry->setByline($row['byline']);
		$blogEntry->setDatePosted($row['date_posted']);
		$blogKeywordDao = DAORegistry::getDAO('BlogKeywordDAO');
		$entryKeywords = $blogKeywordDao->getKeywordsByEntryId($blogEntry->getId());		
		$blogEntry->setKeywords($entryKeywords);		
		return $blogEntry;
	}

	/**
	 * Get the insert ID for the last inserted blog entry.
	 * @return int
	 */
	function getInsertId() {
		return $this->_getInsertId('blog_entries', 'entry_id');
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

	function getCountByContextId($contextId, $keyword, $year){	
		$params = array((int) $contextId);
		if ($keyword) $params[] = $keyword;
                if ($year) $params[] = $year;
		$sql = 'SELECT COUNT(*) FROM blog_entries e '
                        . ($keyword?', blog_keywords k, blog_entries_keywords b ':'')
                        . ' WHERE e.context_id = ? '
                        . ($keyword?' AND e.entry_id=b.entry_id AND k.keyword_id=b.keyword_id AND k.keyword = ? ':'')
			. ($year?' AND year(e.date_posted) = ? ':'');
		$result = $this->retrieve($sql, $params);
                $resultFactory = new DAOResultFactory($result, $this, '_getCount', [], $sql, $params);
		$returner = $resultFactory->next();
		return $returner;
		
	}


}

