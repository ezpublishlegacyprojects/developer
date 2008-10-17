{include uri='design:developer/treefetchbuilder_window_controls.tpl'}

{if $warnings|count|gt(0)}
<div class="message-warning">
<h2>Warning</h2>
{foreach $warnings as $warning}
{$warning|wash}
{/foreach}
</div>
{/if}

{let $classes=fetch('class', 'list')}

<script type="text/javascript">
<!--
    var optionArray;
    var valueArray;

    optionArray = new Array( );
    valueArray = new Array( );

    {cache-block}
    {foreach $classes as $class}
        optionArray['{$class.identifier}'] = new Array( );
        valueArray['{$class.identifier}'] = new Array( );
        {let $attributes = fetch( 'class', 'attribute_list', hash( 'class_id', $class.id ) )}
        {foreach $attributes as $attribute}
            optionArray['{$class.identifier}'].push( "{$attribute.name|addslashes}" );
            valueArray['{$class.identifier}'].push( "{$attribute.id}" );
        {/foreach}
        {/let}
    {/foreach}
    {/cache-block}


    {literal}
function syncSelectBoxes( fromid, toid, optionArray, valueArray )
{
    var i;
    var from;
    var to;
    var fromlen;
    var selected;
    var selectedValue;
    var optionlen;
    var chosenOptions;
    var chosenValues;
    var tolen;

    from = document.getElementById( fromid );
    to = document.getElementById( toid );

    if ( from != null && to != null )
    {
        removeAllOptions( toid );
        if ( from.selectedIndex > -1 )
        {
            selected = from.options[from.selectedIndex];

            selectedValue = selected.value;

            chosenOptions = optionArray[selectedValue];
            chosenValues = valueArray[selectedValue];

            optionlen = chosenOptions.length;

            for ( i = 0; i < optionlen; i++ )
            {
                tolen = to.length;
                to.options[tolen] = new Option( chosenOptions[i], chosenValues[i], false, false );
            }
        }
    }
}

function removeAllOptions( selectid )
{
    var i;
    var select;

    select = document.getElementById( selectid );

    if ( select != null )
    {
        for ( i = ( select.length - 1 ); i >= 0; i-- )
        {
            select.options[i] = null;
        }
    }
}

    {/literal}
-->
</script>
{*<script type="text/javascript" src={'js/syncselectboxes.js'|ezdesign}></script>*}
<div class="context-block">

{* do not remove the form's name attribute, it is used to toggle the result list's checkboxes *}
<form method="post" action={'developer/treefetchbuilder'|ezurl} name="children">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Tree Fetch Builder'|i18n('extension/developer/treefetchbuilder')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
    <div class="block">
        <div class="element">
            <label for="ParentNodeID">{'Parent node ID'|i18n('extension/developer/treefetchbuilder')}</label> <input type="text" name="ParentNodeID" id="ParentNodeID" value="{$parentNodeID}" size="4" />

            <label for="Offset">{'Offset'|i18n('extension/developer/treefetchbuilder')}</label> <input type="text" name="Offset" id="Offset" value="{$offset}" size="4" disabled="disabled" />

            <label for="Limit">{'Limit'|i18n('extension/developer/treefetchbuilder')}</label> <input type="text" name="Limit" id="limit" value="{$limit}" size="4" />
        </div>
        <div class="element">
            <label for="DepthOperator">{'Depth Operator'|i18n('extension/developer/treefetchbuilder')}</label>
            <select name="DepthOperator" id="DepthOperator">
                <option value="eq" {if $depthOperator|eq('eq')}selected="selected"{/if}>{'equal to'|i18n('extension/developer/treefetchbuilder')}</option>
                <option value="lt" {if $depthOperator|eq('lt')}selected="selected"{/if}>{'less than'|i18n('extension/developer/treefetchbuilder')}</option>
                <option value="gt" {if $depthOperator|eq('gt')}selected="selected"{/if}>{'greater than'|i18n('extension/developer/treefetchbuilder')}</option>
            </select>

            <label for="Depth">{'Depth'|i18n('extension/developer/treefetchbuilder')}</label>
            <input type="text" name="Depth" id="Depth" value="{$depth}" size="4" />
        </div>

        <div class="element">
            <label for="ClassFilterType">{'Class Filter Type'|i18n('extension/developer/treefetchbuilder')}</label>
            <select name="ClassFilterType" id="ClassFilterType">
                <option value="include" {if $classFilterType|eq('include')}selected="selected"{/if}>{'include'|i18n('extension/developer/treefetchbuilder')}</option>
                <option value="exclude" {if $classFilterType|eq('exclude')}selected="selected"{/if}>{'exclude'|i18n('extension/developer/treefetchbuilder')}</option>
            </select>

            <label for="ClassFilterArray[]">{'Class Filter'|i18n('extension/developer/treefetchbuilder')}</label>
            <select name="ClassFilterArray[]" id="ClassFilterArray" multiple="multiple" size="8">
                {foreach $classes as $class}
                    <option value="{$class.identifier}" {if $classFilterArray|contains($class.identifier)}selected="selected"{/if}>{$class.name|wash}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="block">
        <label>{'Languages'|i18n('extension/developer/treefetchbuilder')}</label>
        <select name="Language[]" multiple="multiple">
        {foreach $languages as $locale => $name}
            <option value="{$locale}" {if and($language|is_array,$language|contains($locale))}selected="selected"{/if}>{$name|wash}</option>
        {/foreach}
        </select>
    </div>
    <div class="block">
        <input type="checkbox" {if $ignoreVisibility}checked="checked"{/if} name="IgnoreVisibility" />{'Ignore visibility'|i18n('extension/developer/treefetchbuilder')}
        <input type="checkbox" {if $mainNodeOnly}checked="checked"{/if} name="MainNodeOnly" />{'Main node only'|i18n('extension/developer/treefetchbuilder')}
        <input type="checkbox" {if $onlyTranslated}checked="checked"{/if} name="OnlyTranslated" />{'Only translated'|i18n('extension/developer/treefetchbuilder')}
        <input type="checkbox" {if $emptyLimitation}checked="checked"{/if} name="EmptyLimitation" />{'Empty limitation array (ignore policies)'|i18n('extension/developer/treefetchbuilder')}
        <input type="checkbox" {if $loadDataMap}checked="checked"{/if} name="LoadDataMap" />{'Load data map'|i18n('extension/developer/treefetchbuilder')}
    </div>

    <fieldset>
    <legend>{'Sorting'|i18n('extension/developer/treefetchbuilder')}</legend>
    <div class="block">

        <script type="text/javascript">
        <!--
        {literal}
        function enableAttributes( fromID, toID, toID2 )
        {
            var from;
            var to;
            var to2;
            var selected;
            var selectedValue;

            from = document.getElementById( fromID );
            to = document.getElementById( toID );
            to2 = document.getElementById( toID2 );

            if ( from.selectedIndex > -1 )
            {
                selected = from.options[from.selectedIndex];
                selectedValue = selected.value;

                if ( selectedValue == 'attribute' )
                {
                    to.disabled=false;
                    to2.disabled=false;
                }
                else
                {
                    to.disabled=true;
                    to2.disabled=true;
                }
            }
        }
        {/literal}
        -->
        </script>
        <div class="element">
        <label for="NewSortingElement">{'Method'|i18n('extension/developer/treefetchbuilder')}</label>
        <select name="NewSortingElement" id="NewSortingElement" onchange="javascript:enableAttributes('NewSortingElement','SortingClassSelection', 'NewSortingElementValue');">
            {def $sortFields=hash( 'attribute', 'attribute', 'class_identifier', 'class identifier', 'class_name', 'class name', 'modified', 'modified date/time', 'name', 'name', 'path', 'path ID string', 'path_name', 'path string', 'priority', 'priority', 'published', 'published date/time', 'section', 'section ID' )}
            {foreach $sortFields as $value => $label}
            <option value="{$value}">{$label|wash}</option>
            {/foreach}
        </select>
        </div>

        <div class="element">
        <label for="NewSortingElementDirection">{'Direction'|i18n('extension/developer/treefetchbuilder')}</label>
        <select name="NewSortingElementDirection" id="NewSortingElementDirection">
            <option value="1">{'ascending'|i18n('extension/developer/treefetchbuilder')}</option>
            <option value="0">{'descending'|i18n('extension/developer/treefetchbuilder')}</option>
        </select>
        </div>

        <input class="button" type="submit" value="{'Add sorting element'|i18n('extension/developer/treefetchbuilder')}" name="AddSortingElementButton" />
    </div>

    <div class="block">
        <select id="SortingClassSelection" onchange="javascript:syncSelectBoxes( 'SortingClassSelection', 'NewSortingElementValue', optionArray, valueArray );">
            {foreach $classes as $class}
                <option value="{$class.identifier}">{$class.name|wash}</option>
            {/foreach}
        </select>

        <select name="NewSortingElementValue" id="NewSortingElementValue">
            <option value="">&nbsp;</option>
        </select>
    </div>

    <script type="text/javascript">
    <!--
        javascript:syncSelectBoxes( 'SortingClassSelection', 'NewSortingElementValue', optionArray, valueArray );
    -->
    </script>

    {if $sortingElements|count|gt( 0 )}
        <table class="list" cellspacing="0">
            <tr>
                <th></th>
                <th>{'Method'|i18n('extension/developer/treefetchbuilder')}</th>
                <th>{'Direction'|i18n('extension/developer/treefetchbuilder')}</th>
                <th>{'Attribute'|i18n('extension/developer/treefetchbuilder')}</th>
            </tr>
            {foreach $sortingElements as $key => $element sequence array('bglight','bgdark') as $sequence}
                <tr class="{$sequence}">
                    <td><input type="checkbox" name="SelectedSorting[]" value="{$key}" /></td>
                    <td>{$element.method}<input type="hidden" name="SortingElementsMethod[]" value="{$element.method}" /></td>
                    <td>{if $element.direction|eq(1)}ascending{else}descending{/if}<input type="hidden" name="SortingElementsDirection[]" value="{$element.direction}" /></td>
                    <td>{if $element.method|eq('attribute')}
                        {def $classAttribute=fetch( 'content', 'class_attribute', hash( 'attribute_id', $element.value ) )
                             $class=fetch('content','class', hash( 'class_id', $classAttribute.contentclass_id ) )}
                        <a href={concat('/class/view/',$class.id)|ezurl}>{$class.name|wash}</a> / {$classAttribute.name|wash} [ID: {$element.value}]
                        {undef $classAttribute}
                        {/if}<input type="hidden" name="SortingElementsValue[]" value="{$element.value}" /></td>
                </tr>
            {/foreach}
        </table>

        <input type="submit" class="button" name="RemoveSelectedSortingButton" value="{'Remove selected sorting'|i18n('extension/developer/treefetchbuilder')}" />
    {else}
        <p>{'No sorting specified yet.'|i18n('extension/developer/treefetchbuilder')}</p>
    {/if}
    </fieldset>
</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">

{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">
    <div class="left">
        <input class="button" type="submit" value="{'Update'|i18n('extension/developer/treefetchbuilder')}" name="UpdateButton" />
        <input class="button" type="reset" value="{'Reset'|i18n('extension/developer/treefetchbuilder')}" />
    </div>
    <div class="right">
        <input class="button" type="submit" value="{'Empty'|i18n('extension/developer/treefetchbuilder')}" name="EmptyButton" />
    </div>
</div>

<div class="break"></div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</form>

</div>
{/let}

{include uri="design:developer/treefetchbuilder_windows.tpl"}
