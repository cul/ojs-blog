<?php

/**
 * @file controllers/grid/form/BlogEntryForm.inc.php
 *
 *
 * @class BlogEntryForm
 * @ingroup controllers_grid_blog
 *
 * Form for press managers to create and modify sidebar blocks
 *
 */

import('lib.pkp.classes.form.Form');

class BlogEntryForm extends Form {
	/** @var int Context (press / journal) ID */
	var $contextId;

	/** @var string blog entry id */
	var $blogEntryId;

	/** @var BlogPlugin plugin */
	var $plugin;

	/**
	 * Constructor
	 * @param $BlogPlugin BlogPlugin 
	 * @param $contextId int Context ID
	 * @param $blogEntryId int blog entry ID (if any)
	 */
	function __construct($blogPlugin, $contextId, $blogEntryId = null) {
		//3.1.2?
		//parent::__construct($blogPlugin->getTemplatePath() . ‘templates/editBlogEntryForm.tpl');
		parent::__construct($blogPlugin->getTemplateResource('editBlogEntryForm.tpl'));

		$this->contextId = $contextId;
		$this->blogEntryId = $blogEntryId;
		$this->plugin = $blogPlugin;

		// Add form checks
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
		$this->addCheck(new FormValidator($this, 'title', 'required', 'plugins.generic.blog.nameRequired'));
		$this->addCheck(new FormValidator($this, 'content', 'required', 'plugins.generic.blog.nameRequired'));
		$form = $this;
	}

	/**
	 * Initialize form data from current group.
	 */
	function initData() {
		$templateMgr = TemplateManager::getManager();
		if ($this->blogEntryId) {
			$blogEntryDao = DAORegistry::getDAO('BlogEntryDAO');
			$blogKeywordDao = DAORegistry::getDAO('BlogKeywordDAO');
			$blogEntry = $blogEntryDao->getById($this->blogEntryId);
			$this->setData('title', $blogEntry->getTitle());
			$this->setData('content', $blogEntry->getContent());
			$this->setData('byline', $blogEntry->getByline());
			$this->setData('keywords', $blogKeywordDao->getKeywordsByEntryId($this->blogEntryId));
		}
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('title', 'content', 'byline', 'keywords'));
	}

	/**
	 * @copydoc Form::fetch
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager();
		$templateMgr->assign(array(
			'blogEntryId' => $this->blogEntryId,
			'pluginJavaScriptURL' => $this->plugin->getJavaScriptURL($request),
		));
		return parent::fetch($request, $template, $display);
	}

	/**
	 * Save form values into the database
	 */
	function execute() {
		$blogEntryDao = DAORegistry::getDAO('BlogEntryDAO');
		$blogKeywordDao = DAORegistry::getDAO('BlogKeywordDAO');

		if ($this->blogEntryId) {
			// Load and update an existing entry
			$blogEntry = $blogEntryDao->getById($this->blogEntryId, $this->contextId);
		} else {
			// Create a new blog entry
			$blogEntry = $blogEntryDao->newDataObject();
			$blogEntry->setContextId($this->contextId);
		}
		$blogEntry->setTitle($this->getData('title'));
		$blogEntry->setContent($this->getData('content'));
		$blogEntry->setByline($this->getData('byline'));

		if ($this->blogEntryId) {
			$blogEntryDao->updateObject($blogEntry);
		} else {
			$this->blogEntryId = $blogEntryDao->insertObject($blogEntry);
		}

		$blogKeywordDao->setKeywordsByEntryId($this->blogEntryId, $this->getData('keywords'));

	}
}

