<?php
//
// Definition of mzCategorySelectionType class
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

/*!
  \class   mzcategoryselectiontype mzcategoryselectiontype.php
  \ingroup eZDatatype
  \brief   A port of ezcategoryselectiontype for ezpublish 4.1+
           Handles the single and multiple category based selections.
  \date    Saturday 2 September 2006 21:00:27 pm GMT+8
  \author  Michael Lee ( ported by Lazaro Ferreira May/2009 )

*/

class mzCategorySelectionType extends eZDataType
{
    const DATA_TYPE_STRING = 'mzcategoryselection';
    /*!
      Constructor
    */
    function __construct()
    {
        $this->eZDataType( self::DATA_TYPE_STRING,
	                   ezpI18n::tr( 'kernel/classes/datatypes', "Category Selection", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }

    /*!
     Validates all variables given on content class level
     \return EZ_INPUT_VALIDATOR_STATE_ACCEPTED or EZ_INPUT_VALIDATOR_STATE_INVALID if
             the values are accepted or not
    */
    function validateClassAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches all variables inputed on content class level
     \return true if fetching of class attributes are successfull, false if not
    */
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        //restore attribute content from xml
	$attributeContent_tmp = $this->classAttributeContent( $classAttribute );
        $attributeContent =& $attributeContent_tmp;

	//get class attribute id
        $classAttributeID = $classAttribute->attribute( 'id' );

        $isMultipleSelection = false;

	$threshold = 0;

	$isCrossClass = false;

	$description = "";


	//current options
	//option = array('id'=>id,'name'=>name,'categories'=>
	//array(array('category_id'=>category_id,'category_value'=>array(...)...))
        $currentOptions =& $attributeContent['options'];

	//current categories
	//category = array('id'=>id,'category_id'=>category_id,'category_value'=>array(...))
	$currentCategories = $attributeContent['categories'];
        $hasPostData = false;

	//update ismultiple flag from http input
        if ( $http->hasPostVariable( $base . "_mzcategoryselection_ismultiple_value_" . $classAttributeID ) )
        {
	    if( $http->postVariable( $base . "_mzcategoryselection_ismultiple_value_" . $classAttributeID ) != 0 )
	    {
                $isMultipleSelection = true;
	    }

        }

	//update threshold
	if ( $http->hasPostVariable( $base . "_mzcategoryselection_threshold_" . $classAttributeID ) )
        {
	    $threshold = $http->postVariable( $base . "_mzcategoryselection_threshold_" . $classAttributeID );

	    $threshold = (int)$threshold;

        }


	//update description from http input
	if ( $http->hasPostVariable( $base . "_mzcategoryselection_description_" . $classAttributeID ) )
        {
	     $description = $http->postVariable( $base . "_mzcategoryselection_description_" . $classAttributeID );

        }

	//update isCrossClass flag from http input
	$cross_class_changed = false;

        if ( $http->hasPostVariable( $base . "_mzcategoryselection_iscrossclass_" . $classAttributeID ) )
        {
	    if($http->postVariable( $base . "_mzcategoryselection_iscrossclass_" . $classAttributeID ) !=
               $classAttribute->attribute('data_int2')){

               $cross_class_changed = true;

               $hasPostData = true;

	    }

	    if( $http->postVariable( $base . "_mzcategoryselection_iscrossclass_" . $classAttributeID ) != 0 )
	    {
                $isCrossClass = true;
	    }

        }

	//Update option names from http input
        if ( $http->hasPostVariable( $base . "_mzcategoryselection_option_name_array_" . $classAttributeID ) )
        {
            $nameArray = $http->postVariable( $base . "_mzcategoryselection_option_name_array_" . $classAttributeID );

            // Fill in new names for options
            foreach ( array_keys( $currentOptions ) as $key )
            {
                $currentOptions[$key]['name'] = $nameArray[$currentOptions[$key]['id']];

            }

            $hasPostData = true;

        }

	//update order
	//moves selected option up/down
	if ( $http->hasPostVariable( "option_move_down_array_".$classAttributeID) ||
	     $http->hasPostVariable( "option_move_up_array_".$classAttributeID) )
        {

	    $move_option = "";

	    $move_option_id = -1;

            $option_move_array = null;

            if($http->hasPostVariable( "option_move_down_array_".$classAttributeID)){

	       $move_option = "down";

               $option_move_array = $http->postVariable("option_move_down_array_".$classAttributeID);

	    }else{

               $move_option = "up";

               $option_move_array = $http->postVariable("option_move_up_array_".$classAttributeID);

	    }

	    foreach($option_move_array as $option_id){

	            $move_option_id = $option_id;

		    break;

	    }

	    $current_option = null;

	    $move_category_string = null;

	    $category_options = array();

	    foreach($currentOptions as $key=>$option){

	            $categories = $option['categories'];

	            $category_string_array = $this->generate_category_string($categories);

	            $category_string = $category_string_array['key'];

		    if($category_string==""){

                       $category_string=="none";

		    }

		    $category_options[$category_string][$option['id']]['option']=$option;

		    $category_options[$category_string][$option['id']]['index']=$key;

		    if($option['id']==$move_option_id){

                       $move_category_string = $category_string;

		    }

	    }

	    $move_option_array = $category_options[$move_category_string];

	    $move_option_id_array = array_keys($move_option_array);

	    for($i=0;$i<count($move_option_id_array);$i++){

                $option_id = $move_option_id_array[$i];

                if($option_id==$move_option_id){

			if(($move_option=="down" && $i<(count($move_option_id_array)-1)) ||
		           ($move_option=="up" && $i>0)){

                           if($move_option=="down"){

                              $next_option_id = $move_option_id_array[$i+1];

			   }else{

                              $next_option_id = $move_option_id_array[$i-1];

			   }

                           $next_option_index = $move_option_array[$next_option_id]['index'];

			   $move_option_index = $move_option_array[$move_option_id]['index'];

			   $move_option = $currentOptions[$move_option_index];

			   $currentOptions[$move_option_index]=$currentOptions[$next_option_index];

			   $currentOptions[$next_option_index]=$move_option;

			   break;

			}

		}

	    }

            $hasPostData = true;

        }

	//Create new category entry
        if ( $http->hasPostVariable( $base . "_mzcategoryselection_newcategory_button_" . $classAttributeID ) )
        {

            $currentCount = 0;

            foreach ( $currentCategories as $category )
            {
                $currentCount = max( $currentCount, $category['id'] );
            }

            $currentCount += 1;

            $currentCategories[] = array( 'id' => $currentCount,
		                          'category_class' => '',
                                          'category_id' => '0',
					  'category_value'=> array());
            $hasPostData = true;
        }

	//Remove selected categories. Before removing the selected categories,
	//they should be removed from current options first
        if ( $http->hasPostVariable( $base . "_mzcategoryselection_removecategory_button_" . $classAttributeID ) )
        {
            if ( $http->hasPostVariable( $base . "_mzcategoryselection_category_remove_array_". $classAttributeID ) )
            {
                $removeArray = $http->postVariable( $base . "_mzcategoryselection_category_remove_array_". $classAttributeID );
                foreach ( array_keys( $currentCategories ) as $key )
                {
                    if ( $removeArray[$currentCategories[$key]['id']] ){

			//Remove selected category from current options
			$this->removeCategoryFromOptions($currentOptions,$currentCategories[$key]['category_id']);

			//remove selected category from current categories
                        unset( $currentCategories[$key] );
                    }
                }

                $hasPostData = true;

            }
        }

        //Processing category class list
	$category_class_changed = array();

	if($http->hasPostVariable($base .
	                          "_mzcategoryselection_category_class_array_".
				  $classAttributeID) &&
           $http->hasPostVariable("RefreshButton")){

           $category_class_array = $http->postVariable($base .
	                           "_mzcategoryselection_category_class_array_".
				   $classAttributeID);

	   foreach ( array_keys( $currentCategories ) as $key ){

		     //If the cross class flag has been changed,
		     //clean up the category class from current categories
		     if($cross_class_changed){

                        $currentCategories[$key]['category_class'] = '';

                        $category_class_changed[$key] = true;

			continue;

		     }
		     //Category class has been changed
		     if($currentCategories[$key]['category_class']!=
                        $category_class_array[$currentCategories[$key]['id']]){

                        $category_class_changed[$key] = true;
			//change category class for current category
                        $currentCategories[$key]['category_class'] =
		        $category_class_array[$currentCategories[$key]['id']];

		     }

	   }

           $hasPostData = true;

	}

	//Processing category list
	$category_changed = array();

        if ( $http->hasPostVariable( $base .
	                             "_mzcategoryselection_category_id_array_".
				     $classAttributeID) &&
             $http->hasPostVariable("RefreshButton")){

             $selectArray = $http->postVariable( $base .
	                                         "_mzcategoryselection_category_id_array_".
						 $classAttributeID );

             foreach ( array_keys( $currentCategories ) as $key ){

		    //If cross class flag has been changed,
		    //current category list should be cleaned up
		    if($cross_class_changed){
                        $currentCategories[$key]['category_id'] = 0;
	                $category_changed[$key] = true;
			continue;
		    }

		    //If category class has been changed
		    //We should ignore the category selection changes
		    //and set the category_id to '0'
		    //The category list should be reloaded according to
		    //the new category class in the template
                    if (isset($category_class_changed[$key]) && $category_class_changed[$key]){
                        $currentCategories[$key]['category_id'] = 0;
			continue;
		    }

                    if ( $selectArray[$currentCategories[$key]['id']] ){

		         //Category selection has been changed.
                         //We need change the category_id for current category
			 if($currentCategories[$key]['category_id']!=
			    $selectArray[$currentCategories[$key]['id']][0]){

	                    $category_changed[$key] = true;

			    $currentCategories[$key]['category_id'] =
                            $selectArray[$currentCategories[$key]['id']][0];

                            //$currentCategories[$key]['category_value']=array();

			 }

		    }

             }

             $hasPostData = true;

        }


		//Processing category values
        if ( $http->hasPostVariable( $base .
			             "_mzcategoryselection_category_value_array_".
				     $classAttributeID)){

             $selectValueArray = $http->postVariable( $base .
				                      "_mzcategoryselection_category_value_array_".
					              $classAttributeID );

             foreach ( array_keys( $currentCategories ) as $key ){

		     //If cross class flag has been changed, the category value should be reset
		     if($cross_class_changed){
                        $currentCategories[$key]['category_value'] = array();
			continue;
		     }

		     //If category class has been changed, the category value should be reset
		     if(isset($category_class_changed[$key]) && $category_class_changed[$key]){
                        $currentCategories[$key]['category_value']=array();
			continue;

		     }

		     //If category has been changed, the category value should be reset
		     if(isset($category_changed[$key]) && $category_changed[$key]){
                        $currentCategories[$key]['category_value']=array();
			continue;

		     }

		     //cross class flag has not been changed.
                     //Category class has not been changed.
		     //Category has not been changed either.
		     //Now we should update category values according to the
		     //selection
                     if ( $selectValueArray[$currentCategories[$key]['id']] ){
                          $currentCategories[$key]['category_value'] =
			  $selectValueArray[$currentCategories[$key]['id']];

		     }else{
                          //if no category value has been selected
			  //uncheck all items in category value list
                          $currentCategories[$key]['category_value']=array();

		     }


	     }

             $hasPostData = true;

	}

	//Update categories for selected options according to current categories
        if ( $http->hasPostVariable( $base .
	                             "_mzcategoryselection_option_remove_array_".
				     $classAttributeID ) ){

             $removeArray = $http->postVariable( $base .
	                                         "_mzcategoryselection_option_remove_array_".
						 $classAttributeID );

             foreach ( array_keys( $currentOptions ) as $key ){

                    if ( isset($removeArray[$currentOptions[$key]['id']]) && $removeArray[$currentOptions[$key]['id']] ){

			 $categoryArray = array();

			 foreach($currentCategories as $category){

				 //only non-empty categories will be added to options
				 if(count($category['category_value'])>0){

				       $categoryArray[]=array('category_id'=>$category['category_id'],
			                                      'category_value'=>$category['category_value']);

                                 }

			 }

			 $currentOptions[$key]['categories']=$categoryArray;
		    }
            }

            $hasPostData = true;

        }

	//Create new option in current options
        if ( $http->hasPostVariable( $base .
	                             "_mzcategoryselection_newoption_button_" .
				     $classAttributeID )){
            $currentCount = 0;

            foreach ( $currentOptions as $option )
            {
                $currentCount = max( $currentCount, $option['id'] );
            }
            $currentCount += 1;

            $currentOptions[] = array( 'id' => $currentCount,
                                       'name' => '',
			               'categories'=>array());
            $hasPostData = true;

        }

	//Remove selected options from current options
        if ( $http->hasPostVariable( $base .
	                             "_mzcategoryselection_removeoption_button_" .
				     $classAttributeID ) ){

            if ( $http->hasPostVariable( $base .
	                                 "_mzcategoryselection_option_remove_array_".
					 $classAttributeID ) ){

                $removeArray = $http->postVariable( $base .
		                                    "_mzcategoryselection_option_remove_array_".
						    $classAttributeID );

                foreach ( array_keys( $currentOptions ) as $key ){

                    if ( $removeArray[$currentOptions[$key]['id']] ){

                        unset( $currentOptions[$key] );

		    }

                }

                $hasPostData = true;

            }

        }

	//If anything changed from http input, the changes should be stored here
        if ( $hasPostData )
        {

            // Serialize XML
			$doc = new DOMDocument( '1.0', 'utf-8' );


			$root = $doc->createElement( 'mzcategoryselection' );

			$doc->appendChild( $root );

			$options = $doc->createElement( 'options' );

	    //create <root>/<options> node
            $root->appendChild( $options );

	    //store option information from current options
            foreach ( $currentOptions as $optionArray )
            {
                //create <root>/<options>/<option> nodes
                unset( $optionNode );
				$optionNode = $doc->createElement( "option" );
				$optionNode->setAttribute( 'id', $optionArray['id'] );
				$optionNode->setAttribute( 'name', $optionArray['name'] );

                $optionCategoryArray = $optionArray['categories'];

		//create <root>/<options>/<option>/<category> nodes
		unset($optionCategories);

	        foreach($optionCategoryArray as $optionCategory){

			if(!$cross_class_changed){

               	unset($optionCategoryNode);
				$optionCategoryNode = $doc->createElement("category");

				$optionCategoryNode->setAttribute( 'category_id',
                                     $optionCategory["category_id"]);
				$optionCategoryNode->setAttribute( 'category_value',
                                implode("-",
                               $optionCategory["category_value"]));
   		   		$optionNode->appendChild($optionCategoryNode);

			}

		}

                $options->appendChild( $optionNode );

            }

	    //create <root>/<categories> node
		$categories = $doc->createElement("categories");
            $root->appendChild( $categories );
            foreach ( $currentCategories as $category )
            {
                unset( $categoryNode );
				$categoryNode = $doc->createElement( "category" );
				$categoryNode->setAttribute( 'id', $category['id'] );
				$categoryNode->setAttribute( 'category_class', $category['category_class'] );
				$categoryNode->setAttribute( 'category_id',$category['category_id']);
				$categoryNode->setAttribute( 'category_value',
                                                      implode('-',$category['category_value']));
                $categories->appendChild( $categoryNode );
            }

			$xml = $doc->saveXML();


            $classAttribute->setAttribute( "data_text5", $xml );

            if ( $isMultipleSelection == true )
                $classAttribute->setAttribute( "data_int1", 1 );
            else
                $classAttribute->setAttribute( "data_int1", 0 );

            if ( $isCrossClass == true )
                $classAttribute->setAttribute( "data_int2", 1 );
            else
                $classAttribute->setAttribute( "data_int2", 0 );

	    //update description
	    $classAttribute->setAttribute("data_text4",$description);

	    //update threshold
	    $classAttribute->setAttribute("data_int3",$threshold);

        }

        return true;
    }

    /*!
     Validates input on content object level
     \return EZ_INPUT_VALIDATOR_STATE_ACCEPTED or EZ_INPUT_VALIDATOR_STATE_INVALID if
             the values are accepted or not
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute ){


        if ( $http->hasPostVariable( $base .
	                             '_ezselect_selected_array_' .
				     $contentObjectAttribute->attribute( 'id' ) ) ){

            $data = $http->postVariable( $base .
	                                 '_ezselect_selected_array_' .
					 $contentObjectAttribute->attribute( 'id' ) );

            if ( $data == "" ){

                if ( $contentObjectAttribute->validateIsRequired() ){

                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches all variables from the object
     \return true if fetching of class attributes are successfull, false if not
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute ){


        if ( $http->hasPostVariable( $base .
	                             '_ezselect_selected_array_' .
				     $contentObjectAttribute->attribute( 'id' ) ) ){

            $selectOptions = $http->postVariable( $base .
	                                          '_ezselect_selected_array_' .
						  $contentObjectAttribute->attribute( 'id' ) );

            $idString = ( is_array( $selectOptions ) ? implode( '-', $selectOptions ) : "" );

            $contentObjectAttribute->setAttribute( 'data_text', $idString );

            return true;

        }

        return false;
    }

    /*!
     \reimp
    */
    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute ){

        return eZInputValidator::STATE_ACCEPTED;
    }

   /*!
    \reimp
    Fetches the http post variables for collected information
   */
    function fetchCollectionAttributeHTTPInput( $collection,
                                                $collectionAttribute,
						$http,
						$base,
						$contentObjectAttribute ){

        if ( $http->hasPostVariable( $base .
	                             '_ezselect_selected_array_' .
				     $contentObjectAttribute->attribute( 'id' ) ) ){

            $selectOptions = $http->postVariable( $base .
	                                          '_ezselect_selected_array_' .
						  $contentObjectAttribute->attribute( 'id' ) );

            $idString = ( is_array( $selectOptions ) ? implode( '-', $selectOptions ) : "" );

            $collectionAttribute->setAttribute( 'data_text', $idString );

            return true;

        }

        return false;

    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute,
                                        $currentVersion,
					$originalContentObjectAttribute ){



        if ( $currentVersion != false ){

            $idString = $originalContentObjectAttribute->attribute( "data_text" );

            $contentObjectAttribute->setAttribute( "data_text", $idString );

            $contentObjectAttribute->store();

        }

    }

    /*!
     Returns the selected options by id.
    */
    function objectAttributeContent( $contentObjectAttribute ){

        $idString = explode( '-', $contentObjectAttribute->attribute( 'data_text' ) );

        return $idString;

    }

    /*!
     Returns the content data for the given content class attribute.
    */
    function classAttributeContent( $classAttribute ){

		$dom = new DOMDocument( '1.0', 'UTF-8' );
		$dom->preserveWhiteSpace = false;


		$data_text5 = $classAttribute->attribute( 'data_text5' );
   		$xmlString =& $data_text5;

		$success = $dom->loadXML( $xmlString );

        $optionArray = array();

        $categoryArray = array();

		if ( $success ){
            //restore current options from xml
			$options = $dom->getElementsByTagName( 'option' );

            foreach ( $options as $optionNode ){

			$optionCategories = $optionNode->getElementsByTagName('category');

			$optionCategoryArray = array();

	        //restore option categories from xml
		foreach($optionCategories as $optionCategory){

			$optionCategoryArray[]=array('category_id'=>
                             $optionCategory->getAttribute('category_id'),
                             'category_value'=>
		     	explode("-",$optionCategory->getAttribute('category_value')));

	        }


			$option = array( 'id' => $optionNode->getAttribute( 'id' ),
                           'name' => $optionNode->getAttribute( 'name' ),
		 		'categories'=>$optionCategoryArray);

                /*
                $optionArray[$category_string_array['key']][] = $option;
				*/
                $optionArray[] = $option;


            }

	    //restore current categories from xml
		$categories_element = $dom->getElementsByTagName( 'categories' );
		$categories = $categories_element->item(0)->childNodes;

        $categoryArray = array();

        foreach ( $categories as $categoryNode ){

			$categoryArray[] = array( 'id' => $categoryNode->getAttribute( 'id' ),
           				'category_class'=>$categoryNode->getAttribute('category_class'),
                		'category_id'=>$categoryNode->getAttribute('category_id'),
						'category_value' => explode("-",
                            $categoryNode->getAttribute('category_value')));
            }


        }
		//store current options, current categories and is_multiselect in attrValue
        $attrValue = array( 'options' => $optionArray,
	                    'categories'=> $categoryArray,
			    'is_multiselect' => $classAttribute->attribute( 'data_int1' ),
			    'is_crossclass' => $classAttribute->attribute( 'data_int2' ),
			    'description' => $classAttribute->attribute( 'data_text4' ),
			    'threshold' => $classAttribute->attribute( 'data_int3' ));
        return $attrValue;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute ){

        $selected = $this->objectAttributeContent( $contentObjectAttribute );

        $classContent = $this->classAttributeContent( $contentObjectAttribute->attribute( 'contentclass_attribute' ) );

        $return = '';

        if ( count( $selected ) == 0){

            return '';

        }

        $count = 0;

        $optionArray = $classContent['options'];

        foreach ( $selected as $id ){

            if ( $count++ != 0 )
                $return .= ' ';

            foreach ( $optionArray as $option ){

                $optionID = $option['id'];

                if ( $optionID == $id )
                    $return .= $option['name'];

            }

        }

        return $return;

    }

    /*!
     Returns the value as it will be shown if this attribute is used in the object name pattern.
    */
    function title( $contentObjectAttribute, $name = null ){

        $selected = $this->objectAttributeContent( $contentObjectAttribute );

        $classContent = $this->classAttributeContent( $contentObjectAttribute->attribute( 'contentclass_attribute' ) );

        $return = "";

        if ( count( $selected ) == 0){

            return "";
        }

        $count = 0;

        foreach ( $selected as $id ){

            /*if ( $id == 0 ) // first object gets id==0, while rest of objects get id with offset from 1
                $id++;
            if ( $count++ != 0 )
                $return .= ', ';
            $return .= $classContent['options'][$id-1]['name'];*/
            if ( $count != 0 )
                $return .= ', ';

            $return .= $classContent['options'][$id-1]['name'];

            $count++;

        }

        return $return;

    }

    function hasObjectAttributeContent( $contentObjectAttribute ){

        return true;

    }

    /*!
     \reimp
    */
    function sortKey( $contentObjectAttribute ){

        return strtolower( $contentObjectAttribute->attribute( 'data_text' ) );

    }

    /*!
     \reimp
    */
    function sortKeyType(){

        return 'string';

    }

    /*!
     \return true if the datatype can be indexed
    */
    function isIndexable(){

        return true;

    }

    /*!
     \reimp
    */
    function isInformationCollector(){

        return true;

    }

    /*!
     \reimp
    */
    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode ){

		$doc = new DOMDocument( '1.0', 'UTF-8' );
		$doc->preserveWhiteSpace = false;

		$description =& $classAttribute->attribute('data_text4');

		$threshold =& $classAttribute->attribute('data_int3');

        $isMultipleSelection =& $classAttribute->attribute( 'data_int1'  );

        $isCrossClass =& $classAttribute->attribute( 'data_int2'  );

        $xmlString           =& $classAttribute->attribute( 'data_text5' );

		$success = $doc->loadXML( $xmlString );

		$domRoot = $doc->documentElement;

        $options = $domRoot->getElementsByTagName( 'options' )->item( 0 );
		$categories = $domRoot->getElementsByTagName( 'categories' )->item( 0 );
        
        $dom = $attributeParametersNode->ownerDocument;

        $importedOptionsNode = $dom->importNode( $options, true );
        $attributeParametersNode->appendChild( $importedOptionsNode );
        
        $importedCategoriesNode = $dom->importNode( $categories, true );
        $attributeParametersNode->appendChild( $importedCategoriesNode );
        
        $isMultiSelectNode = $dom->createElement( 'is-multiselect' );
        $isMultiSelectNode->appendChild( $dom->createTextNode( $isMultipleSelection ) );
        $attributeParametersNode->appendChild( $isMultiSelectNode );		
		
		$isCrossClassNode = $dom->createElement( 'is-crossclass' );
		$isCrossClassNode->appendChild( $dom->createTextNode( $isCrossClass ) );
		$attributeParametersNode->appendChild( $isCrossClassNode );
		
		$descriptionNode = $dom->createElement( 'description' );
		$descriptionNode->appendChild( $dom->createTextNode( $description ) );
		$attributeParametersNode->appendChild( $descriptionNode );
		
		$thresholdNode = $dom->createElement( 'threshold' );
		$thresholdNode->appendChild( $dom->createTextNode( $threshold ) );
		$attributeParametersNode->appendChild( $thresholdNode );
        
    }

    /*!
     \reimp
    */
    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $options = $attributeParametersNode->getElementsByTagName( 'options' )->item( 0 );
    	
        $categories = $attributeParametersNode->getElementsByTagName( 'categories' )->item( 0 );
        
		$doc = new DOMDocument( '1.0', 'UTF-8' );
		$doc->preserveWhiteSpace = false;

		$root = $doc->createElement( "mzcategoryselection" );

		$doc->appendChild( $root );

		$importedOptions = $doc->importNode( $options, true );
        $root->appendChild( $importedOptions );

		$importedCategories = $doc->importNode( $categories, true );
        $root->appendChild( $importedCategories );
        
		$xml = $doc->saveXML();

        $classAttribute->setAttribute( "data_text5", $xml );

        if ( $attributeParametersNode->getElementsByTagName( 'is-multiselect' )->item( 0 )->textContent == 0 )
            $classAttribute->setAttribute( 'data_int1', 0 );
        else
            $classAttribute->setAttribute( 'data_int1', 1 );
        
        if ( $attributeParametersNode->getElementsByTagName( 'is-crossclass' )->item( 0 )->textContent == 0 )
            $classAttribute->setAttribute( 'data_int2', 0 );
        else
            $classAttribute->setAttribute( 'data_int2', 1 );
            
        $description = $attributeParametersNode->getElementsByTagName( 'description' )->item( 0 )->textContent;
		$classAttribute->setAttribute("data_text4",$description);

        $threshold = $attributeParametersNode->getElementsByTagName( 'threshold' )->item( 0 )->textContent;
		$classAttribute->setAttribute("data_int3",$threshold);

    }

    function removeCategoryFromOptions(&$currentOptions,$category_id){

             foreach(array_keys($currentOptions) as $option_key){

                     $optionCategories = $currentOptions[$option_key]['categories'];

		     foreach(array_keys($optionCategories) as $option_category_key){

                             if($optionCategories[$option_category_key]['category_id']==$category_id){

				 unset($currentOptions[$option_key]['categories'][$option_category_key]);

			      }

		     }

	     }

    }

    /*
     * Param: categories - categories is the category array of an option
     * Returns: array('key'=>$category_id_string,'value'=>$category_value_string)
     * For example: category1 [ cccc | dddd ] <br> category2 [ gggg | hhhh ]
    */
    static function &generate_category_string(&$categories){

               $category_id_string="";

               $category_value_string="";

               foreach ($categories as $category){

                        $category_id_string .= ($category['category_id']."(");

                        $category_attribute=eZContentClassAttribute::fetch($category['category_id'],true,1);

                        if($category_attribute==null){

                           $category_attribute=eZContentClassAttribute::fetch($category['category_id'],true,2);

                        }

                        if($category_attribute==null){

                           $category_attribute=eZContentClassAttribute::fetch($category['category_id'],true,0);

                        }

                        $category_value_string .= ($category_attribute->attribute('name')." [ ");

                        $value_key=0;

                        foreach ($category['category_value'] as $value){

                                 $category_id_string .= $value;

	                         $content = $category_attribute->attribute('content');

	                         $options = $content['options'];

				 foreach($options as $option){

					 if($option['id']==$value){

                                            $category_value_string .= $option['name'];

					    break;

					 }

				 }

                                 if ($value_key!=(count($category['category_value'])-1)){

                                     $category_id_string .= "|";

                                     $category_value_string .= " | ";

                                 }

                                 $value_key++;

                        }

                        $category_id_string .= ") ";

                        $category_value_string .= " ]<br/>";

               }

	       $tmp = array('key'=>$category_id_string,'value'=>$category_value_string);

               return $tmp;
      }


}

/*

    function serializeContentObjectAttribute( &$package, &$objectAttribute )
    {
       $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );
       $idString = $objectAttribute->attribute( 'data_text' );

       $node->appendChild( eZDOMDocument::createElementTextNode( 'idstring', $idString ) );
       return $node;
    }

    function unserializeContentObjectAttribute( &$package, &$objectAttribute, $attributeNode )
    {
        $idString = $attributeNode->elementTextContentByName( 'idstring' );

        if ( $idString === false )
            $idString = '';

        $objectAttribute->setAttribute( 'data_text', $idString );
    }

*/

eZDataType::register( mzCategorySelectionType::DATA_TYPE_STRING, "mzCategorySelectionType" );
?>
