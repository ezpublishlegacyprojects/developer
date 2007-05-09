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

$Module = array( 'name' => 'Developer' );

$updateParameterArray = array(
            'ParentNodeID' => 'ParentNodeID',
            'Limit' => 'Limit',
            'IgnoreVisibility' => 'IgnoreVisibility',
            'MainNodeOnly' => 'MainNodeOnly',
            'OnlyTranslated' => 'OnlyTranslated',
            'EmptyLimitation' => 'EmptyLimitation',
            'ClassFilterType' => 'ClassFilterType',
            'ClassFilterArray' => 'ClassFilterArray',
            'SortingElementsMethod' => 'SortingElementsMethod',
            'SortingElementsValue' => 'SortingElementsValue',
            'SortingElementsDirection' => 'SortingElementsDirection',
            'Depth' => 'Depth',
            'DepthOperator' => 'DepthOperator',
            'Language' => 'Language'
        );

$ViewList = array();
$ViewList['treefetchbuilder'] = array(
    'default_navigation_part' => 'ezdevnavigationpart',
    'script' => 'treefetchbuilder.php',
    'functions' => array( ),
    'single_post_actions' => array(
        'UpdateButton' => 'Update',
        'EmptyButton' => 'Empty',
        'AddSortingElementButton' => 'AddSortingElement',
        'RemoveSelectedSortingButton' => 'RemoveSelectedSorting'
        ),
    'post_action_parameters' => array(
        'Update' => $updateParameterArray,
        'AddSortingElement' => array_merge(
            array(
                'NewSortingElementMethod' => 'NewSortingElement',
                'NewSortingElementDirection' => 'NewSortingElementDirection',
                'NewSortingElementValue' => 'NewSortingElementValue'
                ),
            $updateParameterArray
        ),
        'RemoveSelectedSorting' => array_merge(
            array(
                'SelectedSorting' => 'SelectedSorting',
                ),
            $updateParameterArray
        )
    )
);

$ViewList['template'] = array(
    'default_navigation_part' => 'ezdevnavigationpart',
    'script' => 'template.php',
    'functions' => array(),
    'single_post_actions' => array(
        'ParseButton' => 'Parse'
    ),
    'post_action_parameters' => array(
        'Parse' => array( 'TemplateCode' => 'TemplateCodeInput' )
    ) );

?>
