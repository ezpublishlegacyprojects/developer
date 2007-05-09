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

$Module =& $Params['Module'];
$moduleInfo =& $Module->attribute( 'info' );
$moduleName = $Params['ModuleName'];
$functionName = $Params['FunctionName'];

include_once( 'kernel/common/template.php' );

$templateCode = '';

if ( $Module->isCurrentAction( 'Parse' ) )
{
    if ( $Module->hasActionParameter( 'TemplateCode' ) )
    {
        $templateCode = $Module->actionParameter( 'TemplateCode' );
        $newTpl=& templateInit();
        //$newTpl->setShowDetails( true );

        $resourceData = array();
        $resourceData['text'] = $templateCode;
        $resourceData['root-node'] = null;
        $resourceData['compiled-template'] = false;
        $resourceData['time-stamp'] = null;
        $resourceData['key-data'] = null;
        $resourceData['locales'] = null;
        $resourceData['resource'] = null;
        $resourceData['template-filename'] = 'temp';

        $root =& $resourceData['root-node'];
        $root = array( EZ_TEMPLATE_NODE_ROOT, false );

        $rootNamespace = '';
        $newTpl->parse( $templateCode, $root, $rootNamespace, $resourceData );

        $text = '';
        $newTpl->process( $root, $text, "", "" );

        $parsedCode = $text;
        //eZDebug::writeDebug( $text, 'returned template output' );
    }
}

$tpl = templateInit();

if ( isset( $parsedCode ) )
{
    $tpl->setVariable( 'parsed_code', $parsedCode );
}

$tpl->setVariable( 'template_code', $templateCode );

$Result = array();
$Result['left_menu'] = 'design:parts/developer/menu.tpl';
$Result['content'] =& $tpl->fetch( "design:$moduleName/$functionName.tpl" );
$Result['path'] = array( array( 'url' => false, 'text' => $moduleInfo['name'] ),
                         array( 'url' => false, 'text' => 'Template parser' )
                       );

?>