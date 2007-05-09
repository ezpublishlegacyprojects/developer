#!/usr/bin/env php
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

// include needed kernel classes
include_once( 'kernel/classes/ezscript.php' );
include_once( 'kernel/classes/ezcontentclass.php' );
include_once( 'kernel/classes/ezcontentcache.php' );
include_once( 'kernel/classes/ezcontentobject.php' );

// include needed library classes
include_once( 'lib/ezutils/classes/ezcli.php' );

$cli = &eZCLI::instance( );

$script = &eZScript::instance( array( 'description' => 'this script automates the adding of new nodes for objects',
                                      'use-session' => false,
                                      'use-modules' => false,
                                      'use-extensions' => true ) );

$script->startup( );

$options = &$script->getOptions( '[classidentifier:][objectid:][exclude:][preview][parentnode:]', '', array( 'classidentifier' => 'class identifier of the objects you want to add nodes', 'exclude' => 'comma seperated list of object id\'s to exclude from adding', 'preview' => 'don\'t really add new nodes, just show what would happen', 'objectid' => 'comma seperated list of the object id\'s you want to use', 'parentnode' => 'id of the new parentnode' ) );
$script->initialize( );

//var_dump( $options );
$objects = array( );

if ( ( $options['classidentifier'] or $options['objectid'] ) and $options['parentnode'] )
{
    // get the objects according to specified options
    if ( $options['classidentifier'] )
    {
        $class = &eZContentClass::fetchByIdentifier( $options['classidentifier'] );

        if ( $class )
        {
            $objects = &eZContentObject::fetchSameClassList( $class->attribute( 'id' ) );
        }
        else
        {
            $cli->output( 'could not find class' );
        }
    }
    elseif ( $options['objectid'] )
    {
        $idList = explode( ',', $options['objectid'] );

        if ( is_array( $idList ) )
        {
            $objects = &eZContentObject::fetchIDArray( $idList );
        }
        else
        {
            $objects[] = &eZContentObject::fetch( $idList );
        }
    }

    if ( count( $objects ) > 0 )
    {
        $removedCount = 0;
        $excludes = array( );

        if ( $options['exclude'] )
        {
            $excludes = explode( ',', $options['exclude'] );
        }

        foreach( array_keys( $objects ) as $key )
        {
            if ( in_array( $objects[$key]->attribute( 'id' ), $excludes ) )
            {
                $cli->output( 'excluding object ' . $objects[$key]->attribute( 'id' ) . ': ' . $objects[$key]->attribute( 'name' ) );
            }
            else
            {
                $cli->output( 'adding new node for object ' . $objects[$key]->attribute( 'id' ) . ': ' . $objects[$key]->attribute( 'name' ) );

                if ( $options['preview'] !== true )
                {
                    $nodeAssignment = &eZNodeAssignment::create( array( 'contentobject_id' => $objects[$key]->attribute( 'id' ), 'contentobject_version' => $objects[$key]->attribute( 'current_version' ), 'parent_node' => $options['parentnode'],
                                    'sort_field' => 1,
                                    'sort_order' => 1,
                                    'is_main' => 0
                            ) );

                    $nodeAssignment->store();

                    $parentNodeObject = eZContentObjectTreeNode::fetch( $options['parentnode'] );

                    $targetNode = &eZContentObjectTreeNode::addChild( $objects[$key]->attribute( 'id' ), $options['parentnode'], true );
                    // var_dump( $targetNode );
                    $targetNode->setAttribute( 'sort_field', $nodeAssignment->attribute( 'sort_field' ) );
                    $targetNode->setAttribute( 'sort_order', $nodeAssignment->attribute( 'sort_order' ) );
                    $targetNode->setAttribute( 'contentobject_version', $objects[$key]->attribute( 'current_version' ) );
                    $targetNode->setAttribute( 'contentobject_is_published', 1 );
                    $targetNode->setAttribute( 'main_node_id', $objects[$key]->attribute( 'main_node_id' ) );
                    $targetNode->setName( $objects[$key]->attribute( 'name' ) );

                    $targetNode->store();
                    $targetNode->updateSubTreePath();
                    // $targetNode->store( );
                    eZContentObject::expireTemplateBlockCache( );
                    eZContentCache::cleanup( array( $options['parentnode'] ) );
                }

                $removedCount++;
            }
        }
    }

    $cli->output( 'count: ' . $removedCount );
}
else
{
    $script->showHelp( );
}

$script->shutdown( );

?>
