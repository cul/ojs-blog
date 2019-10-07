<?php

/**
 * @file classes/blogKeywordDAO.inc.php
 *
 *
 * @package plugins.generic.blog
 * @class blogKeywordDAO
 * Operations for retrieving and modifying blog keyword objects.
 */

import('lib.pkp.classes.db.DAO');

class BlogKeywordDAO extends DAO {

	/**
	 * Get the insert ID for the last inserted blog Keyword.
	 * @return int
	 */
	function getInsertId() {
		return $this->_getInsertId('blog_keywords', 'keyword_id');
	}


	function getKeywordsByEntryId($entryId){
			$kw =[];
			$keywords = [];
			$result = $this->retrieve(
				'SELECT keyword_id FROM blog_entries_keywords WHERE entry_id = ?',
				$entryId
			);
			if ($result->RecordCount() != 0) {
				while (!$result->EOF) {
					$row = $result->GetRowAssoc(false);
					$kidres = $this->retrieve(
						'SELECT keyword FROM blog_keywords WHERE keyword_id = ?',
						$row['keyword_id']
					);
					$kw[] = $kidres->GetRows()[0]['keyword'];
					$result->MoveNext();
				}
			}
			$keywords['en_US'] = $kw;
			return $keywords; 	
	}


	function setKeywordsByEntryId($entryId, $keywords){
		$k = $keywords['keywords'];

		//first clear entries
		$this->update(
		   			'DELETE FROM blog_entries_keywords where entry_id = ?',
					$entryId
				);

		//now update new values
		foreach($k as $keyword){
			$result = $this->retrieve(
				'SELECT keyword_id FROM blog_keywords WHERE keyword = ?',
				$keyword
			);
			if ($result->RecordCount() != 0) {	
				$keywordId = $result->GetRows(1,0)[0]['keyword_id'];
				$valArray = [$entryId, $keywordId];
				$this->update(
		   			'INSERT INTO blog_entries_keywords (entry_id, keyword_id) VALUES (?,?)',
					$valArray
				);
			}else{
				$this->update(
		   			'INSERT INTO blog_keywords (keyword) VALUES (?)',
					$keyword
				);
				$valArray = [$entryId, $this->getInsertId()];
				$this->update(
		   			'INSERT INTO blog_entries_keywords (entry_id, keyword_id) VALUES (?,?)',
					$valArray
				);
			}
		}
	}

	function getBlogKeywords($contextId){
			$kw =[];
			$keywords = [];
			$result = $this->retrieve(
				'SELECT distinct k.keyword as keyword FROM blog_keywords k, blog_entries_keywords b, blog_entries e WHERE e.context_id = ? and k.keyword_id=b.keyword_id and b.entry_id=e.entry_id',
				$contextId
			);
			if ($result->RecordCount() != 0) {
				while (!$result->EOF) {
					$row = $result->GetRowAssoc(false);
					$kw[] = $row['keyword'];
					$result->MoveNext();
				}
			}
			// $keywords['en_US'] = $kw;
			// return $keywords; 	
			return $kw;
	} 

	function getBlogKeywordsAsJSON(){
		$contextId = Application::getApplication()->getRequest()->getContext()->getId();
		$kw = $this->getBlogKeywords($contextId);
	}


}

