<?php

/**
 * ---------------------------------------------------------------------
 * GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2015-2022 Teclib' and contributors.
 *
 * http://glpi-project.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */

use Glpi\Event;

include('../inc/includes.php');

Session::checkRight('database', READ);

if (empty($_GET["id"])) {
    $_GET["id"] = "";
}
if (!isset($_GET['databaseinstances_id'])) {
    $_GET['databaseinstances_id'] = '';
}

$database = new Database();

if (isset($_POST["add"])) {
    $database->check(-1, CREATE, $_POST);

    if ($newID = $database->add($_POST)) {
        Event::log(
            $newID,
            "database",
            4,
            "management",
            sprintf(__('%1$s adds the item %2$s'), $_SESSION["glpiname"], $_POST["name"])
        );
        if ($_SESSION['glpibackcreated']) {
            Html::redirect($database->getLinkURL());
        }
    }
    Html::back();
} else if (isset($_POST["delete"])) {
    $database->check($_POST["id"], DELETE);
    $database->delete($_POST);

    Event::log(
        $_POST["id"],
        "database",
        4,
        "management",
        //TRANS: %s is the user login
        sprintf(__('%s deletes an item'), $_SESSION["glpiname"])
    );
    $database->redirectToList();
} else if (isset($_POST["restore"])) {
    $database->check($_POST["id"], DELETE);

    $database->restore($_POST);
    Event::log(
        $_POST["id"],
        "database",
        4,
        "management",
        //TRANS: %s is the user login
        sprintf(__('%s restores an item'), $_SESSION["glpiname"])
    );
    $database->redirectToList();
} else if (isset($_POST["purge"])) {
    $database->check($_POST["id"], PURGE);

    $database->delete($_POST, 1);
    Event::log(
        $_POST["id"],
        "database",
        4,
        "management",
        //TRANS: %s is the user login
        sprintf(__('%s purges an item'), $_SESSION["glpiname"])
    );
    $database->redirectToList();
} else if (isset($_POST["update"])) {
    $database->check($_POST["id"], UPDATE);

    $database->update($_POST);
    Event::log(
        $_POST["id"],
        "database",
        4,
        "management",
        //TRANS: %s is the user login
        sprintf(__('%s updates an item'), $_SESSION["glpiname"])
    );
    Html::back();
} else {
    Html::header(Database::getTypeName(Session::getPluralNumber()), $_SERVER['PHP_SELF'], "management", "database");
    $options = [
        'id'           => $_GET['id'],
        'databaseinstances_id' => $_GET['databaseinstances_id']
    ];
    $database->display($options);
    Html::footer();
}