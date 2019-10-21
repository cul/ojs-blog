{**
 * templates/content.tpl
 *
 * Display blog content
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$title}

<div class="container page-blog">
	<div class="row page-header justify-content-md-center">
		<div class="col-md-8">
			<h1>{$title}</h1>
		</div>
	</div>
<div class="container page-blog">


	<form method="get">
	<div class="row page-header justify-content-md-center">
		<div class="col-md-8">
			Filter by keyword: 
		<select name="keyword" onchange='this.form.submit()'>
		{foreach from=$keywords item=word }
			<option value="{$word}" {if $word == $currentKeyword}selected{/if}>{$word}</option>
		{/foreach}
		</select> 
	</form>
		</div>
	</div>
</div>	
	<div class="row justify-content-md-center">
		<div class="col-md-8">
			<div class="page-content">

	{foreach from=$entries item=entry}        

 <article class="blog-summary">
	<h2>
		<a class="btn" href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="view" path=$entry->getId()}">
			{$entry->getTitle()}
		</a>
	</h2>
	<div>
		{if null !== $entry->getByline() }By{/if}  {$entry->getByline()}
	</div>
	<div class="blog-summary-date">
		{$entry->getDatePosted()|date_format:"%B %e, %Y"}
	</div>
	<div class="blog-summary-description">

		{$entry->getAbbreviatedContent()|strip_unsafe_html}...
		<div class="blog-summary-more">
			<a class="btn" href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="view" path=$entry->getId()}">
				<span aria-hidden="true">Read More</span>
			</a>
		</div>
	</div>
	<div class="row justify-content-md-center blog-summary-tags">
		<div class="col-md-8">
			<article class="page-content">
		{assign var=entry_keywords value=$entry->getKeywords()}
		{foreach from=$entry_keywords item=word }
						<a class="btn" href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="index" args="$word"}">{$word}</a>
		{/foreach}
		</article>
		</div>
	</div>	
</article>      	    				

{/foreach}
			</div>
		</div>
	</div>
</div>


{include file="frontend/components/footer.tpl"}


