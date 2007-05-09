<div class="content-navigation-childlist">
<table class="list-thumbnails" cellspacing="0">
    <tr>
    {section var=Nodes loop=$results sequence=array( bglight, bgdark )}
    {let child_name=$Nodes.item.name|wash}
        <td width="25%">
        {node_view_gui view=thumbnail content_node=$Nodes.item}

        <div class="controls">
        {* Remove checkbox *}
        {section show=$Nodes.item.can_remove}
            <input type="checkbox" name="DeleteIDArray[]" value="{$Nodes.item.node_id}" title="{'Use these checkboxes to select items for removal. Click the "Remove selected" button to actually remove the selected items.'|i18n( 'design/admin/node/view/full' )|wash()}" />
            {section-else}
            <input type="checkbox" name="DeleteIDArray[]" value="{$Nodes.item.node_id}" title="{'You do not have permissions to remove this item.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
        {/section}

        {* Edit button *}
        {section show=$Nodes.item.can_edit}
            <a href={concat( 'content/edit/', $Nodes.item.contentobject_id )|ezurl}><img src={'edit.gif'|ezimage} alt="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'Edit <%child_name>.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )|wash}" /></a>
        {section-else}
            <img src={'edit-disabled.gif'|ezimage} alt="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permissions to edit <%child_name>.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )|wash}" /></a>
        {/section}

        <p><a href={$Nodes.url_alias|ezurl}>{$child_name}</a></p>
        </div>
    {/let}
</td>
{delimiter modulo=4}
</tr><tr>
{/delimiter}
{/section}
</tr>
</table>
</div>
