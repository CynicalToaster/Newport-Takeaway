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
            $this->defineColumn('dob', 'dob', '');
            $this->defineColumn('postcode', 'postcode', '');
        }
    }
?>