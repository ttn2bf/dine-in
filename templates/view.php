<!DOCTYPE html>
 <html lang="en">
     <head>
         <meta charset="utf-8">
         <meta http-equiv="X-UA-Compatible" content="IE=edge">
         <meta name="viewport" content="width=device-width, initial-scale=1"> 

         <meta name="author" content="Tiffany Nguyen, Katie Knipmeyer">
         <meta name="description" content="A digital cookbook for storing and sharing recipes.">
         <meta name="keywords" content="recipe, organization, digital, virtual, cookbook">     
         <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" 
            integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> 
         <link rel="stylesheet" href="styles/main.css">
         <title>DineIN: Collections</title>    
     </head>  
     <body style="background-color: #F1E4E3">
        <header>
            <h1 class="logo">DineIN</h1>
            <p class="mb-3"><b>Hello, <?=$_SESSION["name"]?> (<?=$_SESSION["email"]?>)!</b></p>

                <nav class="mynav navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                        <a class="nav-link active" href="?command=dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="?command=collections">Collections</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link active" href="?command=postrecipe">Post Recipe</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link active" href="?command=view" style="background-color: #F1E4E3; border-radius: 25px;">Recipe View</a>
                        </li>
                    </ul>
                    <a class="btn btn-outline-dark me-2" style="background-color: #F1E4E3" href="?command=logout">Log Out</a>
                    </div>
                </div>
                </nav>
        </header>

        <div class="container-fluid col-11 text-center p-4" id="searchResults" style="margin-left: auto; margin-right: auto;">
            <div class="d-flex mb-3" id="searchHeader"></div>
            <h5 id="debug"></h5>
            <div id="searchCards" style="display: flex; flex-wrap: wrap; justify-content: center;"></div>
        </div>

        <div class="row" style="display: flex;flex-wrap:wrap; justify-content:center;">
            <div class="viewcontainer col-9"> 
                <div class="my-4">
                    <h2 class="mb-1"><?=$recipe["title"]?></h2>
                    <h5>Posted by <?=$user["name"]?></h5>
                </div>

                <img id="photo" src='data:image/jpeg;base64,<?=$recipe["image"]?>' class='card-img-top mb-3' alt='<?=$recipe["caption"]?>'>

                <h6><b>Description</b></h6>
                <div class="mx-3 mt-3 mb-4 viewsection">
                    <p class="extraspacing"><?=$recipe["description"]?></p>
                </div>

                <h6><b>What You'll Need</b></h6>
                <div class="m-3">
                    <p><?=$ingredients?></p>
                </div>

                <h6><b>What to Do</b></h6>
                <div class="my-4 mx-3">
                    <p class="extraspacing" style="white-space: pre-wrap;"><?=$recipe["steps"]?></p>
                </div>

                <h6><b>Tags</b></h6>
                <div id="tagdiv" class="my-3 mx-3">
                    <?=$tagbuttons?>
                </div>

                <?=$interact?>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

        <script>

            const photo = document.getElementById("photo");

            photo.addEventListener("mouseover", highlight);
            photo.addEventListener("mouseout", dehighlight);

            function highlight() {
                document.getElementById("photo").style.border = "solid 2px black";
            }
            function dehighlight() {
                document.getElementById("photo").style.removeProperty("border");
            }

            function search(tagtext) {
                // SPRINT 4: Use of JQuery.
                // SPRINT 4: Input validation of search terms.
                var query = tagtext;

                $("div#searchHeader").empty();
                $("div#searchCards").empty();
                $("div#searchResults p").remove();

                // Display search results.
                $("div#searchResults").addClass("dashcontainer");

                // SPRINT 4: AJAX query, anonymous function.
                $.get("search.php", { query: query, domain: "all" }, function(results) {
                    // SPRINT 4: Javascript object.
                    results = $.parseJSON(results);
                    if (results.length == 0) {
                        $("div#searchHeader").append("<h3><b>No results.</b></h3>");
                        $("div#searchHeader").append("<button class='btn button-outline-dark mx-3 mb-4' style='background-color: #F1E4E3; border: solid 1px black;' id='clearSearch'>CLEAR</button>");
                        $("div#searchResults").append("<p class='mb-3'>No recipes to display for search term <b>" + query + "</b>. Try another search?</p>");
                    }
                    else {
                        $("div#searchHeader").append("<h3><b>" + query + "</b></h3>");
                        $("div#searchHeader").append("<button class='btn button-outline-dark mx-3' style='background-color: #F1E4E3; border: solid 1px black;' id='clearSearch'>CLEAR</button>");
                        for (let i = 0; i < results.length; i++) {
                            $("div#searchCards").append(makeCard(results[i].resImage, results[i].resCaption, results[i].resTitle, results[i].resDescription, results[i].resID));
                        }
                    }
                });
            }

            function makeCard(resImage, resCaption, resTitle, resDescription, resID) {
                return "<div class='card flex-item'><img src='data:image/jpeg;base64," + resImage + "' class='card-img-top' alt='" + resCaption + "'><div class='card-body' style='justify-content: center'><h5 class='card-title'>" + resTitle + "</h5><p class='card-text'>" + resDescription + "..." + "</p><form action='?command=view' method='post'><button class='btn btn-outline-dark' name='view' type='submit' value='" + resID + "'>View Recipe</button></form></div></div>";
            }

            function clearSearch() {
                // Get rid of search result display.
                $("div#searchHeader").empty();
                $("div#searchCards").empty();
                $("div#searchResults p").remove();
                $("div#searchResults").removeClass("dashcontainer");

                // Reinstate the search button event listener.
                $("div#tagdiv").on("click", "button", tagSearch);
            }

            function tagSearch() {
                var tagtext = $(this).text();
                search(tagtext);
            }

            // SPRINT 4: Event listeners, anonymous function, arrow.
            $( document ).ready(() => {
                $("button#searchButton").click(search);
                $("div#searchResults").on("click", "button#clearSearch", function () {
                    clearSearch();
                    return false;
                });
                $("div#tagdiv").on("click", "button", tagSearch);
            });
        </script>
    </body>
 </html>