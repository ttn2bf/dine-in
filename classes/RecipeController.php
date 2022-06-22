<?php
// Katherine Knipmeyer (kak9gsz), Tiffany Nguyen (ttn2bf)

class RecipeController {
    private $command;

    private $db;

    public function __construct($command) {
        $this->command = $command;
        $this->db = new Database();
    }

    public function run() {
        switch($this->command) {
            case "dashboard":
                $this->dashboard();
                break;
            case "collections":
                $this->collections();
                break;
            case "postrecipe":
                $this->postrecipe();
                break;
            case "view":
                $this->view();
                break;
            case "logout":
                session_destroy();
            case "login":
            default:
                $this->login();
        }
    }

    private function login() {
        $errormessage = "";

        if (isset($_POST["email"])) {
            $data = $this->db->query("select * from ProjectUser where email = ?;", "s", $_POST["email"]);

            if ($data === false) {
                $errormessage = "<div class='alert alert-danger'>Error checking for user.</div>";
            } else if (!empty($data)) {
                if (password_verify($_POST["password"], $data[0]["password"])) {
                    $_SESSION["id"] = $data[0]["id"];
                    $_SESSION["name"] = $data[0]["name"];
                    $_SESSION["email"] = $data[0]["email"];
                    $_SESSION["viewed"] = 0;

                    header("Location: ?command=dashboard");
                } else {
                    $errormessage = "<div class='alert alert-danger'>Incorrect password. Try again!</div>";
                }
            } else { // Create a new account.
                if (empty($_POST["email"]) || !$this->validateEmail($_POST["email"])) {
                    $errormessage = "<div class='alert alert-danger'>Please enter a valid email address.</div>";
                }
                else if (empty($_POST["name"])) {
                    $errormessage = "<div class='alert alert-danger'>Please enter a username.</div>";
                }
                else if (empty($_POST["password"])) {
                    $errormessage = "<div class='alert alert-danger'>Please enter a password.</div>";
                }

                // After input validation, add the user to the database.
                if ($errormessage == "") {
                    $likes = serialize(array());
                    $insert = $this->db->query("insert into ProjectUser (name, email, password, likes) values (?, ?, ?, ?);", 
                        "ssss", $_POST["name"], $_POST["email"], password_hash($_POST["password"], PASSWORD_DEFAULT), $likes);
                    if ($insert === false) {
                        $errormessage = "<div class='alert alert-danger'>Error creating account.</div>";
                    } else {
                        $data = $this->db->query("select * from ProjectUser where email = ?;", "s", $_POST["email"]);

                        $_SESSION["id"] = $data[0]["id"];
                        $_SESSION["name"] = $data[0]["name"];
                        $_SESSION["email"] = $data[0]["email"];
                        $_SESSION["viewed"] = 0;

                        header("Location: ?command=dashboard");
                    }
                }
            }
        }
        include("templates/login.php");
    }

    private function validateEmail($email, $reg = false) // Copied from my HW3 submission.
    {
        $validator = '/^[^.][A-Za-z.0-9-_+]+[^.]@[A-Za-z0-9][A-Za-z0-9-.]*.[A-Za-z0-9]+$/';
        if ($reg) {
            if (preg_match($reg, $email) && preg_match($validator, $email)) {
                return true;
            }
            return false;
        }

        if (preg_match($validator, $email)) {
            return true;
        }
        return false;
    }

    private function dashboard() {

        // If a user has opted to delete one of their recipes, remove it from the database.
        if (isset($_POST["delete"])) {
            $data = $this->db->query("delete from ProjectRecipe where id = ?;", "i", $_SESSION["viewed"]);
            $_SESSION["viewed"] = 0;
            unset($_POST["delete"]);
        }
        // Add a recipe to a user's likes.
        else if (isset($_POST["like"])) {
            $data = $this->db->query("select likes from ProjectUser where id = ?;", "i", $_SESSION["id"]);
            $likes = unserialize($data[0]["likes"]);
            $likes[] = $_SESSION["viewed"];
            $this->db->query("update ProjectUser set likes = ? where id = ?;", "si", serialize($likes), $_SESSION["id"]);
            unset($_POST["like"]);
        }
        // Remove a recipe from a user's likes.
        else if (isset($_POST["unlike"])) {
            $data = $this->db->query("select likes from ProjectUser where id = ?;", "i", $_SESSION["id"]);
            $likes = unserialize($data[0]["likes"]);
            $newlikes = array();
            foreach ($likes as $entry) {
                if ($_SESSION["viewed"] != $entry) {
                    $newlikes[] = $entry;
                }
            }
            $this->db->query("update ProjectUser set likes = ? where id = ?;", "si", serialize($newlikes), $_SESSION["id"]);
            unset($_POST["unlike"]);
        }

        $disc = "";
        $recs = "";
        $views = "";
        $disctags = "";

        // Fill out the "DISCOVER" section: suggest a random assortment of recipes written by other users.
        $data = $this->db->query("select * from ProjectRecipe where user != ? and private = 0 order by rand() limit 3;", "i", $_SESSION["id"]);
        if (!isset($data[0])) {
            $disc = "<p class='my-3 mx-5'>No recipes available to show.</p>";
        }
        else {
            foreach ($data as $card) {
                $disc .= "<div class='card flex-item'>
                            <img src='data:image/jpeg;base64," . $card["image"] . "' class='card-img-top' alt='" . $card["caption"] . "'>
                            <div class='card-body' style='justify-content: center'>
                            <h5 class='card-title'>" . $card["title"] . "</h5>
                            <p class='card-text'>
                                " . substr($card["description"], 0, 60) . "... ". "
                            </p>
                            <form action='?command=view' method='post'>
                                <button class='btn btn-outline-dark' name='view' type='submit' value='" . $card["id"] . "'>View Recipe</button>
                            </form>
                            </div>
                            </div>";
            }
        }
        unset($data);

        // Fill out the "RECOMMENDED" section: suggest other recipes written by the same person as the user's last liked recipe.
        $likedata = $this->db->query("select likes from ProjectUser where id = ?;", "i", $_SESSION["id"]);
        $likes = unserialize($likedata[0]["likes"]);
        $latest = end($likes);
        $latestuserdata = $this->db->query("select user from ProjectRecipe where id = ? and private = 0;", "i", $latest);
        if (isset($latestuserdata[0])) {
            $latestuser = $latestuserdata[0]["user"];
        }
        else {
            $latestuser = 0;
        }

        $data = $this->db->query("select * from ProjectRecipe where id != ? and user = ? order by rand() limit 3;", "ii", $latest, $latestuser);
        if (!isset($data[0])) {
            $recs = "<p class='my-3 mx-5'>Like some recipes to see your recommendations.</p>";
        }
        else {
            foreach ($data as $card) {
                $recs .= "<div class='card flex-item'>
                            <img src='data:image/jpeg;base64," . $card["image"] . "' class='card-img-top' alt='" . $card["caption"] . "'>
                            <div class='card-body' style='justify-content: center'>
                            <h5 class='card-title'>" . $card["title"] . "</h5>
                            <p class='card-text'>
                                " . substr($card["description"], 0, 60) . "..." . "
                            </p>
                            <form action='?command=view' method='post'>
                                <button class='btn btn-outline-dark' name='view' type='submit' value='" . $card["id"] . "'>View Recipe</button>
                            </form>
                            </div>
                            </div>";
            }
        }
        unset($data);

        // Fill out the "RECENTLY VIEWED" section.
        $data = $this->db->query("select * from ProjectRecipe where id = ? limit 1;", "i", $_SESSION["viewed"]);
        if (!isset($data[0])) {
            $views = "<p class='my-3 mx-5'>No recently viewed recipes.</p>";
        }
        else {
            foreach ($data as $card) {
                $views .= "<div class='card flex-item' style='width:450px'>
                            <img src='data:image/jpeg;base64," . $card["image"] . "' class='card-img-top' alt='" . $card["caption"] . "' style='height:80px'>
                            <div class='card-body' style='justify-content: center'>
                            <h5 class='card-title'>" . $card["title"] . "</h5>
                            <p class='card-text'>
                                " . substr($card["description"], 0, 60) . "..." . "
                            </p>
                            <form action='?command=view' method='post'>
                                <button class='btn btn-outline-dark' name='view' type='submit' value='" . $card["id"] . "'>View Recipe</button>
                            </form>
                            </div>
                            </div>";
            }
        }
        unset($data);

        // Fill out the "DISCOVER TAGS" section. TODO: implement search by tag.
        $data = $this->db->query("select tags from ProjectRecipe where user != ? and private = 0 order by rand() limit 3;", "i", $_SESSION["id"]);
        $usedTags = array();
        if (!isset($data[0])) {
            $disctags = "<p class='my-3 mx-5'>No tags available to show.</p>";
        }
        else {
            foreach ($data as $card) {
                $tagarray = json_decode($card["tags"]);
                foreach ($tagarray as $tag) {
                    if (!in_array($tag, $usedTags)) {
                        $disctags .= "<button type='button' class='btn btn-tag'>" . $tag . "</button>";
                        $usedTags[] = $tag;
                    }
                }
            }
        }
        unset($data);
        unset($usedTags);

        include("templates/dashboard.php");
    }

    private function collections() {
        $mine = "";
        $liked = "";

        // Display all of the recipes that a user has posted.
        $data = $this->db->query("select * from ProjectRecipe where user = ?;", "i", $_SESSION["id"]);
        if (!isset($data[0])) {
            $mine = "<p class='my-3 mx-5'>Recipes you post will appear here.</p>";
        }
        else {
            foreach ($data as $card) {
                $mine .= "<div class='card flex-item'>
                            <img src='data:image/jpeg;base64," . $card["image"] . "' class='card-img-top' alt='" . $card["caption"] . "'>
                            <div class='card-body' style='justify-content: center'>
                            <h5 class='card-title'>" . $card["title"] . "</h5>
                            <p class='card-text'>
                                " . substr($card["description"], 0, 60) . "... ". "
                            </p>
                            <form action='?command=view' method='post'>
                                <button class='btn btn-outline-dark' name='view' type='submit' value='" . $card["id"] . "'>View Recipe</button>
                            </form>
                            </div>
                            </div>";
            }
        }
        unset($data);

        // Display all of the recipes that a user has liked.
        $likedata = $this->db->query("select likes from ProjectUser where id = ?;", "i", $_SESSION["id"]);
        $likes = unserialize($likedata[0]["likes"]);
        foreach ($likes as $rec) {
            $data = $this->db->query("select * from ProjectRecipe where id = ?;", "i", $rec);
            if (isset($data[0])) {
                $liked .= "<div class='card flex-item'>
                        <img src='data:image/jpeg;base64," . $data[0]["image"] . "' class='card-img-top' alt='" . $data[0]["caption"] . "'>
                        <div class='card-body' style='justify-content: center'>
                        <h5 class='card-title'>" . $data[0]["title"] . "</h5>
                        <p class='card-text'>
                            " . substr($data[0]["description"], 0, 60) . "... ". "
                        </p>
                        <form action='?command=view' method='post'>
                            <button class='btn btn-outline-dark' name='view' type='submit' value='" . $data[0]["id"] . "'>View Recipe</button>
                        </form>
                        </div>
                        </div>";
            }
        }
        if(empty($liked)) {
            $liked = "<p class='my-3 mx-5'>No liked recipes to display.</p>";
        }
        unset($data);

        include("templates/collections.php");
    }

    private function postrecipe() {
        $errormessage = "";

        // Input validation!
        if(isset($_POST["recipeTitle"])) {
            if (empty($_POST["recipeTitle"])) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please enter a recipe name.</div>";
            }
            else if(empty($_POST["description"])) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please enter a description for your recipe.</div>";
            }
            else if(strlen($_POST["description"]) > 300) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please enter a shorter description for your recipe.</div>";
            }
            else if(strlen($_POST["description"]) < 50) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please enter a longer description for your recipe.</div>";
            }
            else if (empty($_POST["ingredients"])) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please enter a comma-separated list of ingredients.</div>";
            }
            else if (!$this->validateIngredients($_POST["ingredients"])) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Invalid ingredient list.</div>";
            }
            else if(empty($_POST["steps"])) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please enter some steps for your recipe.</div>";
            }
            else if(empty($_POST["tags"])) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please enter at least one tag.</div>";
            }
            else if(!$this->validateTags(strtolower($_POST["tags"]))) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Invalid tag list.</div>";
            }
            else if($_POST["prepTime"] == "" || $_POST["prepTime"] < 0) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please enter a valid prep time.</div>";
            }
            else if($_POST["cookTime"] == "" || $_POST["cookTime"] < 0) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please enter a valid cook time.</div>";
            }
            else if(empty($_FILES["img"]["tmp_name"])){
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please select an image to upload.</div>";
            }
            else if(empty($_POST["caption"])) {
                $errormessage = "<div class='alert alert-secondary'><b>Oops!</b> Please enter a short image caption.</div>";
            }

            // Post recipe.
            if ($errormessage == "") {
                if (isset($_POST["private"])) {
                    $private = 1;
                }
                else {
                    $private = 0;
                }

                // Get the image data and store it as hex.
                $imghex = $this->prepImg($_FILES["img"]["tmp_name"]);

                // Get the tags and store them in a JSON array.
                $tagarray = explode(", ", strtolower($_POST["tags"]));
                $tags = "[";
                foreach ($tagarray as $tg) {
                    $tags .= "\"" . $tg . "\", ";
                }
                $tags = substr($tags,0,-2) . "]";

                // Add the recipe to the database.
                $insert = $this->db->query("insert into ProjectRecipe (user, title, description, ingredients, steps, tags, prep, cook, image, caption, private) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);", 
                        "isssssiissi", $_SESSION["id"], $_POST["recipeTitle"], $_POST["description"], $_POST["ingredients"], $_POST["steps"], $tags, $_POST["prepTime"], $_POST["cookTime"], $imghex, $_POST["caption"], $private);
                if ($insert === false) {
                    $errormessage = "<div class='alert alert-secondary'>Unable to post recipe.</div>";
                }

                // Set the user's new recipe as their "RECENTLY VIEWED" and then go back to the dashboard.
                $max = $this->db->query("select max(id) from ProjectRecipe;");
                $_SESSION["viewed"] = $max[0]["max(id)"];
                header("Location: ?command=dashboard");
            }
        }
        include("templates/postrecipe.php");
    }

    private function validateTags($taglist) {
        $validator = '/^#[a-z]+(, #[a-z]+)*$/';

        if (preg_match($validator, $taglist)) {
            return true;
        }
        return false;
    }

    private function validateIngredients($ingredlist) {
        $validator = '/^[ .0-9A-Za-z]+(, [ .0-9A-Za-z]+)*$/';

        if (preg_match($validator, $ingredlist)) {
            return true;
        }
        return false;
    }

    private function prepImg($filepath) {
        // Based on StackOverflow posts cited in sources.
        $content = 'null';
        $file = fopen($filepath, 'rb');
        if ($file) {
            $content = fread($file, filesize($filepath));
            $content = base64_encode($content);
        }
        return $content;
    }

    private function view() {
        // Update the user's "RECENTLY VIEWED" display.
        $_SESSION["viewed"] = $_POST["view"];

        // Get recipe data.
        $data = $this->db->query("select * from ProjectRecipe where id = ?;", "i", $_SESSION["viewed"]);
        $recipe = $data[0];

        // Format ingredients.
        $ingredarray = explode(", ", $recipe["ingredients"]);
        $ingredients = "";
        foreach ($ingredarray as $in) {
            $ingredients .= $in . nl2br("\n");
        }

        // Query that returns a JSON object:
        // $jsonquery = $this->db->query("select json_object('id', id, 'user', user, 'title', title) as obj from ProjectRecipe where id = ?;", "i", $_SESSION["viewed"]);
        // $json = $jsonquery[0]["obj"];

        // Format tags.
        $tags = json_decode($recipe["tags"]);
        $tagbuttons = "";
        foreach ($tags as $tag) {
            $tagbuttons .= "<button type='button' class='btn btn-tag'>" . $tag . "</button>";
        }

        // Get poster data.
        $interact = "";
        $data = $this->db->query("select * from ProjectUser where id = ?;", "i", $recipe["user"]);
        $user = $data[0];

        // Get user like data.
        $likedata = $this->db->query("select likes from ProjectUser where id = ?;", "i", $_SESSION["id"]);
        $likes = unserialize($likedata[0]["likes"]);

        // Allow user to delete their own recipes...
        if ($user["id"] === $_SESSION["id"]) {
            $interact  = "<form action='?command=dashboard' method='post'>
                            <button class='btn btn-outline-dark m-3' name='delete' type='submit' value='true' style='border: solid 2px black; background-color: #F1E4E3'>Delete Recipe</button>
                        </form>";
        }
        else if (!in_array($_SESSION["viewed"], $likes)) { // and like others' recipes...
            $interact  = "<form action='?command=dashboard' method='post'>
                            <button class='btn btn-outline-dark m-3' name='like' type='submit' value='true' style='border: solid 2px black; background-color: #F1E4E3'>Like Recipe</button>
                        </form>";
        }
        else { // and unlike them, too.
            $interact  = "<form action='?command=dashboard' method='post'>
                            <button class='btn btn-outline-dark m-3' name='unlike' type='submit' value='true' style='border: solid 2px black; background-color: #F1E4E3'>Unlike Recipe</button>
                        </form>";
        }

        include("templates/view.php");
    }
}