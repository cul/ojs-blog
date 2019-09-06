<?php

/**
 * @file controllers/grid/StaticPageGridHandler.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StaticPageGridHandler
 * @ingroup controllers_grid_blog
 *
 * @brief Handle static pages grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('plugins.generic.blog.controllers.grid.StaticPageGridRow');
import('plugins.generic.blog.controllers.grid.StaticPageGridCellProvider');

class StaticPageGridHandler extends GridHandler {
	/** @var blogPlugin The static pages plugin */
	static $plugin;

	/**
	 * Set the static pages plugin.
	 * @param $plugin blogPlugin
	 */
	static function setPlugin($plugin) {
		self::$plugin = $plugin;
	}

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER),
			array('index', 'fetchGrid', 'fetchRow', 'addStaticPage', 'editStaticPage', 'updateStaticPage', 'delete')
		);
	}


	//
	// Overridden template methods
	//
	/**
	 * @copydoc PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		import('lib.pkp.classes.security.authorization.ContextAccessPolicy');
		$this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));
		return parent::authorize($request, $args, $roleAssignments);
	}

	/**
	 * @copydoc GridHandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request, $args);
		$context = $request->getContext();

		// Set the grid details.
		$this->setTitle('plugins.generic.blog.blog');
		$this->setEmptyRowText('plugins.generic.blog.noneCreated');

		// Get the pages and add the data to the grid
		$blogEntryDao = DAORegistry::getDAO('blogEntryDAO');
		$this->setGridDataElements($blogEntryDao->getByContextId($context->getId()));

		// Add grid-level actions
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$this->addAction(
			new LinkAction(
				'addStaticPage',
				new AjaxModal(
					$router->url($request, null, null, 'addStaticPage'),
					__('plugins.generic.blog.addStaticPage'),
					'modal_add_item'
				),
				__('plugins.generic.blog.addStaticPage'),
				'add_item'
			)
		);

		// Columns
		$cellProvider = new StaticPageGridCellProvider();
		$this->addColumn(new GridColumn(
			'title',
			'plugins.generic.blog.pageTitle',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
		$this->addColumn(new GridColumn(
			'path',
			'plugins.generic.blog.path',
			null,
			'controllers/grid/gridCell.tpl', // Default null not supported in OMP 1.1
			$cellProvider
		));
	}

	//
	// Overridden methods from GridHandler
	//
	/**
	 * @copydoc GridHandler::getRowInstance()
	 */
	function getRowInstance() {
		return new StaticPageGridRow();
	}

	//
	// Public Grid Actions
	//
	/**
	 * Display the grid's containing page.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function index($args, $request) {
		$context = $request->getContext();
		import('lib.pkp.classes.form.Form');
		$form = new Form(self::$plugin->getTemplateResource('blog.tpl'));
		return new JSONMessage(true, $form->fetch($request));
	}

	/**
	 * An action to add a new custom static page
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest Request object
	 */
	function addStaticPage($args, $request) {
		// Calling editStaticPage with an empty ID will add
		// a new static page.
		return $this->editStaticPage($args, $request);
	}

	/**
	 * An action to edit a static page
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest Request object
	 * @return string Serialized JSON object
	 */
	function editStaticPage($args, $request) {
		$staticPageId = $request->getUserVar('staticPageId');
		$context = $request->getContext();
		$this->setupTemplate($request);

		// Create and present the edit form
		import('plugins.generic.blog.controllers.grid.form.BlogEntryForm');
		$blogPlugin = self::$plugin;
		$blogEntryForm = new BlogEntryForm(self::$plugin, $context->getId(), $staticPageId);
		$blogEntryForm->initData();
		return new JSONMessage(true, $blogEntryForm->fetch($request));
	}

	/**
	 * Update a custom block
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function updateStaticPage($args, $request) {
		$staticPageId = $request->getUserVar('staticPageId');
		$context = $request->getContext();
		$this->setupTemplate($request);

		// Create and populate the form
		import('plugins.generic.blog.controllers.grid.form.BlogEntryForm');
		$blogPlugin = self::$plugin;
		$blogEntryForm = new BlogEntryForm(self::$plugin, $context->getId(), $staticPageId);
		$blogEntryForm->readInputData();

		// Check the results
		if ($blogEntryForm->validate()) {
			// Save the results
			$blogEntryForm->execute();
 			return DAO::getDataChangedEvent();
		} else {
			// Present any errors
			return new JSONMessage(true, $blogEntryForm->fetch($request));
		}
	}

	/**
	 * Delete a blog entry
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function delete($args, $request) {
		$staticPageId = $request->getUserVar('staticPageId');
		$context = $request->getContext();

		// Delete the static page
		$blogEntryDao = DAORegistry::getDAO('blogEntryDAO');
		$staticPage = $blogEntryDao->getById($staticPageId, $context->getId());
		$blogEntryDao->deleteObject($staticPage);

		return DAO::getDataChangedEvent();
	}
}

