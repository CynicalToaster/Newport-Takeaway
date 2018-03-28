<?
    class Db_Controller
    {
        public static $connection;

        public function __construct()
        {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "newport_takeaway";
            
            // Create connection
            self::$connection = new mysqli($servername, $username, $password, $dbname);
            
            // Check connection
            if (self::$connection->connect_error) {
                die("Connection failed: " . self::$connection->connect_error);
            }

            // Include all models
            $model_files = array_diff(scandir('core/models/'), array('.', '..'));
            foreach ($model_files as $model_file)
                include_once('core/models/' . $model_file);
        }

        static function query($sql, $parameters = array())
        {
            foreach ($parameters as $name => $value)
                $sql = str_replace('{{'.$name.'}}', $value, $sql);

            traceLog($sql);

            return self::$connection->query($sql);
        }

        static function queryArray($sql, $parameters = array())
        {
            foreach ($parameters as $name => $value)
                $sql = str_replace('{{'.$name.'}}', $value, $sql);

            $result = array();
            $queryResult = self::$connection->query($sql);
            if ($queryResult->num_rows > 0) {
                while($row = $queryResult->fetch_assoc()) {
                    $result[] = $row;
                }
            }
            return $result;
        }
    }

    class Db_Model {
        public $table_name = '';
        public $columns = array();

        public function __construct()
        {
            $this->defineColumns();
        }

        public function defineColumns()
        {
            $this->defineColumn('id', 'id', null);
        }

        public function defineColumn($name, $column_name, $default = '')
        {
            $this->columns[$name] = $column_name;
            $this->$name = $default;
        }

        public function findById($id)
        {
            $result = Db_Controller::queryArray(
                'SELECT
                    *
                FROM
                    {{table_name}}    
                WHERE
                    id = {{id}}
            ', array(
                'id' => $id,
                'table_name' => $this->table_name
            ));

            foreach ($result[0] as $property => $value)
                $this->$property = $value;

            return $this;
        }

        public function save()
        {
            $columns = array();
            $values = array();
            
            foreach ($this->columns as $name => $column_name)
            {
                if ($this->$name != null)
                {
                    $columns[] = $column_name;
                    $values[] = '"'.$this->$name.'"';
                }
            }

            $columns = implode(',', $columns);
            $values = implode(',', $values);

            return Db_Controller::query(
                'REPLACE INTO
                    {{table_name}} ({{columns}})
                VALUES
                    ({{values}})
            ', array(
                'table_name' => $this->table_name,
                'columns' => $columns,
                'values' => $values
            ));
        }
    }
?>