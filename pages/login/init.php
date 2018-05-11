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

            $users = (new Db_User())->findWhere(
                'WHERE
                    username = {{username}}
            ', array(
                'username' => '\''.$username.'\''
            ));

            if (!sizeof($users))
                return 'Please enter your Username and Password';

            foreach ($users as $user)
            {
                $password_hash = hash('sha256', $username . $password);
                if(password_verify($password_hash, $user->hash))
                {
                    $_SESSION = array();
                    $_SESSION['user_id'] = $user->id;

                    return 1;
                }
                else
                    return 'Username or Password is incorrect';
            }

            return 'Please enter your Username and Password';
        }
    }
?>