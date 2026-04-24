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
use APP\plugins\generic\blog\classes\BlogEntryDAO;
use APP\plugins\generic\blog\classes\BlogKeywordDAO;

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
		$application = \PKP\core\PKPApplication::get();
		$products = $application->getEnabledProducts('plugins.generic');
		return (isset($products['tinymce']));
	}

	public function register($category, $path, $mainContextId = null)
	{
		if (!parent::register($category, $path, $mainContextId)) {
			return false;
		}

		if (!$this->getEnabled($mainContextId)) {
			return true;
		}

		DAORegistry::registerDAO('BlogEntryDAO', new BlogEntryDAO());
		DAORegistry::registerDAO('BlogKeywordDAO', new BlogKeywordDAO());

		// Hook::add('Template::Settings::website', [$this, 'callbackShowWebsiteSettingsTabs']);
		// Hook::add('LoadHandler', [$this, 'callbackHandleContent']);
		// Hook::add('LoadComponentHandler', [$this, 'setupGridHandler']);
		Hook::add('Template::Settings::website', $this->callbackShowWebsiteSettingsTabs(...));
		Hook::add('LoadHandler', $this->callbackHandleContent(...));
		Hook::add('LoadComponentHandler', $this->setupGridHandler(...));

		return true;
	}

	public function callbackShowWebsiteSettingsTabs($hookName, $args)
	{
error_log('BLOG TAB HOOK FIRED');
		$templateMgr = $args[1];
		$output = &$args[2];

		$request = Application::get()->getRequest();

		$output .= $templateMgr->fetch(
			$this->getTemplateResource('blogTab.tpl')
		);

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
	public function setupGridHandler($hookName, $params)
	{
		$component = &$params[0];
		$componentInstance = &$params[2];
		
		if ($component == 'plugins.generic.blog.controllers.grid.BlogGridHandler') {
			$componentInstance = new \APP\plugins\generic\blog\controllers\grid\BlogGridHandler($this);
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
					new RedirectAction(
						$dispatcher->url(
							$request,
							\PKP\core\PKPApplication::ROUTE_PAGE,
							null,
							'management',
							'settings',
							['website'],
							['uid' => uniqid()],
							'blog'
						)
					),
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
