<?php
/**
 * Template Name: DF Blog Page Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package understrap
 */

 ?>

<? get_header(); ?>
<!-- FEATURE HEADER -->
<div class="jumbotron df-blog-header">
  <div class="background-overlay"></div>
  <div class="container">
    <div class="row">
      <div class="col-lg-6 row1">
        <h2 class="">Subscribe for Actionable Freelancing Tips</h2>
        <?php es_subbox( $namefield = "NO", $desc = "", $group = "" ); ?>
        <div class="uparrow"></div>
        <div class="media ">
          <img class="d-flex align-self-center mr-1" src="/wp-content/themes/digital-freelancer/dist/images/connor.png" alt="Generic placeholder image">
          <div class="media-body align-self-center">
            The same tips I used to grow my agency to $2M+ revenue in two years!
          </div>
        </div>
      </div>

      <div class="col-lg-6 row2 ">
        <div class="guarantee">
          <div class="guarantee-body">
            <h5>OUR GUARANTEE:</h5>
            <ul class="">
              <li>Hard hitting, "no fluff", and highly actionable articles, guides, and courses.</li>
              <li>No preference given to any particular technology or industry.</li>
              <li>A more profitable and sustainable freelancing business or agency.</li>
            </ul>
            <p class="">
              Whether through articles that captures a collective 25+ years of freelancing experience, software tools, or our freelancing community, my #1 goal is to help you become a more successful freelancer.
            </p>
          </div>
          <div class="guarantee-sign">
            <img class="" src="/wp-content/themes/digital-freelancer/dist/images/signature.png" alt="Signature image" />
          </div>
          <div class="guarantee-name">
            <p>Connor Black<br/>
            Founder, DigitalFreelancer.io</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
global $post;
$start_category_id = get_cat_ID('Starting a Freelancing Business');
$start_category_link = get_category_link($start_category_id);

?>
<div class="container-fluid blog-category-section even">
  <div class="container">
    <div class="row">
      <div class="col-lg-5 category-section">
        <h1>Starting a Freelancing Business</h1>
        <div class="content">
          Everything you need to start a freelancing business. Here you’ll learn how to get in the right mindset, find your first clients and kickstart your freelancing career.
        </div>
        <a href="<?php echo $start_category_link;?>" class="btn dfbtn-green-bloglist">View All Articles & Guides</a>
      </div>
      <div class="col-lg-7 posts-section">
        <div class="card-deck">
        <?php
        $start_args_1 = array('tag' => 'starting-a-freelancing-business-1', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $starting_query_1 = new WP_Query($start_args_1);
        if ($starting_query_1->have_posts()) {
          while ($starting_query_1->have_posts()) :
            $starting_query_1->the_post(); 
            $starting_thumbnail_1 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $starting_thumbnail_1;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        <?php
        $start_args_2 = array('tag' => 'starting-a-freelancing-business-2', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $starting_query_2 = new WP_Query($start_args_2);
        if ($starting_query_2->have_posts()) {
          while ($starting_query_2->have_posts()) :
            $starting_query_2->the_post(); 
            $starting_thumbnail_2 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $starting_thumbnail_2;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$building_category_id = get_cat_ID('Building a Sales Machine');
$building_category_link = get_category_link($building_category_id);
?>
<div class="container-fluid blog-category-section odd">
  <div class="container">
    <div class="row">
      <div class="col-lg-5 category-section">
        <h1>Building a Sales Machine</h1>
        <div class="content">
          Working in your business is very different than working ON your business. Here’s you’ll learn how to consistently find clients and make sure you never run out of work.
        </div>
        <a href="<?php echo $building_category_link;?>" class="btn dfbtn-green-bloglist">View All Articles & Guides</a>
      </div>
      <div class="col-lg-7 posts-section">
        <div class="card-deck">
        <?php
        $building_args_1 = array('tag' => 'building-a-sales-machine-1', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $building_query_1 = new WP_Query($building_args_1);
        if ($building_query_1->have_posts()) {
          while ($building_query_1->have_posts()) :
            $building_query_1->the_post(); 
            $building_thumbnail_1 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $building_thumbnail_1;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        <?php
        $building_args_2 = array('tag' => 'building-a-sales-machine-2', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $building_query_2 = new WP_Query($building_args_2);
        if ($building_query_2->have_posts()) {
          while ($building_query_2->have_posts()) :
            $building_query_2->the_post(); 
            $building_thumbnail_2 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $building_thumbnail_2;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$pricing_category_id = get_cat_ID('Pricing Strategy');
$pricing_category_link = get_category_link($pricing_category_id);
?>
<div class="container-fluid blog-category-section even">
  <div class="container">
    <div class="row">
      <div class="col-lg-5 category-section">
        <h1>Pricing Strategy</h1>
        <div class="content">
          Everything you need to start a freelancing business. Here you’ll learn how to get in the right mindset, find your first clients and kickstart your freelancing career.
        </div>
        <a href="<?php echo $pricing_category_link;?>" class="btn dfbtn-green-bloglist">View All Articles & Guides</a>
      </div>
      <div class="col-lg-7 posts-section">
        <div class="card-deck">
        <?php
        $pricing_args_1 = array('tag' => 'pricing-strategy-1', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $pricing_query_1 = new WP_Query($pricing_args_1);
        if ($pricing_query_1->have_posts()) {
          while ($pricing_query_1->have_posts()) :
            $pricing_query_1->the_post(); 
            $pricing_thumbnail_1 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $pricing_thumbnail_1;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        <?php
        $pricing_args_2 = array('tag' => 'pricing-strategy-2', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $pricing_query_2 = new WP_Query($pricing_args_2);
        if ($pricing_query_2->have_posts()) {
          while ($pricing_query_2->have_posts()) :
            $pricing_query_2->the_post(); 
            $pricing_thumbnail_2 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $pricing_thumbnail_2;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$project_category_id = get_cat_ID('Project Management Mastery');
$project_category_link = get_category_link($project_category_id);
?>
<div class="container-fluid blog-category-section odd">
  <div class="container">
    <div class="row">
      <div class="col-lg-5 category-section">
        <h1>Project Management Mastery</h1>
        <div class="content">
          Working in your business is very different than working ON your business. Here’s you’ll learn how to consistently find clients and make sure you never run out of work.
        </div>
        <a href="<?php echo $project_category_link;?>" class="btn dfbtn-green-bloglist">View All Articles & Guides</a>
      </div>
      <div class="col-lg-7 posts-section">
        <div class="card-deck">
        <?php
        $project_args_1 = array('tag' => 'project-management-mastery-1', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $project_query_1 = new WP_Query($project_args_1);
        if ($project_query_1->have_posts()) {
          while ($project_query_1->have_posts()) :
            $project_query_1->the_post(); 
            $project_thumbnail_1 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $project_thumbnail_1;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php echo the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        <?php
        $project_args_2 = array('tag' => 'project-management-mastery-2', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $project_query_2 = new WP_Query($project_args_2);
        if ($project_query_2->have_posts()) {
          while ($project_query_2->have_posts()) :
            $project_query_2->the_post(); 
            $project_thumbnail_2 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $project_thumbnail_2;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$client_category_id = get_cat_ID('Successful Client Management');
$client_category_link = get_category_link($client_category_id);
?>
<div class="container-fluid blog-category-section even">
  <div class="container">
    <div class="row">
      <div class="col-lg-5 category-section">
        <h1>Successful Client Management</h1>
        <div class="content">
          Everything you need to start a freelancing business. Here you’ll learn how to get in the right mindset, find your first clients and kickstart your freelancing career.
        </div>
        <a href="<?php echo $client_category_link;?>" class="btn dfbtn-green-bloglist">View All Articles & Guides</a>
      </div>
      <div class="col-lg-7 posts-section">
        <div class="card-deck">
        <?php
        $client_args_1 = array('tag' => 'successful-client-management-1', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $client_query_1 = new WP_Query($client_args_1);
        if ($client_query_1->have_posts()) {
          while ($client_query_1->have_posts()) :
            $client_query_1->the_post(); 
            $client_thumbnail_1 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $client_thumbnail_1;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        <?php
        $client_args_2 = array('tag' => 'successful-client-management-2', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $client_query_2 = new WP_Query($client_args_2);
        if ($client_query_2->have_posts()) {
          while ($client_query_2->have_posts()) :
            $client_query_2->the_post(); 
            $client_thumbnail_2 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $client_thumbnail_2;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$proposal_category_id = get_cat_ID('Proposals and Negotiation');
$proposal_category_link = get_category_link($proposal_category_id);
?>
<div class="container-fluid blog-category-section odd">
  <div class="container">
    <div class="row">
      <div class="col-lg-5 category-section">
        <h1>Proposals and Negotiation</h1>
        <div class="content">
          Working in your business is very different than working ON your business. Here’s you’ll learn how to consistently find clients and make sure you never run out of work.
        </div>
        <a href="<?php echo $proposal_category_link;?>" class="btn dfbtn-green-bloglist">View All Articles & Guides</a>
      </div>
      <div class="col-lg-7 posts-section">
        <div class="card-deck">
        <?php
        $proposal_args_1 = array('tag' => 'proposals-and-negotiation-1', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $proposal_query_1 = new WP_Query($proposal_args_1);
        if ($proposal_query_1->have_posts()) {
          while ($proposal_query_1->have_posts()) :
            $proposal_query_1->the_post(); 
            $proposal_thumbnail_1 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $proposal_thumbnail_1;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        <?php
        $proposal_args_2 = array('tag' => 'proposals-and-negotiation-2', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $proposal_query_2 = new WP_Query($proposal_args_2);
        if ($proposal_query_2->have_posts()) {
          while ($proposal_query_2->have_posts()) :
            $proposal_query_2->the_post(); 
            $proposal_thumbnail_2 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $proposal_thumbnail_2;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$tax_category_id = get_cat_ID('Taxes, Legal and Insurance');
$tax_category_link = get_category_link($tax_category_id);
?>
<div class="container-fluid blog-category-section even">
  <div class="container">
    <div class="row">
      <div class="col-lg-5 category-section">
        <h1>Taxes, Legal and Insurance</h1>
        <div class="content">
          Everything you need to start a freelancing business. Here you’ll learn how to get in the right mindset, find your first clients and kickstart your freelancing career.
        </div>
        <a href="<?php echo $tax_category_link;?>" class="btn dfbtn-green-bloglist">View All Articles & Guides</a>
      </div>
      <div class="col-lg-7 posts-section">
        <div class="card-deck">
        <?php
        $tax_args_1 = array('tag' => 'taxes-legal-and-insurance-1', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $tax_query_1 = new WP_Query($tax_args_1);
        if ($tax_query_1->have_posts()) {
          while ($tax_query_1->have_posts()) :
            $tax_query_1->the_post(); 
            $tax_thumbnail_1 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $tax_thumbnail_1;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        <?php
        $tax_args_2 = array('tag' => 'taxes-legal-and-insurance-2', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        $tax_query_2 = new WP_Query($tax_args_2);
        if ($tax_query_2->have_posts()) {
          while ($tax_query_2->have_posts()) :
            $tax_query_2->the_post(); 
            $tax_thumbnail_2 = get_the_post_thumbnail_url();
        ?>
          <div class="col-md-6 col-lg-6 mb-2 products-widget">
            <a href="<?php the_permalink();?>">
              <div class="card">
                <span>
                  <img  src="<?php echo $tax_thumbnail_2;?>" class="attachment-fpw_big size-fpw_big wp-post-image" alt="" srcset="" sizes="(max-width: 360px) 100vw, 360px">
                </span>
                <div class="card-block">
                  <p><?php the_title();?></p>
                </div>
              </div>
            </a>
          </div>
        <?php
          endwhile;
        }
        wp_reset_query();
        ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid blogs-button-section">
  <div class="container">
    <a href="#"  class="btn dfbtn-view-articles">View Recent Articles & Guides</a>
  </div>
</div>
<!-- <div class="container-fluid guide-wrapper blog">
  <div class="container guide-and-tools-section">
    <h5 class="sub-title">TOP POSTS</h5>
    <?php //get_sidebar( 'topposts' ); ?>
    <?php
      //$top_post_cate_id = get_cat_ID("Recent Posts");
      //$top_post_cate_link = get_category_link($top_post_cate_id);
    ?>
    <a href="<?php //echo esc_url($top_post_cate_link);?>"  class="btn dfbtn-readmore">Read more</a>
  </div>
</div> -->
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
