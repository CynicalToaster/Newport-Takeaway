<?
    class Db_Item extends Db_Model
    {
        public $table_name = 'items';

        public function defineColumns()
        {
            $this->defineColumn('id', 'id', null);
            $this->defineColumn('category_id', 'category_id', null);
            $this->defineColumn('name', 'name', '');
            $this->defineColumn('description', 'description', '');
            $this->defineColumn('price', 'price', '');

            $this->defineRelation('category', 'category_id', 'Db_Category', 'belongs_to');
        }

        public function defineFields()
        {
            $this->defineField('name', 'Name');
            $this->defineField('description', 'Description', 'textarea');
            $this->defineField('price', 'Price');
            $this->defineRelationField('category', 'Category', 'name');
        }
    }
?>