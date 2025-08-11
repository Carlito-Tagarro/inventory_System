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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Reset and page layout */
html, body {
    height: 100%;
    margin: 0;
}

body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f4f6f8;
    box-sizing: border-box;
}

/* Main content takes available space */
main {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Navigation */
nav {
    padding: 16px 0;
    margin-bottom: 32px;
    display: grid;
    grid-template-columns: auto 1fr;
    align-items: center;
    gap: 20px;
    background-color: #fff;
}

.nav-container {
    display: flex;
    justify-content: center;
    gap: 84px; /* Increased gap from 32px to 84px */
    width: 100%;
}

nav img {
    height: 50px;
    margin-left: 50px;

}

nav a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    
}

/* Main content spacing */
.main-content {
    flex: 1;
    padding: 30px;
    display: flex;
    flex-direction: column;
}

/* Footer */
.footersection {
    background: #a94d4d;
    color: #fff;
    width: 100%;
    font-size: 1rem;
}

.footersection > div {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 32px;
    flex-wrap: wrap;
}

.footersection a {
    color: #fff;
    text-decoration: none;
}

/* Carousel */
* { box-sizing: border-box; }

.slideshow-container {
    max-width: 1000px;
    position: relative;
    margin: auto;
}

.slideshow-container img {
    max-width: 100%;
    max-height: 700px;
    display: block;
    margin-right: 180px;
    border-radius: 12px;
}

.mySlides {
    display: none;
}

.prev, .next {
    cursor: pointer;
    position: absolute;
    top: 50%;
    margin-top: -22px;
    padding: 16px;
    color: white;
    font-weight: bold;
    font-size: 18px;
    transition: 0.6s ease;
    user-select: none;
}

.next {
    right: 0;
    border-radius: 3px 0 0 3px;
}

.prev:hover, .next:hover {
    background-color: rgba(0,0,0,0.8);
}

.dot {
    cursor: pointer;
    height: 15px;
    width: 15px;
    margin: 0 2px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
    transition: background-color 0.6s ease;
}

.active, .dot:hover {
    background-color: #717171;
}

.fade {
    animation-name: fade;
    animation-duration: 1.5s;
}

@keyframes fade {
    from { opacity: .4; }
    to { opacity: 1; }
}

/* Grouped circle styles */
.circle {
    position: absolute;
    background: #cfa7a7;
    border-radius: 50%;
    opacity: 0.7;
}
.circle1 { top: 10px; left: 40px; width: 120px; height: 120px; }
.circle2 { top: -30px; left: 320px; width: 70px; height: 70px; }
.circle3 { top: 220px; left: 260px; width: 180px; height: 180px; }
.circle4 { top: 340px; left: 120px; width: 110px; height: 110px; }

.main-title {
    font-size: 2.2rem;
    font-weight: bold;
    font-family: 'Segoe UI', Arial, sans-serif;
}
.main-title span:last-child {
    color: #a94d4d;
    font-weight: normal;
}
.main-desc {
    font-size: 1.1rem;
    max-width: 500px;
    margin-top: 10px;
}
.get-started-btn {
    display: inline-block;
    margin-top: 28px;
    background: #a94d4d;
    color: #fff;
    padding: 12px 32px;
    border-radius: 24px;
    font-weight: 600;
    text-decoration: none;
    font-size: 1rem;
}
    </style>
</head>
<body>
  <main>
    <div class="main-content">
        <nav>
            <img src="images/AUDENTES LOGO.png" alt="Company Logo">
            <div class="nav-container">
                <a href="landingpage.php">HOME</a>
                <a href="https://www.facebook.com/audentestechnologies">ABOUT</a>
                <a href="https://www.facebook.com/audentestechnologies">CONTACT US</a>
            </div>
        </nav>
        <br><br>

        <div style="display: flex; gap: 0px; align-items: flex-start; flex: 1; height: 100%;">
            <div style="flex: 1; position: relative; padding: 40px 0 0 40px; margin-left: 200px;">
                <div class="circle circle1"></div>
                <div class="circle circle2"></div>
                <div class="circle circle3"></div>
                <div class="circle circle4"></div>
                <div style="position: relative; z-index: 1;">
                    <h1 class="main-title">
                        <span>AUDENTES</span>
                        <span>TECHNOLOGIES</span>
                    </h1>
                    <p class="main-desc">
                        With 5+ years of experience in the technology and education space, we envision ourselves to become the leading technology partner of schools and universities, providing internationally-recognized certificates through training as a service.
                    </p>
                    <a href="index.php" class="get-started-btn">GET STARTED</a>
                </div>
            </div>
            
            <div style="flex: 1; display: flex; justify-content: center; align-items: center;">
               <div class="slideshow-container">
                  <div class="mySlides fade">
                    <img src="images/CCS_Program_Datasheet_page-0001.jpg" alt="Slide 1">
                  </div>
                  <div class="mySlides fade">
                    <img src="images/Cisco Program Overview 1124 PRINT_page-0001.jpg" alt="Slide 2">
                  </div>
                  <div class="mySlides fade">
                    <img src="images/Hospitality and Culinary Arts Careers - Culinary Foundations - Infographic_page-0001.jpg" alt="Slide 3">
                  </div>
               </div>
            </div>
        </div>
    </div>

    <div class="second-content"></div>
   </main> 

    <footer class="footersection">
        <div style="background: #a94d4d; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 18px 32px; font-size: 1rem;">
            <div>
                <span style="margin-right: 18px;">
                    <i class="fa-solid fa-envelope"></i>
                    info@audentestechnologies.com
                </span>
                <span style="margin-right: 18px;">
                    <i class="fa-solid fa-phone"></i>
                    +639206028971
                </span>
                <div class="social-icons" style="display: inline-block; margin-left: 20px; text-decoration: none;">
                    <a href="https://www.linkedin.com/company/audentestechnologies/" target="_blank"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="https://www.facebook.com/audentestechnologies" target="_blank"><i class="fa-brands fa-facebook"></i></a>
                </div>
            </div>
            <div style="text-align: center;">
                &copy; 2018 Audentes Technologies. All rights reserved.
            </div>
            <div style="text-align: right;">
               <i class="fa-solid fa-location-dot"></i>
                VCC BUILDING B16 L2 SAN AGUSTIN VILLAGE,<br> BRGY. SAN FRANCISCO, BIÑAN, LAGUNA, Biñan, Philippines
            </div>
        </div>
    </footer>

    <script>
        // Optimized carousel JS
        let slideIndex = 0;
        function showSlides() {
            const slides = document.getElementsByClassName("mySlides");
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex = (slideIndex % slides.length) + 1;
            slides[slideIndex - 1].style.display = "block";
            setTimeout(showSlides, 4000);
        }
        showSlides();
    </script>
</body>
</html>
