<?php
if ( ! defined('CCTM_PATH')) exit('No direct script access allowed');
if (!current_user_can('manage_options')) exit('You do not have permission to do that.');
/*------------------------------------------------------------------------------
Standalone controller to cough up a download.
------------------------------------------------------------------------------*/
include_once(CCTM_PATH.'/includes/ImportExport.php');

ImportExport::export_to_desktop();

/*EOF*/