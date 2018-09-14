<?
    class Url_Controller
    {
        public $controllers = array();

        // Initalise the URL controller and begin processing the request.
        public function init($controllers = array())
        {
            // Break down the request into it's URL and GET parameters
            $request_url = parse_url($_SERVER['REQUEST_URI']);
            $url = $request_url['path'];

            $query = array();
            if (isset($request_url['query']))
                parse_str($request_url['query'], $query);

            // Look for a controller that is able to process the request.
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