<?php
/**
 * Template Name: DF Pipeline Page Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package understrap
 */

 ?>

<? get_header(); ?>

<!-- FEATURE HEADER -->
<section class="jumbotron landing-header pipeline-landing-header">
  <div  class="background-img"></div>
  <div  class="background-overlay"></div>
  <div class="container">
    <div class="pipeline-header">
      <ul>
        <li class="pipeline-logo-left"><a href="#" id="pipeline-header-features">Features</a></li>
        <li class="pipeline-logo-left"><a href="#" id="pipeline-header-who">Who It's For</a></li>
        <li class="pipeline-logo"><a href="#"><img src="/wp-content/themes/digital-freelancer/dist/images/pipeline-logo.png"/></a></li>
        <li class="pipeline-logo-right"><a href="#" id="pipeline-header-testimonials">Testimonials</a></li>
        <li class="pipeline-logo-right"><a href="#" id="pipeline-header-pricing">Pricing</a></li>
      </ul>
    </div>
    <h1 class="title">Hand-Picked Freelancing Leads</h1>
    <p class="sub-title col-md-8 offset-md-2">
      Forget dry-spells. Start building a steady freelancing <br/>business.
    </p>
    <?php echo do_shortcode('[mepr-show rules="156,157" ifallowed="show"]<a href="/pipeline/app" class="btn dfbtn-red-pipeline btn-lg" >Launch Pipeline</a>[/mepr-show]');?>
    <?php echo do_shortcode('[mepr-hide rules="156,157" ifallowed="hide"]<button class="btn dfbtn-red-pipeline btn-lg"  data-toggle="modal" data-target="#joinPipelineModal">Join for Free</button>[/mepr-hide]');?>
    
    <div class="learn-more" id="pipeline-learn-more">Learn More<i class="fa fa-chevron-down" aria-hidden="true"></i></div>
  </div>
</section>
<section class="df-wrapper piepline-top-companies">
  <div class="df-container">
    <h1>GET LEADS FROM TOP COMPANIES</h1>
    <div class="divider"><hr></div>
    <ul>
      <li><img class="hp" src="/wp-content/themes/digital-freelancer/dist/images/Shape-hp.png"/></li>
      <li><img class="amazon" src="/wp-content/themes/digital-freelancer/dist/images/Shape-amazon.png"/></li>
      <li><img class="disney" src="/wp-content/themes/digital-freelancer/dist/images/Shape-disney.png"/></li>
      <li><img class="airbnb" src="/wp-content/themes/digital-freelancer/dist/images/Shape-airbnb.png"/></li>
      <li><img class="adidas" src="/wp-content/themes/digital-freelancer/dist/images/Shape-adidas.png"/></li>
    </ul>
  </div>
</section>
<section class="df-wrapper pipeline-features" id="pipeline-features">
  <div class="df-container">
    <h5>Features</h5>
    <h1>Boost Your Freelancing Business</h1>
    <div class="row">
      <div class="col-lg-4 features-block">
        <img src="/wp-content/themes/digital-freelancer/dist/images/pf1.png"/>
        <h3>Hand-Picked Leads</h3>
        <p>Not every lead is a winner. We personally screen each lead by hand and make sure it’s a high value prospect before sending it your way.</p>
      </div>
      <div class="col-lg-4 features-block">
        <img src="/wp-content/themes/digital-freelancer/dist/images/pf2.png"/>
        <h3>"All In One" Job Board</h3>
        <p>Great clients are all over the web, not on any one site. We monitor 300+ job boards (saving you countless hours) to find projects that are a perfect match.</p>
      </div>
      <div class="col-lg-4 features-block">
        <img src="/wp-content/themes/digital-freelancer/dist/images/pf3.png"/>
        <h3>Best Practice Sales Strategy</h3>
        <p>Freelancing is hard enough. Use our proven emails from our template library and follow our sales steps to close more deals.</p>
      </div>
    </div>
  </div>
</section>
<section class="jumbotron landing-header pipeline-how-it-works"  id="pipeline-how-it-works">
  <div  class="background-img"></div>
  <div  class="background-overlay"></div>
  <div class="container">
    <h5>How it Works</h5>
    <h1 class="title">Let Qualified Leads <span>Come to You</span></h1>
    <img class="sub-image" src="/wp-content/themes/digital-freelancer/dist/images/pipeline-example-app.png"/>
  </div>
</section>
<section class="df-wrapper pipeline-feature-blocks" id="feature-block">
  <div class="feature-row">
    <h5>Feature #1</h5>
    <div class="features-content">
      <div class="feature-icon pad">
        <img src="/wp-content/themes/digital-freelancer/dist/images/features-image.png"/>
      </div>
      <div class="feature-message r-m">
        <h2 class="feature-title"><i class="ion-search"></i>Hand-Picked Leads</h2>
        <p>Stop scouring job boards and spend more time freelancing. Wake up every morning to hand-picked leads based on your skill-set.</p>
        <?php echo do_shortcode('[mepr-show rules="156,157" ifallowed="show"]<a href="/pipeline/app" class="btn dfbtn-blue-pipeline btn-lg" >Launch Pipeline</a>[/mepr-show]');?>
        <?php echo do_shortcode('[mepr-hide rules="156,157" ifallowed="hide"]<button class="btn dfbtn-blue-pipeline btn-lg"  data-toggle="modal" data-target="#joinPipelineModal">Join for Free</button>[/mepr-hide]');?>
      </div>
    </div>
  </div>
  <div class="feature-row feature-background">
    <h5>Feature #2</h5>
    <div class="features-content">
      <div class="feature-message pad">
        <h2 class="feature-title"><i class="fa fa-file-text-o"></i>Template Library</h2>
        <p>Stop struggling to write your emails. Use our constantly updated template library that have won over 200 clients and counting.</p>
        <?php echo do_shortcode('[mepr-show rules="156,157" ifallowed="show"]<a href="/pipeline/app" class="btn dfbtn-blue-pipeline btn-lg" >Launch Pipeline</a>[/mepr-show]');?>
        <?php echo do_shortcode('[mepr-hide rules="156,157" ifallowed="hide"]<button class="btn dfbtn-blue-pipeline btn-lg"  data-toggle="modal" data-target="#joinPipelineModal">Join for Free</button>[/mepr-hide]');?>
      </div>
      <div class="feature-icon">
        <img src="/wp-content/themes/digital-freelancer/dist/images/features-image.png"/>
      </div>
    </div>
  </div>
  <div class="feature-row">
    <h5>Feature #3</h5>
    <div class="features-content">
      <div class="feature-icon pad">
        <img src="/wp-content/themes/digital-freelancer/dist/images/features-image.png"/>
      </div>
      <div class="feature-message r-m">
        <h2 class="feature-title"><i class="fa fa-random"></i>Sales Steps</h2>
        <p>On average, 44% of freelancers give up after one follow-up, yet 80% of deals require five. Our sales steps make that easy.</p>
        <?php echo do_shortcode('[mepr-show rules="156,157" ifallowed="show"]<a href="/pipeline/app" class="btn dfbtn-blue-pipeline btn-lg" >Launch Pipeline</a>[/mepr-show]');?>
        <?php echo do_shortcode('[mepr-hide rules="156,157" ifallowed="hide"]<button class="btn dfbtn-blue-pipeline btn-lg"  data-toggle="modal" data-target="#joinPipelineModal">Join for Free</button>[/mepr-hide]');?>
      </div>
    </div>
  </div>
  <div class="feature-row feature-background">
    <h5>Feature #4</h5>
    <div class="features-content">
      <div class="feature-message pad">
        <h2 class="feature-title"><i class="fa fa-bell-o"></i>Realtime Alerts</h2>
        <p>Opportunities are posted at all hours of the day. We monitor job boards in real-time and send you alerts in Slack and email as they come.</p>
        <?php echo do_shortcode('[mepr-show rules="156,157" ifallowed="show"]<a href="/pipeline/app" class="btn dfbtn-blue-pipeline btn-lg" >Launch Pipeline</a>[/mepr-show]');?>
        <?php echo do_shortcode('[mepr-hide rules="156,157" ifallowed="hide"]<button class="btn dfbtn-blue-pipeline btn-lg"  data-toggle="modal" data-target="#joinPipelineModal">Join for Free</button>[/mepr-hide]');?>
      </div>
      <div class="feature-icon">
        <img src="/wp-content/themes/digital-freelancer/dist/images/features-image.png"/>
      </div>
    </div>
  </div>
</section>
<section class="df-wrapper pipeline-testimonials" id="pipeline-testimonials">
  <h5>Testimonials</h5>
  <h1>People Who Love Pipeline</h1>
  <div id="pipelinecarousel" class="pipeline-carousel carousel slide" data-ride="carousel">
   
    <div class="carousel-inner" role="listbox">
      <div class="carousel-item  carousel-item-left">
        <div class="textwidget">


              <div class="media">
                <img class="d-flex align-self-center" src="/wp-content/themes/digital-freelancer/dist/images/pipline-testimonials.png" alt="">
                <div class="media-body align-self-center">
                  <h5 class="">“Just started using awesome Module. Great way to boost the hard designing or prototyping process. Also a perfect tool for creative studios and freelancers”</h5>
                  <p class="name">Irina Bykova</p>
                  <p class="job">Photographer, works with Slack team</p>
                </div>
              </div>


        </div>
      </div>
      <div class="carousel-item  carousel-item-left">
        <div class="textwidget">


              <div class="media">
                <img class="d-flex align-self-center" src="/wp-content/themes/digital-freelancer/dist/images/pipline-testimonials.png" alt="">
                <div class="media-body align-self-center">
                  <h5 class="">“Just started using awesome Module. Great way to boost the hard designing or prototyping process. Also a perfect tool for creative studios and freelancers”</h5>
                  <p class="name">Irina Bykova</p>
                  <p class="job">Photographer, works with Slack team</p>
                </div>
              </div>

        </div>
      </div>
    </div>  


    <a class="carousel-control-prev pipecarouselprev" href="javascript: void(0);" role="button" data-slide="prev">
      <i class="fa fa-angle-left prev"></i>
    </a>

    <a class="carousel-control-next pipecarouselnext" href="javascript: void(0);" role="button" data-slide="next">
      <i class="fa fa-angle-right next"></i>
    </a>
  </div>
</section>
<section class="df-wrapper pipeline-price-group" id="pipeline-price-group">
  <div class="df-container">
    <h5>Pricing</h5>
    <h1>Simple, No Contact Pricing</h1>
    <div class="price-group">
      <div class="light-price">
        <h3>LIGHT</h3>
        <h4>For solo and new freelancers</h4>
        <hr>
        <div>
          <p class="price">$97</p>
          <p class="period">per month</p>
          <p class="description">Computer War Games How To Estimate Decisions Made By C C Trainees</p>
        </div>
        <hr>
        <div>
          <ul>
            <li>8 Gb of space</li>
            <li>60 days of file recovery</li>
            <li>MS Office 365 integration</li>
            <li>Unlimited third-party integrations</li>
            <li class="disable">Remote wipe</li>
          </ul>
          <button class="btn dfbtn-white-pipeline-price"  data-toggle="modal" data-target="#joinPipelineModal">Get Started</button>
        </div>
      </div>
      <div class="pro-price">
        <h3>PROFESSINAL</h3>
        <h4>For experienced and agency freelancers</h4>
        <hr>
        <div>
          <p class="price">$197</p>
          <p class="period">per month</p>
          <p class="description">Direct Mail Advertising How I Made 47 325 In 30 Days By Mailing</p>
        </div>
        <hr>
        <div>
          <ul>
            <li>30 Gb of space</li>
            <li>90 days of file recovery</li>
            <li>MS Office 365 integration</li>
            <li>Unlimited third-party integrations</li>
            <li>Remote wipe</li>
          </ul>
          <button class="btn dfbtn-red-pipeline-price btn-lg" data-toggle="modal" data-target="#joinPipelineModal">Get Started</button>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="df-wrapper pipeline-faq">
  <div class="df-pipe-container">
    <h5>FAQ</h5>
    <h1>Frequently Asked Questions</h1>
    <div class="row">
      <div class="col-lg-6 col-md-12">
        <div class="faq-block">
          <h3>Advanced design</h3>
          <p>Canary is elegant enough to enhance almost any room, and understated enough to go unnoticed by intruders</p>
        </div>
        <div class="faq-block">
          <h3>See and hear your home</h3>
          <p>Stream real-time video of your home, and receive notifications whenever motion is detected, with Canary’s 1080p HD camera, 147-degree wide-angle lens, automatic night vision, and high-quality audio.</p>
        </div>
        <div class="faq-block">
          <h3>Protect your home at all times</h3>
          <p>No need to worry about daytime or nighttime settings. Canary’s night vision is automatically activated by low-light situations.</p>
        </div>
      </div>
      <div class="col-lg-6 col-md-12">
        <div class="faq-block">
          <h3>More Than a Camera</h3>
          <p>Protect your home with Canary’s 90+ dB siren, motion-activated recording, auto-arm/disarm, and exclusive air quality, temperature, and humidity monitoring.</p>
        </div>
        <div class="faq-block">
          <h3>Superior Value</h3>
          <p>There are no installation or monthly fees and you don't need additional sensors, base stations, or cameras. </p>
        </div>
        <div class="faq-block">
          <h3>Multiple users</h3>
          <p>Everyone in your home can download the app and check on your Canary, with no additional fees.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="df-wrapper pipeline-buddy">
  <div class="df-container">
    <h5>HEY, BUDDY!</h5>
    <h1>Get Your Hand-Picked Leads Today</h1>
    <?php echo do_shortcode('[mepr-show rules="156,157" ifallowed="show"]<a href="/pipeline/app" class="btn dfbtn-red-pipeline-1 btn-lg" >Launch Pipeline</a>[/mepr-show]');?>
    <?php echo do_shortcode('[mepr-hide rules="156,157" ifallowed="hide"]<button class="btn dfbtn-red-pipeline-1 btn-lg"  data-toggle="modal" data-target="#joinPipelineModal">Join for Free</button>[/mepr-hide]');?>
  </div>
</section>
<section class="container-fluid address-section">
  <address>
    <p><strong>Digital Freelancer</strong></p>
    <p>433 Broadway<br>New York, New York 10013</p>
  </address>
  <div class="social">
    <i class="fa fa-facebook-official" aria-hidden="true"></i>
    <i class="fa fa-twitter" aria-hidden="true"></i>
    <i class="fa fa-instagram" aria-hidden="true"></i>
  </div>
</section>
<!-- Modal -->
<div class="df-wrapper pipeline-register-modal-wrapper">
  <!-- New Modal -->
  <div aria-hidden="true" aria-labelledby="myModalLabel" class="modal fade in" id="joinPipelineModal" role="dialog" tabindex="-1" style="padding-right: 15px;" data-backdrop="static">
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
                  <h3 class="step-title">First step</h3><span class="step-sub-title">Sign Up</span>
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
                  <h3 class="step-title">Second step</h3><span class="step-sub-title">Plan Selection</span>
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
<? get_footer(); ?>
