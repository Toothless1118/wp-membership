<?php
/**
 * Template Name: DF Freelancer Chat Landing Page Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package understrap
 */

 ?>

<? get_header(); ?>

<!-- FEATURE HEADER -->
<div class="jumbotron landing-header  chat-header">
  <div  class="background-img"></div>
  <div class="container">
    <h1 class="title-freelance">freelance</h1>
    <h1 class="title  col-md-10 offset-md-1">The Top Community for Freelancers<br> on Slack</h1>
    <p class="sub-title col-md-10 offset-md-1">
      Join 1000+ other freelancers hanging out, sharing knowledge and <br>discovering new opportunites. 
    </p>
    <?php echo do_shortcode('[mepr-show rules="155" ifallowed="show"]<a href="https://hashtagfreelance.slack.com" class="btn btn-lg dfbtn-green-chat" >Log Into Slack</a>[/mepr-show]');?>
    <?php echo do_shortcode('[mepr-hide rules="155" ifallowed="hide"]<a href="#" class="btn btn-lg dfbtn-green-chat"  data-toggle="modal" data-target="#joinModal">Join Community Now</a>[/mepr-hide]');?>

  </div>
</div>
<div class="df-wrapper chat-relationship-wrapper">
  <div class="df-container chat-relationship">
    <h1 class="col-md-10 offset-md-1">Success is the Sum of your<br> Relationships</h1>
    <div class="row">

      <div class="col-md-4 relation-item">
        <div class="relation-img-1"></div>
        <h2>HANG OUT</h2>
        <div class="relation-content">
          Take a break in your day to shoot the breeze, get tips, or just mess around.
        </div>
      </div>
      <div class="col-md-4 relation-item">
        <div class="relation-img-2"></div>
        <h2>DISCOVER OPPORTUNITIES</h2>
        <div class="relation-content">
          Find new clients and other creatives eager to partner up on projects.
        </div>
      </div>
      <div class="col-md-4 relation-item">
        <div class="relation-img-3"></div>
        <h2>&nbsp;&nbsp;SHARE KNOWLEDGE</h2>
        <div class="relation-content">
          Share your experiences and learn insider secrets from freelancing veterans.
        </div>
      </div>

    </div>
  </div>
</div>
<section class="features">
  <div class="feature-row">
    <div class="features-content">
      <div class="feature-icon pad">
        <ul>
          <li class="ion-android-contacts" data-pack="android" data-tags=""></li>
        </ul>
      </div>
      <div class="feature-message">
        <h2 class="feature-title">Find New Freelancing</h2>
        <h3 class="feature-sub-title">Job Opportunities</h3>
        <ul>
          <li>Give and receive job referrals</li>
          <li>Partner with other freelancers on projects</li>
          <li>Discover new clients in the community</li>
          <li>
            <?php echo do_shortcode('[mepr-show rules="155" ifallowed="show"]<a href="https://hashtagfreelance.slack.com" class="" >Log Into Slack</a>[/mepr-show]');?>
            <?php echo do_shortcode('[mepr-hide rules="155" ifallowed="hide"]<a href="#" class=""  data-toggle="modal" data-target="#joinModal">Join Community Now</a>[/mepr-hide]');?>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="feature-row feature-background">
    <div class="features-content">
      <div class="feature-message pad">
        <h2 class="feature-title">Join and Present In</h2>
        <h3 class="feature-sub-title">Weekly Webinars</h3>
        <ul>
          <li>Watch webinars given by experts</li>
          <li>Have the chance to pitch your services</li>
          <li>Learn &amp; share insider secrets and tactics</li>
          <li>
            <?php echo do_shortcode('[mepr-show rules="155" ifallowed="show"]<a href="https://hashtagfreelance.slack.com" class="" >Log Into Slack</a>[/mepr-show]');?>
            <?php echo do_shortcode('[mepr-hide rules="155" ifallowed="hide"]<a href="#" class=""  data-toggle="modal" data-target="#joinModal">Join Community Now</a>[/mepr-hide]');?>
          </li>
        </ul>
      </div>
      <div class="feature-icon">
        <ul>
          <li class="ion-easel" data-pack="default" data-tags=""></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="feature-row">
    <div class="features-content">
      <div class="feature-icon pad">
        <ul>
          <li class="ion-email" data-pack="default" data-tags=""></li>
        </ul>
      </div>
      <div class="feature-message">
        <h2 class="feature-title">Receive Our Freelancing</h2>
        <h3 class="feature-sub-title">Email Roundup</h3>
        <ul>
          <li>Submit articles to channels for discussion</li>
          <li>Learn &amp; share experiences from the web</li>
          <li>Receive the best-of every Friday by email</li>
          <li>
            <?php echo do_shortcode('[mepr-show rules="155" ifallowed="show"]<a href="https://hashtagfreelance.slack.com" class="" >Log Into Slack</a>[/mepr-show]');?>
            <?php echo do_shortcode('[mepr-hide rules="155" ifallowed="hide"]<a href="#" class=""  data-toggle="modal" data-target="#joinModal">Join Community Now</a>[/mepr-hide]');?>
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>
<!--
<div class="df-wrapper chat-opportunity-wrapper">
  <div class="df-container chat-opportunity">

      <div class="icon-body">
        <i class="icon ion-android-contacts"></i>
      </div>
      <div class="content-body">
        <h1 class="title-first">Find New Freelancing</h1>
        <h2 class="title-second">Job Opportunities</h2>
        <div class="opp-content">
          <ul>
            <li>Give and receive job referrals</li>
            <li>Partner with other freelancers on projects</li>
            <li>Discover new clients in the community</li>
            <li><a href="#">Join Community Now</a></li>
          </ul>
        </div>
      </div>

  </div>
</div>
<div class="df-wrapper chat-webinar-wrapper">
  <div class="df-container chat-webinar">
    <div class="row">
      <div class="col-md-4 no-padding icon-body icon-responsive">
        <i class="icon ion-easel"></i>
      </div>
      <div class="col-md-6 offset-md-2 no-padding content-body">
        <h1 class="title-first">Join and Present In</h1>
        <h2 class="title-second">Weekly Webinars</h2>
        <div class="webinar-content">
          <ul>
            <li>Watch webinars given by experts</li>
            <li>Have the chance to pitch your services</li>
            <li>Learn & share insider secrets and tactics</li>
            <li><a href="#">Join Community Now</a></li>
          </ul>
        </div>
      </div>
      <div class="col-md-4 no-padding icon-body">
        <i class="icon ion-easel"></i>
      </div>
    </div>
  </div>
</div>
<div class="df-wrapper chat-email-wrapper">
  <div class="container chat-email">
    <div class="row">
      <div class="col-md-5 no-padding icon-body">
        <i class="icon ion-email"></i>
      </div>
      <div class="col-md-6 no-padding content-body">
        <h1 class="title-first">Receive Our Freelancing</h1>
        <h2 class="title-second">Email Roundup</h2>
        <div class="email-content">
          <ul>
            <li>Submit articles to channels for discussion</li>
            <li>Learn & share experiences from the web</li>
            <li>Receive the best-of every Friday by email</li>
            <li><a href="#">Join Community Now</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
-->
<div class="df-wrapper chat-members-wrapper">
  <div class="df-container chat-members">
    <h1>What Members Are Saying</h1>
    <div class="row pics">
      <div id="img-4" class="userpic col-df-20">
        <img src="/wp-content/themes/digital-freelancer/dist/images/userpic1.png" alt="" />
        <p>Yuliya Petrova</p>
      </div>
      <div id="img-0" class="userpic col-df-20">
        <img src="/wp-content/themes/digital-freelancer/dist/images/userpic2.png" alt="" />
        <p>Nikita Markov</p>
      </div>
      <div id="img-1" class="userpic col-df-20">
        <img src="/wp-content/themes/digital-freelancer/dist/images/userpic3.png" alt="" />
        <p>Alexey Rybin</p>
      </div>
      <div id="img-2" class="userpic col-df-20">
        <img src="/wp-content/themes/digital-freelancer/dist/images/userpic4.png" alt="" />
        <p>Artem Tarasov</p>
      </div>
      <div id="img-3" class="userpic col-df-20">
        <img src="/wp-content/themes/digital-freelancer/dist/images/userpic5.png" alt="" />
        <p>Yuri Yasyuk</p>
      </div>
    </div>
  </div>

  <div id="chatmembercarousel" class="chat-carousel carousel slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">
      <div class="carousel-item ">
        <div class="textwidget">
          <div class="jumbotron  jumbotron-fluid text-black">
            <div class="df-container">
              <h2 class="display-4">My membership has paid for itself 100x over</h2>
              <p class="sub-content">“The relationships and networks I’ve been able to build through this <br/>community have helped me take my freelancing business to the next level.“ </p>
              <p class="name">— Yuliya Petrova, Freelance Web Developer</p>
            </div>
          </div>
        </div>
      </div>
      <div class="carousel-item ">
        <div class="textwidget">
          <div class="jumbotron  jumbotron-fluid text-black">
            <div class="df-container">
              <h2 class="display-4">My membership has paid for itself 100x over</h2>
              <p class="sub-content">“The relationships and networks I’ve been able to build through this <br/>community have helped me take my freelancing business to the next level.“ </p>
              <p class="name">— Nikita Markov, Freelance Web Developer</p>
            </div>
          </div>
        </div>
      </div>
      <div class="carousel-item ">
        <div class="textwidget">
          <div class="jumbotron  jumbotron-fluid text-black">
            <div class="df-container">
              <h2 class="display-4">My membership has paid for itself 100x over</h2>
              <p class="sub-content">“The relationships and networks I’ve been able to build through this <br/>community have helped me take my freelancing business to the next level.“ </p>
              <p class="name">— Alexey Rybin, Freelance Web Developer</p>
            </div>
          </div>
        </div>
      </div>
      <div class="carousel-item ">
        <div class="textwidget">
          <div class="jumbotron  jumbotron-fluid text-black">
            <div class="df-container">
              <h2 class="display-4">My membership has paid for itself 100x over</h2>
              <p class="sub-content">“The relationships and networks I’ve been able to build through this <br/>community have helped me take my freelancing business to the next level.“ </p>
              <p class="name">— Artem Tarasov, Freelance Web Developer</p>
            </div>
          </div>
        </div>
      </div>
      <div class="carousel-item ">
        <div class="textwidget">
          <div class="jumbotron  jumbotron-fluid text-black">
            <div class="df-container">
              <h2 class="display-4">My membership has paid for itself 100x over</h2>
              <p class="sub-content">“The relationships and networks I’ve been able to build through this <br/>community have helped me take my freelancing business to the next level.“ </p>
              <p class="name">— Gabriel Ferrin, Freelance Web Developer</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <a class="carousel-control-prev chatmembercarouselprev" href="javascript: void(0);" role="button" data-slide="prev">
      <i class="fa fa-angle-left prev"></i>
    </a>

    <a class="carousel-control-next chatmembercarouselnext" href="javascript: void(0);" role="button" data-slide="next">
      <i class="fa fa-angle-right next"></i>
    </a>
  </div>
</div>
<section class="application">
  <div class="application-content">
  <h2 class="application-title">It’s the Modern Way to Network</h2>
  <h3 class="application-sub-title">Form networks and friendships with freelancers across the globe.</h3>
  <img alt="Freelance slack channel" class="desktop-device-pic" src="/wp-content/themes/digital-freelancer/dist/images/browser.png">
  <img alt="Freelance slack channel" class="mobile-device-pic" src="/wp-content/themes/digital-freelancer/dist/images/mobile.png"></div>
</section>
<!-- <div class="df-wrapper chat-modern-way-wrapper">
  <div class="container chat-modern-way">
      <h1 class="">It’s the Modern Way to Network</h1>
      <h3 class="">Form networks and friendships with freelancers across the globe.</h3>
      <div class="mordern-way-container">
        <img src="/wp-content/themes/digital-freelancer/dist/images/browser.png" alt="" />
      </div>
  </div>
</div> -->
<div class="jumbotron landing-header  chat-footer-jumbortrom">
  <div  class="background-img"></div>
  <div class="df-container">
    <h1 class="title">Join #freelance for $49/year</h1>
    <p class="sub-title col-md-8 offset-md-2">
      <i class="fa fa-lock"></i>&nbsp;&nbsp;&nbsp;100% Money-Back Guarantee
    </p>
    <?php echo do_shortcode('[mepr-show rules="155" ifallowed="show"]<a href="https://hashtagfreelance.slack.com" class="btn btn-lg dfbtn-green-chat" >Log Into Slack</a>[/mepr-show]');?>
    <?php echo do_shortcode('[mepr-hide rules="155" ifallowed="hide"]<a href="#" class="btn btn-lg dfbtn-green-chat"  data-toggle="modal" data-target="#joinModal">Join Community Now</a>[/mepr-hide]');?>
  </div>
</div>

<!-- Modal -->
<div class="df-wrapper chat-register-modal-wrapper">
  <!-- New Modal -->
  <div aria-hidden="true" aria-labelledby="myModalLabel" class="modal fade in" id="joinModal" role="dialog" tabindex="-1" style="padding-right: 15px;" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="loader" style="display:none;"></div>
          <!-- modal header -->
          <div class="modal-header">
            <button class="close" data-dismiss="modal" type="button">×</button>
            <div class="signup-wizard">
              <div class="step-container">
                <div class="circle first-step active-step">
                  <svg height="21" version="1.1" viewBox="0 0 21 21" width="21" xmlns="https://www.w3.org/2000/svg">
                  <g fill="none" fill-rule="evenodd" stroke="none" stroke-width="1">
                    <g class="user-icon active-icon" fill="#E6E6E6" transform="translate(-173.000000, -64.000000)">
                      <g transform="translate(153.000000, 45.000000)">
                        <path d="M31.2 30.2C34.3 30.2 36.8 27.7 36.8 24.6 36.8 21.5 34.3 19 31.2 19 28.1 19 25.6 21.5 25.6 24.6 25.6 27.7 28.1 30.2 31.2 30.2L31.2 30.2ZM30.5 33C27 33 20 34.6 20 37.7L20 40 41 40 41 37.7C41 34.6 34 33 30.5 33L30.5 33Z"></path>
                      </g>
                    </g>
                  </g></svg>
                </div>
                <div>
                  <h3 class="step-title">First step</h3><span class="step-sub-title">Profile Info</span>
                </div>
              </div>
              <div class="step-divider">
                <hr>
              </div>
              <div class="step-container">
                <div class="circle second-step">
                  <svg height="16" version="1.1" viewBox="0 0 20 16" width="20" xmlns="https://www.w3.org/2000/svg">
                  <g fill="none" fill-rule="evenodd" stroke="none" stroke-width="1">
                    <g class="card-icon" fill="#E6E6E6" transform="translate(-582.000000, -67.000000)">
                      <g transform="translate(153.000000, 45.000000)">
                        <g transform="translate(409.000000, 0.000000)">
                          <g transform="translate(20.000000, 22.000000)">
                            <path d="M18 0L2 0C0.9 0 0 0.9 0 2L0 14C0 15.1 0.9 16 2 16L18 16C19.1 16 20 15.1 20 14L20 2C20 0.9 19.1 0 18 0L18 0ZM18 14L2 14 2 8 18 8 18 14 18 14ZM18 4L2 4 2 2 18 2 18 4 18 4Z"></path>
                          </g>
                        </g>
                      </g>
                    </g>
                  </g></svg>
                </div>
                <div>
                  <h3 class="step-title">Second step</h3><span class="step-sub-title">Payment Methods</span>
                </div>
              </div>
            </div>
          </div>
          <!-- end modal header -->
          <!-- modal body -->
          <div class="modal-body">
            <div class="form-container">
              <?php echo the_content();?>
            </div>
          </div>
          <!-- end modal body -->
      </div>
    </div>
  </div>
  <!-- end New Modal --> 
</div>
  
<? //get_footer('chat'); ?>
<? get_footer(); ?>