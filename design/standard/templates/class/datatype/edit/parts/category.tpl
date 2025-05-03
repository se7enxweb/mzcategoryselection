{* Categories start *}
{*
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
*}
{def $data_types=array('mzcategoryselection','ezselection')}
{def $contentclass_identifier = false()}
{def $current_class = false()}
{def $contentclasses = false()}
{def $contentclass_id = false()}
{def $contentclass_attributes = false()}
{def $selected_attribute = false()}
{def $classes = false()}
<div class="block">
<fieldset>
<legend>{'Categories'|i18n( 'design/standard/class/datatype' )}</legend>
{if gt(count($class_attribute.content.categories),0)}
<table class="list" cellspacing="0">
<tr>
    <th class="tight">&nbsp;</th>
    {if eq($class_attribute.data_int2,1)}
    <th class="tight">{'Class'}</th>
    {/if}
    <th class="tight">{'Attribute'|i18n( 'design/standard/class/datatype' )}</th>
    <th>{'Category'|i18n( 'design/standard/class/datatype' )}</th>
</tr>
{/if}

{foreach $class_attribute.content.categories as $Category}
<tr>
    {* Remove. *}
    <td><input type="checkbox" name="ContentClass_mzcategoryselection_category_remove_array_{$class_attribute.id}[{$Category.id}]" value="1" title="{'Select category for removal.'|i18n( 'design/standard/class/datatype' )}" /></td>

    {* Class *}
    {if eq($class_attribute.data_int2,1)}
    <td>
    {set $current_class=fetch( 'class', 'list', hash( 'class_filter', array($class_attribute.contentclass_id))).0}
    {set $contentclass_identifier=$current_class.identifier}
    {set $classes=fetch('mzcategoryselection','category_class_list')}
    <input type="hidden" name="ContentClass_mzcategoryselection_category_class_array_{$class_attribute.id}[{$Category.id}]" value="" />
    <select name="ContentClass_mzcategoryselection_category_class_array_{$class_attribute.id}[{$Category.id}]" onchange="document.getElementById('mzcategoryselection_refresh_button_{$class_attribute.id}').click();">
    {foreach $classes as $class}
    <option value="{$class}" {if eq($class,$Category.category_class)}selected{/if}>{$class}</option>
    {/foreach}
    </select>
    </td>
    {else}
    {set $current_class=fetch( 'class', 'list', hash( 'class_filter', array($class_attribute.contentclass_id))).0}
    {set $contentclass_identifier=$current_class.identifier}
    <input type="hidden" name="ContentClass_mzcategoryselection_category_class_array_{$class_attribute.id}[{$Category.id}]" value="" style="display:none" />
    <select name="ContentClass_mzcategoryselection_category_class_array_{$class_attribute.id}[{$Category.id}]" style="display:none">
    <option value="{$contentclass_identifier}" selected>{$contentclass_identifier}</option>
    </select>
    {/if}

    {* Category *}
    <td>
    <input type="hidden" name="ContentClass_mzcategoryselection_category_id_array_{$class_attribute.id}[{$Category.id}]" value="" />
    <select name="ContentClass_mzcategoryselection_category_id_array_{$class_attribute.id}[{$Category.id}][]" onchange="document.getElementById('mzcategoryselection_refresh_button_{$class_attribute.id}').click();">

        {if ne($Category.category_class,'')}
            {set $contentclass_identifier=$Category.category_class}
        {/if}

        {set $contentclasses = fetch('class','list',hash('class_filter',array($contentclass_identifier)))}

        {set $contentclass_id = $contentclasses.0.id}

        {if ne($contentclass_id,0)}

           {if eq($class_attribute.contentclass_id,$contentclass_id)}

            {set $contentclass_attributes=fetch( 'content', 
                                                 'class_attribute_list',
                                                 hash('class_id', $contentclass_id,
                                                 'version_id',1))}
           {else}

            {set $contentclass_attributes=fetch( 'content', 
                                                 'class_attribute_list',
                                                 hash('class_id', $contentclass_id,
                                                 'version_id',0))}

           {/if}

        {/if}

        {foreach $contentclass_attributes as $attribute}
        {if and($data_types|contains($attribute.data_type_string),ne($attribute.id,$class_attribute.id))}
        <option value="{$attribute.id}" {if eq($Category.category_id,$attribute.id)}selected="selected"{/if}>{$attribute.name}</option>
        {/if}
        {/foreach}
    </select>
    </td>
    
    {* Value *}
    <td>
    <input type="hidden" name="ContentClass_mzcategoryselection_category_value_array_{$class_attribute.id}[{$Category.id}]" value="" />
    <select name="ContentClass_mzcategoryselection_category_value_array_{$class_attribute.id}[{$Category.id}][]" multiple>
        {* if ne($Category.category_id,0) *}

        {if eq($class_attribute.contentclass_id,$contentclass_id)}

            {set $selected_attribute = fetch('content',
                                             'class_attribute',
                                             hash('attribute_id',$Category.category_id,
                                                  'version_id',1))}
        {else}

            {set $selected_attribute = fetch('content',
                                             'class_attribute',
                                             hash('attribute_id',$Category.category_id,
                                                  'version_id',0))}

        {/if}

        {foreach $selected_attribute.content.options as $option}
        <option value="{$option.id}" {if $Category.category_value | contains($option.id)}selected="selected"{/if}>{$option.name}</option>
        {/foreach}

        {*/if *}

        </select>
    </td>
</tr>
{/foreach}

{if gt(count($class_attribute.content.categories),0)}
</table>
{/if}

{if eq(count($class_attribute.content.categories),0)}
<p>{'There are no categories.'|i18n( 'design/standard/class/datatype' )}</p>
{/if}

{* Buttons. *}
{section show=$class_attribute.content.categories}
<input class="button" type="submit" name="ContentClass_mzcategoryselection_removecategory_button_{$class_attribute.id}" value="{'Remove selected'|i18n( 'design/standard/class/datatype' )}" title="{'Remove selected categories.'|i18n( 'design/standard/class/datatype' )}" />
{section-else}
<input class="button-disabled" type="submit" name="ContentClass_mzcategoryselection_removecategory_button_{$class_attribute.id}" value="{'Remove selected'|i18n( 'design/standard/class/datatype' )}" disabled="disabled" />
{/section}

{section show=$class_attribute.content.categories}
<input id="mzcategoryselection_refresh_button_{$class_attribute.id}" class="button" type="submit" name="RefreshButton" value="{'Refresh category'|i18n( 'design/standard/class/datatype' )}" title="{'Refresh categories.'|i18n( 'design/standard/class/datatype' )}" />
{section-else}
<input class="button-disabled" type="submit" name="RefreshButton" value="{'Refresh category'|i18n( 'design/standard/class/datatype' )}" disabled="disabled" />
{/section}

{section show=$class_attribute.content.options}
<input class="button" type="submit" name="BindButton" value="{'Bind category'|i18n( 'design/standard/class/datatype' )}" title="{'Bind categories to selected options.'|i18n( 'design/standard/class/datatype' )}" />
{section-else}
<input class="button-disabled" type="submit" name="BindButton" value="{'Bind category'|i18n( 'design/standard/class/datatype' )}" disabled="disabled" />
{/section}

<input class="button" type="submit" name="ContentClass_mzcategoryselection_newcategory_button_{$class_attribute.id}" value="{'New category'|i18n( 'design/standard/class/datatype' )}" title="{'Add a new category.'|i18n( 'design/standard/class/datatype' )}" />

</fieldset>
</div>

{* Categories end *}

