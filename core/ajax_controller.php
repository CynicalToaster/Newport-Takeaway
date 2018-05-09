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

        public function getPageEvents($page_controller)
        {
            foreach ($page_controller->pages as $page)
                foreach ($page->ajax_events as $ajax_event)
                    $this->event_handlers[$page->url .'/'. $ajax_event] = $page;
        }

        public function getGlobalEvents()
        {
            foreach ($this->ajax_events as $ajax_event)
                $this->event_handlers[$ajax_event] = $this;
        }

        public function canProcessUrl($url = '/')
        {
            if (isset($_SERVER['HTTP_EVENT']))
                return true;
            return false;
        }

        public function processUrl($url = '/', $query = array())
        {
            $event = $_SERVER['HTTP_EVENT'];
            $callback_name = 'ajax_'. $event;

            if (isset($this->event_handlers[$url .'/'. $event]))
                echo $this->event_handlers[$url .'/'. $event]->$callback_name();  
            else if (isset($this->event_handlers[$event]))
                echo $this->event_handlers[$event]->$callback_name();
        }



        public function ajax_logout()
        {
            traceLog('Logout');
            if (isset($_SESSION) && isset($_SESSION['user_id']))
                $_SESSION['user_id'] = null;
        }
    }
?>