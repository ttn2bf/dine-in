<?php

spl_autoload_register(function($classname) {
    include "classes/$classname.php";
});

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(Config::$db["host"], Config::$db["user"], Config::$db["pass"], Config::$db["database"]);

$db->query("drop table if exists ProjectUser;");
$db->query("create table ProjectUser (
                id int not null auto_increment,
                name text not null,
                email text not null,
                password text not null,
                likes varchar(255) not null,
                primary key (id)
            );");

$db->query("drop table if exists ProjectRecipe;");
$db->query("create table ProjectRecipe (
                id int not null auto_increment,
                user int not null,
                title text not null,
                description text not null,
                ingredients text not null,
                steps text not null,
                tags text not null,
                prep int not null,
                cook int not null,
                image longtext not null,
                caption text not null,
                private int default 0,
                primary key (id)
            );");