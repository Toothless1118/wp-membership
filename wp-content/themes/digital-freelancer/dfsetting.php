<?php
/**
 * Template Name: DF Homepage Setting Template
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
          <a class="nav-link nav-home" href="/member-dashboard">Home  <i class="fa fa-caret-right"></i></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/member-setting">Settings</a>
        </li>
      </ul>
      <!--
      <div class="form-inline btn-group saved">
        <i class="fa fa-circle"></i>Saved
      </div>
      -->
    </div>
  </nav>
</div>
<div class="wrapper dfsetting">
  <div class="container">
  <?php the_content(); ?>
  </div>
</div>
<div class="container-fluid address-section">
  <address>
    <strong>Digital Freelancer</strong><br>
    433 Broadway<br>
    New York, New York 10013
  </address>
  <div class="social pt-3 pb-3">
    <i class="fa fa-facebook-official" aria-hidden="true"></i>
    <i class="fa fa-twitter" aria-hidden="true"></i>
    <i class="fa fa-instagram" aria-hidden="true"></i>
  </div>
</div>
</div>

<? get_footer(); ?>
