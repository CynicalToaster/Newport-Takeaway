<?
    class Page_Manager_Menu_Items_Edit extends Page
    {
        public $name = 'manager-menu-items-edit';
        public $url = '/manager/menu/items/edit';

        public $ajax_events = array(
            'save_item',
            'delete_item'
        );

        public function ajax_save_item()
        {
            $item_id = $_POST['id'];

            if ($item_id != null && $item_id != 0)
                $item = (new Db_Item)->findById($item_id);
            else
                $item = new Db_Item();

            $item->updateFromPost($_POST);            
            
            $saved = $item->save();
            
            echo '<div class="alert alert-'. ($saved ? 'success' : 'error') .'">';
            if ($saved)
                echo 'Item saved';
            else
                echo 'Item was not saved';
            echo ' at '. date('h:i a m/d/Y', time());
            echo '</div>';
            
        }

        public function ajax_delete_item()
        {
            $item_id = $_POST['id'];
            $deleted = (new Db_Item)->findById($item_id)->delete();

            echo '<div class="alert alert-'. ($deleted ? 'success' : 'error') .'">';
            if ($deleted)
                echo 'Item deleted';
            else
                echo 'Item was not deleted';
            echo ' at '. date('h:i a m/d/Y', time());
            echo '</div>';
        }
    }
?>