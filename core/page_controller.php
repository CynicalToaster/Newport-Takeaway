<?
    class Page_Controller
    {
        public $pages = array();
        public $pages_dir = 'pages/';

        public function __construct()
        {
            $this->getPages();
        }

        public function getPages()
        {
            $pages = glob($this->pages_dir . '*', GLOB_ONLYDIR);

            foreach ($pages as $page_dir) {
                if (file_exists($page_dir . '/init.php'))
                {
                    include_once($page_dir . '/init.php');

                    $split_page_dir = explode('/', $page_dir);
                    $page_name = 'Page_' . end($split_page_dir);

                    if (class_exists($page_name))
                    {
                        $page = new $page_name($this, $page_dir);
                        $this->pages[$page->url] = $page;
                    }
                }
            }
        }

        public function canProcessUrl($url = '/')
        {
            if (isset($this->pages[$url]))
                return true;
            return false;
        }

        public function processUrl($url = '/')
        {
            if (isset($this->pages[$url]))
            {
                $this->pages[$url]->render();
                return true;
            }
            return false;
        }
    }

    class Page
    {
        public $url = '/test';
        public $dir = '';
        public $controller = null;
        public $user = null;
        public $use_standard_head = true;
        public $ajax_events = array();

        public function __construct($controller, $dir)
        {
            $this->controller = $controller;
            $this->dir = $dir;

            if (isset($_SESSION) && isset($_SESSION['user_id']))
                $this->user = (new Db_User)->findById($_SESSION['user_id']);
        }

        public function render()
        {
            $this->render_html();
        }

        private function render_html()
        {
            echo '<!DOCTYPE html>';
            echo '<html>';
                $this->render_head();
                $this->render_body();
            echo '</html>';
        }

        private function render_head()
        {
            echo '<head>';
                if ($this->use_standard_head && file_exists('core/standard/head.htm'))
                    include('core/standard/head.htm');
                    
                if (file_exists($this->dir . '/head.htm'))
                    include($this->dir . '/head.htm');
            echo '</head>';
        }

        private function render_body()
        {
            echo '<body>';
                if (file_exists($this->dir . '/body.htm'))
                    include($this->dir . '/body.htm');
            echo '</body>';
        }
    }
?>