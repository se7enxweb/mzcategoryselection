<?php
//
// Definition of mzcategoryselectionfunctioncollection class
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
  \class   mzcategoryselectionfunctioncollection mzcategoryselectionfunctioncollection.php
  \ingroup eZDatatype
  \brief   
  \date    Saturday 17 March 2007 19:13:52 GMT+8
  \author  Michael Lee
*/

//include_once( 'kernel/error/errors.php' );
//include_once( 'kernel/class/ezclassfunctioncollection.php');

class mzcategoryselectionFunctionCollection
{
    /*!
     Constructor
    */
    function mzcategoryselectionFunctionCollection(){

    }

    function &fetchCategoryClassList($parent_class_filter = false){

             $ini = eZINI::instance('mzcategoryselection.ini');

	     $classes = array();

	     $result = array();

	     if($parent_class_filter){

	        foreach($parent_class_filter as $parent_class){

		     $category_class_identifiers = $ini->variable('CategoryClassFilterSettings',$parent_class);

		     if(isset($category_class_identifiers)){

			$classes = array_merge($classes,$category_class_identifiers);

		     }

	        }

	     }else{

	        //if($classes[0]==false){ $classes = $ini->variable('CategoryClassFilterSettings','default'); }
	        $contentClassList = eZContentClass::fetchList( 0, false);
		foreach($contentClassList as $class){

			$classes[]=$class['identifier'];

		}

	     }

	     $classes = array_unique($classes);

	     $result['result'] = $classes;
	     return $result;

    }

}

?>
