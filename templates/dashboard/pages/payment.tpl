<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="dns-prefetch" href="//fonts.googleapis.com">

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <link rel="icon" href="{_assets("images/favicon.png")}">

        <title>Order Confirmation &middot; {system_site_name}</title>
        
        <link rel="stylesheet" href="//fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap">

        <style>
            body {
                text-align: left;
                font-family: "Rubik",sans-serif;
                color: #333;
                font-size: 20px;
                padding: 150px;
            }
            h1 {
                font-size: 50px;
            }
            article {
                display: block;
                width: 650px;
                margin: 0 auto;
            }
            a {
                color: #dc8100;
                text-decoration: none;
            }
            a:hover {
                color: #333;
                text-decoration: none;
            }
        </style>
    </head>

    <body>
        <article>
            <h1>Order Confirmation</h1>

            <p>We have received your order request, our system will automatically send you an email and change the subscription of your account once the payment transaction has been verified.</p>

            <p>Payment ID: {$order}</p>

            <p>
              <a href="{site_url}"><<< Back to Dashboard</a>
            </p>
        </article>
    </body>
</html>