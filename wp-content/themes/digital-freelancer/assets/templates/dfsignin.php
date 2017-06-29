<?php
/**
 * Template Name: DF Sign In Page Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package understrap
 */

 ?>

<? get_header(); ?>

<!-- FEATURE HEADER -->
<div class="jumbotron sign">
  <div  class="background-overlay"></div>
  <div class="container">
    <h1 class="title">Sign In to Digital Freelancer</h1>
      <?php the_content(); ?>
      <p class="sub-title"><a href="/sign-up">Don't have an account?</a></p>    
  </div>
</div>


<div class="container-fluid address-section">
  <address>
    <strong>Digital Freelancer</strong><br>
    433 Broadway<br>
    New York, New York 10013
  </address>
  <div class="social">
    <i class="fa fa-facebook-official" aria-hidden="true"></i>
    <i class="fa fa-twitter" aria-hidden="true"></i>
    <i class="fa fa-instagram" aria-hidden="true"></i>
  </div>
</div>
</div>
<? get_footer(); ?>
