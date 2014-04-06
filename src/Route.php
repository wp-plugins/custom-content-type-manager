<?php
/**
 * Maps a manager URL to a controller. 
 *
 * Wordpress uses only the "page" parameter to map a menu to a callback, but we 
 * need to take this further so that:
 *
 *      &page=cctm_{$controller_class_name}
 *      &a={$controller_function_name}
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
    public static $default_function = 'index';
    
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
        $pos = strpos($str, '/');
        if ($pos === false) {
            return array($str,'index');
        }
        else {
            return array(substr($str,0,$pos), substr($str,$pos +1));
        }
    }
    
    /**
     * parse the request, listening for URL parameters
     *
     */
    public static function parse() {
        $class = (isset(self::$GET[self::$class_param])) ? self::$GET[self::$class_param] : '';
        if (substr($class, 0, strlen(self::$slug_pre)) == self::$slug_pre) {
            $class = substr($class, strlen(self::$slug_pre));
        }
        $function = (isset(self::$GET[self::$function_param])) ? self::$GET[self::$function_param] : self::$default_function;
        self::$Log->debug('Parsing request: class: '.$class .' function: '.$function, __CLASS__, __LINE__);        
        return array($class,$function);
    }
    
    /**
     * Handle a request, triggering parsing of URL parameters...
     * could be in a dedicated "Request" class.
     */
    public static function handle() {
        list($class,$function) = self::parse();
        return self::send($class,$function);
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
     *
     * @param string $routename  Similar to codeigniter: {$controllerclass}/{$function}
     *      if "/" is omitted, we assume the default function: index
     * @return string HTML web page usually
     */
    public static function send($class,$function) {
        // Our 404
        if(!$Controller = self::$Load->controller($class)) {
            return self::sendError('Controller class not found: '.$routename);
        }
        $function = (self::$POST) ? 'post'.$function : 'get'.$function;
        self::$Log->debug('Sending request to '.$class .'::'.$function, __CLASS__, __LINE__);        
        return $Controller->$function();
    }
    
    /**
     * The URL to a particular route.
     *
     * @param string virtual $routename
     * @return string
     */
    public function url($routename) {
        if (!is_scalar($routename)) {
            self::$Log->error('$routename must be a scalar: '.print_r($routename,true), __CLASS__, __LINE__);
            return;
        }    
        list($class, $function) = self::split($routename);
        return get_admin_url(false,'admin.php').'?page='.self::$slug_pre.$class.'&=a'.$function;
    }
    
    /**
     * Make a full anchor tag to a particular route
     *
     * @param string virtual $routename
     * @param string clickable $title in the anchor tag
     * @param array optional $attributes to include in the link tag
     * @return string     
     */
    public function a($routename, $title='',$attributes=array()) {
        $url = self::url($routename);
        return 'TEST';
    }
}