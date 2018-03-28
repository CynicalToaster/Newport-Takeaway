<?
    function traceLog($message)
    {
        file_put_contents('logs/info.log', @date('d-m-Y h:i:s').' INFO '.print_r($message, true).' '.PHP_EOL, FILE_APPEND);
    }

    include_once('core/url_controller.php');
    include_once('core/db_controller.php');
    include_once('core/ajax_controller.php');
    include_once('core/page_controller.php');

    session_start();

    $db_controller = new Db_Controller();
    $url_controller = new Url_Controller();
    $page_controller = new Page_Controller();
    $ajax_controller = new Ajax_Controller($page_controller);

    $url_controller->init(array(
        $ajax_controller,
        $page_controller
    ));
?>