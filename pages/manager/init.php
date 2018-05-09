<?
    class Page_Manager extends Page
    {
        public $name = 'manager';
        public $url = '/manager';
        public $allowed_user_types = array('admin');
        public $denied_redirect = '/login';
    }
?>