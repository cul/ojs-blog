{**
 * templates/content.tpl
 *
 * Display blog content
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$title}

<article class="container page-announcement">
	<div class="row page-header justify-content-md-center">
		<div class="col-md-8">
		    <div class="announcement-date">
		      {$entry->getDatePosted()|date_format:"F jS, Y"}
		    </div>
			<h1>{$entry->getTitle()}</h1>
		</div>
		<div>
		By {$entry->getByline()}
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="col-md-8">
			<article class="page-content">
			{$entry->getContent()}
    		</article>
		</div>
	</div>
	<div class="row justify-content-md-center">
		<div class="col-md-8">
			<article class="page-content">
		{foreach from=$keywords item=word }
						<a class="btn" href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="index" path="$word"}">{$word}</a>
		{/foreach}
		</article>
		</div>
	</div>
</article>

{include file="frontend/components/footer.tpl"}
