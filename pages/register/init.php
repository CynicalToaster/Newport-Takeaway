<?
    class Page_Register extends Page
    {
        public $url = '/register';

        public $ajax_events = array(
            'create_account'
        );

        public function ajax_create_account()
        {
            $user_type = 'customer';
            $username = $_POST['username'];
            $password = $_POST['password'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $dob = $_POST['dob'];

            $current_users = Db_Controller::queryArray('SELECT username FROM users');
            foreach ($current_users as $user) {
                if ($user['username'] == $username)
                    return 'User already exists!';
            }
            
            $password_hash = hash('sha256', $username . $password);
            $user_hash = password_hash($password_hash, PASSWORD_BCRYPT);

            $new_user = new Db_User();
                $new_user->type = $user_type;
                $new_user->hash = $user_hash;
                $new_user->username = $username;
                $new_user->firstname = $firstname;
                $new_user->lastname = $lastname;
                $new_user->dob = $dob;
            $new_user->save();
            
            return 'User created!';
        }
    }
?>