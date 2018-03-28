<?
    class Url_Controller
    {
        public $controllers = array();

        public function __construct()
        {
            
        }

        public function init($controllers = array())
        {
            $request_url = explode('?', $_SERVER['REQUEST_URI']);
            $url = $request_url[0];

            foreach ($controllers as  $controller) {
                if (method_exists($controller, 'canProcessUrl') && $controller->canProcessUrl($url))
                {
                    $controller->processUrl($url);
                    break;
                }
            }
        }
    }
?>