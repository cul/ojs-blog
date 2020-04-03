<?php

/**
 * @file controllers/grid/BlogGridHandler.inc.php
 *
 * @class BlogGridHandler
 * @ingroup controllers_grid_blog
 *
 * @brief Handle blog entry grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('plugins.generic.blog.controllers.grid.BlogGridRow');
import('plugins.generic.blog.controllers.grid.BlogGridCellProvider');

class BlogGridHandler extends GridHandler {
	/** @var BlogPlugin The blog plugin */
	static $plugin;

	/**
	 * Set the blog plugin.
	 * @param $plugin BlogPlugin
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
			array('index', 'fetchGrid', 'fetchRow', 'addBlogEntry', 'editBlogEntry', 'updateBlogEntry', 'delete')
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
		$blogEntryDao = DAORegistry::getDAO('BlogEntryDAO');
		$this->setGridDataElements($blogEntryDao->getEntriesByContextId($context->getId()));

		// Add grid-level actions
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$this->addAction(
			new LinkAction(
				'addBlogEntry',
				new AjaxModal(
					$router->url($request, null, null, 'addBlogEntry'),
					__('plugins.generic.blog.addBlogEntry'),
					'modal_add_item'
				),
				__('plugins.generic.blog.addBlogEntry'),
				'add_item'
			)
		);
		

		// Columns
		$cellProvider = new BlogGridCellProvider();
		$this->addColumn(new GridColumn(
			'title',
			'plugins.generic.blog.pageTitle',
			null,
			'controllers/grid/gridCell.tpl', 
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
		return new BlogGridRow();
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
	 * An action to add a new custom blog entry
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest Request object
	 */
	function addBlogEntry($args, $request) {
		// Calling editBlogEntry with an empty ID will add
		// a new blog entry
		return $this->editBlogEntry($args, $request);
	}

	/**
	 * An action to edit a blog entry
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest Request object
	 * @return string Serialized JSON object
	 */
	function editBlogEntry($args, $request) {
		$blogEntryId = $request->getUserVar('blogEntryId');
		$context = $request->getContext();
		$this->setupTemplate($request);

		// Create and present the edit form
		import('plugins.generic.blog.controllers.grid.form.BlogEntryForm');
		$blogPlugin = self::$plugin;
		$blogEntryForm = new BlogEntryForm(self::$plugin, $context->getId(), $blogEntryId);
		$blogEntryForm->initData();
		return new JSONMessage(true, $blogEntryForm->fetch($request));
	}

	/**
	 * Update a custom block
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function updateBlogEntry($args, $request) {
		$blogEntryId = $request->getUserVar('blogEntryId');
		$context = $request->getContext();
		$this->setupTemplate($request);

		// Create and populate the form
		import('plugins.generic.blog.controllers.grid.form.BlogEntryForm');
		$blogPlugin = self::$plugin;
		$blogEntryForm = new BlogEntryForm(self::$plugin, $context->getId(), $blogEntryId);
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
		$blogEntryId = $request->getUserVar('blogEntryId');
		$context = $request->getContext();

		// Delete the blog entry
		$blogEntryDao = DAORegistry::getDAO('BlogEntryDAO');
		$blogEntry = $blogEntryDao->getById($blogEntryId, $context->getId());
		$blogEntryDao->deleteObject($blogEntry);

		return DAO::getDataChangedEvent();
	}


}

