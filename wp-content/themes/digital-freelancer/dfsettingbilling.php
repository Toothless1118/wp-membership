<?php
/**
 * Template Name: DF Homepage Setting Billing Template
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
        <li class="nav-item">
          <a class="nav-link" href="/members-setting">Settings</a>
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
<div class="wrapper">
  <div class="container">
    <div class="settings-nav pt-5 pb-3">
      <a href="/members-setting">
      <div class="media ">
        <div class="ico-wrapper d-flex align-self-center mr-3">
          <i class="fa fa-user-o"></i>
        </div>
        <div class="media-body align-self-center">
          <h5 class="mt-0">My Profile</h5>
          Your main account settings
        </div>
      </div>
      </a>
      <div class="media active">
        <div class="ico-wrapper d-flex align-self-center mr-3">
          <i class="fa fa-shopping-cart"></i>
        </div>
        <div class="media-body align-self-center">
          <h5 class="mt-0">Billing and Subscriptions</h5>
          Configure your billing info
        </div>
      </div>
      <div class="media">
        <div class="ico-wrapper d-flex align-self-center mr-3">
          <i class="fa fa-support"></i>
        </div>
        <div class="media-body align-self-center">
          <h5 class="mt-0">Support</h5>
          Do you need help?
        </div>
      </div>
    </div>

    <div class="settings-input pt-5 pb-5 col-sm-10 offset-sm-1">

      <label for="basic-url">Billing Address</label>
      <div class="input-group setting-btn">
        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" placeholder="1234 Street Way">
      </div>
      <br>
      <div class="input-group setting-btn">
        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" placeholder="APT #4">
      </div>
      <br>
      <div class="row">
        <div class="col-4">
          <div class="input-group setting-btn">
            <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" placeholder="New York">
          </div>
        </div>
        <div class="col-4">
          <div class="input-group setting-btn">
            <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" placeholder="NY">
          </div>
        </div>
        <div class="col-4">
          <div class="input-group setting-btn">
            <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" placeholder="United States">
          </div>
        </div>
      </div>
      
      <br>
      <label class="pt-2" for="basic-url">Payment Method</label>
      <div class="input-group setting-btn">
        <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" placeholder="4444*******">
        <button class="btn dfbtn-green">Update</button>
      </div>
      <br>

    </div>

    <div class="settings-description pt-3 pb-5 col-sm-10 offset-sm-1">
      <p>Subscriptions</p>
      <p>
        <ul>
          <li>Pipeline Professional - !$197/mo</li>
        </ul>
      </p>
      <p>To cancel a subscription please email subscriptions@digitalfreelancer.io</p>
    </div>

  </div>
</div>
<div class="container-fluid address-section pt-5 pb-4">
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
 <?php //the_content(); ?>
<? get_footer(); ?>
