<?php

/**
 * @file BlogPlugin.inc.php
 *
 * @package plugins.generic.blog
 * @class BlogPlugin
 * Blog plugin main class
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class BlogPlugin extends GenericPlugin {
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
				// Register the blog entry DAO.
				import('plugins.generic.blog.classes.BlogEntryDAO');
				import('plugins.generic.blog.classes.BlogKeywordDAO');
				$blogEntryDao = new BlogEntryDAO();
				$blogKeywordDao = new BlogKeywordDAO();
				DAORegistry::registerDAO('BlogEntryDAO', $blogEntryDao);
				DAORegistry::registerDAO('BlogKeywordDAO', $blogKeywordDao);


				HookRegistry::register('Templates::Management::Settings::website', array($this, 'callbackShowWebsiteSettingsTabs'));

				// Intercept the LoadHandler hook to present
				// blog entries when requested.
				HookRegistry::register('LoadHandler', array($this, 'callbackHandleContent'));

				// Register the components this plugin implements to
				// permit administration of blog entries
				HookRegistry::register('LoadComponentHandler', array($this, 'setupGridHandler'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Extend the website settings tabs to include the blog
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function callbackShowWebsiteSettingsTabs($hookName, $args) {
		$templateMgr = $args[1];
		$output =& $args[2];
		$request =& Registry::get('request');
		$dispatcher = $request->getDispatcher();

       $output .= '<li><a name="blog" href="' . $dispatcher->url($request, ROUTE_COMPONENT, null, 'plugins.generic.blog.controllers.grid.BlogGridHandler', 'fetchGrid') . '">' . __('plugins.generic.blog.blog') . '</a></li>';
		

		// Permit other plugins to continue interacting with this hook
		return false;
	}

	/**
	 * Declare the handler function to process the actual entry
	 * @param $hookName string The name of the invoked hook
	 * @param $args array Hook parameters
	 * @return boolean Hook handling status
	 */
	function callbackHandleContent($hookName, $args) {
		$page = $args[0];
		if ($page === 'blog') {
			$this->import('BlogHandler');
			define('HANDLER_CLASS', 'BlogHandler');
			// Allow the blog handler to get the plugin object
			blogHandler::setPlugin($this);
			return true;
		}
		return false;
	}


	/**
	 * Permit requests to the blog entry grid handler
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function setupGridHandler($hookName, $params) {
		$component =& $params[0];
		if ($component == 'plugins.generic.blog.controllers.grid.BlogGridHandler') {
			// Allow the blog handler to get the plugin object
			import($component);
			BlogGridHandler::setPlugin($this);
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
