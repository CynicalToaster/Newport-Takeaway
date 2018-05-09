<?
    class Page_Home extends Page
    {
        public $url = '/';

        public $ajax_events = array(
            'add_item_to_order',
            'place_order'
        );

        public function ajax_add_item_to_order()
        {
            if (isset($_POST['id']))
            {
                $item_id = $_POST['id'];

                if (isset($_SESSION['order'][$item_id]))
                    $_SESSION['order'][$item_id] += 1;
                else
                    $_SESSION['order'][$item_id] = 1;
            }

            $order_items = array();
            foreach ($_SESSION['order'] as $item_id => $item_count)
                $order_items[] = (new Db_Item())->findById($item_id);   

            $total_price = 0;
            echo '<ul>';
                foreach ($order_items as $item)
                {
                    $item_count = $_SESSION['order'][$item->id];
                    $total_price += $item_count * $item->price;
                    echo '<li>'. $item->name .' - #'. $item_count .' - &pound;'. ($item_count * $item->price) .'</li>';
                }
            echo '</ul>';
            echo '<p>Total: &pound;'. $total_price .'</p>';
            echo '<button onClick="placeOrder(); return false;">Place Order</button>';
        }

        public function ajax_place_order()
        {
            $success = false;
            if (isset($_SESSION['order']))
            {
                $order = (new Db_Order());
                $order->customer_id = $this->user->id;
                $order->items;

                foreach ($_SESSION['order'] as $item_id => $item_count) {
                    $order_item = (new Db_Order_Item());
                    $order_item->order_id = $order->id;
                    $order_item->item_id = $item_id;
                    $order_item->count = $item_count;

                    $order->items[] = $order_item;
                }

                $success = $order->save();

                $_SESSION['order'] = null;
            }

            if ($success)
                echo 'Your order has been placed.';
            else
                echo 'Order could not be placed.';
        }
    }
?>