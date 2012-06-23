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

class saleListHomeItemAdmin extends home_item {

  function saleListHomeItemAdmin() {
    home_item::home_item("sale_list_admin", "Sales Pending Admin", "sale", "saleListHomeM.tpl", "narrow", 38);
  }

  function visible() {
    global $current_user;
    return isset($current_user) && $current_user->have_role("admin");
  }

  function render() {
    global $current_user;
    global $TPL;
    $ops["return"] = "array";
    $ops["status"] = array("admin");
    $rows = productSale::get_list($ops);
    $TPL["saleListRows"] = $rows;
    if ($TPL["saleListRows"]) {
      return true;
    }
  }
}

?>
