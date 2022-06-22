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
         <title>DineIN: My Recipes</title>    
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
                        <a class="nav-link active" href="?command=collections">Collections</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="?command=postrecipe" style="background-color: #F1E4E3; border-radius: 25px;">Post Recipe</a>
                        </li>
                    </ul>
                    </div>
                    <a class="btn btn-outline-dark me-2" style="background-color: #F1E4E3" href="?command=logout">Log Out</a>
                </div>
                </nav>
        </header>

        <div class="postrecipecontainer">
            <?=$errormessage?>
            <div id="titleError"></div>
            <form action="?command=postrecipe" method="post" enctype="multipart/form-data">
                <div class="mb-3 form-row">
                    <label for="recipeTitle" class="form-label" style="font-size: 120%">Recipe Title</label>
                    <input type="text" class="form-control form-control-lg" name="recipeTitle" id="recipeTitle">
                </div>
                <div class="mb-3 form-row">
                    <label for="description" class="form-label">Describe your recipe (50-300 characters).</label>
                    <input type="text" class="form-control" name="description" id="description">
                </div>
                <div class="mb-3 form-row">
                    <label for="ingredients" class="form-label">Enter a comma-separated list of ingredients (e.g. 2 eggs, 1 cup flour, 1 cup sugar).</label>
                    <input type="text" class="form-control" name="ingredients" id="ingredients">
                </div>
                <div class="mb-3 form-row">
                    <label for="steps" class="form-label">Give step-by-step instructions for your recipe.</label>
                    <textarea class="form-control" name="steps" id="steps"></textarea>
                </div>

                <div class="mb-5 form-row">
                    <label for="tags" class="form-label">Enter a comma-separated list of tags (e.g. #salad, #kale, #vinaigrette).</label>
                    <input type="text" class="form-control" name="tags" id="tags">
                </div>

                <div class="form-row">
                    <div class="mb-3 col-2">
                        <label for="prepTime" class="form-label">Prep Time, in minutes.</label>
                        <input type="number" class="form-control" name="prepTime" id="prepTime">
                    </div>
                    <div class="mb-5 col-2">
                        <label for="cookTime" class="form-label">Cook Time, in minutes.</label>
                        <input type="number" class="form-control" name="cookTime" id="cookTime">
                    </div>
                </div>

                <div class="form-row mt-3">
                    <label for="img" class="form-label">Select an image (.jpeg) for your recipe.</label>
                    <input type="file" id="img" name="img" accept="image/jpeg">
                </div>
                <div class="mt-3 mb-4 form-row">
                    <label for="caption" class="form-label">Enter a short caption (1-2 words) for your image.</label>
                    <input type="text" class="form-control form-control-sm col-4" name="caption" id="caption">
                </div>
                <div class="mx-2 mt-5 mb-2 form-check">
                    <input type="checkbox" class="form-check-input" name="private" id="private">
                    <label class="form-check-label" for="private">Private</label>
                </div>
                <button id="submit" type="submit" class="btn btn-outline-dark mb-3" style="background:#F1E4E3;">POST RECIPE</button>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script type="text/javascript">
            const submit = document.getElementById("submit");

            submit.addEventListener("click", validate);

            function validate(e) {

                const recipeTitle = document.getElementById("recipeTitle").value;
                console.log(recipeTitle)
                const titleError = document.getElementById("titleError");
                const matched = recipeTitle.match(/^([a-zA-Z0-9 _-]+)$/)
                console.log(matched)

                let valid = true;

                if (!matched) {
                    e.preventDefault();
                    titleError.innerHTML = "<div class='alert alert-secondary'><b>Invalid recipe title!</b> Please enter a recipe name using only characters and/or numbers.</div>";
                } else {
                    titleError.innerHTML = "";
                }
                return valid;
            }
        </script>
     </body>
 </html>