{**
 * templates/content.tpl
 *
 * Display blog content
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$title}

<div class="container page-announcement">
	<div class="row page-header justify-content-md-center">
		<div class="col-md-8">
			<h1>{$title}</h1>
		</div>
	</div>
<div class="container page-announcement">


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
        
{foreach $entries as $entry}

 <article class="announcement-summary">
	<h2>
		<a class="btn" href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="view" path=$entry->getId()}">
			{$entry->getTitle()}
		</a>
	</h2>
	<div>
		By {$entry->getByline()}
	</div>
	<div class="announcement-summary-date">
		{$entry->getDatePosted()|date_format:"F jS, Y"}
	</div>
	<div class="announcement-summary-description">

		{$entry->getAbbreviatedContent()|strip_unsafe_html}...
		<div class="announcement-summary-more">
			<a class="btn" href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="view" path=$entry->getId()}">
				<span aria-hidden="true">Read More</span>
			</a>
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="col-md-8">
			<article class="page-content">
		{$keywords = $entry->getKeywords()}
		{foreach from=$keywords item=word }
						<a class="btn" href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="index" path="$word"}">{$word}</a>
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


