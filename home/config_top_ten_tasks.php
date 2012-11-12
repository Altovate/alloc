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

require_once("../alloc.php");

$defaults = array("showHeader"=>true
                 ,"showTaskID"=>true
                 ,"taskView" => "prioritised"
                 ,"showStatus" => "true"
                 ,"url_form_action"=>$TPL["url_alloc_home"]
                 ,"form_name"=>"taskListHome_filter"
                 );

$_FORM = task::load_form_data($defaults);
$arr = task::load_task_filter($_FORM);
is_array($arr) and $TPL = array_merge($TPL,$arr);
$TPL["showCancel"] = true;
include_template("../task/templates/taskFilterS.tpl");

?>