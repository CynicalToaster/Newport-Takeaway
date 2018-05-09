<?
    class Page_Manager_Orders extends Page
    {
        public $name = 'manager-orders';
        public $url = '/manager/orders';

        public $ajax_events = array(
            'delete_order'
        );

        public function ajax_delete_order()
        {
            $order_id = $_POST['id'];
            $order = (new Db_Order)->findById($order_id);

            foreach ($order->items as $order_item)
                $order_item->delete();

            $deleted = $order->delete();

            echo '<div class="alert alert-'. ($deleted ? 'success' : 'error') .'">';
            if ($deleted)
                echo 'Order deleted';
            else
                echo 'Order was not deleted';
            echo ' at '. date('h:i a m/d/Y', time());
            echo '</div>';
        }
    }
?>