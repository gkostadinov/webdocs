<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Webdocs - anonymous collaborative document editing</title>
        <meta name="description" content="Anonymous collaborative document editing">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="http://fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/base.css">

        <link rel="stylesheet" href="css/vendor/trix.css">

        <link rel="stylesheet" href="css/app.css">
    </head>
    <body>
        <header>
            <div class="container">
                <div class="row">
                    <h2 class="title">Webdocs <sup>beta</sup></h2>
                    <h5 class="subtitle">
                        anonymous collaborative document editing
                    </h5>
                </div>
            </div>
        </header>

        <section class="content">
            <div class="container">
                 <section class="box">
                    <div class="row box-title">
                        <div class="box-title-text">
                            <h4>Untitled</h4>
                        </div>
                        <div class="box-title-button">
                            <a href="#" class="button" title="Share for viewing or editing">Share</a>
                        </div>
                    </div>
                    <div class="row box-content small-padding round-bottom">
                        <trix-editor class="trix-content" placeholder="Start writing something..."></trix-editor>
                    </div>
                </section>
            </div>
        </section>

        <script async src="js/vendor/trix.js"></script>
        <script async src="js/base.js"></script>
    </body>
</html>
