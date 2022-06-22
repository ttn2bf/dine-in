<?php
// Katherine Knipmeyer (kak9gsz), Tiffany Nguyen (ttn2bf)

spl_autoload_register(function($classname) {
    include "classes/$classname.php";
});

$db = new Database();
$tag = $_GET["query"];

$allresults = array();
$result = array();

if ($_GET["domain"] === "all") {
    $data = $db->query("select * from ProjectRecipe where private = 0 order by rand();");
}
else {
    $likesarray = array();
    $likes = $db->query("select likes from ProjectUser where id = ?;", "i", $_GET["domain"]);
    $likes = unserialize($likes[0]["likes"]);
    foreach ($likes as $rec) {
        $data = $db->query("select * from ProjectRecipe where id = ?;", "i", $rec);
        if (isset($data[0])) {
            $likesarray[] = $data[0];
        }
    }

    $data = $likesarray;

    $mine = $db->query("select * from ProjectRecipe where user = ?;", "i", $_GET["domain"]);
    for ($i = 0; $i < sizeof($mine); $i++) {
        $data[] = $mine[$i];
    }
}

if (isset($data[0])) {
    foreach ($data as $recipe) {
        $tagarray = json_decode($recipe["tags"]);
        if (in_array($tag, $tagarray)) {
            $result["resImage"] = $recipe["image"];
            $result["resCaption"] = $recipe["caption"];
            $result["resTitle"] = $recipe["title"];
            $result["resDescription"] = substr($recipe["description"], 0, 60);
            $result["resID"] = $recipe["id"];
            $allresults[] = $result;
            $result = array();
        }
    }
}

echo json_encode($allresults);