<?php
/*------------------------------------------------------------------------------
These are defined in here because they have to be referenced by the AJAX
controllers as well as the main plugin. Sorry for the weirdness. 
------------------------------------------------------------------------------*/
define('CCTM_PATH', dirname( dirname( __FILE__ ) ) );
define('CCTM_URL', WP_PLUGIN_URL .'/'. basename( CCTM_PATH ) );
define('CCTM_TXTDOMAIN', 'custom-content-type-mgr');

/*EOF*/