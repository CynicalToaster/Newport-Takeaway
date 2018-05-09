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

            return self::$connection->query($sql);
        }

        static function queryArray($sql, $parameters = array())
        {
            foreach ($parameters as $name => $value)
                $sql = str_replace('{{'.$name.'}}', $value, $sql);

            $result = array();
            $queryResult = self::$connection->query($sql);
            if ($queryResult != null && $queryResult->num_rows > 0) {
                while($row = $queryResult->fetch_assoc()) {
                    $result[] = $row;
                }
            }
            return $result;
        }

        static function lastInsertId()
        {
            return self::$connection->insert_id;
        }
    }

    class Db_Model {
        public $table_name = '';
        public $columns = array();
        public $fields = array();
        public $relations = array();

        public function __construct()
        {
            $this->defineColumns();
            $this->defineFields();
        }

        public function defineColumns()
        {
            $this->defineColumn('id', 'id', null);
        }

        public function defineColumn($name, $column, $default = '')
        {
            $this->columns[$name] = $column;
            $this->$name = $default;
        }

        public function defineRelation($name, $column, $model, $type)
        {
            $this->relations[$name] = array(
                'column' => $column,
                'model' => $model,
                'type' => $type
            );

            $this->defineColumn($name .'_id', $column, 0);
        }

        public function defineFields()
        {
        }

        public function defineField($column, $label, $type = 'text')
        {
            $this->fields[] = array(
                'column' => $column,
                'label' => $label,
                'type' => $type
            );
        }

        public function defineRelationField($relation, $label, $display_column)
        {
            $this->fields[] = array(
                'column' => $relation,
                'label' => $label,
                'display_column' => $display_column
            );
        }

        public function renderForm()
        {
            echo '<input name="id" type="hidden" value="'. $this->id .'">';
            foreach ($this->fields as $index => $field)
                $this->renderField($field);
        }

        public function renderField($field)
        {
            $column = $field['column'];
            echo '<div class="form-field">';
            echo    '<label for="'. $column .'">'. $field['label'] .'</label>';

            if (array_key_exists($column, $this->relations))
            {
                $display_column = $field['display_column'];
                $relation = $this->relations[$column];
                $relation_column = $relation['column'];
                $model_name = $relation['model'];
                $models = (new $model_name())->findAll();

                echo '<select name="'. $relation_column .'">';

                foreach ($models as $model)
                {
                    echo '<option value="'. $model->id .'" '. ($model->id == $this->$relation_column ? 'selected' : '') .'>';
                    echo $model->$display_column;
                    echo '</option>';
                }

                echo '</select>';
            }
            else if ($field['type'] == 'text')
                echo    '<input name="'. $column .'" type="text" value="'. $this->$column .'">';
            else if ($field['type'] == 'textarea')
                echo    '<textarea name="'. $column .'">'. $this->$column . '</textarea>';
            
            echo '</div>';
        }

        public function findAll()
        {
            $results = array();

            $result = Db_Controller::queryArray(
                'SELECT
                    *
                FROM
                    {{table_name}}    
            ', array(
                'table_name' => $this->table_name
            ));

            foreach ($result as $index => $item)
            {
                $new_item = new $this;
                foreach ($item as $property => $value)
                    $new_item->$property = $value;

                $results[] = $new_item;
            }

            return $results;
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

        public function findWhere($sql, $parameters = array())
        {
            $parameters['table_name'] = $this->table_name;
            $results = Db_Controller::queryArray(
                'SELECT
                    *
                FROM
                    {{table_name}}
                ' . $sql
            , $parameters);

            $model_results = array();
            foreach ($results as $result)
            {
                $model = new $this();
                foreach ($result as $property => $value)
                    $model->$property = $value;
                $model_results[] = $model;
            }

            return $model_results;
        }

        public function updateFromPost($post)
        {
            foreach ($this->columns as $index => $column_name) {
                if (isset($post[$column_name]))
                    $this->$column_name = $post[$column_name];
            }
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

            if ($this->id != null && $this->id != 0)
            {
                $set_values = array();
                for ($i = 0; $i < sizeof($columns); $i++) { 
                    $set_values[] = $columns[$i] .' = '. $values[$i];
                }
                $set_values = implode(',', $set_values);

                $success = Db_Controller::query(
                    'UPDATE
                        {{table_name}}
                    SET
                        {{values}}
                    WHERE
                        id = {{id}}
                ', array(
                    'table_name' => $this->table_name,
                    'id' => $this->id,
                    'values' => $set_values
                ));
            }
            else
            {
                $columns = implode(',', $columns);
                $values = implode(',', $values);

                $success = Db_Controller::query(
                    'INSERT INTO
                        {{table_name}} ({{columns}})
                    VALUES
                        ({{values}})
                ', array(
                    'table_name' => $this->table_name,
                    'columns' => $columns,
                    'values' => $values
                ));

                $this->id = Db_Controller::lastInsertId();
            }
            
            foreach ($this->relations as $name => $relation)
            {
                if (isset($this->$name))
                {
                    if ($relation['type'] == 'belongs_to')
                        $this->$name->save();
                    else
                        foreach ($this->$name as $item)
                        {
                            $relation_id = $relation['column'];
                            $item->$relation_id = $this->id;
                            $item->save();
                        }
                }
            }
            return $success;
        }

        public function delete()
        {
            return Db_Controller::query(
                'DELETE FROM
                    {{table_name}}
                WHERE
                    id = {{id}}
            ', array(
                'table_name' => $this->table_name,
                'id' => $this->id
            ));
        }

        public function validate()
        {
        }

        public function __get($property)
        {
            if (isset($this->relations[$property]))
            {
                $relation = $this->relations[$property];
                if ($relation['type'] == 'belongs_to')
                {
                    $column = $relation['column'];
                    $model_name = $relation['model'];
                    $model = new $model_name();
                    $value = $model->findById($this->$column);
                    return $this->$property = $value;
                }
                else if ($relation['type'] == 'has_many')
                {
                    $model_name = $relation['model'];
                    $model = new $model_name();
                    $value = $model->findWhere(
                        'WHERE
                            {{column}} = {{id}}
                        ',
                        array(
                            'id' => $this->id,
                            'column' => $relation['column']
                        )
                    );
                    return $this->$property = $value;
                }
            }
        }
    }
?>