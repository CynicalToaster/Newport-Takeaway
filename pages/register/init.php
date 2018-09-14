<?
    class Page_Register extends Page
    {
        public $url = '/register';

        public $ajax_events = array(
            'create_account'
        );

        public function ajax_create_account()
        {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if ($password == null || $password == '')
                return 'Please enter a Username and Password';

            if (strlen($password) < 8)
                return 'Please enter a secure password. (8 or more characters)';

            $current_users = Db_Controller::queryArray('SELECT username FROM users');
            foreach ($current_users as $user) {
                if ($user['username'] == $username)
                    return 'User already exists!';
            }
            
            $password_hash = hash('sha256', $username . $password);
            $user_hash = password_hash($password_hash, PASSWORD_BCRYPT);

            $new_user = new Db_User();
            $new_user->updateFromPost($_POST);
            $new_user->type = 'customer';
            $new_user->hash = $user_hash;

            $valid = $new_user->validate();
            if (!isset($valid))
            {
                $new_user->save();
                return 1;
            }
            else
                return $valid;
        }
    }
?>