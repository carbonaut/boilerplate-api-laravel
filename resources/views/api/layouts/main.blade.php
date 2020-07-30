<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/api/swagger/swagger-ui.css') }}" >
    <link rel="stylesheet" type="text/css" href="{{ asset('/assets/api/swagger/swagger-ui-boot.css') }}" >
    <link rel="icon" type="image/png" href="{{ asset('/assets/api/swagger/favicon-32x32.png') }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ asset('/assets/api/swagger/favicon-16x16.png') }}" sizes="16x16" />
  </head>

  <body>
    <div id="swagger-ui"></div>

    <script src="{{ asset('/assets/api/swagger/swagger-ui-bundle.js') }}"> </script>
    <script src="{{ asset('/assets/api/swagger/swagger-ui-standalone-preset.js') }}"> </script>
    <script src="{{ asset('/assets/api/swagger/swagger-ui-boot.js') }}"> </script>
  </body>
</html>