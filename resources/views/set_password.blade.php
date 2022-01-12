<!DOCTYPE html>
<html>
    <head>
        <!--META Tags-->
        <meta charset="utf-8"/>
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="NOFOLLOW, NOINDEX">
        <!--Stylesheets-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link href="/css/login.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Advent+Pro:400,600|Encode+Sans:400,600" rel="stylesheet"/>
    </head>
    <body class="w-full h-screen relative">
        <form class="absolute login_container py-8 px-6 flex flex-col flex-wrap w-full text-center mx-auto" action="/reset/{{ $token }}" method="POST">
            @csrf
            <div class="logo_box mb-2 mx-auto">
                <img src="/img/logo.svg" class="hq_logo"/>
            </div>
            <input type="password" name="pass" placeholder="PASSWORD" class="login_input my-2 mx-auto "/>
            <input type="password" name="pass2" placeholder="REPEAT PASSWORD" class="login_input my-2 mx-auto "/>
            <button type="submit" class="login_submit login_input mt-2 mb-4 mx-auto">ZAPISZ</button>
            <span class="w-full text-center" >Wszedłeś tu przypadkiem?</span>
            <a href="/">Wróc do logowania</a>
        </form>
    </body>
</html>
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
    <body class="antialiased relative text-center w-full h-screen overflow-x-hidden">
        <div class="app-logo w-full text-center text-white text-7xl mt-8">
            Czatap
        </div>

        <div class="w-screen mt-12 relative flex flex-col justify-center content-center items-center overflow-x-hidden">
            <form class="flex flex-col items-center text-gray-800 py-4 startForm rounded-lg px-2" action="/reset/{{ $token }}" method="POST">
                <div class="w-full text-center text-lg form-text mb-8 mt-4">Zaloguj się</div>
                @csrf
                <div class="input-group relative">
                    <input class="form-input" type="password" name="pass" id="pass" required onchange="this.setAttribute('value', this.value);"/>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>Hasło</label>
                </div>
                <div class="input-group relative">
                    <input class="form-input" type="password" name="pass2" id="pass2" required onchange="this.setAttribute('value', this.value);"/>
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    <label>Powtórz hasło</label>
                </div>
                <button class="w-36 cta-btn text-gray-800 my-4 px-4 py-2 rounded-md" id="send" type="submit">Zapisz <i class="far fa-paper-plane"></i></button>
            </form>
        </div>
        <div>Icons made by <a href="https://www.freepik.com" rel="nofollow" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" rel="nofollow" title="Flaticon">www.flaticon.com</a></div>
    </body>
</html>