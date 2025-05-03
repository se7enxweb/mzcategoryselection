<?php
//
//
// Created on: <2-Sep-2006 21:00:27 GTM+8>
//
// SOFTWARE NAME: 
// SOFTWARE RELEASE: 
// BUILD VERSION: 
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 ZERUS TECHNOLOGY LTD (http://www.zerustech.com) AS
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


 $eZTemplateOperatorArray = array();
 $eZTemplateOperatorArray[] = array( 'script' => 'extension/mzcategoryselection/autoloads/mzcategoryselectionutiloperators.php',
        'class' => 'mzCategorySelectionUtil',
        'class_parameter' => 'mzCategorySelectionUtil',
	'operator_names' => array( 'option_filter',
	                           'category_options',
				   'object_category_options',
				   'object_category_values',
				   'has_related_category_options',
				   'category_option_simple_array',
				   'category_option_array',
				   'thresholds',
				   'generate_category_string',
				   'is_empty_category',
			           'category_option_values'));

?>
