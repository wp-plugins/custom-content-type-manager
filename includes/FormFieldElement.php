<?php
/**
* TODO: http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=42
We need a class that can be extended on a per custom-field-type basis.

Think about this... each form element gets its own array in the custom_fields array...
but each form element might have completely disparate attributes.  The whole FormGenerator
approach may not be the best way to do this.

How should this be extended by other users?  I should register an action that savvy users
can tap into.

*/
abstract class FormFieldElement
{
	public $something;
	
	//------------------------------------------------------------------------------
	/**
	* get_tpl
	* Return the formatting template string to use when formatting instances of this
	* field when a user is creating/editing posts.
	*/
	abstract public function get_tpl(&$data);
	
	//------------------------------------------------------------------------------
	/**
	* Used when editing the definition for this type of element
	*/
	abstract public function get_definition_tpl();

}
/*EOF*/