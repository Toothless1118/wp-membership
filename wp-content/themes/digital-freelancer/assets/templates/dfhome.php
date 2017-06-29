<?php
/**
 * Template Name: DF Home Page Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package understrap
 */

 ?>

<? get_header(); ?>
<!-- DF Toolbar-->
<div class="df-toolbar">
  <nav class="navbar navbar-toggleable-md navbar-light">
    <div class="collapse1 navbar-collapse" id="dfToolbar">
      <ul class="navbar-nav mr-auto col">
        <li class="nav-item">
          <a class="nav-link" href="/member-dashboard">Home</a>
        </li>
      </ul>
      <div class="form-inline btn-group">
        <a class="btn " href="/member-setting">Settings</a>
      </div>
    </div>
  </nav>
</div>
<!-- FEATURE HEADER -->
<div class="jumbotron df-home-header">
  <div class="background-overlay"></div>
  <div class="container">
    <h1 class="title">Welcome to Digital Freelancer!</h1>
    <p class="sub-title col-md-10 offset-md-1">
      For a limited time, <b>earn $50</b> for every friend you refer.
    </p>
    <?php the_content(); ?>
  </div>
</div>

<div class="dfhome top-products-section">
  <div class="container">
  <h5 class="sub-title">TOP PRODUCTS</h5>
  <div class="card-deck">
    <?php get_sidebar( 'topproducts' ); ?>
  </div>
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
