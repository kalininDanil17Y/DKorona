<!DOCTYPE html>
<html lang="ru">
<head>
  <title>{{ title }}</title>
</head>
<body>
<h1>{{ title }}</h1>
<hr>
<a href="{{ getPath('home') }}">Back to home</a> | <a href="{{ getPath('about') }}">About page</a> | <a href="{{ getPath('get_user', 'id', 2) }}">User 2</a>
</body>
</html>
