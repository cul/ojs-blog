<?php

/**
 * @file BlogHandler.inc.php
 *
 * @package plugins.generic.blog
 * @class BlogHandler
 * Find blog entries and display them when requested.
 */

import('classes.handler.Handler');


class BlogHandler extends Handler {
	/** @var blogPlugin The blog plugin */
	static $plugin;

	/** @var blogEntry, the entry to view */
	static $blogEntry;


	/**
	 * Provide the blog plugin to the handler.
	 * @param $plugin blogPlugin
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	/**
	 * Set a blog entry to view.
	 * @param $blogEntry BlogEntry
	 */
	static function setEntry($blogEntry) {
		self::$blogEntry = $blogEntry;
	}

	/**
	 * Handle index request
	 * @param $args array Arguments array.
	 * @param $request PKPRequest Request object.
	 */
	function index($args, $request) {
		$keyword = null;
		if(isset($_GET['keyword'])){
			$keyword=$_GET['keyword'];
		}
                $year = null;
                if(isset($_GET['year'])){
                        $year=$_GET['year'];
                }


		$templateMgr = TemplateManager::getManager($request);
		$context = $request->getContext();
		$contextId = $context?$context->getId():CONTEXT_ID_NONE;
		$blogEntryDao = DAORegistry::getDAO('BlogEntryDAO');
		$blogKeywordDao = DAORegistry::getDAO('BlogKeywordDAO');

		$page = isset($args[0]) ? (int) $args[0] : 1;
		$count = 10;
		$offset = $page > 1 ? ($page - 1) * $count : 0;
		$paging_params = array(
			'offset' => $offset,
			'count' => $count
		);

		$blogEntries = $blogEntryDao->getEntriesByContextId($contextId, $keyword, $year, $paging_params)->toArray();
		$years = $blogEntryDao->getEntryYears($contextId, $keyword);
		$blogKeywords = $blogKeywordDao->getBlogKeywords($contextId, $year);

		$total = $blogEntryDao->getCountByContextId($contextId, $keyword, $year); 
		$showingStart = $offset + 1;
		$showingEnd = min($offset + $count, $offset + count($blogEntries));
		$nextPage = $total > $showingEnd ? $page + 1 : null;
		$prevPage = $showingStart > 1 ? $page - 1 : null;
		
		$templateMgr->assign(array(
			'showingStart' => $showingStart,
			'showingEnd' => $showingEnd,
			'total' => $total,
			'nextPage' => $nextPage,
			'prevPage' => $prevPage,
		));

		$templateMgr->assign('entries', $blogEntries);
		$templateMgr->assign('keywords', $blogKeywords);
		if($keyword){
			$templateMgr->assign('currentKeyword', $keyword);
		}
                $templateMgr->assign('years', $years);
                if($year){
                        $templateMgr->assign('selectedYear', $year);
                }

		$templateMgr->display(self::$plugin->getTemplateResource('index.tpl'));
	}

	/**
	 * Handle view entry request 
	 * @param $args array Arguments array.
	 * @param $request PKPRequest Request object.
	 */
	function view($args, $request) {
		$templateMgr = TemplateManager::getManager($request);
		$id = $args[0];
		$blogEntryDao = DAORegistry::getDAO('BlogEntryDAO');
		$blogKeywordDao = DAORegistry::getDAO('BlogKeywordDAO');
		$blogEntry = $blogEntryDao->getById($id);
		$templateMgr->assign('entry', $blogEntry);
		$templateMgr->assign('keywords', $blogEntry->getKeywords());
		$templateMgr->display(self::$plugin->getTemplateResource('entry.tpl'));
	}
}

