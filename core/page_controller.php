<?
    class Page_Controller
    {
        public $pages = array();

        // Location where pages are stored on the server.
        public $pages_dir = 'pages/';

        public function __construct()
        {
            $this->getPages();
        }

        // Search the pages directory and load all pages wthin it.
        public function getPages()
        {
            // Get all files from the /pages/ directory.
            $pages = glob($this->pages_dir . '*', GLOB_ONLYDIR);

            foreach ($pages as $page_dir) {
                if (file_exists($page_dir . '/init.php'))
                {
                    // Include the pages init.php file (Containing information about the page).
                    include_once($page_dir . '/init.php');

                    $split_page_dir = explode('/', $page_dir);
                    $page_name = 'Page_' . end($split_page_dir);

                    // Check if the pages init file contains the required class.
                    if (class_exists($page_name))
                    {
                        $page = new $page_name($this, $page_dir);
                        $this->pages[$page->url] = $page;
                    }
                }
            }
        }

        // Tell the URL controller if the Page controller is able to process the current request.
        public function canProcessUrl($url = '/')
        {
            if (isset($this->pages[$url]))
                return true;
            return false;
        }

        // Method used by the URL controller if it has decided that the Page controller will process the URL.
        public function processUrl($url = '/', $query = array())
        {
            // Look for the page relating to the URL.
            if (isset($this->pages[$url]))
            {
                // If the page was found then execute the Render() method.
                $this->pages[$url]->query = $query;
                $this->pages[$url]->render();
                return true;
            }
            return false;
        }
    }

    class Page
    {
        // Default Properties for a page.
        public $name = '';
        public $url = '/test';
        public $query = array();
        public $dir = '';
        public $controller = null;
        public $user = null;
        public $allowed_user_types = array();
        public $denied_redirect = '/';
        public $use_standard_head = true;
        public $ajax_events = array();

        public function __construct($controller, $dir)
        {
            $this->controller = $controller;
            $this->dir = $dir;

            if (isset($_SESSION) && isset($_SESSION['user_id']))
                $this->user = (new Db_User)->findById($_SESSION['user_id']);
        }

        // Render the page so it can be displayed to the user.
        public function render()
        {
            if (!sizeof($this->allowed_user_types) || in_array($this->user->type, $this->allowed_user_types))
                $this->render_html();
            else
                header('Location: '. $this->denied_redirect);
        }

        // Build the basic html page structure with required tags.
        private function render_html()
        {
            echo '<!DOCTYPE html>';
            echo '<html>';
                $this->render_head();
                $this->render_body();
            echo '</html>';
        }

        // Render the head of the page and standard head if defined.
        private function render_head()
        {
            echo '<head>';
                if ($this->use_standard_head && file_exists('core/standard/head.htm'))
                    include('core/standard/head.htm');
                    
                if (file_exists($this->dir . '/head.htm'))
                    include($this->dir . '/head.htm');
            echo '</head>';
        }

        // Render the body of the page, and all html the user will see.
        private function render_body()
        {
            echo '<body class="'. $this->name .'">';
                if (file_exists($this->dir . '/body.htm'))
                    include($this->dir . '/body.htm');
            echo '</body>';
        }
    }
?>