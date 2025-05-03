<?php
//
// Function definition
//
// Created on: <17-Mar-2007 21:00:27 GTM+8>
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

/*!
  \class   
  \ingroup eZDatatype
  \brief   
  \date    Saturday 17 March 2007 19:13:52 GMT+8
  \author  Michael Lee
*/


$FunctionList = array();

$FunctionList['category_class_list'] = array( 'name' => 'category_class_list',
                                                        'operation_types' => array( 'read' ),
                                                        'call_method' => array('include_file' => 'extension/mzcategoryselection/modules/mzcategoryselection/mzcategoryselectionfunctioncollection.php',
                                              'class' => 'mzcategoryselectionFunctionCollection',
                                              'method' => 'fetchCategoryClassList' ),
                                              'parameter_type' => 'standard',
                                              'parameters' => array(array('name' => 'parent_class_filter',
                                                                          'type' => 'array',
									  'required' => false,
								          'default' => false)));

?>
