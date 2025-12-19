<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Obertrack')</title>
    <style>
        /* Base styles */
        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        
        table {
            border-spacing: 0;
            border-collapse: collapse;
            width: 100%;
        }
        
        img {
            border: 0;
            -ms-interpolation-mode: bicubic;
        }

        /* Container */
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding-bottom: 40px;
        }
        
        .main-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Header */
        .header {
            padding: 30px 40px;
            text-align: center;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .logo {
            height: 50px;
            width: auto;
        }

        /* Content */
        .content {
            padding: 40px;
            color: #1a202c;
            line-height: 1.6;
        }
        
        .title {
            font-size: 24px;
            font-weight: 700;
            color: #23272A;
            margin-bottom: 24px;
            margin-top: 0;
        }
        
        .text {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 24px;
        }

        /* Highlight box */
        .highlight-box {
            background-color: #f1f5f9;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
        }

        /* Button */
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .button {
            display: inline-block;
            background-color: #22A9C8;
            color: #ffffff !important;
            padding: 14px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        /* Footer */
        .footer {
            padding: 30px 40px;
            text-align: center;
            font-size: 12px;
            color: #718096;
            background-color: #f8fafc;
        }
        
        .footer p {
            margin: 8px 0;
        }

        /* Mobile Adjustments */
        @media screen and (max-width: 600px) {
            .content {
                padding: 24px !important;
            }
            .header {
                padding: 20px !important;
            }
            .main-container {
                border-radius: 0 !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="wrapper">
        <div style="height: 40px;">&nbsp;</div>
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <img src="https://obertrack.com/images/logo.png" alt="Obertrack" class="logo">
                <div style="margin-top: 10px;">
                    <span style="font-weight: bold; font-size: 20px; color: #22A9C8;">Obertrack</span>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                @yield('content')
            </div>

            <!-- Pre-footer divider -->
            <div style="border-top: 1px solid #e2e8f0; margin: 0 40px;"></div>

            <!-- Footer -->
            <div class="footer">
                <p>Este es un correo automático de Obertrack.</p>
                <p>Por favor no respondas a este mensaje.</p>
                <p>&copy; {{ date('Y') }} Obertrack. Todos los derechos reservados.</p>
                @yield('footer_links')
            </div>
        </div>
        <div style="height: 40px;">&nbsp;</div>
    </div>
</body>
</html>
