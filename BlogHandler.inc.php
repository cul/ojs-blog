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
		if($_GET['keyword']){
			$keyword=$_GET['keyword'];
		}else{
			$keyword = $args[0];
		}
		$templateMgr = TemplateManager::getManager($request);
		$context = $request->getContext();
		$contextId = $context?$context->getId():CONTEXT_ID_NONE;

		$blogEntryDao = DAORegistry::getDAO('BlogEntryDAO');
		$blogKeywordDao = DAORegistry::getDAO('BlogKeywordDAO');
		$blogEntries = $blogEntryDao->getByContextId($context->getId(), $keyword)->toArray();
		$blogKeywords = $blogKeywordDao->getBlogKeywords($context->getId());
		$templateMgr->assign('entries', $blogEntries);
		$templateMgr->assign('keywords', $blogKeywords);
		if($keyword){
			$templateMgr->assign('currentKeyword', $keyword);
		}
		//3.1.2?
		//$templateMgr->display(self::$plugin->getTemplatePath() . 'templates/index.tpl');
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
	//	$templateMgr->display(self::$plugin->getTemplateResource('entry.tpl'));
		$templateMgr->display(self::$plugin->getTemplatePath() . 'templates/entry.tpl');
	}
}

