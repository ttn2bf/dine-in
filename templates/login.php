<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">  

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Katherine Knipmeyer (kak9gsz), Tiffany Nguyen (ttn2bf)">
        <meta name="description" content="CS 4640: Project Login Page">  

        <title>DineIN: Login</title>

        <link rel="stylesheet" href="styles/main.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous"> 
    </head>

    <body style="background-color: #F1E4E3">
        <div class="container" style="margin-top: 15px;">
            <div class="row col-xs-8 text-center">
                <h1 style="color:#733E3C">Welcome to DineIN.</h1>
                <p> Please enter your email address, username, and password to get started.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-4">
                <form action="?command=login" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label" style="color:#733E3C">Email</label>
                        <input type="email" class="form-control" id="email" name="email"/>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label" style="color:#733E3C">Username</label>
                        <input type="text" class="form-control" id="name" name="name"/>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label" style="color:#733E3C">Password</label>
                        <input type="password" class="form-control" id="password" name="password"/>
                    </div>
                    <div class="text-center mb-3">                
                        <button id="submit" type="submit" class="btn btn-outline-dark" style="background: #733E3C; color:white">GO</button>
                    </div>
                    <div id="emailError"></div>
                    <div id="nameError"></div>
                    <div id="pwError"></div>
                </form>
                
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>

        <script type="text/javascript">
            const submit = document.getElementById("submit");

            submit.addEventListener("click", validate);

            function validate(e) {

                const emailField = document.getElementById("email");
                const nameField = document.getElementById("name");
                const pwField = document.getElementById("password");
                const emailError = document.getElementById("emailError");
                const nameError = document.getElementById("nameError");
                const pwError = document.getElementById("pwError");

                let valid = true;

                if (!emailField.value) {
                    e.preventDefault();
                    emailError.innerHTML = "<div class='alert alert-danger col-11' style='text-align:center; margin-left: auto; margin-right:auto'><b>Error!</b> Please enter an email!</div>";
                } else {
                    emailError.innerHTML = "";
                } if (!nameField.value) {
                    e.preventDefault();
                    nameError.innerHTML = "<div class='alert alert-danger col-11' style='text-align:center; margin-left: auto; margin-right:auto'><b>Error!</b> Please enter a name!</div>";
                } else {
                    nameError.innerHTML = "";
                } if (!pwField.value) {
                    e.preventDefault();
                    pwError.innerHTML = "<div class='alert alert-danger col-11' style='text-align:center; margin-left: auto; margin-right:auto'><b>Error!</b> Please enter a password!</div>";
                } else {
                    pwError.innerHTML = "";
                }
                return valid;
            }
        </script>
    </body>
</html>