<?php
include 'connection.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audentes Technologies</title>
    <link rel="icon" type="image/x-icon" href="images/images__1_-removebg-preview.png">
    <style>
          body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
        }
         nav {
            
            padding: 16px 0;
            margin-bottom: 32px;
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 20px;
        }
        .nav-container {
            display: flex;
            justify-content: center;
            gap: 32px;
            width: 100%;
        }
        nav img {
            height: 50px;
            margin-left: 20px;
        }
        nav a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <a href="admin.php"><img src="images/AUDENTES LOGO.png" alt="Company Logo"></a>
        <div class="nav-container">
            <a href="landingpage.php">HOME</a>
            <a href="https://www.facebook.com/audentestechnologies">ABOUT</a>
            <a href="https://www.facebook.com/audentestechnologies">CONTACT US</a>
        </div>
    </nav>

    <div style="display: flex; gap: 40px; align-items: flex-start; min-height: 500px;">
        
        <div style="flex: 1; position: relative; padding: 40px 0 0 40px;">
            
            <div style="position: absolute; top: 10px; left: 40px; width: 120px; height: 120px; background: #cfa7a7; border-radius: 50%; opacity: 0.7;"></div>
            <div style="position: absolute; top: -30px; left: 320px; width: 70px; height: 70px; background: #cfa7a7; border-radius: 50%; opacity: 0.7;"></div>
            <div style="position: absolute; top: 220px; left: 260px; width: 180px; height: 180px; background: #cfa7a7; border-radius: 50%; opacity: 0.7;"></div>
            <div style="position: absolute; top: 340px; left: 120px; width: 110px; height: 110px; background: #cfa7a7; border-radius: 50%; opacity: 0.7;"></div>
           
            <div style="position: relative; z-index: 1;">
                <h1 style="font-size: 2.2rem; font-weight: bold; margin-bottom: 0;">
                    <span style="font-family: 'Segoe UI', Arial, sans-serif;">AUDENTES</span>
                    <span style="color: #a94d4d; font-weight: normal;">TECHNOLOGIES</span>
                </h1>
                <p style="font-size: 1.1rem; max-width: 500px; margin-top: 10px;">
                    With 5+ years of experience in the technology and education space, we envision ourselves to become the leading technology partner of schools and universities, providing internationally-recognized certificates through training as a service.
                </p>
                <a href="admin.php" style="display: inline-block; margin-top: 28px; background: #a94d4d; color: #fff; padding: 12px 32px; border-radius: 24px; font-weight: 600; text-decoration: none; font-size: 1rem;">GET STARTED</a>
            </div>
        </div>
        
        <div style="flex: 1; display: flex; justify-content: center; align-items: center;">
            <div style="width: 350px; height: 350px; background: #a94d4d; display: flex; align-items: center; justify-content: center;">
                <span style="color: #fff; font-size: 1.5rem; letter-spacing: 0.25em; text-align: center; font-family: 'Segoe UI', Arial, sans-serif;">
                    CAROUSEL<br>BROCHURE
                </span>
            </div>
        </div>
    </div>

    <footer s>
        <div style="background: #a94d4d; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 18px 32px; font-size: 1rem;">
            <div>
                <span style="margin-right: 18px;">
                    <img src="images/email_icon.png" alt="Email" style="height: 18px; vertical-align: middle; margin-right: 6px;">
                    info@audentestechnologies.com
                </span>
                <span style="margin-right: 18px;">
                    <img src="images/phone_icon.png" alt="Phone" style="height: 18px; vertical-align: middle; margin-right: 6px;">
                    +639206028971
                </span>
                <span>
                    <img src="images/linkedin_icon.png" alt="LinkedIn" style="height: 18px; vertical-align: middle; margin-right: 6px;">
                    <img src="images/facebook_icon.png" alt="Facebook" style="height: 18px; vertical-align: middle;">
                </span>
            </div>
            <div style="text-align: center;">
                All rights reserved &copy;<br>
                Audentes Technologies 2018
            </div>
            <div style="text-align: right;">
                <img src="images/location_icon.png" alt="Location" style="height: 18px; vertical-align: middle; margin-right: 6px;">
                VCC BUILDING B16 L2 SAN AGUSTIN VILLAGE, BARANGAY SAN FRANCISCO, BIÑAN, LAGUNA, Biñan, Philippines
            </div>
        </div>
    </footer>
</body>
</html>