<?php
/* 
   * Session Management for PHP3
   *
   * Copyright (c) 1998,1999 SH Online Dienst GmbH
   *                    Boris Erdmann, Kristian Koehntopp
   *
   */
  function page_open($feature) {

  # enable sess and all dependent features.
  if (isset($feature["sess"])) {
    global $sess;
    $sess = new $feature["sess"];
    $sess->start();

    # the auth feature depends on sess
    if (isset($feature["auth"])) {
      global $auth;

      if (!isset($auth)) {
        $auth = new $feature["auth"];
      }
      $auth->start();


      # the perm feature depends on auth and sess
      if (isset($feature["perm"])) {
        global $perm;

        if (!isset($perm)) {
          $perm = new $feature["perm"];
        }
      }
      
      # the user feature depends on auth and sess
      if (isset($feature["user"])) {
        global $user;

        if (!isset($user)) {
          $user = new $feature["user"];
        }
        $user->start($auth->auth["uid"]);
      }
    }
    # Load the auto_init-File, if one is specified.
    if (($sess->auto_init != "") && ($sess->in == "")) {
      $sess->in = 1;
      include(ALLOC_MOD_DIR."/shared/".$sess->auto_init);
      if ($sess->secure_auto_init != "") {
        $sess->freeze();
      }
    }
  }
}

function page_close() {
  global $sess, $user;

  if (isset($sess)) {
    $sess->freeze();
    if (isset($user)) {
      $user->freeze();
    }
  }
}

function sess_load($session) {
  reset($session);
  while (list($k, $v) = each($session)) {
    $GLOBALS[$k] = new $v;
    $GLOBALS[$k]->start();
  }
}

function sess_save($session) {
  reset($session);
  while (list(, $v) = each($session)) {
    $GLOBALS[$v]->freeze();
  }
}



?>
