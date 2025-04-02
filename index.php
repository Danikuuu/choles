<?php 
session_start();
include "./data-handling/db/connection.php";
$menus = "SELECT 
    m.name AS menu_name, 
    m.image AS menu_image,
    m.description AS menu_description, 
    COUNT(m.id) AS reservation_count
    FROM customer_package_menu cpm
    JOIN reservations r ON cpm.id = r.customer_package_id 
    JOIN menu m ON cpm.menu_id = m.id
    GROUP BY m.id, m.image
    ORDER BY reservation_count DESC
    LIMIT 3;";

  $MenuResult = $con->query($menus);

  $topMenus = [];
  while ($row = $MenuResult->fetch_assoc()) {
    $topMenus[] = $row;
  }
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>CHOLES Catering Services</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <!-- <link href="assets/img/favicon.png" rel="icon"> -->
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">CHOLES</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php" class="active">Home<br></a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="catering.php">Catering</a></li>
          <li><a href="events.php">Event Styling</a></li>
          <!-- <li><a href="contact.php">Contact Us</a></li> -->
          <li><a href="login.php">Login</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="signup.php">Create an account</a>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <img src="./assets/img/darwin.jpg" alt="" data-aos="fade-in">

      <div class="container">
        <h2 data-aos="fade-up" data-aos-delay="100">CHOLES Catering</h2>
        <p data-aos="fade-up" data-aos-delay="200">Excellent cuisine and catering services tailored to your budget and needs.</p>
        <div class="d-flex mt-4" data-aos="fade-up" data-aos-delay="300">
          <a href="login.php" class="btn-get-started">Get Started</a>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/choose-us.jpg" class="img-fluid" alt="">
          </div>

          <div class="col-lg-6 order-2 order-lg-1 content" data-aos="fade-up" data-aos-delay="200">
            <h3>WHY CHOOSE US?</h3>
            <p class="fst-italic">
              We are more than just your typical catering service provider. <br> At CHOLES Catering, we are committed to turning your vision into reality.
            </p>
            <p>
              Our goal is to help you create an incomparable experience by going above and beyond through our :
            </p>
            <ul>
              <li><i class="bi bi-check-circle"></i> <span>Food Offering</span></li>
              <li><i class="bi bi-check-circle"></i> <span>Service</span></li>
              <li><i class="bi bi-check-circle"></i> <span>and Styling</span></li>
            </ul>
            <p>
              that is tailored to your taste and budget without scrimping on quality
            </p>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Counts Section -->
    <section id="counts" class="section counts light-background">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="1232" data-purecounter-duration="1" class="purecounter"></span>
              <p>Total Clients</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="64" data-purecounter-duration="1" class="purecounter"></span>
              <p>Venues Styled</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="42" data-purecounter-duration="1" class="purecounter"></span>
              <p>Events Hosted</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="24" data-purecounter-duration="1" class="purecounter"></span>
              <p>Occasions</p>
            </div>
          </div><!-- End Stats Item -->

        </div>

      </div>

    </section><!-- /Counts Section -->

    <section id="why-us" class="section why-us">

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="why-box">
              <h3>Celebration Packages</h3>
              <p>
                Our celebration packages offer a seamless experience, including catering, decorations, and event coordination.
                Perfect for birthdays, anniversaries, and corporate gatherings. Choose from our premium, deluxe, and standard packages.
              </p>
            </div>
          </div>
  
          <div class="col-lg-8 d-flex align-items-stretch">
            <div class="row gy-4" data-aos="fade-up" data-aos-delay="200">
  
              <div class="col-xl-4">
                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                  <i class="bi bi-clipboard-data"></i>
                  <h4>Detailed Planning</h4>
                  <p>We handle every aspect of your event, from scheduling to final execution, ensuring a stress-free experience.</p>
                </div>
              </div>
  
              <div class="col-xl-4" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                  <i class="bi bi-gem"></i>
                  <h4>Premium Services</h4>
                  <p>Experience luxury with our high-quality catering, entertainment, and venue customization options.</p>
                </div>
              </div>
  
              <div class="col-xl-4" data-aos="fade-up" data-aos-delay="400">
                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                  <i class="bi bi-inboxes"></i>
                  <h4>Customizable Packages</h4>
                  <p>Tailor our packages to fit your event's theme, budget, and guest count for a truly unique experience.</p>
                </div>
              </div>
  
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Why Us Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

      <div class="container">
        <center><h3 class="mb-4" data-aos="fade-up" data-aos-delay="100">Occasions we celebrate</h3></center>
        <div class="row gy-4">

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="features-item">
              <i class="bi bi-balloon" style="color: #ffbb2c;"></i>
              <h3><a href="" class="stretched-link">Birthday</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="features-item">
              <i class="bi bi-balloon-heart" style="color: #5578ff;"></i>
              <h3><a href="" class="stretched-link">Wedding</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="features-item">
              <i class="bi bi-mortarboard" style="color: #e80368;"></i>
              <h3><a href="" class="stretched-link">Anniversary</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="400">
            <div class="features-item">
              <i class="bi bi-mortarboard" style="color: #e361ff;"></i>
              <h3><a href="" class="stretched-link">Graduation</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="500">
            <div class="features-item">
              <i class="bi bi-shuffle" style="color: #47aeff;"></i>
              <h3><a href="" class="stretched-link">Corporate Events</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="600">
            <div class="features-item">
              <i class="bi bi-star" style="color: #ffa76e;"></i>
              <h3><a href="" class="stretched-link">Holloween Party</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="700">
            <div class="features-item">
              <i class="bi bi-tree" style="color: #11dbcf;"></i>
              <h3><a href="" class="stretched-link">Christmas Party</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="800">
            <div class="features-item">
              <i class="bi bi-camera-video" style="color: #4233ff;"></i>
              <h3><a href="" class="stretched-link">Promotion Party</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="900">
            <div class="features-item">
              <i class="bi bi-heart" style="color: #b2904f;"></i>
              <h3><a href="" class="stretched-link">Valentine Event</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="1000">
            <div class="features-item">
              <i class="bi bi-dribbble" style="color: #b20969;"></i>
              <h3><a href="" class="stretched-link">Reunion</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="1100">
            <div class="features-item">
              <i class="bi bi-person-arms-up" style="color: #ff5828;"></i>
              <h3><a href="" class="stretched-link">Baby Shower</a></h3>
            </div>
          </div><!-- End Feature Item -->

          <div class="col-lg-3 col-md-4" data-aos="fade-up" data-aos-delay="1200">
            <div class="features-item">
              <i class="bi bi-brightness-high" style="color: #29cc61;"></i>
              <h3><a href="" class="stretched-link">Retirement Party  </a></h3>
            </div>
          </div><!-- End Feature Item -->

        </div>

      </div>

    </section><!-- /Features Section -->

    <section id="why-us" class="section why-us">

      <div class="container">
        <div class="row gy-4">

  
          <div class="col-lg-8 d-flex align-items-stretch">
            <div class="row gy-4" data-aos="fade-up" data-aos-delay="200">
              <?php foreach ($topMenus as $menu) { ?>
                <div class="col-xl-4">
                  <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                    <img src="./admin/dashboard/<?= htmlspecialchars($menu['menu_image']); ?>" alt="<?= htmlspecialchars($menu['menu_name']); ?>" style="width: 180px; height: 130px; object-fit: cover;">
                    <h4><?= htmlspecialchars($menu['menu_name']); ?></h4>
                    <p><?= htmlspecialchars($menu['menu_description']); ?></p>
                  </div>
                </div>
              <?php } ?>
  
            </div>
          </div>
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="why-box" style="height: 100%;">
              <h3>Favorite Foods</h3>
              <p>
                Explore our most-loved dishes, carefully selected by our customers. These top-rated menu items bring joy to every celebration, offering a perfect balance of taste and presentation.  
                Whether you're planning a birthday, anniversary, or corporate event, our delicious favorites ensure a memorable dining experience.
              </p>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Why Us Section -->

    <!-- Trainers Index Section -->
    <section id="trainers-index" class="section trainers-index">

      <div class="container">

        <center><h3 data-aos="fade-up" data-aos-delay="100">CUSTOMER FEEDBACK</h3></center>
        <div class="row">
        <?php
session_start();
include "./data-handling/db/connection.php";

$query = "SELECT u.fname, u.lname, f.rating, f.comment 
          FROM feedback f
          JOIN user u ON f.user_id = u.id
          ORDER BY f.created_at DESC 
          LIMIT 3";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $fullName = $row['fname'] . ' ' . $row['lname'];
    $comment = $row['comment'];
    $rating = $row['rating'];

    // Generate star ratings dynamically
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<a href=""><i class="bi bi-star-fill" style="color: #ffbb2c;"></i></a>';
        } else {
            $stars .= '<a href=""><i class="bi bi-star"></i></a>';
        }
    }
?>
<div class="col-lg-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="100">
    <div class="member">
        <div class="member-content p-3" style="width: 400px;">
            <h4><?= htmlspecialchars($fullName); ?></h4>
            <span>Customer</span>
            <p><?= htmlspecialchars($comment); ?></p>
            <div class="social">
                <?= $stars; ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>

        </div>

      </div>

    </section><!-- /Trainers Index Section -->

  </main>

  <footer id="footer" class="footer position-relative light-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">CHOLES Catering Services</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Cabanatuan</p>
            <p>Nueva Ecija, Philippines</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+63 912-345-6789</span></p>
            <p><strong>Email:</strong> <span>cholescatering@gmail.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Terms of service</a></li>
            <li><a href="#">Privacy policy</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="#">Catering</a></li>
            <li><a href="#">Venue Styling</a></li>
            <li><a href="#">Menu Choosing</a></li>
            <li><a href="#">Chairs and Tables</a></li>
            <li><a href="#">And many more</a></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>©  <strong class="px-1 sitename">CHOLES</strong> <span>All Rights Reserved</span></p>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>