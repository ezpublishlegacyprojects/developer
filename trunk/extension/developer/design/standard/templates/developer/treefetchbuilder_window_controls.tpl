{* Window controls for Tree Fetch Builder. *}
<div class="menu-block{section show=fetch( content, translation_list )|count|eq( 1 )} notranslations{/section}">
<ul>

    {section show=ezpreference( 'developer_treefetchbuilder_navigation_template' )}
    <li class="enabled">
    <div class="button-bc"><div class="button-tl"><div class="button-tr"><div class="button-br">
        <a href={'/user/preferences/set/developer_treefetchbuilder_navigation_template/0'|ezurl} title="{'Hide template code.'|i18n('extension/developer/treefetchbuilder')}">{'Template code'|i18n( 'extension/developer/treefetchbuilder' )}</a>
    </div></div></div></div>
    </li>
    {section-else}
    <li class="disabled">
    <div class="button-bc"><div class="button-tl"><div class="button-tr"><div class="button-br">
        <a href={'/user/preferences/set/developer_treefetchbuilder_navigation_template/1'|ezurl} title="{'Show template code.'|i18n('extension/developer/treefetchbuilder')}">{'Template code'|i18n( 'extension/developer/treefetchbuilder' )}</a>
    </div></div></div></div>
    </li>
    {/section}

    {section show=ezpreference( 'developer_treefetchbuilder_navigation_php' )}
    <li class="enabled">
    <div class="button-bc"><div class="button-tl"><div class="button-tr"><div class="button-br">
        <a href={'/user/preferences/set/developer_treefetchbuilder_navigation_php/0'|ezurl} title="{'Hide PHP code.'|i18n('extension/developer/treefetchbuilder')}">{'PHP code'|i18n( 'extension/developer/treefetchbuilder' )}</a>
    </div></div></div></div>
    </li>
    {section-else}
    <li class="disabled">
    <div class="button-bc"><div class="button-tl"><div class="button-tr"><div class="button-br">
        <a href={'/user/preferences/set/developer_treefetchbuilder_navigation_php/1'|ezurl} title="{'Show PHP code.'|i18n('extension/developer/treefetchbuilder')}">{'PHP code'|i18n( 'extension/developer/treefetchbuilder' )}</a>
    </div></div></div></div>
    </li>
    {/section}

    {section show=ezpreference( 'developer_treefetchbuilder_navigation_results' )}
    <li class="enabled">
    <div class="button-bc"><div class="button-tl"><div class="button-tr"><div class="button-br">
        <a href={'/user/preferences/set/developer_treefetchbuilder_navigation_results/0'|ezurl} title="{'Hide results.'|i18n('extension/developer/treefetchbuilder')}">{'Results'|i18n( 'extension/developer/treefetchbuilder' )}</a>
    </div></div></div></div>
    </li>
    {section-else}
    <li class="disabled">
    <div class="button-bc"><div class="button-tl"><div class="button-tr"><div class="button-br">
        <a href={'/user/preferences/set/developer_treefetchbuilder_navigation_results/1'|ezurl} title="{'Show results.'|i18n('extension/developer/treefetchbuilder')}">{'Results'|i18n( 'extension/developer/treefetchbuilder' )}</a>
    </div></div></div></div>
    </li>
    {/section}
</ul>

<div class="break"></div>

</div>
