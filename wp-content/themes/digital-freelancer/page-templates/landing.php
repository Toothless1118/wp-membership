<?php
/**
 * Template Name: Landing Page Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package understrap
 */

 ?>

<? get_header(); ?>

<!-- FEATURE HEADER -->
<div class="jumbotron landing-header">
  <div  class="background-img"></div>
  <div  class="background-overlay"></div>
  <div class="container">
    <h1 class="title">10x Your Freelancing Business</h1>
    <p class="sub-title col-md-6 offset-md-3">
      Everything you need to<span> domicate the business side </span>of your freelancing life. Start winning today
    </p>
    <form class="form-inline justify-content-center">
      <input type="text" class="form-control col-lg-3 col-sm-8 mb-2 mr-sm-2 mb-sm-2 form-control-lg" id="inlineFormInput" placeholder="Email Address">
      <input type="password" class="form-control col-lg-3 col-sm-8 mb-2 mr-sm-2 mb-sm-2 form-control-lg" id="inlineFormInput" placeholder="Password">
      <button type="submit" class="btn btn-primary btn-lg col-lg-2 col-sm-8">Join for Free</button>
    </form>
    <?php the_content(); ?>
    <?php //echo do_shortcode('[mepr-membership-registration-form id="55"]');?>
    <p class="learn-more">Learn More<i class="fa fa-chevron-down" aria-hidden="true"></i></p>
  </div>
</div>

<div class="container sales-letter-section pt-5 mt-5">
  <h2 class="title col-lg-10 col-xl-10 offset-lg-1 offset-xl-1">The #1 Resource for Business-Minded Freelancing Knowledge, Tools and Community</h2>
  <div class="media mt-5 col-sm-6 col-md-4 col-xl-4 offset-sm-3 offset-md-4 offset-xl-4">
    <img class="d-flex align-self-center mr-3" src="/wp-content/themes/digital-freelancer/dist/images/connor.png" alt="Generic placeholder image">
    <div class="media-body align-self-center">
      <h5 class="mt-0">Connor Black</h5>
      Founder, DigitalFreelancer.io
    </div>
  </div>

  <div class="letter mt-5">
    <p>Like most freelancers, I didn't study business in school. In fact,I dropped out three years into my comp-sci degree to pursue the startup dream.</p>
    <p>After some highs, lows and the fire-sale of my startup, I found myself in a situation common to a lot people I know: freelancing.</p>
    <p>I told my hopelessly clueless self:</p>
    <p>"I'll do this long enough until I have enough money to start my next business".</p>
    <p>I charged an insultingly low late, waited for clients to come to me, worked fo almost anyone and found myself in a miserabel freelancing rollacoaster.</p>
    <p>It wasn't until I realized a simple fact that things started to take off.</p>
    <p>I wasn't waiting to start to my next business, I already was running a business.</p>
  </div>
  <div class="letter-button mt-5">
    <button  class="btn btn-primary btn-lg col-lg-2 col-sm-8">Join for Free</button>
  </div>
</div>
<!--
<div class="container guide-and-tools-section pt-5 mt-5">
  <h2 class="title col-lg-8 col-xl-8 offset-lg-2 offset-xl-2">Guides and Tools Made for Freelancers by Freelancers</h2>
  <h5 class="sub-title pt-4 mt-4">TOP POSTS</h5>
  <div class="card-deck pt-3 mt-3">
    <div class="col-lg-4 mb-2">
      <div class="card card-inverse">
        <span>
          <img class="card-img" src="/wp-content/themes/digital-freelancer/dist/images/background.jpeg" alt="Card image">
        </span>
        <div class="card-img-overlay">
          <h4 class="card-title pt-3 mt-3 mb-5">Freelance Hustle: How to Create Leads and Find Work</h4>
          <p class="card-text pt-2 mt-2">There is a complicity between the very young and the very old.</p>
          <a href="#" class="read-more ">Read fully story <i class="fa fa-angle-right" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mb-2">
      <div class="card card-inverse">
        <span>
          <img class="card-img" src="/wp-content/themes/digital-freelancer/dist/images/background.jpeg" alt="Card image">
        </span>
        <div class="card-img-overlay">
          <h4 class="card-title pt-3 mt-3 mb-5">Freelance Hustle: How to Create Leads and Find Work</h4>
          <p class="card-text pt-2 mt-2">There is a complicity between the very young and the very old.</p>
          <a href="#" class="read-more">Read fully story <i class="fa fa-angle-right" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mb-2">
      <div class="card card-inverse">
        <span>
          <img class="card-img" src="/wp-content/themes/digital-freelancer/dist/images/background.jpeg" alt="Card image">
        </span>
        <div class="card-img-overlay">
          <h4 class="card-title pt-3 mt-3 mb-5">Freelance Hustle: How to Create Leads and Find Work</h4>
          <p class="card-text pt-2 mt-2">There is a complicity between the very young and the very old.</p>
          <a href="#" class="read-more">Read fully story <i class="fa fa-angle-right" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>
  </div>
  <div class="view-blog-button mt-5">
    <button  class="btn btn-lg col-lg-2 col-sm-8">View Blog</button>
  </div>
</div>
-->
<div class="container guide-and-tools-section pt-5 mt-5">
  <h2 class="title col-lg-8 col-xl-8 offset-lg-2 offset-xl-2">Guides and Tools Made for Freelancers by Freelancers</h2>
  <h5 class="sub-title pt-4 mt-4">TOP POSTS</h5>
  <?php get_sidebar( 'topposts' ); ?>
</div>

<div class="container top-products-section pt-4 mt-4">
  <h5 class="sub-title pt-3 mt-3">TOP PRODUCTS</h5>
  <div class="card-deck pt-3 mt-3">
    <?php get_sidebar( 'topproducts' ); ?>
  </div>
</div>
<!--
<div class="container top-products-section pt-4 mt-4">
  <h5 class="sub-title pt-3 mt-3">TOP PRODUCTS</h5>
  <div class="card-deck pt-3 mt-3">
    <div class="col-lg-3 mb-2">
      <div class="card">
        <img class="card-img-top" src="/wp-content/themes/digital-freelancer/dist/images/background.jpeg" alt="Card image cap">
        <div class="card-block">
          <h4 class="card-title">Card title</h4>
          <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 mb-2">
      <div class="card">
        <img class="card-img-top" src="/wp-content/themes/digital-freelancer/dist/images/background.jpeg" alt="Card image cap">
        <div class="card-block">
          <h4 class="card-title">Card title</h4>
          <p class="card-text">This card has supporting text below as a natural lead-in to additional content.</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 mb-2">
      <div class="card">
        <img class="card-img-top" src="/wp-content/themes/digital-freelancer/dist/images/background.jpeg" alt="Card image cap">
        <div class="card-block">
          <h4 class="card-title">Card title</h4>
          <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This card has even longer content than the first to show that equal height action.</p>
        </div>
      </div>
    </div>
</div>
</div>
-->
<div class="container address-section pt-4 mt-4 pb-4">
  <address>
    <strong>Digital Freelancer</strong><br>
    433 Broadway<br>
    New York, New York<br>
    10013
  </address>
  <div class="social">
    <i class="fa fa-facebook-official" aria-hidden="true"></i>
    <i class="fa fa-twitter" aria-hidden="true"></i>
    <i class="fa fa-instagram" aria-hidden="true"></i>
  </div>
</div>

<? get_footer(); ?>
