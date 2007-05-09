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

include_once( 'kernel/classes/ezcontentobject.php' );
include_once( 'kernel/classes/ezscript.php' );

// needed to avoid a bug of a missing include (after including ezoperationhandler)
include_once( 'kernel/classes/ezpersistentobject.php' );

include_once( 'lib/ezutils/classes/ezcli.php' );

$cli =& eZCLI::instance();
$script =& eZScript::instance( array( 'description' => ( 'Remove invalid content objects' ),
                                      'use-session' => false,
                                      'use-modules' => true,
                                      'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions( '[remove]', '[CLASS IDENTIFIER*]', array( 'remove' => 'removed invalid data' ), false, array( 'user' => true ) );

// make sure the script logs in as the specified user in the options
// (actually this is something that they forgotten to program in ezscript)
if ( $options['login'] and $options['password'] )
{
    $script->setUser( $options['login'], $options['password'] );
}

$script->initialize( );

$argumentCount = count( $options['arguments'] );

if ( $argumentCount  > 0 )
{
    include_once( 'kernel/classes/ezcontentclass.php' );

    foreach ( $options['arguments'] as $classIdentifier )
    {
        $removedCount = 0;
        $class =& eZContentClass::fetchByIdentifier( $classIdentifier );

        if ( !$class )
        {
            $script->shutdown( 1, 'invalid class identifier: ' . $classIdentifier );
        }

        $objects = &eZContentObject::fetchSameClassList( $class->attribute( 'id' ) );

        $objectCount = count( $objects );

        $cli->output( 'object count of class "' . $classIdentifier . '": ' . $objectCount );

        if ( $objectCount > 0 )
        {
            $script->setIterationData( '.', '~' );

            $script->resetIteration( $objectCount );

            foreach ( array_keys( $objects ) as $objectKey )
            {
                $status = (int)$objects[$objectKey]->attribute( 'status' );

                $name = $objects[$objectKey]->attribute( 'name' );

                if ( $status === EZ_CONTENT_OBJECT_STATUS_PUBLISHED )
                {
                    $status = $objects[$objectKey]->attribute( 'main_node' );

                    if ( $status )
                    {
                        $text = 'passed validation check: ';
                    }
                    else
                    {
                        $owner =& $objects[$objectKey]->attribute( 'owner' );
                        /*
                        $cli->output( $owner->attribute( 'name' ) );
                        $cli->output( date( 'Y-m-d H:i', $objects[$objectKey]->attribute( 'modified' ) ) );
                        */
                        if ( $options['remove'] )
                        {
                            $text = 'failed validation check, removing: ';
                            $objects[$objectKey]->remove( );
                            $objects[$objectKey]->purge( );
                        }
                        else
                        {
                            $text = 'failed validation check: ';
                        }

                        $removedCount++;
                    }
                }
                else
                {
                    $status = true;
                    $text = 'passed validation check: ';
                }

                $script->iterate( $cli, $status, $text . $cli->stylize( 'file', $name ) );
            }
        }

        $cli->output( );

        if ( $options['remove'] )
        {
            $cli->warning( 'removed invalid objects: ' . $removedCount );
        }
        else
        {
            $cli->warning( 'invalid objects: ' . $removedCount );
        }
    }
}
else
{
    $script->shutdown( 1, 'Missing argument [CLASS IDENTIFIER]' . "\n" . 'use --help to show more info' );
}

$script->shutdown();

?>
