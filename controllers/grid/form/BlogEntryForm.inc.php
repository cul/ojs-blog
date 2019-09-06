<?php

/**
 * @file controllers/grid/form/StaticPageForm.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StaticPageForm
 * @ingroup controllers_grid_blog
 *
 * Form for press managers to create and modify sidebar blocks
 *
 */

import('lib.pkp.classes.form.Form');

class BlogEntryForm extends Form {
	/** @var int Context (press / journal) ID */
	var $contextId;

	/** @var string Static page name */
	var $staticPageId;

	/** @var blogPlugin Static pages plugin */
	var $plugin;

	/**
	 * Constructor
	 * @param $blogPlugin blogPlugin The static page plugin
	 * @param $contextId int Context ID
	 * @param $staticPageId int Static page ID (if any)
	 */
	function __construct($blogPlugin, $contextId, $staticPageId = null) {
		parent::__construct($blogPlugin->getTemplateResource('editBlogEntryForm.tpl'));

		$this->contextId = $contextId;
		$this->staticPageId = $staticPageId;
		$this->plugin = $blogPlugin;

		// Add form checks
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
		$this->addCheck(new FormValidator($this, 'title', 'required', 'plugins.generic.blog.nameRequired'));
		$this->addCheck(new FormValidator($this, 'content', 'required', 'plugins.generic.blog.nameRequired'));
		$form = $this;
	}

	/**
	 * Initialize form data from current group group.
	 */
	function initData() {
		$templateMgr = TemplateManager::getManager();
		if ($this->staticPageId) {
			$blogEntryDao = DAORegistry::getDAO('blogEntryDAO');
			$staticPage = $blogEntryDao->getById($this->staticPageId, $this->contextId);
			$this->setData('title', $staticPage->getTitle(null)); // Localized
			$this->setData('content', $staticPage->getContent(null)); // Localized
		}

	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('title', 'content'));
	}

	/**
	 * @copydoc Form::fetch
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager();
		$templateMgr->assign(array(
			'staticPageId' => $this->staticPageId,
			'pluginJavaScriptURL' => $this->plugin->getJavaScriptURL($request),
		));

		if ($context = $request->getContext()) $templateMgr->assign('allowedVariables', array(
			'contactName' => __('plugins.generic.tinymce.variables.principalContactName', array('value' => $context->getData('contactName'))),
			'contactEmail' => __('plugins.generic.tinymce.variables.principalContactEmail', array('value' => $context->getData('contactEmail'))),
			'supportName' => __('plugins.generic.tinymce.variables.supportContactName', array('value' => $context->getData('supportName'))),
			'supportPhone' => __('plugins.generic.tinymce.variables.supportContactPhone', array('value' => $context->getData('supportPhone'))),
			'supportEmail' => __('plugins.generic.tinymce.variables.supportContactEmail', array('value' => $context->getData('supportEmail'))),
		));

		return parent::fetch($request, $template, $display);
	}

	/**
	 * Save form values into the database
	 */
	function execute() {
		$blogEntryDao = DAORegistry::getDAO('blogEntryDAO');
		if ($this->staticPageId) {
			// Load and update an existing page
			$staticPage = $blogEntryDao->getById($this->staticPageId, $this->contextId);
		} else {
			// Create a new static page
			$staticPage = $blogEntryDao->newDataObject();
			$staticPage->setContextId($this->contextId);
		}

		$staticPage->setTitle($this->getData('title'), null); // Localized
		$staticPage->setContent($this->getData('content'), null); // Localized

		if ($this->staticPageId) {
			$blogEntryDao->updateObject($staticPage);
		} else {
			$blogEntryDao->insertObject($staticPage);
		}
	}
}

