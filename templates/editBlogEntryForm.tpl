{**
 * templates/editBlogEntryForm.tpl
 *
 * Form for editing a blog entry
 *}

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#blogEntryForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

{capture assign=actionUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.blog.controllers.grid.BlogGridHandler" op="updateBlogEntry" existingPageName=$blockName escape=false}{/capture}
<form class="pkp_form" id="blogEntryForm" method="post" action="{$actionUrl}">
	{csrf}
	{if $blogEntryId}
		<input type="hidden" name="blogEntryId" value="{$blogEntryId|escape}" />
	{/if}
	{fbvFormArea id="blogEntryFormArea" class="border"}
		{fbvFormSection label="plugins.generic.blog.pageTitle" for="title"}
		 <tr valign="top">
	<td class="value"><input type="text" id="title" name="title" value="{$title}" size="20" maxlength="255" class="textField" /></td>
 </tr>
	{/fbvFormSection}
	{fbvFormSection label="plugins.generic.blog.content" for="content"}
	<tr valign="top">
		<td class="value">
		{fbvElement type="textarea" id="content" value=$content rich=true}
		</td>
 	</tr>
	{/fbvFormSection}
	{fbvFormSection label="plugins.generic.blog.byline" for="byline"}
		 <tr valign="top">
	<td class="value"><input type="text" id="byline" name="byline" value="{$byline}" size="20" maxlength="255" class="textField" /></td>
 </tr>
	{/fbvFormSection}
	{fbvFormSection label="plugins.generic.blog.datePosted" for="datePosted"}
		<tr valign="top">	
			{fbvElement type="text" id="datePosted" value=$datePosted size=$fbvStyles.size.SMALL class="datepicker"}
 		</tr>			
	{/fbvFormSection}
	{/fbvFormArea}	
	{fbvFormArea id="tagitFields" class="border"}
        {fbvFormSection label="common.keywords"}
                                {fbvElement type="keyword" id="keywords" current=$keywords }
                        {/fbvFormSection}
	{/fbvFormArea}	
	{fbvFormSection class="formButtons"}
		{assign var=buttonId value="submitFormButton"|concat:"-"|uniqid}
		{fbvElement type="submit" class="submitFormButton" id=$buttonId label="common.save"}
	{/fbvFormSection}
</form>
