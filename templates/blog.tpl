{**
 * templates/blog.tpl
 *
 *
 * Blog plugin -- displays the blogGrid.
 *}
{capture assign=blogGridUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.blog.controllers.grid.blogGridHandler" op="fetchGrid" escape=false}{/capture}
{load_url_in_div id="blogGridContainer" url=$blogGridUrl}
