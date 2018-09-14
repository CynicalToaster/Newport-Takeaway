<?
    class Ajax_Controller
    {
        public $event_handlers = array();
        public $ajax_events = array(
            'logout'
        );

        public function __construct($page_controller)
        {
            if (isset($page_controller))
                $this->getPageEvents($page_controller);

            $this->getGlobalEvents();
        }

        // Execute an AJAX handler on a page (Only accessible from the page it was request from).
        public function getPageEvents($page_controller)
        {
            foreach ($page_controller->pages as $page)
                foreach ($page->ajax_events as $ajax_event)
                    $this->event_handlers[$page->url .'/'. $ajax_event] = $page;
        }

        // Execute a global AJAX handler (Accessible from any page).
        public function getGlobalEvents()
        {
            foreach ($this->ajax_events as $ajax_event)
                $this->event_handlers[$ajax_event] = $this;
        }

        // Tell the URL controller if the AJAX controller is able to process the current request.
        public function canProcessUrl($url = '/')
        {
            if (isset($_SERVER['HTTP_EVENT']))
                return true;
            return false;
        }

        // Method used by the URL controller if it has decided that the AJAX controller will process the URL.
        public function processUrl($url = '/', $query = array())
        {
            $event = $_SERVER['HTTP_EVENT'];
            $callback_name = 'ajax_'. $event;

            // Look for the AJAX handler to process the request.
            if (isset($this->event_handlers[$url .'/'. $event]))
                echo $this->event_handlers[$url .'/'. $event]->$callback_name();  
            else if (isset($this->event_handlers[$event]))
                echo $this->event_handlers[$event]->$callback_name();
        }


        // Global AJAX handler to logout the user.
        public function ajax_logout()
        {
            $_SESSION = array();
        }
    }
?>