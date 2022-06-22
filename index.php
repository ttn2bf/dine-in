<?php
// Katherine Knipmeyer (kak9gsz), Tiffany Nguyen (ttn2bf)
/* Sources used: CS 4640 Trivia Game, PHP documentation, https://stackoverflow.com/questions/689735/insert-binary-data-into-sql-server-using-php, https://stackoverflow.com/questions/2429934/is-it-possible-to-put-binary-image-data-into-html-markup-and-then-get-the-image,
    https://www.w3schools.com/tags/att_input_type_hidden.asp, https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Functions/Arrow_functions */
// Published at https://cs4640.cs.virginia.edu/kak9gsz/project/

session_start();

spl_autoload_register(function($classname) {
    include "classes/$classname.php";
});

$command = "login";
if (isset($_GET["command"])) {
    $command = $_GET["command"];
}

if (!isset($_SESSION["email"])) {
    $command = "login";
}

// Instantiate the controller and run.
$controller = new RecipeController($command);
$controller->run();