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

    //include needed kernel classes
    include_once( 'kernel/classes/ezscript.php' );
    include_once( 'kernel/classes/ezcontentobjectversion.php' );
    include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );

    // include needed library classes
    include_once( 'lib/ezutils/classes/ezcli.php' );

    $cli = &eZCLI::instance( );

    $script = &eZScript::instance( array( 'description' => 'remove draft of users that aren\'t logged in',
                                      'use-session' => false,
                                      'use-modules' => false,
                                      'use-extensions' => true ) );
    $script->startup( );
    $options = &$script->getOptions( '[preview][sortbyname][untouched]', '', array( 'preview' => 'only preview the results, do not really make changes', 'sortbyname' => 'sort the summary by user name instead of draft count', 'untouched' => 'only remove untouched drafts' ) );
    $script->initialize( );

    $loggedInUsers =& eZUser::fetchLoggedInList( false );

    $draftUsers = array( );

    if ( count( $loggedInUsers ) > 0 )
    {
        $cli->output( 'currently logged in users:' );
        foreach ( $loggedInUsers as $key => $user )
        {
            $cli->output( $user . ' (' . $key . ')' );
            $draftUsers[$key] = array( 'name' => $user, 'drafts' => 0 );
        }
    }
    else
    {
        $cli->output( 'there are no users logged in' );
    }

    $cli->output( '' );

    $cli->output( 'searching for drafts...' );

    $drafts =& eZContentObjectVersion::fetchFiltered( array( 'status' => EZ_VERSION_STATUS_DRAFT ), false, false );

    $draftCount = count( $drafts );

    if ( $draftCount > 0 )
    {
        $removedCount = 0;
        $staleCount = 0;

        foreach ( array_keys( $drafts ) as $draftKey )
        {
            $cli->output( $drafts[$draftKey]->attribute( 'name' ) . ' (object id: ' . $drafts[$draftKey]->attribute( 'contentobject_id' ) . ', version id: ' . $drafts[$draftKey]->attribute( 'id' ) . ')' );

            $creatorID = $drafts[$draftKey]->attribute( 'creator_id' );

            if ( array_key_exists( $creatorID, $draftUsers ) )
            {
                $creatorName = $draftUsers[$creatorID]['name'];
                $draftUsers[$creatorID]['drafts'] = $draftUsers[$creatorID]['drafts'] + 1;
            }
            else
            {
                $creator = $drafts[$draftKey]->attribute( 'creator' );

                if ( $creator )
                {
                    $creatorName = $creator->attribute( 'name' );
                }
                else
                {
                    $creatorName = 'unknown';
                }

                $draftUsers[$creatorID] = array( 'name' => $creatorName, 'drafts' => 1 );
            }

            $cli->output( 'created by: ' . $creatorName );

            if ( $drafts[$draftKey]->attribute( 'created' ) == $drafts[$draftKey]->attribute( 'modified' ) )
            {
                $staleCount++;
            }

            $isLoggedIn =& eZUser::isUserLoggedIn( $creatorID );

            if ( $isLoggedIn )
            {
                $cli->output( 'creator is logged in' );
            }
            else
            {
                $cli->output( 'creator is not logged in, removing draft...' );

                if ( !$options['untouched' ] || ( $options['untouched'] && ( $drafts[$draftKey]->attribute( 'created' ) == $drafts[$draftKey]->attribute( 'modified' ) ) ) )
                {
                    if ( !$options['preview'] )
                    {
                        $drafts[$draftKey]->remove( );
                    }
                }

                $removedCount++;
            }

            $cli->output( '' );
        }

        $cli->output( 'draft count / user:' );

        function sortByName( $a, $b )
        {
            $aName = strtolower( $a['name'] );
            $bName = strtolower( $b['name'] );

            if ( $aName == $bName )
            {
                return 0;
            }

            return ( $aName < $bName ) ? -1 : 1;
        }

        function sortByCount( $a, $b )
        {
            $aName = $a['drafts'];
            $bName = $b['drafts'];

            if ( $aName == $bName )
            {
                return 0;
            }

            return ( $aName < $bName ) ? -1 : 1;
        }

        if ( !$options['sortbyname'] )
        {
            usort( $draftUsers, 'sortByCount' );
        }
        else
        {
            usort( $draftUsers, 'sortByName' );
        }

        foreach ( array_keys( $draftUsers ) as $userKey )
        {
            if ( $draftUsers[$userKey]['drafts'] != 0 )
            {
                $cli->output( $draftUsers[$userKey]['name'] . ': ' . $draftUsers[$userKey]['drafts'] );
            }
        }

        $cli->output( '' );

        $cli->output( 'total number of drafts: ' . $draftCount );
        $cli->output( 'number of removed drafts: ' . $removedCount );
        $cli->output( 'number of untouched drafts (never modified): ' . $staleCount );
    }
    else
    {
        $cli->output( 'no drafts found' );
    }

    $script->shutdown( );

?>
