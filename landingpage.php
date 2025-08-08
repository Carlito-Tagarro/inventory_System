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
    <!-- Replace the incorrect Font Awesome link with the CDN version -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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

        /* Footer Styles */
        .footersection {
            background: #a94d4d;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            font-size: 1rem;
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100vw;
            z-index: 100;
            margin: 0;
        }

        /* Carousel Styles */
        * {box-sizing:border-box}

/* Slideshow container */
.slideshow-container {
  max-width: 1000px;
  position: relative;
  margin: auto;
}

/* Hide the images by default */
.mySlides {
  display: none;
}

/* Next & previous buttons */
.prev, .next {
  cursor: pointer;
  position: absolute;
  top: 50%;
  width: auto;
  margin-top: -22px;
  padding: 16px;
  color: white;
  font-weight: bold;
  font-size: 18px;
  transition: 0.6s ease;
  border-radius: 0 3px 3px 0;
  user-select: none;
}

/* Position the "next button" to the right */
.next {
  right: 0;
  border-radius: 3px 0 0 3px;
}

/* On hover, add a black background color with a little bit see-through */
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.8);
}

/* Caption text */
.text {
  color: #f2f2f2;
  font-size: 15px;
  padding: 8px 12px;
  position: absolute;
  bottom: 8px;
  width: 100%;
  text-align: center;
}

/* Number text (1/3 etc) */
.numbertext {
  color: #f2f2f2;
  font-size: 12px;
  padding: 8px 12px;
  position: absolute;
  top: 0;
}

/* The dots/bullets/indicators */
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

/* Fading animation */
.fade {
  animation-name: fade;
  animation-duration: 1.5s;
}

@keyframes fade {
  from {opacity: .4}
  to {opacity: 1}
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
           <div class="slideshow-container">

  <!-- Carousel -->
  <div class="mySlides fade">
    <!-- <div class="numbertext">1 / 3</div> -->
    <img src="images/CCS_Program_Datasheet_page-0001.jpg" style="width:100%">
    
  </div>

  <div class="mySlides fade">
    <!-- <div class="numbertext">2 / 3</div> -->
    <img src="images/Cisco Program Overview 1124 PRINT_page-0001.jpg" style="width:100%">
    
  </div>

  <div class="mySlides fade">
    <!-- <div class="numbertext">3 / 3</div> -->
    <img src="images/Hospitality and Culinary Arts Careers - Culinary Foundations - Infographic_page-0001.jpg" style="width:100%">
    
  </div>

  <!-- Next and previous buttons -->
  <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
  <a class="next" onclick="plusSlides(1)">&#10095;</a>
</div>
<br>

<!-- The dots/circles -->
<!-- <div style="text-align:center">
  <span class="dot" onclick="currentSlide(1)"></span>
  <span class="dot" onclick="currentSlide(2)"></span>
  <span class="dot" onclick="currentSlide(3)"></span>
</div>
        </div>
    </div> -->

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
                All rights reserved &copy;<br>
                Audentes Technologies 2018
            </div>
            <div style="text-align: right;">
               <i class="fa-light fa-location-dot"></i>
                VCC BUILDING B16 L2 SAN AGUSTIN VILLAGE, BARANGAY SAN FRANCISCO, BIÑAN, LAGUNA, Biñan, Philippines
            </div>
        </div>
    </footer>
    <script>
let slideIndex = 0;
showSlides();

function showSlides() {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slideIndex++;
  if (slideIndex > slides.length) {slideIndex = 1}
  slides[slideIndex-1].style.display = "block";
  setTimeout(showSlides, 4000); // Change image every 2 seconds
}
    </script>
</body>
</html>