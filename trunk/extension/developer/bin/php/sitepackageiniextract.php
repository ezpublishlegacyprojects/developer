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

include_once( 'kernel/classes/ezscript.php' );
include_once( 'lib/ezutils/classes/ezcli.php' );

$cli =& eZCLI::instance();

$scriptSettings = array();
$scriptSettings['description'] = 'Extract INI settings to an INI file from site packages';
$scriptSettings['use-session'] = true;
$scriptSettings['use-modules'] = true;
$scriptSettings['use-extensions'] = true;

$script =& eZScript::instance( $scriptSettings );
$script->startup();

$config = '';
$argumentConfig = '[includefile][function][outputdir]';
$optionHelp = false;
$arguments = false;
$useStandardOptions = true;

$options = $script->getOptions( $config, $argumentConfig, $optionHelp, $arguments, $useStandardOptions );
$script->initialize();

if ( count( $options['arguments'] ) != 3 )
{
    $script->shutdown( 1, 'wrong argument count' );
}

$includeFile = $options['arguments'][0];
$function = $options['arguments'][1];
$outputDir = $options['arguments'][2];

include_once( $includeFile );

$settingsArray = call_user_func( $function );

//var_dump( $settingsArray );

// original code taken from kernel/setup/steps/ezstep_create_sites.php

include_once( 'lib/ezutils/classes/ezini.php' );

$iniName = $settingsArray['name'];
$settings = $settingsArray['settings'];
$resetArray = false;
if ( isset( $settingsArray['reset_arrays'] ) )
{
    $resetArray = $settingsArray['reset_arrays'];
}

$cli->output( $iniName );

$tmpINI =& eZINI::create( $iniName );
// Set ReadOnlySettingsCheck to false: towards
// Ignore site.ini[eZINISettings].ReadonlySettingList[] settings when saving ini variables.
$tmpINI->setReadOnlySettingsCheck( false );

$tmpINI->setVariables( $settings );
$cli->output( 'saving ' . $iniName . ' to ' . $outputDir );
$saveResult = $tmpINI->save( false, '.append.php', false, true, $outputDir, $resetArray );
eZDebug::writeDebug( $saveResult );

$script->shutdown( 0 );

?>