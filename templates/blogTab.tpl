{**
 * plugins/generic/blog/templates/blogTab.tpl
 *
 * Copyright (c) 2026 Your Name/Organization
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * blog plugin -- displays the blogGrid.
 *}
<tab id="blog" label="{translate key="plugins.generic.blog.blog"}">
    {capture assign=blogGridUrl}{url router=\PKP\core\PKPApplication::ROUTE_COMPONENT component="plugins.generic.blog.controllers.grid.BlogGridHandler" op="fetchGrid" escape=false}{/capture}
    {load_url_in_div id="blogGridContainer" url=$blogGridUrl}
</tab>