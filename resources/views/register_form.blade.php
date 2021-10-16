<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    
        <title>Czatap</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet">
        
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&family=Rampart+One&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/309a8b3aa5.js" crossorigin="anonymous"></script>>
    </head>
    <body class="antialiased relative text-center w-full h-screen">
        <div class="app-logo w-full text-center text-white text-7xl mt-8">
            Czatap
        </div>

        <div class="w-screen mt-12 relative flex flex-col justify-center content-center items-center">
            <form class="flex flex-col items-center text-gray-800 py-4 startForm rounded-lg px-2" action="/register" method="POST">
            <div class="w-full text-center text-lg form-text mb-8">Zarejestruj się</div>
                @csrf
                <input class="form-input my-2 mb-4 block w-9/12" type="text" name="nick" id="nick" placeholder="Nick" onchange="this.setAttribute('value', this.value);"/>
                <input class="form-input my-2 mb-4 block w-9/12" type="email" name="email" id="email" placeholder="Email" onchange="this.setAttribute('value', this.value);"/>
                <input class="form-input my-2 mb-4 block w-9/12" type="password" name="pass" id="pass" placeholder="Hasło"/>
                <input class="form-input my-2 mb-4 block w-9/12" type="password" name="pass_2" id="pass_2" placeholder="Powtórz hasło"/>
                <button class="w-36 cta-btn text-gray-800 my-4 px-4 py-2 rounded-md" id="send" type="submit">Załóż konto <i class="fas fa-plus"></i></button>
                <p class="form-text text-base mt-8">Wszedłeś tu przez przypadek? </br> <a href="/" class="text-blue-400">Wróć na strone główną.</a> </p>
            </form>
        </div>
    </body>
</html>
