<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Czatap</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="icon" type="image/png" sizes="192x192"  href="/storage/images/android-icon-192x192.png">

<!-- Styles -->
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
<link href="{{ mix('/css/app.css') }}" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&family=Rampart+One&display=swap" rel="stylesheet">

<script src="https://kit.fontawesome.com/309a8b3aa5.js" crossorigin="anonymous"></script>
<script>
    navigator.serviceWorker.register('/sw.js').catch(e=>console.error('Ups!' + e))
</script>
<link rel="manifest" href="/manifest.json">
<meta name="csrf-token" content="{{ csrf_token() }}" />