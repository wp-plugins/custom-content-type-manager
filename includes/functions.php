<?php
/**
These are functions in the main namespace, primarily reserved for use in 
theme files.

See http://code.google.com/p/wordpress-custom-content-type-manager/wiki/TemplateFunctions
for the official documentation.
*/

//------------------------------------------------------------------------------
/**
Scour the custom field definitions for any fields
of the type specified.  This is useful e.g. if you want to return all images 
attached to a post. Perhaps this would be more useful if I included a "prefix"
argument so you could retrieve all values from fields named with that prefix.

Must be used when there is an active post.

A $def looks something like this:
 Array
(
    [label] => Author
    [name] => author
    [description] => This is who wrote the book
    [type] => text
    [sort_param] => 
)

@param	string	$type is one of the defined field types , currently:
	'checkbox','dropdown','media','relation','text','textarea','wysiwyg'
@param	string	$prefix	string identifying the beginning of the name of each field.
@return	array	List of values from each field of the type specified. 
*/
function get_all_fields_of_type($type, $prefix='')
{
	global $post;

	$values = array();

	$data = get_option( CCTM::db_key );
	
	$post_type = $post->post_type;
	if ( !isset($data[$post_type]['custom_fields']) )
	{
		return  sprintf( __('No custom fields defined for the %1$s field.', CCTM_TXTDOMAIN), $fieldname );
	}
	
	foreach ( $data[$post_type]['custom_fields'] as $def )
	{
		if ($def['type'] == $type )
		{
			if ($prefix)
			{			
				if ( preg_match('/^'.$prefix.'/', $def['name']) )
				{
					$values[] = get_custom_field($def['name']);
				}
			}
			else
			{
				$values[] = get_custom_field($def['name']);
			}
		}		
	}
	
	return $values;

}

//------------------------------------------------------------------------------
/**
SYNOPSIS: Used inside theme files, e.g. single.php or single-my_post_type.php
where you need to print out the value of a specific custom field.

This prints the 1st instance of the meta_key identified by $fieldname 
associated with the current post. See get_post_meta() for more details.

INPUT: 
	$fieldname (str) the name of the custom field as defined inside the 
		Manage Custom Fields area for a particular content type.
OUTPUT:
	The contents of that custom field for the current post.

See also 	
http://codex.wordpress.org/Function_Reference/get_post_custom_values
*/
function get_custom_field($fieldname)
{
	// the_ID() function won't work because it *prints* its output
	$post_id = get_the_ID();
	return get_post_meta($post_id, $fieldname, true);
}

//------------------------------------------------------------------------------
/**
* Gets info about a custom field's definition (i.e. the meta info about the
* field). Returns error messages if no data found.
*
* Sample usage: <?php print get_custom_field_meta('my_dropdown','label'); ?>
*
* @param	string	$fieldname	The name of the custom field
* @param	string	$item		The name of the definition item that you want
* @param	string	$post_type	Optional.  Default is read from the global $post.
* @return	mixed	Usually a string, but some items are arrays (e.g. options)
*/
function get_custom_field_meta($fieldname, $item, $post_type=null)
{
	if (!$post_type)
	{
		global $post;
		$post_type = $post->post_type;
		if (!$post_type)
		{
			return __('Could not determine the post_type.',CCTM_TXTDOMAIN);
		}
	}

	$data = get_option( CCTM::db_key, array() );
	
	if ( $data[$post_type]['custom_fields'] )
	{
		// Go through the custom field defs
		foreach ( $data[$post_type]['custom_fields'] as $i => $def_array )
		{
			if ( $def_array['name'] == $fieldname )
			{
				if ( isset($def_array[$item]) )
				{
					return $def_array[$item];
				}
				else
				{
					return sprintf( __('%$1s is an invalid item for the %$2s field.',CCTM_TXTDOMAIN), $item, $fieldname );
				}
			}
		}
		
		return sprintf( __('Invalid field name: %s', CCTM_TXTDOMAIN), $fieldname );
	}
}

//------------------------------------------------------------------------------
/**
* Gets the definition array for the fieldname specified.
*
* @param	string	Name of the custom field.
* @return	array	Associative array containing all definition items for the custom 
*				field indicated by the $fieldname.
*/
function get_custom_field_def($fieldname, $post_type=null)
{
	if (!$post_type)
	{
		global $post;
		$post_type = $post->post_type;
		if (!$post_type)
		{
			return __('Could not determine the post_type.',CCTM_TXTDOMAIN);
		}
	}
	
	$data = get_option( CCTM::db_key, array() );
	
	if ( $data[$post_type]['custom_fields'] )
	{
		// Go through the custom field defs
		foreach ( $data[$post_type]['custom_fields'] as $i => $def_array )
		{
			if ( $def_array['name'] == $fieldname )
			{
				return $def_array;
			}
		}
	
		return sprintf( __('Invalid field name: %s', CCTM_TXTDOMAIN), $fieldname );
	}
}

//------------------------------------------------------------------------------
/**
* Gets the custom image referenced by the custom field $fieldname. 
* Relies on the WordPress wp_get_attachment_image() function.
*
* @param	string	$fieldname name of the custom field
* @return	string	an HTML img element or empty string on failure.
*/
function get_custom_image($fieldname)
{
	$id = get_custom_field($fieldname);
	return wp_get_attachment_image($id, 'full');
}


//------------------------------------------------------------------------------
/**
Retrieves a complete post object, including all meta fields.
Ah... get_post_custom() will treat each custom field as an array, because in WP
you can tie multiple rows of data to the same fieldname (which can cause some
architectural related headaches).

At the end of this, I want a post object that can work like this:

print $post->post_title;
print $post->my_custom_field; // no $post->my_custom_fields[0];

and if the custom field *is* a list of items, then attach it as such.
@param	integer	$id is valid ID of a post (regardless of post_type).
@return	object	post object with all attributes, including custom fields.
*/
function get_post_complete($id)
{
	$complete_post = get_post($id, OBJECT);
	if ( empty($complete_post) )
	{
		return array();
	}
	$custom_fields = get_post_custom($id);
	if (empty($custom_fields))
	{
		return $complete_post;
	}
	foreach ( $custom_fields as $fieldname => $value )
	{
		if ( count($value) == 1 )
		{
			$complete_post->$fieldname = $value[0];
		}
		else
		{
			$complete_post->$fieldname = $value[0];		
		}
	}
	
	return $complete_post;	
}


/**
Returns an array of post "complete" objects (including all custom fields)
where the custom fieldname = $fieldname and the value of that field is $value.
This is used to find a bunch of related posts in the same way you would with 
a taxonomy, but this uses custom field values instead of taxonomical labels.

INPUT: 
	$fieldname (str) name of the custom field
	$value (str) the value that you are searching for.

OUTPUT:
	array of post objects (complete post objects, with all attributes).

USAGE:
	One example:
	$posts = get_posts_sharing_custom_field_value('genre', 'comedy');
	
	foreach ($posts as $p)
	{
		print $p->post_title;
	}

This is a hefty, db-intensive function... (bummer).
*/
function get_posts_sharing_custom_field_value($fieldname, $value)
{
	global $wpdb;
	$query = "SELECT DISTINCT {$wpdb->posts}.ID 
		FROM {$wpdb->posts} JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id  
		WHERE 
		{$wpdb->posts}.post_status = 'publish'
		AND {$wpdb->postmeta}.meta_key=%s AND {$wpdb->postmeta}.meta_value=%s";
	$results = $wpdb->get_results( $wpdb->prepare( $query, $fieldname, $value ), OBJECT );
	
	$completes = array();
	foreach ( $results as $p )
	{
		$completes[] = get_post_complete($p->ID);
	}
	return $completes;
}


//------------------------------------------------------------------------------
/**
A relation field stores a post ID, and that ID identifies another post.  So given 
a fieldname, this returns the complete post object for that was referenced by
the custom field.  You can see it's a wrapper function which relies on 
get_post_complete() and get_custom_field().
INPUT: 
	$fieldname (str) name of a custom field
OUTPUT:
	post object
*/
function get_relation($fieldname)
{
	return get_post_complete( get_custom_field($fieldname) );
}

//------------------------------------------------------------------------------
/**
Given a specific custom field name ($fieldname), return an array of all unique
values contained in this field by *any* published posts which use a custom field 
of that name, regardless of post_type, and regardless of whether or not the custom 
field is defined as a "standardized" custom field. 

This filters out empty values ('' or null). 

INPUT:
	$fieldname (str) name of a custom field
OUTPUT:
	array of unique values.

USAGE:
Imagine a custom post_type that profiles you and your friends. There is a custom 
field that defines your favorite cartoon named 'favorite_cartoon':

	$array = get_unique_values_this_custom_field('favorite_cartoon');
	
	print_r($array);
		Array ( 'Family Guy', 'South Park', 'The Simpsons' );

*/
function get_unique_values_this_custom_field($fieldname)
{
	global $wpdb;
	$query = "SELECT DISTINCT {$wpdb->postmeta}.meta_value 
		FROM {$wpdb->postmeta} JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
		WHERE {$wpdb->postmeta}.meta_key=%s 
		AND {$wpdb->postmeta}.meta_value !=''
		AND {$wpdb->posts}.post_status = 'publish'";
	$results = $wpdb->get_results( $wpdb->prepare($query, $fieldname), ARRAY_N );	
	// Repackage
	$uniques = array();
	foreach ($results as $r )
	{
		$uniques[] = $r[0];
	}

	return array_unique($uniques);
}


//------------------------------------------------------------------------------
/**
SYNOPSIS: Used inside theme files, e.g. single.php or single-my_post_type.php
where you need to print out the value of a specific custom field.

This prints the 1st instance of the meta_key identified by $fieldname 
associated with the current post. See get_post_meta() for more details.

INPUT: 
	$fieldname (str) the name of the custom field as defined inside the 
		Manage Custom Fields area for a particular content type.
OUTPUT:
	The contents of that custom field for the current post.
*/
function print_custom_field($fieldname)
{
	print get_custom_field($fieldname);
}

//------------------------------------------------------------------------------
/**
* Convenience function to print the result of get_custom_field_meta().  See
* get_custom_field_meta.
*/
function print_custom_field_meta($fieldname, $item, $post_type=null)
{
	print get_custom_field_meta($fieldname, $item, $post_type);
}

//------------------------------------------------------------------------------
/**
* Prints the custom image referenced by the custom field $fieldname. 
* Relies on the WordPress wp_get_attachment_image() function.
*
* @param	string	$fieldname name of the custom field
* @return	none	Prints the results of get_custom_image()
*/
function print_custom_image($fieldname)
{
	print get_custom_image($fieldname);
}

/*EOF*/