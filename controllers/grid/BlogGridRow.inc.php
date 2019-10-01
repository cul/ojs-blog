<?php

/**
 * @file controllers/grid/BlogGridRow.inc.php
 *
 * @class BlogGridRow
 * @ingroup controllers_grid_blog
 *
 * @brief Handle custom blocks grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridRow');

class BlogGridRow extends GridRow {

	//
	// Overridden template methods
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null) {
		parent::initialize($request, $template);

		$blogEntryId = $this->getId();
		if (!empty($blogEntryId)) {
			$router = $request->getRouter();

			// Create the "edit blog entry" action
			import('lib.pkp.classes.linkAction.request.AjaxModal');
			$this->addAction(
				new LinkAction(
					'editBlogEntry',
					new AjaxModal(
						$router->url($request, null, null, 'editBlogEntry', null, array('blogEntryId' => $blogEntryId)),
						__('grid.action.edit'),
						'modal_edit',
						true),
					__('grid.action.edit'),
					'edit'
				)
			);

			// Create the "delete blog entry" action
			import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
			$this->addAction(
				new LinkAction(
					'delete',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('common.confirmDelete'),
						__('grid.action.delete'),
						$router->url($request, null, null, 'delete', null, array('blogEntryId' => $blogEntryId)), 'modal_delete'
					),
					__('grid.action.delete'),
					'delete'
				)
			);
		}
	}
}

