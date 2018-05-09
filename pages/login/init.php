<?
    class Page_Login extends Page
    {
        public $name = 'login';
        public $url = '/login';

        public $ajax_events = array(
            'login'
        );

        public function ajax_login()
        {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $users = Db_Controller::queryArray('SELECT id, hash FROM users WHERE username = "'. $username .'"');

            $password_hash = hash('sha256', $username . $password);
            if(password_verify($password_hash, $users[0]['hash']))
            {

                $_SESSION['user_id'] = $users[0]['id'];

                return 'Logged in as: '. $username;
            }
            else
                return 'Username or Password is incorrect.';
        }
    }
?>