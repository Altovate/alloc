<?php

/*
 *
 * Copyright 2006, Alex Lance, Clancy Malcolm, Cybersource Pty. Ltd.
 * 
 * This file is part of allocPSA <info@cyber.com.au>.
 * 
 * allocPSA is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 * 
 * allocPSA is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * allocPSA; if not, write to the Free Software Foundation, Inc., 51 Franklin
 * St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 */

require_once("../alloc.php");


$page_vars = array("projectID"
                  ,"taskStatus"
                  ,"taskTypeID"
                  ,"personID"
                  ,"taskView"
                  ,"projectType"
                  ,"applyFilter"
                  ,"showDescription"
                  ,"showDates"
                  ,"showCreator"
                  ,"showAssigned"
                  ,"showTimes"
                  ,"showPercent"
                  ,"showPriority"
                  ,"showStatus"
                  ,"showTaskID"
                  );

$_FORM = get_all_form_data($page_vars);

// If have check Show Description checkbox then display the Long Description and the Comments
if ($_FORM["showDescription"]) {
  $_FORM["showComments"] = true;
} else {
  unset($_FORM["showComments"]);
}

if (!$_FORM["applyFilter"]) {
  $_FORM = $current_user->prefs["user_FORM"];
} else if ($_FORM["applyFilter"]) {
  is_object($current_user) and $current_user->prefs["user_FORM"] = $_FORM;
}

$db = new db_alloc;

$_FORM["showHeader"] = true;
$_FORM["showProject"] = true;
$_FORM["padding"] = 1;

// Get task list
$TPL["task_summary"] = task::get_task_list($_FORM);

// Load up the filter bits
$TPL["projectOptions"] = project::get_project_list_dropdown($_FORM["projectType"],$_FORM["projectID"]);

$_FORM["projectType"] or $_FORM["projectType"] = "mine";
$_FORM["projectType"] and $TPL["projectType_checked_".$_FORM["projectType"]] = " checked"; 

$TPL["personOptions"] = "\n<option value=\"\"> ";
$TPL["personOptions"].= get_select_options(person::get_username_list($_FORM["personID"]), $_FORM["personID"]);

$taskType = new taskType;
$TPL["taskTypeOptions"] = "\n<option value=\"\"> ";
$TPL["taskTypeOptions"].= $taskType->get_dropdown_options("taskTypeID","taskTypeName",$_FORM["taskTypeID"]);


$_FORM["taskView"] or $_FORM["taskView"] = "byProject";
$_FORM["taskView"] and $TPL["taskView_checked_".$_FORM["taskView"]] = " checked";

$taskStatii = task::get_task_statii_array();
$TPL["taskStatusOptions"] = get_options_from_array($taskStatii, $_FORM["taskStatus"]);

$_FORM["showDescription"] and $TPL["showDescription_checked"] = " checked";
$_FORM["showDates"]       and $TPL["showDates_checked"]       = " checked";
$_FORM["showCreator"]     and $TPL["showCreator_checked"]     = " checked";
$_FORM["showAssigned"]    and $TPL["showAssigned_checked"]    = " checked";
$_FORM["showTimes"]       and $TPL["showTimes_checked"]       = " checked";
$_FORM["showPercent"]     and $TPL["showPercent_checked"]     = " checked";
$_FORM["showPriority"]    and $TPL["showPriority_checked"]    = " checked";
$_FORM["showStatus"]      and $TPL["showStatus_checked"]      = " checked";
$_FORM["showTaskID"]      and $TPL["showTaskID_checked"]      = " checked";



include_template("templates/taskSummaryM.tpl");
page_close();
?>
