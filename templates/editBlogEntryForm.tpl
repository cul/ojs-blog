{**
 * templates/editBlogEntryForm.tpl
 *
 * Form for editing a blog entry
 *}
<script src="{$pluginJavaScriptURL}/StaticPageFormHandler.js"></script>
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#staticPageForm').pkpHandler(
			'$.pkp.controllers.form.blog.StaticPageFormHandler',
			{ldelim}
				previewUrl: {url|json_encode router=$smarty.const.ROUTE_PAGE page="pages" op="preview"}
			{rdelim}
		);
	{rdelim});
</script>

{capture assign=actionUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.blog.controllers.grid.StaticPageGridHandler" op="updateStaticPage" existingPageName=$blockName escape=false}{/capture}
<form class="pkp_form" id="blogEntryForm" method="post" action="{$actionUrl}">
	{csrf}
	{if $staticPageId}
		<input type="hidden" name="staticPageId" value="{$staticPageId|escape}" />
	{/if}
	{fbvFormArea id="blogEntryFormArea" class="border"}
		{fbvFormSection}
			{fbvElement type="text" label="plugins.generic.blog.pageTitle" id="title" value=$title maxlength="255" inline=true multilingual=true size=$fbvStyles.size.MEDIUM}
		{/fbvFormSection}
		{fbvFormSection label="plugins.generic.blog.content" for="content"}
			{fbvElement type="textarea" multilingual=true name="content" id="content" value=$content rich=true height=$fbvStyles.height.TALL variables=$allowedVariables}
		{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormSection class="formButtons"}
		{assign var=buttonId value="submitFormButton"|concat:"-"|uniqid}
		{fbvElement type="submit" class="submitFormButton" id=$buttonId label="common.save"}
	{/fbvFormSection}
</form>
