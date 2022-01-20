{**
 * templates/blogTab.tpl
 *
 *
 * blog plugin -- displays the blogGrid.
*}

<tab id="blog" label="{translate key="plugins.generic.blog.blog"}">
{capture assign=blogGridUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.blog.controllers.grid.BlogGridHandler" op="fetchGrid" escape=false}{/capture}
	{load_url_in_div id="blogGridContainer" url=$blogGridUrl}
</tab>

