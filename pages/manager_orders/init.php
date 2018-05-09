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

            $orders = (new Db_Order)->findAll();
            foreach ($orders as $order)
            {
                echo '<div class="order">';
                echo    '<p class="order-header">';
                echo        '<strong>Order #'. $order->id .'</strong>';
                echo        '<a class="order-delete" href="#" onclick="deleteOrder('. $order->id .');">Mark as Complete</a>';
                echo    '</p>';
                echo    '<ul class="order-items">';
                            foreach ($order->items as $order_item)
                                echo '<li>#'. $order_item->count .' '. $order_item->item->name .'</li>';
                echo    '</ul>';
                echo '</div>';
            }

            if (sizeof($orders) == 0)
                echo '<span>No orders have been created.</span>';
        }
    }
?>