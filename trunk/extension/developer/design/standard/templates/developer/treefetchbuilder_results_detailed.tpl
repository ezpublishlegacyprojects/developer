<div class="content-navigation-childlist">
    <table class="list" cellspacing="0">
    <tr>
        {* Name column *}
        <th class="name">{'Name'|i18n( 'design/admin/node/view/full' )}</th>

        {* Hidden/Invisible column *}
        <th class="hidden_invisible">{'Visibility'|i18n( 'design/admin/node/view/full' )}</th>

        {* Class type column *}
        <th class="class">{'Type'|i18n( 'design/admin/node/view/full' )}</th>

        {* Modifier column *}
        <th class="modifier">{'Modifier'|i18n( 'design/admin/node/view/full' )}</th>

        {* Modified column *}
        <th class="modified">{'Modified'|i18n( 'design/admin/node/view/full' )}</th>

        {* Section column *}
        <th class="section">{'Section'|i18n( 'design/admin/node/view/full' )}</th>

        {* Move column *}
        <th class="move">&nbsp;</th>

        {* Edit column *}
        <th class="edit">&nbsp;</th>
    </tr>

    {section var=Nodes loop=$results sequence=array( bglight, bgdark )}
    {let child_name=$Nodes.item.name|wash}

        <tr class="{$Nodes.sequence}">

        {* Name *}
        <td>{node_view_gui view=line content_node=$Nodes.item}</td>

        {* Visibility. *}
        <td class="nowrap">{$Nodes.item.hidden_status_string}</td>

        {* Class type *}
        <td class="class">{$Nodes.item.class_name|wash}</td>

        {* Modifier *}
        <td class="modifier"><a href={$Nodes.item.object.current.creator.main_node.url_alias|ezurl}>{$Nodes.item.object.current.creator.name|wash}</a></td>

        {* Modified *}
        <td class="modified">{$Nodes.item.object.modified|l10n( shortdatetime )}</td>

        {* Section *}
        <td>{let section_object=fetch( section, object, hash( section_id, $Nodes.object.section_id ) )}{section show=$section_object}<a href={concat( '/section/view/', $Nodes.object.section_id )|ezurl}>{$section_object.name|wash}</a>{section-else}<i>{'Unknown'|i18n( 'design/admin/node/view/full' )}</i>{/section}{/let}</td>

        {* Move button. *}
        <td>
        <a href={concat( 'content/move/', $Nodes.item.node_id )|ezurl}><img src={'move.gif'|ezimage} alt="{'Move'|i18n( 'design/admin/node/view/full' )}" title="{'Move <%child_name> to another location.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )|wash}" /></a>
        </td>

        {* Edit button *}
        {* section show=$can_edit *}
        <td>
        {section show=$Nodes.item.can_edit}
            <a href={concat( 'content/edit/', $Nodes.item.contentobject_id )|ezurl}><img src={'edit.gif'|ezimage} alt="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'Edit <%child_name>.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )|wash}" /></a>
        {section-else}
            <img src={'edit-disabled.gif'|ezimage} alt="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permissions to edit <%child_name>.'|i18n( 'design/admin/node/view/full',, hash( '%child_name', $child_name ) )|wash}" /></a>
        {/section}
        </td>
        {* /section *}
  </tr>

{/let}
{/section}

</table>
</div>

