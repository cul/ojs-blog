<?php

/**
 * @file controllers/grid/BlogGridCellProvider.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class BlogGRidCellProvider
 * @ingroup controllers_grid_blog
 *
 * @brief Class for a cell provider to display information about blog entries
 */

import('lib.pkp.classes.controllers.grid.GridCellProvider');
import('lib.pkp.classes.linkAction.request.RedirectAction');

class BlogGridCellProvider extends GridCellProvider {

	//
	// Template methods from GridCellProvider
	//
	/**
	 * Get cell actions associated with this row/column combination
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @param $position int GRID_ACTION_POSITION_...
	 * @return array an array of LinkAction instances
	 */
	function getCellActions($request, $row, $column, $position = GRID_ACTION_POSITION_DEFAULT) {
		$blogEntry = $row->getData();

		switch ($column->getId()) {
			default:
				return parent::getCellActions($request, $row, $column, $position);
		}
	}

	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @return array
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$blogEntry = $row->getData();
		return array('label' => $blogEntry->getTitle());
	}
}

