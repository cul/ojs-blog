{**
 * templates/blog.tpl
 *
 *
 * Blog plugin -- displays the blogGrid.
 *}
{capture assign=blogGridUrl}{url router=\PKP\core\PKPApplication::ROUTE_COMPONENT component="plugins.generic.blog.controllers.grid.BlogGridHandler" op="fetchGrid" escape=false}{/capture}
{load_url_in_div id="blogGridContainer" url=$blogGridUrl}
