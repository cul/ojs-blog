{**
 * templates/blog.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Static pages plugin -- displays the blogGrid.
 *}
{capture assign=staticPageGridUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.blog.controllers.grid.StaticPageGridHandler" op="fetchGrid" escape=false}{/capture}
{load_url_in_div id="staticPageGridContainer" url=$staticPageGridUrl}
