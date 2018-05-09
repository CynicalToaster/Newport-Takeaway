<?
    class Db_Order extends Db_Model
    {
        public $table_name = 'orders';

        public function defineColumns()
        {
            $this->defineColumn('id', 'id', null);

            $this->defineRelation('customer', 'customer_id', 'Db_Customer', 'belongs_to');
            $this->defineRelation('items', 'order_id', 'Db_Order_Item', 'has_many');
        }
    }
?>