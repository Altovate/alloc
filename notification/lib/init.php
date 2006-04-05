<?php
class notification_module extends module {
  var $db_entities = array("eventFilter", "reminder");

  function handle_event($event) {
    $notification = new notification;
    $notification->handle_event($event);
  }

  function register_toolbar_items() {
    register_toolbar_item("eventFilterList", "Reminders");
  }
}

include(ALLOC_MOD_DIR."/notification/lib/notification.inc.php");
include(ALLOC_MOD_DIR."/notification/lib/reminder.inc.php");



?>
