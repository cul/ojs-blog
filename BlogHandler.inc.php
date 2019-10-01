<?php

/**
 * @file blogHandler.inc.php
 *
 * @package plugins.generic.blog
 * @class blogHandler
 * Find blog content and display it when requested.
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
		$templateMgr = TemplateManager::getManager($request);
		$context = $request->getContext();
		$contextId = $context?$context->getId():CONTEXT_ID_NONE;

		$blogEntryDao = DAORegistry::getDAO('BlogEntryDAO');
		$blogKeywordDao = DAORegistry::getDAO('BlogKeywordDAO');
		$blogEntries = $blogEntryDao->getByContextId($context->getId())->toArray();
//		$blogKeywords = $blogKeywordDAO->getBlogKeywordsByContext($context->getId());
		$templateMgr->assign('entries', $blogEntries);
//		$templateMgr->assign('keywords', $blogKeywords);
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
		$blogEntry = $blogEntryDao->getById($id);
		$templateMgr->assign('entry', $blogEntry);
		$templateMgr->display(self::$plugin->getTemplateResource('entry.tpl'));
	}
}

