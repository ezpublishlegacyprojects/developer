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

// include needed library classes
include_once( 'lib/ezutils/classes/ezcli.php' );

$cli = &eZCLI::instance( );

$script = &eZScript::instance( array( 'description' => 'this script automates the removal of objects, usefull when removing corrupt items or cleaning up test data on development portals',
                                      'use-session' => true,
                                      'use-modules' => true,
                                      'use-extensions' => true ) );

$script->startup( );
$options =& $script->getOptions( '[preview]', '[DATATYPESTRING]' );

$script->initialize( );

// check argument count
if ( count( $options['arguments'] ) < 1 )
{
    $script->shutdown( 1, 'wrong argument count' );
}

// get class attributes
$datatypeString =& $options['arguments'][0];

if ( $cli->isLoud( ) )
{
    $cli->output( 'fetching attributes with datatype: ' . $datatypeString );
}

include_once( 'lib/ezdb/classes/ezdb.php' );

$db =& eZDB::instance( );

$sql = 'SELECT id FROM ezcontentclass_attribute WHERE data_type_string=\'' . $db->escapeString( $datatypeString ) . '\'';

$rows =& $db->arrayQuery( $sql );

$rowCount = count( $rows );

if ( $rowCount == 0 )
{
    if ( $cli->isLoud( ) )
    {
        $cli->output( 'no attributes with the specified datatype were found' );
    }
    $script->shutdown( 0 );
}

// delete content and class attributes
$script->setIterationData( '.', '~' );
$script->resetIteration( $rowCount );

foreach( $rows as $row )
{
    if ( !$options['preview'] )
    {
        $db->begin( );

        $result = $db->query( 'DELETE FROM ezcontentobject_attribute WHERE contentclassattribute_id=' . $row['id'] );

        if ( $result === false )
        {
            $db->rollback( );
            $status = false;
            $script->iterate( &$cli, $status );
            continue;
        }

        $result = $db->query( 'DELETE FROM ezcontentclass_attribute WHERE id=' . $row['id'] );

        if ( $result !== false )
        {
            $db->commit( );
            $status = true;
        }
        else
        {
            $db->rollback( );
            $status = false;
        }
    }
    else
    {
        $status = false;
    }

    $script->iterate( &$cli, $status );
}

$script->shutdown( 0 );

?>
