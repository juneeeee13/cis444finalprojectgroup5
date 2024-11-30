<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    die("Access denied. Please log in first.");
}

// Retrieve session information
$user_id = $_SESSION['user_id']; //Grab the user_id from the saved session state.
$username = $_SESSION['username']; //Grab the username from the saved session state.
$isAdmin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : 0; //Grab the isAdmin from the saved session state.
?>
<!DOCTYPE html>
<html>

    <head>
        <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <meta charset="UTF-8">
        <link rel = "icon" type = "image/png" src = "../../esdeeimgs/browsericon.png">
        <link rel = "stylesheet" type = "text/css" href = "home.css">
        <script src="home.js"></script>
    </head>

    <h1>
        <img class="icon" src="../../esdeeimgs/esdeebrowsericon.png">
            eSDee     
        <img class="icon" src="../../esdeeimgs/pinkshell.png">
        <br>
        <img class="headerimage" src="../../esdeeimgs/waves.png">
    </h1>
    <div class="topnav">
        <a href="../home/home.html">home</a>
        <a href="../about/about.html">about us</a>
        <a href="../food/food.html">food</a>
        <a href="../events/events.html">events</a>
        <a href="../culture/culture.html">culture</a>
        <a href="../place/place.html">places</a>
        <a href="../settings/settings.html">settings</a>
    </div>
    <body>
        
        <div class="container">
            <div class ="column left">
                <p class= "leftcolumn">
                    <div class="card left">
                        <a href="../food/food.html">
                            <img class= "postimages" src="../../esdeeimgs/foodex.jpg">
                            food
                        </a>
                    </div>
                    <br>
                    <div class="card left">
                        <a href="../events/events.html">
                            <img class= "postimages" src="../../esdeeimgs/eventsex.jpg">
                            events
                        </a>
                    </div>
                </p>
            </div>
                
            <div class ="column middle">
                <h1  style="font-size: 25px"> new posts for you... </h1>

                <p class="middlecolumn">
                    <div class="card middle">
                        <img class="pfp" src="../../esdeeimgs/girlpfp.jpg">
                        <br>
                        <br>
                        <div class="username">
                            @user1 posted to culture :
                        </div>
                        <br>
                        <img class= "postimages" src="../../esdeeimgs/summernails.jpg">
                        <br>
                        <br>
                        new summer nails &#9829;
                        i love local nail salons !
                        <br>
                        <br>
                        <div class="hashtag">
                            #nails #demure #summer #beach #itgirl #tropical #aesthetic
                        </div>
                        <br>
                        <div class="poststats">
                            &#9829; 50 likes
                            <br>
                            >> 10 comments
                        </div>
                    </div>

                    <div class="card middle">
                        <img class="pfp" src="../../esdeeimgs/girlpfp2.jpg">
                        <br>
                        <br>
                        <div class="username">
                            @user2 posted to food :
                        </div>
                        <br>
                        <img class= "postimages" src="../../esdeeimgs/avocadotoast.jpg">
                        <br>
                        <br>
                        avocado toast is the perfect brunch meal !
                        <br>
                        <br>
                        <div class="hashtag">
                            #food #foodie #aesthetic #avocado #summerloving
                        </div>
                        <br>
                        <div class="poststats">
                            &#9829; 100 likes
                            <br>
                            >> 20 comments
                        </div>
                    </div>

                    <div class="card middle">
                        <img class="pfp" src="../../esdeeimgs/girlpfp3.jpg">
                        <br>
                        <br>
                        <div class="username">
                            @user3 posted to events :
                        </div>
                        <br>
                        <img class= "postimages" src="../../esdeeimgs/yoga.jpg">
                        <br>
                        <br>
                        just finished a yoga class! feel the burn!
                        <br>
                        Thursdays : 7am-8am
                        <br>
                        @ LA Fitness 4S Ranch
                        <br>
                        Instructor: 
                        <br>
                        <br>
                        <div class="hashtag">
                            #wellness #health #healthandwellness #yoga #pink #burn
                        </div>
                        <br>
                        <div class="poststats">
                            &#9829; 258490 likes
                            <br>
                            >> 1000 comments
                        </div>
                    </div>
                </p>
            </div>

            <div class ="column right">
                <p class= "rightcolumn">
                    <div class="card right">
                        <a href="../culture/culture.html">
                            <img class= "postimages" src="../../esdeeimgs/cultureex.jpg">
                            culture
                        </a>
                    </div>
                    <br>
                    <div class="card right">
                        <a href="../place/place.html">
                            <img class= "postimages" src="../../esdeeimgs/placesex.jpg">
                            place
                        </a>
                    </div>
                </p>
            </div>
        </div>

        <br>
    </body>

    <footer>
        <img class="footerimage" src="../../esdeeimgs/esdeefooter.png">
    </footer>
</html>