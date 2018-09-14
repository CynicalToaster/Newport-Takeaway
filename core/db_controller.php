<?
    class Db_Controller
    {
        public static $connection;

        public function __construct()
        {
            // MySQL server database login and connection configuration.
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

        // Execute an SQL query.
        static function query($sql, $parameters = array())
        {
            // Build the SQL query using the parameters provided.
            foreach ($parameters as $name => $value)
                $sql = str_replace('{{'.$name.'}}', $value, $sql);

            // Run the query and return it's results as a string.
            return self::$connection->query($sql);
        }

        // Execute an SQL query but return the each row as an item in an array.
        static function queryArray($sql, $parameters = array())
        {
            $result = array();

            // Execute the SQL query.
            $queryResult = self::query($sql, $parameters);

            // Formate the SQL into an array.
            if ($queryResult != null && $queryResult->num_rows > 0) {
                while($row = $queryResult->fetch_assoc()) {
                    $result[] = $row;
                }
            }

            // Return the array of results.
            return $result;
        }

        static function lastInsertId()
        {
            return self::$connection->insert_id;
        }
    }

    // Db_Model class, which all models should inherit from.
    class Db_Model {
        public $table_name = '';
        public $columns = array();
        public $fields = array();
        public $relations = array();

        public function __construct()
        {
            // Called when a new DB_Model class initialised.
            $this->defineColumns();
            $this->defineFields();
        }

        // A method used to define what database columns the model has.
        public function defineColumns()
        {
            $this->defineColumn('id', 'id', null);
        }

        // When called within the defineColumns method this will tell the model that it has
        // a new column that it should attempt to get data from.
        public function defineColumn($name, $column, $default = '')
        {
            $this->columns[$name] = $column;
            $this->$name = $default;
        }

        // When called within the defineColumns method this will tell the model that it has
        // a column that relates to another table or other table relates to it.
        public function defineRelation($name, $column, $model, $type)
        {
            // Create the relation using the foriegn key column name, the model of the relating table,
            // as well as the type of relation (one-to-many / many-to-one)
            $this->relations[$name] = array(
                'column' => $column,
                'model' => $model,
                'type' => $type
            );

            // This defines the relations foriegn key as a new column.
            $this->defineColumn($name .'_id', $column, 0);
        }

        // A method used to define what fields the model form has.
        public function defineFields()
        {
        }

        // When called within the defineField method this will tell the model that it has
        // a new field that it should render when renderForm() is executed.
        public function defineField($column, $label, $type = 'text')
        {
            // Create the field using the foriegn key column name, the label displayed above the input
            // as well as the type of input to use.
            $this->fields[] = array(
                'column' => $column,
                'label' => $label,
                'type' => $type
            );
        }

        // When called within the defineField method this will tell the model that it has
        // a new relational field that it should render when renderForm() is executed.
        public function defineRelationField($relation, $label, $display_column)
        {
            // Create the field using the foriegn key column name, the label displayed above the input.
            $this->fields[] = array(
                'column' => $relation,
                'label' => $label,
                'display_column' => $display_column
            );
        }

        public function renderForm()
        {
            // Create the hidden input used to store the model id. 
            echo '<input name="id" type="hidden" value="'. $this->id .'">';

            // Loop through each field that has been defined in the model's defineFields() method.
            foreach ($this->fields as $index => $field)
                $this->renderField($field);
        }

        public function renderField($field)
        {
            // Get the name of the column.
            $column = $field['column'];

            // Create the markup for the field.
            echo '<div class="form-field">';
            echo    '<label for="'. $column .'">'. $field['label'] .'</label>';

            // Check if the column is a relation instead of a standard value.
            if (array_key_exists($column, $this->relations))
            {
                // If the column is a database relation then it must be rendered as a select field.
                // As there are only a set number of options that it can have to maintian the relation.
                // For example an item on the menu has a database relation for it's category, so the user
                // can only select between the categories in the system.
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

            // If the column is just a standard column then render an input field depending on the type that was defined.
            else if ($field['type'] == 'text')
                echo    '<input name="'. $column .'" type="text" value="'. $this->$column .'">';
            else if ($field['type'] == 'textarea')
                echo    '<textarea name="'. $column .'">'. $this->$column . '</textarea>';
            
            echo '</div>';
        }

        // Builds and Executes a SQL query to get all models from the database.
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

            // Create an array of new models using the data returned from the database.
            foreach ($result as $index => $item)
            {
                $new_item = new $this;
                foreach ($item as $property => $value)
                    $new_item->$property = $value;

                $results[] = $new_item;
            }

            // Return the array of models.
            return $results;
        }

        // Builds and Executes a SQL query to get the model from the database that has a specific id.
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

        // Builds and Executes a SQL query to get the models from the database that has matches the WHERE sql passed in.
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

        // Use data from the $_POST array attempt to fill in the model with as much data as possible.
        public function updateFromPost($post)
        {
            foreach ($this->columns as $index => $column_name) {
                if (isset($post[$column_name]))
                    $this->$column_name = $post[$column_name];
            }
        }

        // Save the current model and update the database.
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

            // Check if the model id is set. If not an insert query should be run else an update query should be run.
            if ($this->id != null && $this->id != 0)
            {
                // Format the data in the model to work with SQL.
                $set_values = array();
                for ($i = 0; $i < sizeof($columns); $i++) { 
                    $set_values[] = $columns[$i] .' = '. $values[$i];
                }
                $set_values = implode(',', $set_values);

                // Build the SQL UPDATE query with all the data from the model object.
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
                // Format the data in the model to work with SQL.
                $columns = implode(',', $columns);
                $values = implode(',', $values);

                // Build the SQL INSERT query with all the data from the model object.
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

                // As a new record was added, the models id needs to be updated to match the new record.
                $this->id = Db_Controller::lastInsertId();
            }
            
            // If the model has any relations these also need to be saved.
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

            // Return if the saving process was successfull or not.
            return $success;
        }

        // Delete the model from the database.
        public function delete()
        {
            // Build and execute a SQL DELETE command.
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

        // So a recursive loop doesn't not occure and there is less strain on the database.
        // Any relations should only be retrieved if an attempt to access them was made.
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