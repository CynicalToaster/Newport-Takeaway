<?
    class Page_Home extends Page
    {
        public $url = '/';

        public $ajax_events = array(
            'add_item_to_order',
            'place_order',
            'clear_order',
            'search_menu'
        );

        public function ajax_add_item_to_order()
        {
            // Check if the item id is set.
            if (isset($_POST['id']))
            {
                $item_id = $_POST['id'];

                // Check if the item the user is adding to the order is already in thier order.
                // If it is then increase the count by one.
                // Else add the item to the order with a count of one.
                if (isset($_SESSION['order'][$item_id]))
                    $_SESSION['order'][$item_id] += 1;
                else
                    $_SESSION['order'][$item_id] = 1;
            }

            // Create the mark up, to display the current order to the user.
            $order_items = array();
            foreach ($_SESSION['order'] as $item_id => $item_count)
                $order_items[] = (new Db_Item())->findById($item_id);   

            $total_price = 0;

            echo '<p class="title"><strong>Current Order</strong></p>';
            echo '<ul>';
                foreach ($order_items as $item)
                {
                    $item_count = $_SESSION['order'][$item->id];

                    // Increase the total price by the cost of the order item.
                    $total_price += $item_count * $item->price;

                    echo '<li>';
                    echo '<span class="title">x'. $item_count .' '. $item->name .'</span>';
                    echo '<span class="price">&pound;'. ($item_count * $item->price) .'</span>';
                    echo '</li>';
                }
            echo '</ul>';

            // Display the total price after each item in the order has been looped through.
            echo '<p class="price">Total: &pound;'. $total_price .'</p>';

            echo '<button class="btn primary" onClick="placeOrder(); return false;">Place Order</button>';
        }

        public function ajax_place_order()
        {
            $success = false;

            // Check if there is an order in the session data.
            if (isset($_SESSION['order']))
            {
                // Create the new order model object.
                $order = (new Db_Order());
                $order->customer_id = $this->user->id;
                $order->items;

                // Loop through each order item the user has selected and add it to the order model using
                // a database relation.
                foreach ($_SESSION['order'] as $item_id => $item_count) {
                    $order_item = (new Db_Order_Item());
                    $order_item->order_id = $order->id;
                    $order_item->item_id = $item_id;
                    $order_item->count = $item_count;

                    $order->items[] = $order_item;
                }

                // Save the order model to the database.
                $success = $order->save();

                // Remove all order items from the users session so they are able to place a new order if needed.
                $_SESSION['order'] = null;
            }

            // Return an alert message updating the user of the status of thier order.
            if ($success)
                echo '<div class="alert alert-success">Your order has been placed.</div>';
            else
                echo '<div class="alert alert-error">Order could not be placed.</div>';
        }

        public function ajax_clear_order()
        {
            if (isset($_SESSION['order']))
                $_SESSION['order'] = null;
        }

        public function ajax_search_menu()
        {
            // Get the search term the user entered.
            $search_term = $_POST['search'];

            // Start creating the menu.
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
                        // Before displaying the menu item check if it's contains the search term entered by the user.
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