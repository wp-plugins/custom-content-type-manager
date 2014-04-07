<?php
/**
 * Maps a manager URL to a controller. 
 *
 * Wordpress uses only the "page" parameter to map a menu to a callback, and their
 * implementation is very simplified so that one menu item is bound to one action and 
 * to one permission.  So when we route URLs in the manager, we are actually mapping both
 * a request to a classname::function, but we are also "gating" it with a WP permission.
 * 
 * So the app here uses "virtual" urls to loosen things up a bit, even though the permissions
 * can only be locked down per controller class.  For convenience, the url() and a() functions
 * expect a shorthand similar to CodeIgniter: {$classname}/{$function}[/$optional/$args]
 *
 * This translates to GET parameters like this:
 * 
 *      &page=cctm_{$classname}  <-- must be defined via add_menu_page or add_submenu_page
 *      &route={$function[/$optional/$args]}
 *
 * Manager controllers are grouped into main classes.
 * URL segments can be used to map to the functions -- functions have "get" or "post" as
 * a prefix to designate whether or not they're handling a get or post request.
 *
 * The controller should eventually return a view.
 *
 * @package CCTM
 */
namespace CCTM;

class Route {
    
    public static $Log;
    public static $Load;
    public static $View;
    public static $POST;
    public static $GET;

    // We could inject these, but it'd prob'ly be overkill
    public static $slug_pre = 'cctm_'; 
    public static $class_param = 'page';
    public static $function_param = 'a';
    public static $route_param = 'route';
    public static $default_class = 'Posttypes';
    public static $default_function = 'index';
    public static $default_permission = 'cctm';
    
    
    /**
     * Dependency injection used here to make this more testable.
     *
     * @param object $Log for logging info
     */
    public function __construct(\Pimple $dependencies) {
        self::$Log = $dependencies['Log'];
        self::$Load = $dependencies['Load'];
        self::$View = $dependencies['View'];
        self::$POST = $dependencies['POST'];
        self::$GET = $dependencies['GET'];
        self::$Log->debug('Construct.', __CLASS__, __LINE__);        
    }

    /**
     * Translate a URL-ish string into 2 parts representing a controlloer and function.
     * e.g. 'some/thing' becomes array('some','thing)
     * whereas 'nada' becomes array('nada','index')
     *
     * The classname corresponds to WP's "menu slugs" which get passed in the "page"
     * parameter e.g. wp-admin/admin.php?page=cctm (see config/menu.php).
     *
     * @param string $str
     * @return array
     */
    private static function split($str) {
        $segments = explode('/',trim($str,'/'));
        $classname = ($segments) ? array_shift($segments) : self::$default_class;
        $function = ($segments) ? array_shift($segments) : self::$default_function;
        $args = ($segments) ? $segments : array();        
        return array($classname, $function, $args);
    }
    
    /**
     * parse the request into classname + function + args, by 
     * listening for URL parameters
     *
     * @return array $class (str), $function (str), $args (array)
     */
    public static function parse() {
        $class = (isset(self::$GET[self::$class_param])) ? self::$GET[self::$class_param] : self::$default_class;
        // Strip prefix
        if (substr($class, 0, strlen(self::$slug_pre)) == self::$slug_pre) {
            $class = substr($class, strlen(self::$slug_pre));
        }
        $args = (isset(self::$GET[self::$route_param])) ? explode('/',self::$GET[self::$route_param]) : array();
        $function = ($args) ? array_shift($args) : self::$default_function;
        
        self::$Log->debug('Parsing Request -- Class: '.$class .' Function: '.$function .' Args: '.print_r($args,true), __CLASS__, __LINE__);        
        return array($class,$function,$args);
    }
    
    /**
     * Handle a request, triggering parsing of URL parameters...
     * could be in a dedicated "Request" class.
     */
    public static function handle() {
        list($class,$function,$args) = self::parse();
        return self::send($class,$function,$args);
    }
    /** 
     *
     */
    public static function sendError($msg) {
        print View::make('error.php', array('msg'=>'Controller class not found: '.$routename));
        self::$Log->debug('Controller class not found: '.$routename, __CLASS__, __LINE__);
        return;    
    }
    
    /**
     * Send the route to the controller and fulfill the request.
     *
     * @param string $classname stub (namespace of CCTM\Controllers is assumed)
     * @param string $function i.e. method name
     * @param array $args any additional arguments
     * @return string HTML web page usually
     */
    public static function send($class,$function,$args=array()) {
        // Our 404
        if(!$Controller = self::$Load->controller($class)) {
            return self::sendError('Controller class not found: '.$routename);
        }
        if (self::$POST) {
            // test nonces
            $function = 'post'.$function;
        }
        else {
            $function = 'get'.$function;
        }
        self::$Log->debug('Sending request to '.$class .'::'.$function .' with args '.print_r($args,true), __CLASS__, __LINE__);        
        return call_user_func_array(array($Controller, $function), $args);
        //return $Controller->$function();
    }
    
    /**
     * The URL to a particular route in the format of $classname/$function[/$arg1/$arg2/...etc...]
     * The classname need not include the "cctm_" prefix that WordPress requires.
     *
     * @param string virtual $routename, e.g. 'customfields/edit/my-field'
     * @return string url, e.g. http://craftsmancoding.com/wp-admin/admin.php?page=cctm_customfields&route=edit/my-field
     */
    public static function url($routename) {
        if (!is_scalar($routename)) {
            self::$Log->error('$routename must be a scalar: '.print_r($routename,true), __CLASS__, __LINE__);
            return;
        }    
        list($class,$function,$args) = self::split($routename);
        array_unshift($args,$function); // function is 1st argument
        $url = get_admin_url(false,'admin.php').'?page='.self::$slug_pre.$class.'&route='.implode('/',$args);
        self::$Log->debug('URL made from route '.$routename. ' to '.$url, __CLASS__, __LINE__);
        return $url;
    }
    
    /**
     * Make a full anchor tag to a particular route
     *
     * @param string virtual $routename
     * @param string clickable $title in the anchor tag
     * @param array optional $attributes to include in the link tag
     * @return string     
     */
    public static function a($routename, $title='',$attributes=array()) {
        $url = self::url($routename);
        return 'TEST';
    }
}