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
include_once( 'kernel/classes/ezcontentobjecttreenode.php' );

// include needed library classes
include_once( 'lib/ezutils/classes/ezcli.php' );

$cli = &eZCLI::instance( );

$script = &eZScript::instance( array( 'description' => 'this script automates assigning of sections',
                                      'use-session' => false,
                                      'use-modules' => false,
                                      'use-extensions' => true ) );

$script->startup( );

$options = &$script->getOptions( '[classidentifier:][objectid:][exclude:][preview][sectionid:]', '', array( 'classidentifier' => 'class identifier of the objects you want to assign the section to', 'exclude' => 'comma seperated list of object id\'s to exclude', 'preview' => 'don\'t really assign the section, just show what would happen', 'objectid' => 'comma seperated list of the object id\'s you want to use', 'sectionid' => 'id of the new section' ) );
$script->initialize( );

//var_dump( $options );
$objects = array( );

if ( ( $options['classidentifier'] or $options['objectid'] ) and $options['sectionid'] )
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
        $assignedCount = 0;
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
                $cli->output( 'assigning new section for object ' . $objects[$key]->attribute( 'id' ) . ': ' . $objects[$key]->attribute( 'name' ) . ' with main node ' . $objects[$key]->attribute( 'main_node_id' ) );

                if ( $options['preview'] !== true )
                {
                    eZContentObjectTreeNode::assignSectionToSubTree( $objects[$key]->attribute( 'main_node_id' ), $options['sectionid'] );
                }

                $assignedCount++;
            }
        }

        eZContentObject::expireAllCache( );
    }

    $cli->output( 'count: ' . $assignedCount );
}
else
{
    $script->showHelp( );
}

$script->shutdown( );

?>
