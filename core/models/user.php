<?
    class Db_User extends Db_Model
    {
        public $table_name = 'users';

        public function defineColumns()
        {
            $this->defineColumn('id', 'id', null);
            $this->defineColumn('type', 'type', '');
            $this->defineColumn('hash', 'hash', '');
            $this->defineColumn('username', 'username', '');
            $this->defineColumn('firstname', 'firstname', '');
            $this->defineColumn('lastname', 'lastname', '');
            $this->defineColumn('dob', 'dob', '', 'date');
            $this->defineColumn('postcode', 'postcode', '');
        }

        public function validate()
        {
            $username = $this->username;
            if ($username == null || $username == '')
                return 'Please enter your username.';

            $firstname = $this->firstname;
            if ($firstname == null || $firstname == '')
                return 'Please enter your forename.';

            $lastname = $this->lastname;
            if ($lastname == null || $lastname == '')
                return 'Please enter your surname.';

            $dob = $this->dob;
            if ($dob == null || $dob == '')
                return 'Please enter your date of birth.';

            $dob = new DateTime($this->dob);
            $today = new DateTime("now");
            if ($today < $dob || $today->modify('-150 year') > $dob)
                return 'Please enter a valid date of birth.';

            $postcode = $this->postcode;
            if ($postcode == null || $postcode == '')
                return 'Please enter your postcode.';

            if (strlen($postcode) != 7)
                return 'Please enter a valid postcode.';

            return null;
        }
    }
?>