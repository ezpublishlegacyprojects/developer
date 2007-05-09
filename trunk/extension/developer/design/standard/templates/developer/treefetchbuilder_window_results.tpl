<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h2 class="context-title">{'Results [%count]'|i18n('extension/developer/treefetchbuilder','',hash('%count',$resultCount))}</h2>

{* DESIGN: Subline *}<div class="header-subline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

{if $results|count|gt(0)}

{* View mode selector. *}
<div class="context-toolbar">
<div class="block">
<div class="right">
        <p>
        {switch match=ezpreference( 'developer_treefetchbuilder_viewmode' )}
        {case match='thumbnail'}
        <a href={'/user/preferences/set/developer_treefetchbuilder_viewmode/list'|ezurl} title="{'Display results using a simple list.'|i18n( 'design/admin/node/view/full' )}">{'List'|i18n( 'design/admin/node/view/full' )}</a>
        <span class="current">{'Thumbnail'|i18n( 'design/admin/node/view/full' )}</span>
        <a href={'/user/preferences/set/developer_treefetchbuilder_viewmode/detailed'|ezurl} title="{'Display results using a detailed list.'|i18n( 'design/admin/node/view/full' )}">{'Detailed'|i18n( 'design/admin/node/view/full' )}</a>
        {/case}

        {case match='detailed'}
        <a href={'/user/preferences/set/developer_treefetchbuilder_viewmode/list'|ezurl} title="{'Display results using a simple list.'|i18n( 'design/admin/node/view/full' )}">{'List'|i18n( 'design/admin/node/view/full' )}</a>
        <a href={'/user/preferences/set/developer_treefetchbuilder_viewmode/thumbnail'|ezurl} title="{'Display results as thumbnails.'|i18n( 'design/admin/node/view/full' )}">{'Thumbnail'|i18n( 'design/admin/node/view/full' )}</a>
        <span class="current">{'Detailed'|i18n( 'design/admin/node/view/full' )}</span>
        {/case}

        {case}
        <span class="current">{'List'|i18n( 'design/admin/node/view/full' )}</span>
        <a href={'/user/preferences/set/developer_treefetchbuilder_viewmode/thumbnail'|ezurl} title="{'Display results as thumbnails.'|i18n( 'design/admin/node/view/full' )}">{'Thumbnail'|i18n( 'design/admin/node/view/full' )}</a>
        <a href={'/user/preferences/set/developer_treefetchbuilder_viewmode/detailed'|ezurl} title="{'Display results using a detailed list.'|i18n( 'design/admin/node/view/full' )}">{'Detailed'|i18n( 'design/admin/node/view/full' )}</a>
        {/case}
        {/switch}
        </p>
</div>

<div class="break"></div>

</div>
</div>

{let $can_copy=false()
     $can_remove=false()
     $can_edit=false()
     $can_create=false()}

{* Check if the current user is allowed to edit or delete any of the results *}
{foreach $results as $result}
    {*
    {if $result.can_remove}
        {set can_remove=true()}
    {/if}
    *}
    {if $result.can_edit}
        {set can_edit=true()}
    {/if}
{/foreach}

{* Display the actual list of results. *}
{switch match=ezpreference( 'developer_treefetchbuilder_viewmode' )}

{case match='thumbnail'}
    {include uri='design:developer/treefetchbuilder_results_thumbnail.tpl'}
{/case}

{case match='detailed'}
    {include uri='design:developer/treefetchbuilder_results_detailed.tpl'}
{/case}

{case}
    {include uri='design:developer/treefetchbuilder_results_list.tpl'}
{/case}
{/switch}

{/let}

{* Else: there are no results. *}
{else}

<div class="block">
    <p>{'No results.'|i18n( 'extension/developer/treefetchbuilder' )}</p>
</div>

{/if}

<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/developer/treefetchbuilder'
         page_uri_suffix=''
         item_count=$resultCount
         view_parameters=$view_parameters
         item_limit=$item_limit}
</div>

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>
