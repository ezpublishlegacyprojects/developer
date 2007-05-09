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
include_once( 'kernel/classes/ezcontentobject.php' );
include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
include_once( 'kernel/classes/notification/handler/ezcollaborationnotification/ezcollaborationnotificationrule.php' );

// include needed library classes
include_once( 'lib/ezdb/classes/ezdb.php' );
include_once( 'lib/ezutils/classes/ezcli.php' );

$cli = &eZCLI::instance( );

$script = &eZScript::instance( array( 'description' => 'automatically assign collaboration notification rules to specific users',
                                      'use-session' => false,
                                      'use-modules' => false,
                                      'use-extensions' => true ) );
$script->startup( );
$options = &$script->getOptions( '[rule:][login:][exclude:][preview]', '', array( 'rule' => 'name of the rule (collaboration identifier) you wish to assign', 'login' => 'login of the user you want to assign the rule to, supposed to be all users if not specified', 'exclude' => 'comma seperated list of the login names of users to exclude', 'preview' => 'preview the results without making any changes' ) );
$script->initialize( );

if ( $options['rule'] )
{
    $db = &eZDB::instance();

    if ( $options['login'] )
    {
        $results = &$db->arrayQuery( 'SELECT contentobject_id, login FROM ezuser where login=\'' . $options['login'] . '\'' );
    }
    else
    {
        $results = &$db->arrayQuery( 'SELECT contentobject_id, login FROM ezuser' );
    }

    if ( count( $results ) > 0 )
    {
        $count = 0;

        if ( $options['exclude'] )
        {
            $excludes = explode( ',', $options['exclude'] );
        }
        else
        {
            $excludes = array( );
        }

        foreach( array_keys( $results ) as $key )
        {
            if ( in_array( $results[$key]['login'], $excludes ) )
            {
                if ( !$isQuiet )
                {
                    $cli->output( 'excluding ' . $results[$key]['login'] );
                }
            }
            else
            {
                if ( !$isQuiet )
                {
                    $cli->output( 'assigning rule for ' . $results[$key]['login'] );
                }

                $existing = &eZCollaborationNotificationRule::fetchItemTypeList( $options['rule'], array( $results[$key]['contentobject_id'] ) );

                if ( count( $existing ) == 0 )
                {
                    $count++;

                    if ( !$options['preview'] )
                    {
                        $rule = &eZCollaborationNotificationRule::create ( $options['rule'], $results[$key]['contentobject_id'] );
                        $rule->store( );
                    }
                }
                else
                {
                    if ( !$isQuiet )
                    {
                        $cli->output( 'rule already assigned to user ' . $results[$key]['login'] );
                    }
                }
            }
        }

        if ( !$isQuiet )
        {
            $cli->output( 'count: ' . $count );
        }
    }
    else
    {
        if ( !$isQuiet )
        {
            $cli->output( 'no users found' );
        }
    }
}
else
{
    $script->showHelp( );
}

$script->shutdown( );

?>
