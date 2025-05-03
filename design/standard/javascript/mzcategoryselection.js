		//array contains all mzcategoryselection objects
mzcategoryselections = new Array();

//array contains css id of 
//all selection/mzcategoryselection select element
//in current page
//structure is as the following:
//elementIDs = array{class_attribute_id1=>elemente_id1,....}
mzcategoryselection_element_ids = new Array();

/*
 * Returns all selected options of the specific select element
*/
function getSelectedOptions(select_element){

         var selectedOptions = null;

         if(select_element){

            selectedOptions = new Array();

            var options = select_element.options;

            for(var i=0;i<options.length;i++){

                var option = options[i];

                if(option.selected){

                   selectedOptions.push(option);
 
                }

            }

         }

         return selectedOptions;

}

/*
 * Find all categories that current option depends on and check all selected options of
 * the categories. If the selected values match the option's category values, then matched,
 * other wise it does not match. Please note, if current option does not depend on any categories,
 * then return true; If current option depends on any exteranl categories (cross class), the 
 * external categories always make the option visible, but the option has to match other 
 * internal categories if any.
 * Param: class_attribute_id - the class_attribute_id of current option's select element
 * Param: option_id - the option_id (the value) of current option
 * returns: false - does not match
 *          true - matched
*/
function match(option){
         var matched = false;

         var option_categories = option.categories;

         //if current option does not depend on any categories
         //return true directly
         if(option_categories.length==0){

            matched = true;

            return matched;

         }

         for(var i=0;i<option_categories.length;i++){

             matched = false;

             var category = option_categories[i];

             var category_id = category.category_id;

             //if current option depends on any exteranl categories
             //set matched to true and continue;
             //it means the external categories always 
             //show the related options
             //but the option may also depends on other internal categories
             if(!mzcategoryselection_element_ids[category_id]){

                 matched = true;

                 continue;

             }

             //get all possible category values of current category
             var category_values = category.values;

             //the select component of current category
             var category_select = document.getElementById(mzcategoryselection_element_ids[category_id]);

             //the selected options of current category
             var category_selected_options = getSelectedOptions(category_select);

             for(var j=0;j<category_values.length;j++){

                 var category_value = category_values[j];

                 for(var k=0;k<category_selected_options.length;k++){
                    
                     var category_selected_value = category_selected_options[k].value;

                     if(category_value==category_selected_value){

                        matched = true;

                        break;

                     }

                 }

                 if(matched){

                    break;

                 }

             }

             if(!matched){

                 break;

             }

         }

         return matched;

}

/*
 * Update the status of all ezcategory selection select elements in 
 * current page if they depend on any categories. 
 * It loops every selection or ezcategory selection select element and its options
 * in current page. For each option, if its category values match the selected values
 * then append this option, otherwise, remove the option.
 * 
*/
function updatecategory(){

         for(var i=0;i<mzcategoryselections.length;i++){

             var mzcategoryselection = mzcategoryselections[i];

             var element_id = mzcategoryselection.element_id;

             var attribute_id = mzcategoryselection.attribute_id;

             var select_element = document.getElementById(element_id);

             var options = mzcategoryselection.options;

             for(var j=0;j<options.length;j++){

                 var match_flag = match(options[j]);

                 if(match_flag){

                    append_option(select_element,options[j]);

                 }else{

                    remove_option(select_element,options[j]);

                 }

             }

         }


}

/*
 * Append specific option to select element
 * If option already exists in select element, do nothing
*/
function append_option(select,option){

         var options = select.options;

         var matched = false;

         for(var i=0;i<options.length;i++){

            if(options[i].value == option.option_id){

                 matched = true;

                 break;

            } 

         }

         if(!matched){

            select.options[select.options.length] = new Option(option.option_name,option.option_id);

         }

}

/*
 * Remove specific option from select element
 * If option does not exist in select element, do nothing
*/
function remove_option(select,option){

         var options = select.options;

         for(var i=0;i<options.length;i++){

            if(options[i].value == option.option_id){

                 options[i] = null;

                 break;

            } 

         }

}
