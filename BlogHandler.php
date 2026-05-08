<?php

/**
 * @file BlogHandler.php
 *
 * @package plugins.generic.blog
 * @class BlogHandler
 * Find blog entries and display them when requested.
 */

namespace APP\plugins\generic\blog;

use APP\core\Request;
use PKP\handler\PKPHandler;
use PKP\db\DAORegistry;
use APP\template\TemplateManager;
use PKP\core\PKPApplication;

class BlogHandler extends PKPHandler {
	/** @var blogPlugin The blog plugin */
	static protected $plugin;

	/**
	 * Provide the blog plugin to the handler.
	 * @param $plugin blogPlugin
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	/**
	 * Handle index request
	 * @param $args array Arguments array.
	 * @param $request PKPRequest Request object.
	 */
	function index($args, $request) {
		$keyword = $request->getUserVar('keyword');
		$year    = $request->getUserVar('year');

		$templateMgr = TemplateManager::getManager($request);
		$context = $request->getContext();
		$contextId = $context ? $context->getId() : PKPApplication::CONTEXT_ID_NONE;
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

		$templateMgr->display($this->plugin->getTemplateResource('index.tpl'));
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

