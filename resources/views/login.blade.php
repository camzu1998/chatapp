<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>Czatap</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="icon" type="image/png" sizes="192x192"  href="/storage/images/android-icon-192x192.png">

        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet">
        
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&family=Rampart+One&display=swap" rel="stylesheet">  
        
        <script src="https://kit.fontawesome.com/309a8b3aa5.js" crossorigin="anonymous"></script>
        <script>
            navigator.serviceWorker.register('/sw.js').catch(e=>console.error('Ups!' + e))
        </script>
        <link rel="manifest" href="/manifest.json">
    </head>
    <body class="antialiased relative text-center w-full h-screen">
        <div class="app-logo w-full text-center text-white text-7xl mt-8">
            Czatap
        </div>

        <div class="w-screen mt-12 relative flex flex-col justify-center content-center items-center">
            <form class="flex flex-col items-center text-gray-800 py-4 startForm rounded-lg px-2" action="/login" method="POST">
                <div class="w-full text-center text-lg form-text mb-8 mt-4">Zaloguj się</div>
                @csrf
                <input class="form-input mb-4 block w-9/12 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="email" name="email" id="email" placeholder="Email" onchange="this.setAttribute('value', this.value);"/>
                <input class="form-input mb-4 block w-9/12 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="password" name="password" id="password" placeholder="Hasło" onchange="this.setAttribute('value', this.value);"/>
                <button class="w-36 cta-btn text-gray-800 my-4 px-4 py-2 rounded-md" id="send" type="submit">Wyślij <i class="far fa-paper-plane"></i></button>
                <p class="form-text text-base mt-8 mb-4">Nie masz konta? </br> <a href="/register_form" class="text-blue-400">Zarejestruj się!</a> </p>
            </form>
        </div>
    </body>
</html>