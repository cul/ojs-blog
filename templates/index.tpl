{**
 * templates/content.tpl
 *
 * Display blog content
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$title}

	<main class="page-blog col-md-12">
	
		<header class="page-header">
			<h1>{$title}</h1>
			<div class="blog-description">
				 <!-- description can go here -->
			</div>
		</header>

               <form method="get">
                {if $keywords|@count > 0}
                <span class="filter-box"  style="border: none;">
                                Filter by keyword:
                                <select name="keyword" onchange='this.form.submit()'>
                                        <option></option>
                                        {foreach from=$keywords item=word }
                                                <option value="{$word}" {if $word == $currentKeyword}selected{/if}>{$word}</option>
                                        {/foreach}
                                </select>

                </span>
                {/if}
                {if $years|@count > 0}
               <span class="filter-box"  style="border: none;">
                                Filter by year:
                                <select name="year" onchange='this.form.submit()'>
                                        <option></option>
                                        {foreach from=$years item=year }
                                                <option value="{$year}" {if $year == $selectedYear}selected{/if}>{$year}</option>
                                        {/foreach}
                                </select>
                </span>		
		{/if}
		</form>
		
		{foreach from=$entries item=entry}        
		
			<article class="blog-summary">
			
				<h2>
					<a href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="view" path=$entry->getId()}">
						{$entry->getTitle()}
					</a>
				</h2>
				
				<div class="blog-summary-date">
					{$entry->getDatePosted()|date_format:"%B %e, %Y"}
				</div>
				<div class="blog-summary-byline">
					{if null !== $entry->getByline() }| By{/if}  {$entry->getByline()}
				</div>
				
				<div class="blog-summary-description">
					{$entry->getAbbreviatedContent()|strip_unsafe_html}
					<div class="blog-summary-more">
						<a href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="view" path=$entry->getId()}">
							<span aria-hidden="true">Read More</span>
						</a>
					</div>
				</div>
				
				<div class="blog-summary-tags">
					<article class="page-content">
						{assign var=entry_keywords value=$entry->getKeywords()}
						{foreach from=$entry_keywords item=word }
							<a href="{url router=$smarty.const.ROUTE_PAGE page="blog" op="index" keyword="$word"}">{$word}</a>
						{/foreach}
					</article>
				</div>	
			
			</article>      	    				
		
		{/foreach}
	
<div>
		{* Pagination *}
		{if $prevPage > 1}
			{capture assign=prevUrl}{url router=$smarty.const.ROUTE_PAGE  page="blog" op="index"  keyword="$currentKeyword" path=$prevPage}{/capture}
		{elseif $prevPage === 1}
			{capture assign=prevUrl}{url router=$smarty.const.ROUTE_PAGE page="blog" op="index"  keyword="$currentKeyword"}{/capture}
		{/if}
		{if $nextPage}
			{capture assign=nextUrl}{url router=$smarty.const.ROUTE_PAGE  page="blog" op="index" keyword="$currentKeyword" path=$nextPage}{/capture}
		{/if}
		{include
			file="frontend/components/pagination.tpl"
			prevUrl=$prevUrl
			nextUrl=$nextUrl
			showingStart=$showingStart
			showingEnd=$showingEnd
			total=$total
		}
</div>


	</main>
{include file="frontend/components/footer.tpl"}


