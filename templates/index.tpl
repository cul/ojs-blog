{**
 * templates/content.tpl
 *
 * Display blog content
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$title}

<h2>{$title|escape}</h2>
<div class="page">
<ul>
{foreach $entries as $entry}
 		<a href="blog/view/{$entry->getId()}">{$entry->getTitle()}</a>
   	<div class="date">
		{$entry->getDatePosted()|date_format:$dateFormatShort}
	</div>
		<div class="summary">
		{$entry->getAbbreviatedContent()|strip_unsafe_html}...
		<a href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="view" path=$entry->getId()}" class="read_more">
			<span aria-hidden="true" role="presentation">
				{translate key="common.readMore"}
			</span>
	</div>
{/foreach}
</ul>
</div>

{include file="frontend/components/footer.tpl"}


