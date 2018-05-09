<?
    class Db_Category extends Db_Model
    {
        public $table_name = 'categories';

        public function defineColumns()
        {
            $this->defineColumn('id', 'id', null);
            $this->defineColumn('name', 'name', '');
            
            $this->defineRelation('items', 'category_id', 'Db_Item', 'has_many');
        }
    }
?>