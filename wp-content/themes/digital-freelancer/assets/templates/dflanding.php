<?php
/**
 * Template Name: DF Landing Page Template
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
    <h1 class="title">10x Your Freelancing Success</h1>
    <p class="sub-title">
      Everything you need to <span>&nbsp;dominate the business side&nbsp;</span> of your freelancing life. Start winning today
    </p>
    <?php the_content(); ?>
    <div class="learn-more" id="learn-more">Learn More<i class="fa fa-chevron-down" aria-hidden="true"></i></div>
  </div>
</div>

<div class="container sales-letter-section" id="sales-letter-section">
  <h2 class="title col-md-10 offset-md-1">The #1 Resource for <b>Business-Minded</b> Freelancing Knowledge, Tools and Community</h2>
  <div class="media">
    <img class="d-flex align-self-center" src="/wp-content/themes/digital-freelancer/dist/images/connor.png" alt="Generic placeholder image">
    <div class="media-body align-self-center">
      <h5 class="">Connor Black</h5>
      Founder, DigitalFreelancer.io
    </div>
  </div>

  <div class="letter">
    <p>
    Like most freelancers, I didn’t study business in school. In fact, I dropped out three years into my comp-sci degree to pursue the startup dream.</p>
    <p>
    After some highs, lows and the fire-sale of my startup, I found myself in a situation common to a lot people I know: <b>freelancing</b>.</p>
    <p>
    I told my hopelessly clueless self:</p>
    <p class="italic">
    “I’ll do this long enough until I have enough money to start my next business”.</p>
    <p>
    I charged an insultingly low rate, waited for clients to come to me, worked for almost anyone and found myself in a miserable freelancing rollarcoaster.</p>
    <p>
    It wasn’t until I realized a simple fact that things started to take off:</p>
    <p>
    I wasn’t waiting to start my next business, <b>I already was running a business</b>.</p>
    <p>
    I studied everything I could, took my hard-earned lessons from running my startup and starting bugging the crap out of some successful business owners I knew (a couple of which turned into lifelong mentors).</p>
    <p>
    Fast-forward two years and I was running a 15 person agency bringing in millions of dollars a year.</p>
    <p>
    As we grew…</p>
    <p class="">
      <ul class="">
        <li>I learned how to <b>price based on the value I brought to the table</b>.</li>
        <li>I figured out how to <b>systematically and proactively find clients</b>, rather than waiting for them to come to me.</li>
        <li>I put into place processes that ensured <b>I got paid on-time and up-front</b>.</li>
        <li>And most of all, I ended up with a <b>predictable, reliable, and stable business</b>.</li>
      </ul>
    </p>
    <p>
    Helping you avoid many of the mistakes I made so that you can become truly successful is my #1 goal here at Digital Freelancer.</p>
    <p>
    So if you’re looking to build a profitable business and avoid much of the self-help fluff that’s usually associated with freelancing advice, you’re at the right place.</p>
  </div>
  <div class="letter-button">
    <?php echo do_shortcode("[mepr-show if='loggedin']<a class='btn btn-primary btn-lg h-btn' href='/member-dashboard'>Access your Dashboard</a>[/mepr-show]");?>
    <?php echo do_shortcode('[mepr-show if="loggedout"]<a class="btn btn-primary btn-lg h-btn" href="/sign-up">Join Digital Freelancer for Free</a>[/mepr-show]');?>
  </div>
</div>
<div class="container-fluid guide-wrapper">
  <div class="container guide-and-tools-section">
    <h2 class="title"><b>Guides and Tools</b> Made for Freelancers by Freelancers</h2>
    <h5 class="sub-title ">TOP POSTS</h5>
    <?php get_sidebar( 'topposts' ); ?>
    <?php
      $top_post_cate_id = get_cat_ID("Top Post");
      $top_post_cate_link = get_category_link($top_post_cate_id);
    ?>
    <a href="/blog"  class="btn dfbtn-readmore">Read more</a>
  </div>
</div>
<div class="container top-products-section">
  <h5 class="sub-title">TOP PRODUCTS</h5>
  <div class="card-deck">
    <?php get_sidebar( 'topproducts' ); ?>
  </div>
</div>
<div class="jumbotron landing-header land-freelance-now ">
  <div  class="background-img"></div>
  <div  class="background-overlay"></div>
  <div class="container">
    <h1 class="title">10x Your Freelancing Now</h1>
    <p class="sub-title col-md-12 col-lg-10 offset-lg-1">
      Join our 1,000+ member community, where we stay up-to-date with the latest trends, tools and strategies. As a member of DF, you'll receive everything you need to 10x your freelancing success. On top of that, you'll get exclusive access to tools and 1,000+ new best friends.
    </p>
    <?php the_content(); ?>
  </div>
</div>
</div>
<div class="container-fluid address-section">
  <address>
    <p><strong>Digital Freelancer</strong></p>
    <p>433 Broadway<br>New York, New York 10013</p>
  </address>
  <div class="social">
    <i class="fa fa-facebook-official" aria-hidden="true"></i>
    <i class="fa fa-twitter" aria-hidden="true"></i>
    <i class="fa fa-instagram" aria-hidden="true"></i>
  </div>
</div>
<? get_footer(); ?>
