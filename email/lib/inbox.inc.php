<?php

/*
 * Copyright (C) 2006-2011 Alex Lance, Clancy Malcolm, Cyber IT Solutions
 * Pty. Ltd.
 * 
 * This file is part of the allocPSA application <info@cyber.com.au>.
 * 
 * allocPSA is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at
 * your option) any later version.
 * 
 * allocPSA is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public
 * License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with allocPSA. If not, see <http://www.gnu.org/licenses/>.
*/

class inbox extends db_entity {

  function verify_hash($id,$hash) {
    $info = inbox::get_mail_info();
    $email_receive = new email_receive($info);
    $email_receive->open_mailbox($info["folder"],OP_HALFOPEN | OP_READONLY);
    $email_receive->set_msg($id);
    $email_receive->get_msg_header();
    $rtn = ($hash == md5($email_receive->mail_headers["date"]
                        .$email_receive->get_printable_from_address()
                        .$email_receive->mail_headers["subject"]));
    $email_receive->close();
    return $rtn;
  }

  function archive_email($req=array()) {
    global $TPL;
    $info = inbox::get_mail_info();
    $email_receive = new email_receive($info);
    $email_receive->open_mailbox($info["folder"]);
    $mailbox = "INBOX/archive".date("Y");
    $email_receive->create_mailbox($mailbox) and $TPL["message_good"][] = "Created mailbox: ".$mailbox;
    $email_receive->move_mail($req["id"],$mailbox) and $TPL["message_good"][] = "Moved email ".$req["id"]." to ".$mailbox;
    $email_receive->close();
  }

  function download_email($req=array()) {
    global $TPL;
    $info = inbox::get_mail_info();
    $email_receive = new email_receive($info);
    $email_receive->open_mailbox($info["folder"],OP_HALFOPEN | OP_READONLY);
    $email_receive->set_msg($req["id"]);
    $new_nums = $email_receive->get_new_email_msg_uids();
    in_array($req["id"],(array)$new_nums) and $new = true;
    list($h,$b) = $email_receive->get_raw_header_and_body();
    $new and $email_receive->set_unread(); // might have to "unread" the email, if it was new, i.e. set it back to new
    $email_receive->close();
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="email'.$req["id"].'.txt"');
    echo $h.$b;
    exit();
  }

  function process_email($req=array()) {
    global $TPL;
    $info = inbox::get_mail_info();
    $email_receive = new email_receive($info);
    $email_receive->open_mailbox($info["folder"]);
    $email_receive->set_msg($req["id"]);
    $email_receive->get_msg_header();
    inbox::process_one_email($email_receive);
    $email_receive->expunge();
    $email_receive->close();
  }

  function process_one_email($email_receive) {
    $current_user = &singleton("current_user");
    $orig_current_user = &$current_user;

    // Errors from previous iterations shouldn't affect processing of the next email
    db_alloc::$stop_doing_queries = false;

    // wrap db queries in a transaction
    $db = new db_alloc();
    $db->start_transaction();

    // Skip over emails that are from alloc. These emails are kept only for
    // posterity and should not be parsed and downloaded and re-emailed etc.
    if (same_email_address($email_receive->mail_headers["from"], ALLOC_DEFAULT_FROM_ADDRESS)) {
      $email_receive->mark_seen();
      $email_receive->archive();
      return false;
    }
 
    list($from_address,$from_name) = parse_email_address($email_receive->mail_headers["from"]);
    $person = new person();
    $personID = $person->find_by_email($from_address);
    $personID or $personID = $person->find_by_name($from_name);

    // If we've determined a personID from the $from_address
    if ($personID) {
      $current_user = new person();
      $current_user->load_current_user($personID);
      singleton("current_user",$current_user);
    } 

    // Save the email's attachments into a directory, (which also loads up $email_receive->mail_text)
    $dir = ATTACHMENTS_DIR."comment".DIRECTORY_SEPARATOR."tmp-".md5($email_receive->mail_headers["message-id"]);
    $email_receive->save_email($dir.DIRECTORY_SEPARATOR);

    // Run any commands that have been embedded in the email
    $command = new command();
    $fields = $command->get_fields();
    $commands = $email_receive->get_commands($fields);

    try {
      $command->run_commands($commands,$email_receive);
    } catch (Exception $e) {
      $current_user = &$orig_current_user;
      singleton("current_user",$current_user);
      $db->query("ROLLBACK");
      $email_receive->forward(config::get_config_item("allocEmailAdmin")
                             ,"Email command failed"
                             ,"\n".$e->getMessage()."\n\n".$e->getTraceAsString());
      $failed = true;
    }

    // Commit the db, and move the email into its storage location eg: INBOX.task1234
    if (!$failed && !$TPL["message"]) {
      $db->commit();
      $email_receive->archive();
    }

    // Put current_user back to normal
    $current_user = &$orig_current_user;
    singleton("current_user",$current_user);
  }

  function convert_email_to_new_task($req=array()) {
    global $TPL;
    $info = inbox::get_mail_info();
    // Need the task ID up front
    $task = new task();
    $task->set_value("taskName"," ");
    $task->set_value("priority","3");
    $task->set_value("taskTypeID","Task");
    $task->save();
    $taskID = $task->get_id();

    // Save the email's attachments to the task's attachments tab, save_email
    // also loads up mail_text and then archive this email to the task's mbox.
    if (!$TPL["message"]) {
      $email_receive = new email_receive($info);
      $email_receive->open_mailbox($info["folder"]);
      $email_receive->set_msg($req["id"]);
      $email_receive->get_msg_header();
      $email_receive->save_email(ATTACHMENTS_DIR."task".DIRECTORY_SEPARATOR.$task->get_id());
    }

    // Subject line is name, email body is body
    if (!$TPL["message"] && $taskID) {
      $task = new task();
      $task->set_id($taskID);
      $task->select();
      $task->skip_modified_fields = true;
      $task->set_value("taskName",$email_receive->mail_headers["subject"]);
      $task->set_value("taskDescription",$email_receive->mail_text);
      $task->save();
      if ($task->get_id()) {
        $TPL["message_good"][] = "Created task ".$task->get_id()." and moved the email to the task's mail folder.";
        $mailbox = "INBOX/task".$task->get_id();
        $email_receive->create_mailbox($mailbox) and $TPL["message_good"][] = "Created mailbox: ".$mailbox;
        $email_receive->move_mail($req["id"],$mailbox) and $TPL["message_good"][] = "Moved email ".$req["id"]." to ".$mailbox;
      }
    }
    if (is_object($email_receive)) {
      $email_receive->close(); 
    }
  }

  function attach_email_to_existing_task($req=array()) {
    global $TPL;
    $info = inbox::get_mail_info();
    $current_user = &singleton("current_user");
    $orig_current_user = &$current_user;
    $req["taskID"] = sprintf("%d",$req["taskID"]);

    $task = new task();
    $task->set_id($req["taskID"]);
    if ($task->select()) {
      $email_receive = new email_receive($info);
      $email_receive->open_mailbox($info["folder"]);
      $email_receive->set_msg($req["id"]);
      $email_receive->get_msg_header();

      $dir = ATTACHMENTS_DIR."comment".DIRECTORY_SEPARATOR."tmp-".md5($email_receive->mail_headers["message-id"]);
      $email_receive->save_email($dir);

      $c = comment::add_comment_from_email($email_receive,$task);
      $commentID = $c->get_id();
      $commentID and $TPL["message_good"][] = "Created comment ".$commentID." on task ".$task->get_id()." ".$task->get_name();

      // Possibly change the identity of current_user
      list($from_address,$from_name) = parse_email_address($email_receive->mail_headers["from"]);
      $person = new person();
      $personID = $person->find_by_email($from_address);
      $personID or $personID = $person->find_by_name($from_name);
      if ($personID) {
        $current_user = new person();
        $current_user->load_current_user($personID);
        singleton("current_user",$current_user);
      } 

      $quiet = interestedParty::adjust_by_email_subject($email_receive,$task);

      // swap back to normal user
      $current_user = &$orig_current_user;
      singleton("current_user",$current_user);

      // add interested parties
      $ips = interestedParty::get_interested_parties("task",$req["taskID"]);
      foreach((array)$ips as $k => $inf) {
        $inf["entity"] = "comment";
        $inf["entityID"] = $commentID;
        $inf["email"] and $inf["emailAddress"] = $inf["email"];
        if ($req["emailto"] == "internal" && !$inf["external"] && !$inf["clientContactID"]) {
          $id = interestedParty::add_interested_party($inf);
          $recipients[] = $inf["name"]." ".add_brackets($k);
        } else if ($req["emailto"] == "default") {
          $id = interestedParty::add_interested_party($inf);
          $recipients[] = $inf["name"]." ".add_brackets($k);
        }
      }
    
      $recipients and $recipients = implode(", ",(array)$recipients);
      $recipients and $TPL["message_good"][] = "Sent email to ".$recipients;

      // Re-email the comment out
      comment::send_comment($commentID,array("interested"),$email_receive);

      // File email away in the task's mail folder
      $mailbox = "INBOX/task".$task->get_id();
      $email_receive->create_mailbox($mailbox) and $TPL["message_good"][] = "Created mailbox: ".$mailbox;
      $email_receive->move_mail($req["id"],$mailbox) and $TPL["message_good"][] = "Moved email ".$req["id"]." to ".$mailbox;
      $email_receive->close();
    }
  }

  function unread_email($req=array()) {
    global $TPL;
    $info = inbox::get_mail_info();
    $email_receive = new email_receive($info);
    $email_receive->open_mailbox($info["folder"]);
    $email_receive->set_msg($req["id"]);
    $email_receive->set_unread();
    $email_receive->close();
  }

  function read_email($req=array()) {
    global $TPL;
    $info = inbox::get_mail_info();
    $email_receive = new email_receive($info);
    $email_receive->open_mailbox($info["folder"]);
    $email_receive->set_msg($req["id"]);
    list($h,$b) = $email_receive->get_raw_header_and_body();
    $email_receive->close();
  }

  function get_mail_info() {
    $info["host"] = config::get_config_item("allocEmailHost");
    $info["port"] = config::get_config_item("allocEmailPort");
    $info["username"] = config::get_config_item("allocEmailUsername");
    $info["password"] = config::get_config_item("allocEmailPassword");
    $info["protocol"] = config::get_config_item("allocEmailProtocol");
    $info["folder"] = config::get_config_item("allocEmailFolder");
    return $info;
  }

  function get_list() {
    // Get list of emails
    $info = inbox::get_mail_info();
    $email_receive = new email_receive($info);
    $email_receive->open_mailbox($info["folder"],OP_HALFOPEN | OP_READONLY);
    $email_receive->check_mail();
    $new_nums = $email_receive->get_new_email_msg_uids();
    $msg_nums = $email_receive->get_all_email_msg_uids();

    if ($msg_nums) {
      foreach ($msg_nums as $num) {
        $row = array();
        $email_receive->set_msg($num);
        $email_receive->get_msg_header();
        $row["from"] = $email_receive->get_printable_from_address();
        in_array($num,(array)$new_nums) and $row["new"] = true;

        $row["id"] = $num;
        $row["date"] = $email_receive->mail_headers["date"];
        $row["subject"] = $email_receive->mail_headers["subject"];
        $rows[] = $row;
      }
    }
    $email_receive->close();
    return $rows;
  }

}
?>
