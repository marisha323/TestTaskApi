<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>Result Page</h1>
<p>{{ $welcomeMessage }} Your gender is <b>{{ $gender }}</b>, and your age is <b>{{ $age }}</b> years.</p>
<p>{{ $ageCategory }}</p>
<p>{{ $audioFileName }}</p>

@if ($logData)
    <h2>API Log Data</h2>
    <pre>{{ $logData }}</pre>
@endif
</body>
</html>
