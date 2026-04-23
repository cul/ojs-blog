<?php

/**
 * @file BlogPlugin.php
 *
 * @package plugins.generic.blog
 * @class BlogPlugin
 * Blog plugin main class
 */

namespace APP\plugins\generic\blog;

use PKP\plugins\GenericPlugin;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\RedirectAction;
use APP\core\Application;
use PKP\core\Registry;
use PKP\plugins\Hook;
use PKP\db\DAORegistry;

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
				$blogEntryDao = new BlogEntryDAO();
				$blogKeywordDao = new BlogKeywordDAO();
				DAORegistry::registerDAO('BlogEntryDAO', $blogEntryDao);
				DAORegistry::registerDAO('BlogKeywordDAO', $blogKeywordDao);


				Hook::add('Template::Settings::website', [$this, 'callbackShowWebsiteSettingsTabs']);
				// Intercept the LoadHandler hook to present
				// blog entries when requested.
				Hook::add('LoadHandler', array($this, 'callbackHandleContent'));

				// Register the components this plugin implements to
				// permit administration of blog entries
				Hook::add('LoadComponentHandler', array($this, 'setupGridHandler'));
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

                $output .= $templateMgr->fetch($this->getTemplateResource('blogTab.tpl'));

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
        	$args[3] = 'APP\plugins\generic\blog\BlogHandler';
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
		return array_merge(
			$this->getEnabled() ? array(
				new LinkAction(
					'settings',
					new RedirectAction($dispatcher->url(
						$request,
						\PKP\core\PKPApplication::ROUTE_PAGE,
						null,
						'management',
						'settings',
						'website',
						['uid' => uniqid()],
						'blog'
					)),
					__('plugins.generic.blog.editAddContent'),
					null
				),
			) : array(),
			parent::getActions($request, $actionArgs)
		);
	}
	/**
	 * Get the JavaScript URL for this plugin.
	 */
	function getJavaScriptURL($request) {
		return $request->getBaseUrl() . '/' . $this->getPluginPath() . '/js';
	}
}
