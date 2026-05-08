<?php

/**
 * @file BlogPlugin.php
 *
 * @package plugins.generic.blog
 * @class BlogPlugin
 * Blog plugin main class
 */

namespace APP\plugins\generic\blog;

use APP\core\Application;
use PKP\plugins\GenericPlugin;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\RedirectAction;
use APP\template\TemplateManager;
use PKP\core\Registry;
use PKP\plugins\Hook;
use PKP\db\DAORegistry;
use APP\plugins\generic\blog\classes\BlogEntryDAO;
use APP\plugins\generic\blog\classes\BlogKeywordDAO;
use \APP\plugins\generic\blog\controllers\grid\BlogGridHandler;
use \APP\plugins\generic\blog\BlogHandler;

class BlogPlugin extends GenericPlugin {
    
	public function getDisplayName() {
		return __('plugins.generic.blog.displayName');
	}

	public function getDescription() {
		$description = __('plugins.generic.blog.description');
		if (!$this->isTinyMCEInstalled()) {
			$description .= __('plugins.generic.blog.requirement.tinymce');
		}
		return $description;
	}

	public function isTinyMCEInstalled() {
		$application = Application::get();
		$products = $application->getEnabledProducts('plugins.generic');
		return (isset($products['tinymce']));
	}

	public function register($category, $path, $mainContextId = null)
	{
		if (parent::register($category, $path, $mainContextId)) {
			if ($this->getEnabled($mainContextId)) {
				DAORegistry::registerDAO('BlogEntryDAO', new BlogEntryDAO());
				DAORegistry::registerDAO('BlogKeywordDAO', new BlogKeywordDAO());

				Hook::add('Template::Settings::website', $this->callbackShowWebsiteSettingsTabs(...));
				Hook::add('LoadHandler', $this->callbackHandleContent(...));
				Hook::add('LoadComponentHandler', $this->setupGridHandler(...));
			}
			return true;
		}
		return false;
	}

	public function callbackShowWebsiteSettingsTabs($hookName, $args)
	{
		$templateMgr = $args[1];
		$output = &$args[2];
                $request = & Registry::get('request');
                $dispatcher = $request->getDispatcher();

		$output .= $templateMgr->fetch($this->getTemplateResource('blogTab.tpl'));
		return false;
	}

	public function callbackHandleContent($hookName, $args)
	{
		$page = &$args[0];
		$handler = &$args[3];

		if ($page === 'blog') {
			$handler = new \APP\plugins\generic\blog\BlogHandler();
			$handler->setPlugin($this);
			return true;
		}
		return false;
	}

	public function setupGridHandler($hookName, $params)
	{
		$component = &$params[0];
		$componentInstance = &$params[2];
		
		if ($component == 'plugins.generic.blog.controllers.grid.BlogGridHandler') {
			$componentInstance = new \APP\plugins\generic\blog\controllers\grid\BlogGridHandler();
			\APP\plugins\generic\blog\controllers\grid\BlogGridHandler::setPlugin($this);
			return true;
		}
		return false;
	}

	public function getActions($request, $actionArgs) {
		$dispatcher = $request->getDispatcher();
		return array_merge(
			$this->getEnabled() ? [
				new LinkAction(
					'settings',
					new RedirectAction($dispatcher->url(
						$request,
						Application::ROUTE_PAGE,
						null,
						'management',
						'settings',
						['website'],
						['uid' => uniqid()],
						'blog' 
					)),
					__('plugins.generic.blog.editAddContent'),
					null
				),
			] : [],
			parent::getActions($request, $actionArgs)
		);
	}

	/**
     * Get the JavaScript URL for this plugin.
     */
    public function getJavaScriptURL($request)
    {
        return $request->getBaseUrl() . '/' . $this->getPluginPath() . '/js';
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\blog\BlogPlugin', '\BlogPlugin');
}
