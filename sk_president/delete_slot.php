
<?php
require_once '../classes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['type'])) {
    $db = new Database();

    $id = (int) $_POST['id'];
    $type = $_POST['type'];

    if ($type === 'slot') {
        $db->deleteSlot($id);
    } elseif ($type === 'event') {
        $db->deleteEvent($id);
    }
}
