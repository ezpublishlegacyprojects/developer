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
include_once( 'kernel/classes/ezcontentobject.php' );

// include needed library classes
include_once( 'lib/ezutils/classes/ezcli.php' );

$cli = &eZCLI::instance( );

$script = &eZScript::instance( array( 'description' => 'this script automates the removal of objects, usefull when removing corrupt items or cleaning up test data on development portals',
                                      'use-session' => false,
                                      'use-modules' => false,
                                      'use-extensions' => true ) );

$script->startup( );
$options = $script->getOptions( '[classidentifier:*][objectid:][exclude:][preview]', '', array( 'classidentifier' => 'class identifier of the objects you want to remove', 'exclude' => 'comma seperated list of object id\'s to exclude from removal', 'preview' => 'don\'t really remove objects, just show what would happen', 'objectid' => 'comma seperated list of the object id\'s you want to remove' ), false, array( 'user' => true ) );

// make sure the script logs in as the specified user in the options
// (actually this is something that they forgotten to program in ezscript)
if ( $options['login'] and $options['password'] )
{
    $script->setUser( $options['login'], $options['password'] );
}

$script->initialize( );

$objects = array( );

if ( $options['classidentifier'] or $options['objectid'] )
{
    // get the objects according to specified options
    if ( $options['classidentifier'] )
    {
        foreach ( $options['classidentifier'] as $classIdentifier )
        {
            $class = &eZContentClass::fetchByIdentifier( $classIdentifier );

            if ( $class )
            {
                $objects = &eZContentObject::fetchSameClassList( $class->attribute( 'id' ) );
            }
            else
            {
                $cli->output( 'could not find class' . $classIdentifier );
            }
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
                $cli->output( 'removing object ' . $objects[$key]->attribute( 'id' ) . ': ' . $objects[$key]->attribute( 'name' ) );

                if ( $objects[$key]->canRemove() )
                {
                    if ( $options['preview'] !== true )
                    {
                        if ( $objects[$key]->attribute( 'status' ) == EZ_CONTENT_OBJECT_STATUS_PUBLISHED )
                        {
                            $assignedNodes = $objects[$key]->attribute( 'assigned_nodes' );

                            $nodeIdArray = array( );
                            foreach ( array_keys( $assignedNodes ) as $assignedNodeKey )
                            {
                                if ( $assignedNodes[$assignedNodeKey]->attribute( 'can_remove' ) )
                                {
                                    $nodeIdArray[] = $assignedNodes[$assignedNodeKey]->attribute( 'node_id' );
                                }
                                else
                                {
                                    $nodeIdArray = false;
                                    break;
                                }
                            }

                            if ( $nodeIdArray )
                            {
                                eZContentObjectTreeNode::removeSubtrees( $nodeIdArray, false );
                            }
                            else
                            {
                                $objects[$key]->remove();
                                $objects[$key]->purge();
                            }
                        }
                        else
                        {
                            $objects[$key]->remove();
                            $objects[$key]->purge();
                        }
                    }
                }
                else
                {
                    $cli->warning( 'You have insufficient permissions to remove this object.' );
                }

                $removedCount++;
            }
        }

        $cli->output( 'removed items: ' . $removedCount );
    }
    else
    {
        $cli->output( 'no objects of this class were found' );
    }
}
else
{
    $script->showHelp( );
}

$script->shutdown( );

?>
