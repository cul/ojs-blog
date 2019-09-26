{**
 * templates/content.tpl
 *
 * Display blog content
 *}
{include file="frontend/components/header.tpl" pageTitleTranslated=$title}

<h2>{$title|escape}</h2>
<div class="page">
<tr>
<td>
    {$entry->getTitle()}
</td>
</tr>
</div>
<div class="page">
<td>
    {$entry->getContent()}
</td>
</div>

{include file="frontend/components/footer.tpl"}
