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
            // Checking if the username has been entered.
            $username = $this->username;
            if ($username == null || $username == '')
                return 'Please enter your username.';

            // Checking if a first name has been entered.
            $firstname = $this->firstname;
            if ($firstname == null || $firstname == '')
                return 'Please enter your forename.';

            // Check if the first name doesn't contains numbers.
            if (preg_match('~[0-9]~', $firstname))
                return 'Please a valid forename.';

            // Checking if a last name has been entered.
            $lastname = $this->lastname;
            if ($lastname == null || $lastname == '')
                return 'Please enter your surname.';

            // Check if the last name doesn't contains numbers.
            if (preg_match('~[0-9]~', $lastname))
                return 'Please a valid surname.';

            // Checking if a date of birth has been entered.
            $dob = $this->dob;
            if ($dob == null || $dob == '')
                return 'Please enter your date of birth.';

            // Check if the date of birth isn't in the future and isn't more than 150 years ago.
            // This was chosen as it is a reasonable age range for a customer.
            $dob = new DateTime($this->dob);
            $today = new DateTime("now");
            if ($today < $dob || $today->modify('-150 year') > $dob)
                return 'Please enter a valid date of birth.';

            // Checking if a postcode has been entered.
            $postcode = $this->postcode;
            if ($postcode == null || $postcode == '')
                return 'Please enter your postcode.';

            // Check if the postcode has a length of 7 as this is a valid postcode.
            if (strlen($postcode) != 7)
                return 'Please enter a valid postcode.';

            // If all fields are valid then return null instead of a message.
            return null;
        }
    }
?>