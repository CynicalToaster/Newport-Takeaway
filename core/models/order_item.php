<?
    class Db_Order_Item extends Db_Model
    {
        public $table_name = 'order_items';

        public function defineColumns()
        {
            $this->defineColumn('id', 'id', null);
            $this->defineColumn('count', 'count', 0);

            $this->defineRelation('order', 'order_id', 'Db_Order', 'belongs_to');
            $this->defineRelation('item', 'item_id', 'Db_Item', 'belongs_to');
        }
    }
?>