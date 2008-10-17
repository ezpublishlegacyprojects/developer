<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish developer tools
// SOFTWARE RELEASE: 0.x
// COPYRIGHT NOTICE: Copyright (C) 2005-2007 SCK-CEN
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

$Module = $Params['Module'];
$moduleInfo = $Module->attribute( 'info' );
$moduleName = $Params['ModuleName'];
$functionName = $Params['FunctionName'];
$UserParameters = isset( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();

$devIni = eZINI::instance( 'developer.ini' );
$maxLimit = 50;
if ( $devIni->hasVariable( 'TreeFetchBuilder', 'MaxLimit' ) )
{
    $maxLimit = $devIni->variable( 'TreeFetchBuilder', 'MaxLimit' );
}

require_once( 'kernel/common/template.php' );

// default values
$sessionParameters = array( );
$sessionParameters['ParentNodeID'] = 2;
$sessionParameters['Offset'] = 0;
$sessionParameters['Limit'] = 10;
$sessionParameters['Depth'] = '';
$sessionParameters['DepthOperator'] = 'eq';
$sessionParameters['IgnoreVisibility'] = false;
$sessionParameters['MainNodeOnly'] = false;
$sessionParameters['LoadDataMap'] = false;
$sessionParameters['OnlyTranslated'] = false;
$sessionParameters['Limitation'] = false;
$sessionParameters['ClassFilterType'] = 'include';
$sessionParameters['ClassFilterArray'] = array( );
$sessionParameters['SortingElements'] = array( );
$sessionParameters['Language'] = false;

$warnings = array();

function phpToTemplateCode( $value, $currentIndentLength = 0, $startNewLine = false )
{
    $indentLength = $currentIndentLength + 4;
    $indent = str_repeat( ' ', $indentLength );
    $tplCode = '';

    if ( is_array( $value ) )
    {
        $tplCode .= "hash(\r\n$indent";

        $arrayParts = array();
        foreach ( array_keys( $value ) as $key )
        {
            $arrayPart = phpToTemplateCode( $key ) . ', ';
            if ( is_array( $value[$key] ) && $startNewLine )
            {
                $arrayPart .= "\r\n$indent";
            }

            $arrayPart .= phpToTemplateCode( $value[$key], $indentLength, $startNewLine );
            $arrayParts[] = $arrayPart;
        }
        $tplCode .= implode( ",\r\n$indent", $arrayParts );

        $tplCode .= "\r\n$indent)";
    }
    elseif ( is_numeric( $value ) )
    {
        $tplCode .= $value;
    }
    elseif ( is_string( $value ) )
    {
        $tplCode .= "'$value'";
    }
    elseif ( is_bool( $value ) )
    {
        if ( $value )
        {
            $tplCode .= 'true()';
        }
        else
        {
            $tplCode .= 'false()';
        }
    }
    elseif ( is_object( $value ) )
    {
        eZDebug::writeError( 'Can not transform an object into template code.', 'phpToTemplateCode' );
    }

    return $tplCode;
}

$tpl = templateInit();

$http = eZHTTPTool::instance( );

if ( $Module->isCurrentAction( 'Update' ) or $Module->isCurrentAction( 'AddSortingElement' ) or $Module->isCurrentAction( 'RemoveSelectedSorting' ) )
{
    // integer input
    foreach ( array( 'ParentNodeID', 'Limit', 'Depth' ) as $actionParam )
    {
        if ( $Module->hasActionParameter( $actionParam ) )
        {
            $paramValue = $Module->actionParameter( $actionParam );
            if ( is_numeric( $paramValue ) )
            {
                $sessionParameters[$actionParam] = (int)$Module->actionParameter( $actionParam );
            }
        }
    }

    // boolean switches
    foreach ( array( 'IgnoreVisibility', 'MainNodeOnly', 'OnlyTranslated', 'LoadDataMap' ) as $actionParam )
    {
         if ( $Module->hasActionParameter( $actionParam ) )
        {
            $sessionParameters[$actionParam] = true;
        }
    }

    // straight input
    foreach ( array( 'DepthOperator', 'ClassFilterType', 'ClassFilterArray' ) as $actionParam )
    {
        if ( $Module->hasActionParameter( $actionParam ) )
        {
            $sessionParameters[$actionParam] = $Module->actionParameter( $actionParam );
        }
    }

    if ( $Module->hasActionParameter( 'EmptyLimitation' ) )
    {
        $sessionParameters['Limitation'] = array();
    }

    if ( $Module->hasActionParameter( 'Language' ) && is_array( $Module->actionParameter( 'Language' ) ) )
    {
        $sessionParameters['Language'] = $Module->actionParameter( 'Language' );
    }

    if ( $Module->hasActionParameter( 'SortingElementsMethod' ) and
         $Module->hasActionParameter( 'SortingElementsDirection' ) and
         $Module->hasActionParameter( 'SortingElementsValue' ) and
         count( $Module->actionParameter( 'SortingElementsMethod' ) ) == count( $Module->actionParameter( 'SortingElementsDirection' ) ) and
         count( $Module->actionParameter( 'SortingElementsDirection' ) ) == count( $Module->actionParameter( 'SortingElementsValue' ) )
    )
    {
        $methods = $Module->actionParameter( 'SortingElementsMethod' );
        $directions = $Module->actionParameter( 'SortingElementsDirection' );
        $values = $Module->actionParameter( 'SortingElementsValue' );

        foreach ( $methods as $key => $method )
        {
            $element = array();
            $element['method'] = $method;
            $element['direction'] = $directions[$key];
            $element['value'] = $values[$key];
            $sessionParameters['SortingElements'][] = $element;
        }
    }
}
else if ( $Module->isCurrentAction( 'Empty' ) )
{
    // remove session variables
    $http->removeSessionVariable( 'FetchTreeBuilder' );
}
else
{
    // restore session variables
    if ( $http->hasSessionVariable( 'FetchTreeBuilder' ) )
    {
        $sessionParameters = $http->sessionVariable( 'FetchTreeBuilder' );
    }
}

if ( $Module->isCurrentAction( 'AddSortingElement' ) )
{
    if ( $Module->hasActionParameter( 'NewSortingElementMethod' ) and
         $Module->hasActionParameter( 'NewSortingElementDirection' ) )
    {
        $element = array();
        $element['method'] = $Module->actionParameter( 'NewSortingElementMethod' );
        $element['direction'] = $Module->actionParameter( 'NewSortingElementDirection' );

        if ( $Module->hasActionParameter( 'NewSortingElementValue' ) )
        {
            $element['value'] = $Module->actionParameter( 'NewSortingElementValue' );
        }
        else
        {
            $element['value'] = '';
        }

        $sessionParameters['SortingElements'][] = $element;
    }
}
elseif ( $Module->isCurrentAction( 'RemoveSelectedSorting' ) )
{
    if ( $Module->hasActionParameter( 'SelectedSorting' ) )
    {
        $sortIDList = $Module->actionParameter( 'SelectedSorting' );

        foreach ( $sortIDList as $key )
        {
            if ( array_key_exists( $key, $sessionParameters['SortingElements'] ) )
            {
                unset( $sessionParameters['SortingElements'][$key] );
            }
        }
    }
}

$http->setSessionVariable( 'FetchTreeBuilder', $sessionParameters ) ;

$phpFetchParams = array();

if ( count( $sessionParameters['ClassFilterArray'] ) > 0 )
{
    $phpFetchParams['ClassFilterType'] = $sessionParameters['ClassFilterType'];
    $phpFetchParams['ClassFilterArray'] = $sessionParameters['ClassFilterArray'];
}

if ( $sessionParameters['MainNodeOnly'] )
{
    $phpFetchParams['MainNodeOnly'] = true;
}

// default value of this option differs between the template fetch function and the static PHP method
// so we always add it to avoid any confusion
$phpFetchParams['LoadDataMap'] = $sessionParameters['LoadDataMap'];

if ( $sessionParameters['IgnoreVisibility'] )
{
    $phpFetchParams['IgnoreVisibility'] = true;
}

if ( $sessionParameters['OnlyTranslated'] )
{
    $phpFetchParams['OnlyTranslated'] = true;
}

if ( is_array( $sessionParameters['Limitation'] ) )
{
    $phpFetchParams['Limitation'] = array();
}

// sorting
if ( count( $sessionParameters['SortingElements'] ) > 0 )
{
    $phpSorting = array();
    $phpDirection = false;
    foreach ( $sessionParameters['SortingElements'] as $key => $element )
    {
        if ( $element['direction'] == 1 )
        {
            $phpDirection = true;
        }

        if ( $element['method'] == 'attribute' )
        {
            $phpSorting[] = array( 'attribute', $phpDirection, $element['value'] );
        }
        else
        {
            $phpSorting[] = array( $element['method'], $phpDirection );
        }
    }

    if ( count( $phpSorting ) > 1 )
    {
        $phpFetchParams['SortBy'] = $phpSorting;
    }
    else
    {
        $phpFetchParams['SortBy'] = $phpSorting[0];
    }

}

if ( is_numeric( $sessionParameters['Depth'] ) )
{
    $phpFetchParams['Depth'] = $sessionParameters['Depth'];
    $phpFetchParams['DepthOperator'] = $sessionParameters['DepthOperator'];
}

if ( is_array( $sessionParameters['Language'] ) )
{
    $phpFetchParams['Language'] = $sessionParameters['Language'];
}

// so far the parameters we want to use when fetching the total result count
$phpCountFetchParams = $phpFetchParams;

// add params only used for fetching result list
if ( $sessionParameters['Limit'] < 1 || $sessionParameters['Limit'] > $maxLimit )
{
    $sessionParameters['Limit'] = $maxLimit;
    $warnings[] = 'A maximum page result limit of ' . $maxLimit . ' was applied. If desired you can raise the allowed maximum in developer.ini.';
}

$phpFetchParams['Limit'] = $sessionParameters['Limit'];

// if the form isn't posted and there's an offset user parameter, then use it
if ( count( $_POST ) == 0 )
{
    if ( array_key_exists( 'offset', $UserParameters ) )
    {
        $sessionParameters['Offset'] = $UserParameters['offset'];
    }
    else
    {
        $sessionParameters['Offset'] = 0;
    }
}

$phpFetchParams['Offset'] = $sessionParameters['Offset'];

// only fetch results when necessary
if ( eZPreferences::value( 'developer_treefetchbuilder_navigation_results' ) == 1 )
{
    // fetching count
    $resultCount = eZContentObjectTreeNode::subTreeCountByNodeID( $phpCountFetchParams, $sessionParameters['ParentNodeID'] );
    $tpl->setVariable( 'resultCount', $resultCount );

    // fetching result set for current page
    $results = eZContentObjectTreeNode::subTreeByNodeID( $phpFetchParams, $sessionParameters['ParentNodeID'] );
    $tpl->setVariable( 'results', $results );
}

// only set PHP code when necessary
if ( eZPreferences::value( 'developer_treefetchbuilder_navigation_php' ) == 1 )
{
    $phpCode = '$nodes = eZContentObjectTreeNode::subTreeByNodeID( ' . var_export( $phpFetchParams, true ) . ', ' . $sessionParameters['ParentNodeID'] .' );';
    $tpl->setVariable( 'phpCode', $phpCode );
}

// only set template code when necessary
if ( eZPreferences::value( 'developer_treefetchbuilder_navigation_template' ) == 1 )
{
    $fetchParams = array();

    // transform PHP-style array keys to tpl-style hash keys
    foreach ( array_keys( $phpFetchParams ) as $phpKey )
    {
        $templateKey = strtolower( preg_replace( '/([a-z]+)([A-Z]+)/', '\\1_\\2', $phpKey ) );
        $fetchParams[$templateKey] = $phpFetchParams[$phpKey];
    }

    $fetchParams = array_merge( array( 'parent_node_id' => $sessionParameters['ParentNodeID'] ), $fetchParams );

    $indent = 0;
    $templateCode = "fetch( 'content', 'tree', ";
    $templateCode .= phpToTemplateCode( $fetchParams, $indent, true );

    $templateCode .= ")";

    $tpl->setVariable( 'templateCode', $templateCode );
}

$viewParameters = array( 'offset' => $sessionParameters['Offset'] );
$viewParameters = array_merge( $viewParameters, $UserParameters );

$tpl->setVariable( 'parentNodeID', $sessionParameters['ParentNodeID'] );
$tpl->setVariable( 'offset', $sessionParameters['Offset'] );
$tpl->setVariable( 'limit', $sessionParameters['Limit'] );
$tpl->setVariable( 'ignoreVisibility', $sessionParameters['IgnoreVisibility'] );
$tpl->setVariable( 'mainNodeOnly', $sessionParameters['MainNodeOnly'] );
$tpl->setVariable( 'loadDataMap', $sessionParameters['LoadDataMap'] );
$tpl->setVariable( 'onlyTranslated', $sessionParameters['OnlyTranslated'] );
$tpl->setVariable( 'emptyLimitation', is_array( $sessionParameters['Limitation'] ) );
$tpl->setVariable( 'classFilterType', $sessionParameters['ClassFilterType'] );
$tpl->setVariable( 'classFilterArray', $sessionParameters['ClassFilterArray'] );
$tpl->setVariable( 'sortingElements', $sessionParameters['SortingElements'] );
$tpl->setVariable( 'depth', $sessionParameters['Depth'] );
$tpl->setVariable( 'depthOperator', $sessionParameters['DepthOperator'] );
$tpl->setVariable( 'language', $sessionParameters['Language'] );
$tpl->setVariable( 'warnings', $warnings );

$languageList = array();
$languages = eZContentLanguage::fetchList();

foreach ( $languages as $lang )
{
    $languageList[$lang->attribute( 'locale' )] = $lang->attribute( 'name' );
}

$tpl->setVariable( 'languages', $languageList );
$tpl->setVariable( 'item_limit', $phpFetchParams['Limit'] );
$tpl->setVariable( 'view_parameters', $viewParameters );

$Result = array();
$Result['left_menu'] = 'design:parts/developer/menu.tpl';
$Result['content'] = $tpl->fetch( "design:$moduleName/$functionName.tpl" );
$Result['path'] = array( array( 'url' => false, 'text' => $moduleInfo['name'] ),
                         array( 'url' => false, 'text' => 'Tree Fetch Builder' )
                       );

?>