<?php
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="cache-control" content="public">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Audentes Technologies provides innovative IT solutions tailored to your business needs. Grow your digital presence with us.">
  <title>Audentes Technologies</title>
  <link rel="icon" type="image/x-icon" href="images/images__1_-removebg-preview.png">
  <link rel="stylesheet" href="CSS/index.css">

  <!-- Preload critical assets -->
  <link rel="preload" as="image" href="images/images__1_-removebg-preview.png">
  <link rel="preload" as="image" href="images/slide1.jpg">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" media="all">

</head>
<body>

  <!-- Navbar -->
  <nav>
    <div class="container nav-container">
      <div class="nav-logo">
        <img src="images/images__1_-removebg-preview.png" alt="Audentes Technologies Logo">
      </div>
      <div class="nav-links" id="nav-links">
        <a href="index.php">Home</a>
        <a href="#">Services</a>
        <a href="#">About</a>
        <a href="https://www.facebook.com/audentestechnologies">Contact</a>
      </div>
      <div class="hamburger" id="hamburger"><i class="fas fa-bars"></i></div>
    </div>
  </nav>

  <!-- Main Content -->
  <main>
    <!-- Hero -->
    <section class="hero container">
      <div class="hero-text">
        <h1>Welcome to <span>Audentes Technologies</span></h1>
        <p>With 5+ years of experience in the technology and education space, we envision ourselves to become the leading technology partner of schools and universities, providing internationally-recognized certificates through training as a service.</p>
        <a href="userpage.php" class="cta-btn">Get Started</a>
      </div>

      <!-- Slideshow -->
      <div class="slideshow-container">
        <div class="slides" id="slides">
          <img src="images/CCS_Program_Datasheet_page-0001.jpg" alt="Innovative IT solutions" loading="eager">
          <img src="images/Cisco Program Overview 1124 PRINT_page-0001.jpg" alt="Team collaboration" loading="lazy">
          <img src="images/Hospitality and Culinary Arts Careers - Culinary Foundations - Infographic_page-0001.jpg" alt="Technology growth" loading="lazy">
        </div>
        <!-- Arrows -->
        <span class="prev" id="prev">&#10094;</span>
        <span class="next" id="next">&#10095;</span>
        <!-- Dots -->
        <div class="dots" id="dots"></div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer>
    <p>&copy; 2018 Audentes Technologies. All rights reserved.</p>
    <div class="socials">
      <a href="https://www.facebook.com/audentestechnologies" aria-label="Facebook"  target="_blank"><i class="fab fa-facebook"></i></a>
      <a href="https://twitter.com/audentestech" aria-label="Twitter" target="_blank"><i class="fab fa-twitter"></i></a>
      <a href="https://www.instagram.com/officialaudentestechnologies/" aria-label="Instagram" target="_blank"><i class="fab fa-instagram"></i></a>
      <a href="https://www.linkedin.com/company/audentestechnologies" aria-label="LinkedIn" target="_blank"><i class="fab fa-linkedin"></i></a>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="JavaScripts/index.js" defer></script>
</body>
</html>
   