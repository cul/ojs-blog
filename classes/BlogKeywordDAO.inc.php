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

	function getKeyword($row){
		return $row['keyword'];
	}

        function getKeywordId($row){
                return $row['keyword_id'];
        }


	function getKeywordsByEntryId($entryId){
			$kw =[];
			$keywords = [];
			$sql = 'SELECT k.keyword FROM blog_entries_keywords b, blog_keywords k WHERE b.entry_id = ? and b.keyword_id=k.keyword_id';
			$params = [$entryId];
			$result = $this->retrieve($sql, $params);

			$resultFactory = new DAOResultFactory($result, $this, 'getKeyword', [], $sql, $params); 			

			if ($resultFactory->getCount() != 0) {
				while (!$resultFactory->eof()) {
					$keyword = $resultFactory->next();
					$kw[] = $keyword;
				}
			}
			$keywords['en_US'] = $kw;
			return $keywords; 	
	}


	function setKeywordsByEntryId($entryId, $keywords){
		$k = $keywords['keywords'];
		if(is_null($k)){
			return;
		}

		//first clear entries
		$this->update(
		   			'DELETE FROM blog_entries_keywords where entry_id = ?',
					[$entryId]
				);

		//now update new values
		foreach($k as $keyword){
			$sql = 'SELECT keyword_id FROM blog_keywords WHERE keyword = ?';
                        $params = [$keyword];
			$result = $this->retrieve(
				$sql,
				$params
			);
                        $resultFactory = new DAOResultFactory($result, $this, 'getKeywordId', [], $sql, $params);
			if ($resultFactory->getCount() != 0) {	
                                $keywordId = $resultFactory->next();
				$valArray = [$entryId, $keywordId];
				$this->update(
		   		  'INSERT INTO blog_entries_keywords (entry_id, keyword_id) VALUES (?,?)',
				  $valArray
				);
			}else{
				$this->update(
		   			'INSERT INTO blog_keywords (keyword) VALUES (?)',
					[$keyword]
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
			$sql = 'SELECT distinct k.keyword as keyword FROM blog_keywords k, blog_entries_keywords b, blog_entries e WHERE e.context_id = ? and k.keyword_id=b.keyword_id and b.entry_id=e.entry_id';
			$params = [$contextId];
                        $result = $this->retrieve(
                                $sql,
                                $params
                        );
                        $resultFactory = new DAOResultFactory($result, $this, 'getKeyword', [], $sql, $params);
                        if ($resultFactory->getCount() != 0) {
                                while (!$resultFactory->eof()) {
                                        $keyword = $resultFactory->next();
                                        $kw[] = $keyword;
                                }
                        }
                        //$keywords['en_US'] = $kw;
			return $kw;
	} 

	function getBlogKeywordsAsJSON(){
		$contextId = Application::getApplication()->getRequest()->getContext()->getId();
		$kw = $this->getBlogKeywords($contextId);
	}


}

