<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta
        name="description"
        content="Ultimania API documentation"
    />
    <title>Ultimania API documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@4.6.1/swagger-ui.css" />
    <style>
        body {
            margin: 0;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@4.6.1/swagger-ui-bundle.js" crossorigin></script>
    <script src="https://unpkg.com/swagger-ui-dist@4.6.1/swagger-ui-standalone-preset.js" crossorigin></script>
    <script>
        window.onload = () => {
            window.ui = SwaggerUIBundle({
                url: '/openapi',
                dom_id: '#swagger-ui',
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: "StandaloneLayout",
            });
        };
    </script>
</body>
</html>
