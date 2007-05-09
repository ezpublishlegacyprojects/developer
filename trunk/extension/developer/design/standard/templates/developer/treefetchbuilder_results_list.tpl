<div class="content-navigation-childlist">
    <table class="list" cellspacing="0">
    <tr>
        {* Name column *}
        <th class="name">{'Name'|i18n( 'design/admin/node/view/full' )}</th>

        {* Class type column *}
        <th class="class">{'Type'|i18n( 'design/admin/node/view/full' )}</th>

        {* Edit column *}
        <th class="edit">&nbsp;</th>
    </tr>

    {section var=Nodes loop=$results sequence=array( bglight, bgdark )}
    {let child_name=$Nodes.item.name|wash}

        <tr class="{$Nodes.sequence}">

        {* Name *}
    {def $userEnabled='' $userLocked=''}
    {def $nodeContent=fetch( 'content', 'object', hash( 'object_id', $Nodes.item.contentobject_id ) )}
    {if $nodeContent.class_identifier|eq('user')}
        {if not($nodeContent.data_map['user_account'].content.is_enabled)}
           {set $userEnabled=concat( '<span class="userstatus-disabled">', '(disabled)'|i18n("design/admin/node/view/full") ,'</span>')}
        {/if}
        {if $nodeContent.data_map['user_account'].content.is_locked}
           {set $userLocked=concat( '<span class="userstatus-disabled">', '(locked)'|i18n("design/admin/node/view/full") ,'</span>')}
        {/if}
    {/if}
        <td>{node_view_gui view=line content_node=$Nodes.item} {$userEnabled} {$userLocked}</td>
    {undef $userEnabled $nodeContent $userLocked}

        {* Class type *}
        <td class="class">{$Nodes.item.class_name|wash()}</td>

        {* Edit button *}
        <td>
        {section show=$Nodes.item.can_edit}
            <a href={concat( 'content/edit/', $Nodes.item.contentobject_id )|ezurl}><img src={'edit.gif'|ezimage} alt="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'Edit <%child_name>.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )|wash}" /></a>
        {section-else}
            <img src={'edit-disabled.gif'|ezimage} alt="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permissions to edit %child_name.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )|wash}" /></a>
        {/section}
        </td>
  </tr>

{/let}
{/section}

</table>
</div>

