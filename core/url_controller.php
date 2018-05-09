<?
    class Url_Controller
    {
        public $controllers = array();

        public function __construct()
        {
            
        }

        public function init($controllers = array())
        {
            $request_url = parse_url($_SERVER['REQUEST_URI']);
            $url = $request_url['path'];

            $query = array();
            if (isset($request_url['query']))
                parse_str($request_url['query'], $query);

            foreach ($controllers as  $controller) {
                if (method_exists($controller, 'canProcessUrl') && $controller->canProcessUrl($url))
                {
                    $controller->processUrl($url, $query);
                    break;
                }
            }
        }
    }
?>