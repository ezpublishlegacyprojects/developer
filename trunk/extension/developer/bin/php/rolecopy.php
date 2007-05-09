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
include_once( 'kernel/classes/ezrole.php' );

// include needed library classes
include_once( 'lib/ezutils/classes/ezcli.php' );

$cli = &eZCLI::instance( );

$script = &eZScript::instance( array( 'description' => 'this script automates the copying of a role',
                                      'use-session' => false,
                                      'use-modules' => false,
                                      'use-extensions' => true ) );

$script->startup( );
$options = &$script->getOptions( '', '[name of role][name for new role]', array( ) );
$script->initialize( );

if ( count( $options['arguments'] ) == 2 )
{
    $role = &eZRole::fetchByName( $options['arguments'][0] );

    if ( $role )
    {
        $newRole = eZRole::createNew( );
        $newRole->setAttribute( 'name', $options['arguments'][1] );
        $newRoleId = &$newRole->attribute( 'id' );

        if ( !$isQuiet )
        {
            $cli->output( 'copying policies from role \'' . $options['arguments'][0] . '\' (' . $role->attribute( 'id' ) . ') to \'' . $options['arguments'][1] . '\' (' . $newRoleId . ')' );
        }
        $role->copyPolicies( $newRoleId );
        $newRole->store( );
    }
    else
    {
        $cli->output( 'could not find a role with name \'' . $options['arguments'][0] . '\'' );
    }
}
else
{
    $script->showHelp( );
}

$script->shutdown( );

?>
