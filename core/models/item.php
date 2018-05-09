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

        public function validate()
        {
            $name = $this->name;
            if ($name == null || $name == '')
                return 'Please enter an item name.';

            $description = $this->description;
            if ($description == null || $description == '')
                return 'Please enter an item description.';

            $description = $this->description;
            if ($description == null || $description == '')
                return 'Please enter an item description.';

            $price = $this->price;
            if ($price == null || $price == '')
                return 'Please enter an item price.';

            if ($price <= 0 || $price > 99)
                return 'Please enter a valid price.';

            return null;
        }
    }
?>