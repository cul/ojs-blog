<?php

/**
 * @file blogPlugin.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.blog
 * @class blogPlugin
 * Static pages plugin main class
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class blogPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.blog.displayName');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		$description = __('plugins.generic.blog.description');
		if (!$this->isTinyMCEInstalled())
			$description .= __('plugins.generic.blog.requirement.tinymce');
		return $description;
	}

	/**
	 * Check whether or not the TinyMCE plugin is installed.
	 * @return boolean True iff TinyMCE is installed.
	 */
	function isTinyMCEInstalled() {
		$application = Application::getApplication();
		$products = $application->getEnabledProducts('plugins.generic');
		return (isset($products['tinymce']));
	}

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null) {
		if (parent::register($category, $path, $mainContextId)) {
			if ($this->getEnabled($mainContextId)) {
				// Register the static pages DAO.
				import('plugins.generic.blog.classes.blogDAO');
				$blogDao = new blogDAO();
				DAORegistry::registerDAO('blogDAO', $blogDao);

				HookRegistry::register('Template::Settings::website', array($this, 'callbackShowWebsiteSettingsTabs'));

				// Intercept the LoadHandler hook to present
				// static pages when requested.
				HookRegistry::register('LoadHandler', array($this, 'callbackHandleContent'));

				// Register the components this plugin implements to
				// permit administration of static pages.
				HookRegistry::register('LoadComponentHandler', array($this, 'setupGridHandler'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Extend the website settings tabs to include static pages
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function callbackShowWebsiteSettingsTabs($hookName, $args) {
		$templateMgr = $args[1];
		$output =& $args[2];
		$request =& Registry::get('request');
		$dispatcher = $request->getDispatcher();

		$output .= $templateMgr->fetch($this->getTemplateResource('blogTab.tpl'));

		// Permit other plugins to continue interacting with this hook
		return false;
	}

	/**
	 * Declare the handler function to process the actual page PATH
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function callbackHandleContent($hookName, $args) {
		$request = Application::get()->getRequest();
		$templateMgr = TemplateManager::getManager($request);

		$page =& $args[0];
		$op =& $args[1];

		$blogDao = DAORegistry::getDAO('blogDAO');
		if ($page == 'pages' && $op == 'preview') {
			// This is a preview request; mock up a static page to display.
			// The handler class ensures that only managers and administrators
			// can do this.
			$staticPage = $blogDao->newDataObject();
			$staticPage->setContent((array) $request->getUserVar('content'), null);
			$staticPage->setTitle((array) $request->getUserVar('title'), null);
		} else {
			// Construct a path to look for
			$path = $page;
			if ($op !== 'index') $path .= "/$op";
			if ($ops = $request->getRequestedArgs()) $path .= '/' . implode('/', $ops);

			// Look for a static page with the given path
			$context = $request->getContext();
			$staticPage = $blogDao->getByPath(
				$context?$context->getId():CONTEXT_ID_NONE,
				$path
			);
		}

		// Check if this is a request for a static page or preview.
		if ($staticPage) {
			// Trick the handler into dealing with it normally
			$page = 'pages';
			$op = 'view';

			// It is -- attach the static pages handler.
			define('HANDLER_CLASS', 'blogHandler');
			$this->import('blogHandler');

			// Allow the static pages page handler to get the plugin object
			blogHandler::setPlugin($this);
			blogHandler::setPage($staticPage);
			return true;
		}
		return false;
	}

	/**
	 * Permit requests to the static pages grid handler
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function setupGridHandler($hookName, $params) {
		$component =& $params[0];
		if ($component == 'plugins.generic.blog.controllers.grid.StaticPageGridHandler') {
			// Allow the static page grid handler to get the plugin object
			import($component);
			StaticPageGridHandler::setPlugin($this);
			return true;
		}
		return false;
	}

	/**
	 * @copydoc Plugin::getActions()
	 */
	function getActions($request, $actionArgs) {
		$dispatcher = $request->getDispatcher();
		import('lib.pkp.classes.linkAction.request.RedirectAction');
		return array_merge(
			$this->getEnabled()?array(
				new LinkAction(
					'settings',
					new RedirectAction($dispatcher->url(
						$request, ROUTE_PAGE,
						null, 'management', 'settings', 'website',
						array('uid' => uniqid()), // Force reload
						'blog' // Anchor for tab
					)),
					__('plugins.generic.blog.editAddContent'),
					null
				),
			):array(),
			parent::getActions($request, $actionArgs)
		);
	}

	/**
	 * Get the filename of the ADODB schema for this plugin.
	 * @return string Full path and filename to schema descriptor.
	 */
	function getInstallSchemaFile() {
		return $this->getPluginPath() . '/schema.xml';
	}

	/**
	 * Get the JavaScript URL for this plugin.
	 */
	function getJavaScriptURL($request) {
		return $request->getBaseUrl() . '/' . $this->getPluginPath() . '/js';
	}
}
