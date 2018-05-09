<?
    class Page_Home extends Page
    {
        public $url = '/';

        public $ajax_events = array(
            'add_item_to_order',
            'place_order',
            'search_menu'
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

            echo '<p class="title"><strong>Current Order</strong></p>';
            echo '<ul>';
                foreach ($order_items as $item)
                {
                    $item_count = $_SESSION['order'][$item->id];
                    $total_price += $item_count * $item->price;
                    echo '<li>';
                    echo '<span class="title">x'. $item_count .' '. $item->name .'</span>';
                    echo '<span class="price">&pound;'. ($item_count * $item->price) .'</span>';
                    echo '</li>';
                }
            echo '</ul>';
            echo '<p class="price">Total: &pound;'. $total_price .'</p>';
            echo '<button class="btn primary" onClick="placeOrder(); return false;">Place Order</button>';
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
                echo '<div class="alert alert-success">Your order has been placed.</div>';
            else
                echo '<div class="alert alert-error">Order could not be placed.</div>';
        }

        public function ajax_search_menu()
        {
            $search_term = $_POST['search'];
            $categories = (new Db_Category())->findAll();

            echo '<ul class="menu-category-list">';
            foreach ($categories as $category)
            {
                if (sizeof($category->items) > 0)
                {
                    echo '<li class="menu-category">';
                    echo    '<p class="title"><strong>'. $category->name .'</strong></p>';
                    echo    '<ul class="category-item-list">';
                    foreach ($category->items as $item)
                    {
                        if ($search_term == '' || strpos(strtolower($item->name), strtolower($search_term)) !== false)
                        {
                            echo '<li class="category-item">';
                            echo    '<span class="title"><strong>'. $item->name .'</strong></span>';
                            if (isset($this->user))
                            {
                                echo '<a class="category-item-edit" href="#" onClick="addItemToOrder('. $item->id .');">Add</a>';
                            }
                            echo    '<span class="price">&pound;'. $item->price .'</span><br>';
                            echo    '<span class="description">'. $item->description .'</span>';
                            echo '</li>';
                        }
                    }
                    echo    '</ul>';
                    echo '</li>';
                }
            }
            echo '</ul>';
        }
    }
?>