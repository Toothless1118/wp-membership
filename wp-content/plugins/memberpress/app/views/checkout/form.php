<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>
<?php
//dorin's add - getting Basic free plan
//$free_product = new MeprProduct(55);

?>
  <?php  
    switch ($product->signup_form_style) {
      case 'style1':
        # code...
  ?>
  
  <form class="mepr-signup-form mepr-form form-inline justify-content-center" method="post" action="<?php echo $_SERVER['REQUEST_URI'].'#mepr_errors'; ?>" novalidate>
    <input type="hidden" id="mepr_process_signup_form" name="mepr_process_signup_form" value="Y" />
    <input type="hidden" id="mepr_product_id" name="mepr_product_id" value="<?php echo $product->ID; ?>" />

    <?php if(MeprUtils::is_user_logged_in()): ?>
      <input type="hidden" name="logged_in_purchase" value="1" />
    <?php endif; ?>

    <?php if( ($product->register_price_action != 'hidden') && MeprHooks::apply_filters('mepr_checkout_show_terms',true) ): ?>
      <!-- dorin's change
      <div class="mp-form-row mepr_bold mepr_price">
        <?php $price_label = ($product->is_one_time_payment() ? _x('Price:', 'ui', 'memberpress') : _x('Terms:', 'ui', 'memberpress')); ?>
        <label><?php echo $price_label; ?></label>
        <div class="mepr_price_cell">
          <?php MeprProductsHelper::display_invoice( $product, $mepr_coupon_code ); ?>
        </div>
      </div>
      -->
    <?php endif; ?>

    <?php MeprHooks::do_action('mepr-checkout-before-name', $product->ID); ?>
    <?php
    if((!MeprUtils::is_user_logged_in() ||
              (MeprUtils::is_user_logged_in() && $mepr_options->show_fields_logged_in_purchases)) &&
             $mepr_options->show_fname_lname): ?>
      <div class="mp-form-row mepr_first_name">
        <div class="mp-form-label">
          <label><?php _ex('First Name:', 'ui', 'memberpress'); echo ($mepr_options->require_fname_lname)?'*':''; ?></label>
          <span class="cc-error"><?php _ex('First Name Required', 'ui', 'memberpress'); ?></span>
        </div>
        <input type="text" name="user_first_name" id="user_first_name" class="mepr-form-input" value="<?php echo $first_name_value; ?>" <?php echo ($mepr_options->require_fname_lname)?'required':''; ?> />
      </div>
      <div class="mp-form-row mepr_last_name">
        <div class="mp-form-label">
          <label><?php _ex('Last Name:', 'ui', 'memberpress'); echo ($mepr_options->require_fname_lname)?'*':''; ?></label>
          <span class="cc-error"><?php _ex('Last Name Required', 'ui', 'memberpress'); ?></span>
        </div>
        <input type="text" name="user_last_name" id="user_last_name" class="mepr-form-input" value="<?php echo $last_name_value; ?>" <?php echo ($mepr_options->require_fname_lname)?'required':''; ?> />
      </div>
    <?php else: /* this is here to avoid validation issues */ ?>
      <input type="hidden" name="user_first_name" id="user_first_name" value="<?php echo $first_name_value; ?>" />
      <input type="hidden" name="user_last_name" id="user_last_name" value="<?php echo $last_name_value; ?>" />
    <?php endif; ?>

    <?php
      if(MeprUtils::is_user_logged_in() && $mepr_options->show_fields_logged_in_purchases) {
        MeprUsersHelper::render_custom_fields($product);
      }
      elseif(!MeprUtils::is_user_logged_in()) { // We only pass the 'signup' flag on initial Signup
        MeprUsersHelper::render_custom_fields($product, true);
      }
    ?>

    <?php if(MeprUtils::is_user_logged_in()): ?>
      <input type="hidden" name="user_email" id="user_email" value="<?php echo stripslashes($mepr_current_user->user_email); ?>" />
    <?php else: ?>
      <input type="hidden" class="mepr-geo-country" name="mepr-geo-country" value="" />

      <?php if(!$mepr_options->username_is_email): ?>
        <div class="mp-form-row mepr_username">
          <div class="mp-form-label">
            <label><?php _ex('Username:*', 'ui', 'memberpress'); ?></label>
            <span class="cc-error"><?php _ex('Invalid Username', 'ui', 'memberpress'); ?></span>
          </div>
          <input type="text" name="user_login" id="user_login" class="mepr-form-input" value="<?php echo (isset($user_login))?esc_attr(stripslashes($user_login)):''; ?>" required />
        </div>
      <?php endif; ?>
      <!-- dorin's change remove label -->
        <input type="email" name="user_email" id="user_email" class="cc-error form-control " value="<?php echo (isset($user_email))?esc_attr(stripslashes($user_email)):''; ?>" required placeholder="E-mail"/>
      <?php MeprHooks::do_action('mepr-after-email-field'); //Deprecated ?>
      <?php MeprHooks::do_action('mepr-checkout-after-email-field', $product->ID); ?>

        <!-- dorin's change remove label -->
        <input type="password" name="mepr_user_password" id="mepr_user_password" class="mepr-form-input mepr-password form-control" value="<?php echo (isset($mepr_user_password))?esc_attr(stripslashes($mepr_user_password)):''; ?>" required placeholder="Password"/>


        <!-- dorin's change remove label -->
        <input type="hidden" name="mepr_user_password_confirm" id="mepr_user_password_confirm" class="mepr-form-input mepr-password-confirm btn btn-primary btn-lg col-lg-2 col-sm-8" value="<?php echo (isset($mepr_user_password_confirm))?esc_attr(stripslashes($mepr_user_password_confirm)):''; ?>" required />

      <?php MeprHooks::do_action('mepr-after-password-fields'); //Deprecated ?>
      <?php MeprHooks::do_action('mepr-checkout-after-password-fields', $product->ID); ?>
    <?php endif; ?>

    <?php MeprHooks::do_action('mepr-before-coupon-field'); //Deprecated ?>
    <?php MeprHooks::do_action('mepr-checkout-before-coupon-field', $product->ID); ?>

    <?php MeprHooks::do_action('mepr-user-signup-fields'); //Deprecated ?>
    <?php MeprHooks::do_action('mepr-checkout-before-submit', $product->ID); ?>


    <input type="submit" class="mepr-submit  btn btn-primary btn-lg " value="<?php echo stripslashes($product->signup_button_text); ?>" />
    <img src="<?php echo admin_url('images/loading.gif'); ?>" style="display: none;" class="mepr-loading-gif" />
    <?php //MeprView::render('/shared/has_errors', get_defined_vars()); ?>
  </form>
  <?php
        break;
      case 'style2':
        # code...
  ?>
  
  <form class="mepr-signup-form mepr-form form-inline justify-content-center" method="post" action="<?php echo $_SERVER['REQUEST_URI'].'#mepr_errors'; ?>" novalidate>
    <input type="hidden" id="mepr_process_signup_form" name="mepr_process_signup_form" value="Y" />
    <input type="hidden" id="mepr_product_id" name="mepr_product_id" value="<?php echo $product->ID; ?>" />
    <input type="hidden" id="df_redirct_url" name="df_redirct_url" value="free" />
    <?php if(MeprUtils::is_user_logged_in()): ?>
      <input type="hidden" name="logged_in_purchase" value="1" />
    <?php endif; ?>

    <?php MeprHooks::do_action('mepr-checkout-before-name', $product->ID); ?>

    <!-- carousel -->
    <div id="carouselExampleControls1" class="register-carousel" data-ride="carousel">
      <div class="carousel-inner" role="listbox">
        <!-- step1 carousel-item -->
        <div class="carousel-item p1 <?php echo (MeprUtils::is_user_logged_in())? '': 'active';?>">
          <div class="header-error">
            <span></span>
          </div>
        <?php if(MeprUtils::is_user_logged_in()): ?>
          <p><input type="text" name="user_first_name" id="user_first_name" class="input " placeholder="First Name" value="<?php echo $first_name_value; ?>" <?php echo ($mepr_options->require_fname_lname)?'required':''; ?> readonly /></p>

          <p><input type="text" name="user_last_name" id="user_last_name" class="input" placeholder="Last Name" value="<?php echo $last_name_value; ?>" <?php echo ($mepr_options->require_fname_lname)?'required':''; ?> readonly /></p>

        
          <p><input type="email" name="user_email" id="user_email" class="input" placeholder="E-mail" value="<?php echo (isset($mepr_current_user->user_email))?esc_attr(stripslashes($mepr_current_user->user_email)):''; ?>" required readonly /></p>
        <?php else: ?>
          <p><input type="text" name="user_first_name" id="user_first_name" class="input " placeholder="First Name" value="<?php echo $first_name_value; ?>" <?php echo ($mepr_options->require_fname_lname)?'required':''; ?> /></p>

          <p><input type="text" name="user_last_name" id="user_last_name" class="input" placeholder="Last Name" value="<?php echo $last_name_value; ?>" <?php echo ($mepr_options->require_fname_lname)?'required':''; ?> /></p>
          <p><input type="email" name="user_email" id="user_email" class="input" placeholder="E-mail" value="<?php echo (isset($user_email))?esc_attr(stripslashes($user_email)):''; ?>" required /></p>
        <?php endif; ?>
          

          <?php MeprHooks::do_action('mepr-after-email-field'); //Deprecated ?>
          <?php MeprHooks::do_action('mepr-checkout-after-email-field', $product->ID); ?>
          <?php
            if(MeprUtils::is_user_logged_in() && $mepr_options->show_fields_logged_in_purchases) {
              MeprUsersHelper::render_custom_fields($product);
            }
            elseif(!MeprUtils::is_user_logged_in()) { // We only pass the 'signup' flag on initial Signup
              MeprUsersHelper::render_custom_fields($product, true);
            }
          ?>
          <?php if(MeprUtils::is_user_logged_in()): ?>
          <p><input type="password" name="mepr_user_password" id="mepr_user_password" placeholder="Password" class="input mepr-password" value="<?php echo (isset($mepr_user_password))?esc_attr(stripslashes($mepr_user_password)):''; ?>" required readonly/></p>
          <?php else: ?>
          <p><input type="password" name="mepr_user_password" id="mepr_user_password" placeholder="Password" class="input mepr-password" value="<?php echo (isset($mepr_user_password))?esc_attr(stripslashes($mepr_user_password)):''; ?>" required /></p>
        <?php endif; ?>
          <input type="hidden" name="mepr_user_password_confirm" id="mepr_user_password_confirm" class="input mepr-password-confirm" value="" required />

          <?php MeprHooks::do_action('mepr-after-password-fields'); //Deprecated ?>
          <?php MeprHooks::do_action('mepr-checkout-after-password-fields', $product->ID); ?>

          <?php MeprHooks::do_action('mepr-user-signup-fields'); //Deprecated ?>
          <?php MeprHooks::do_action('mepr-checkout-before-submit', $product->ID); ?>

          <p class="other-field"></p>
          <button class="button next-button pull-right chat-next-btn" data-orientation="next" id="modal-next-btn"  type="button">Next page</button>
        </div>
        <!-- end step1 carousel-item -->
        <!-- start step2 carousel-item -->
        <div class="carousel-item <?php echo (MeprUtils::is_user_logged_in())? 'active': '';?>">
          <div class="payment-container">
            <div class="reason-column">

              <div class="left-section">
                <div class="reason-container">
                  <div class="left-column"><img alt="" src="/wp-content/themes/digital-freelancer/dist/images/tick.svg"></div>
                  <div class="right-column">
                    <h3 class="reason-title">Join 1000+ Freelancers</h3>
                    <p class="reason-message">#freelance is the largest and most engaged freelancing community on slack.</p>
                  </div>
                </div>
                <div class="reason-container">
                  <div class="left-column"><img alt="" src="/wp-content/themes/digital-freelancer/dist/images/tick.svg"></div>
                  <div class="right-column">
                    <h3 class="reason-title">Find Job Opportunities</h3>
                    <p class="reason-message">Give and receive referrals, partner with other freelancers on jobs and find new clients within the community.</p>
                  </div>
                </div>
              </div>
              <div class="right-section">
                <div class="reason-container">
                  <div class="left-column"><img alt="" src="/wp-content/themes/digital-freelancer/dist/images/tick.svg"></div>
                  <div class="right-column">
                    <h3 class="reason-title">Weekly Webinars</h3>
                    <p class="reason-message">Watch webinars given by experts and have the opportunity to pitch your own services to the community.</p>
                  </div>
                </div>
                <div class="reason-container">
                  <div class="left-column"><img alt="" src="/wp-content/themes/digital-freelancer/dist/images/tick.svg"></div>
                  <div class="right-column">
                    <h3 class="reason-title">Freelancing Newsletter</h3>
                    <p class="reason-message">Share articles with the community and stay up-to-date with the latest in the freelancing world.</p>
                  </div>
                </div>
              </div>
            </div>
            <!-- end reason column -->
            <?php $active_pms = $product->payment_methods(); ?>
            <?php $pms = $product->payment_methods(); ?>
            <div class="mp-form-row mepr_payment_method">
              <?php echo MeprOptionsHelper::payment_methods_dropdown('mepr_payment_method', $active_pms); ?>
            </div>
            <div class="stripe-column">
              <button type="submit" class="mepr-submit button buy-membership" id="buyMembership" >
                <span class="original-price">Buy Membership - $49/year</span>
              </button>
              <?php //MeprView::render('/shared/has_errors', get_defined_vars()); ?>
              <h3><i aria-hidden="true" class="fa fa-lock"></i> No Questions Asked, 100% Money-Back Guarantee</h3><img alt="" src="/wp-content/themes/digital-freelancer/dist/images/powered_by_stripe.png">
            </div>
          </div>
        </div>
        <!-- end step2 carousel-item -->
      </div>
    </div>
    <!-- end carousel --> 
  </form>
  
  <?php
        break;
      case 'style3':
        # code...
  ?>
  <form class="mepr-signup-form mepr-form form-inline justify-content-center" method="post" action="<?php echo $_SERVER['REQUEST_URI'].'#mepr_errors'; ?>" novalidate>
    <input type="hidden" id="mepr_process_signup_form" name="mepr_process_signup_form" value="Y" />
    <input type="hidden" id="mepr_product_id" name="mepr_product_id" value="<?php echo $product->ID; ?>" />
    <input type="hidden" id="df_redirct_url" name="df_redirct_url" value="free" />
    <?php if(MeprUtils::is_user_logged_in()): ?>
      <input type="hidden" name="logged_in_purchase" value="1" />
    <?php endif; ?>

    <?php MeprHooks::do_action('mepr-checkout-before-name', $product->ID); ?>

    <!-- carousel -->
    <div id="carouselExampleControls1" class="register-carousel" data-ride="carousel">
      <div class="carousel-inner" role="listbox">
        <!-- step1 carousel-item -->
        <div class="carousel-item p1 <?php echo (MeprUtils::is_user_logged_in())? '': 'active';?>">
          <div class="header-error">
            <span></span>
          </div>
          <?php if(MeprUtils::is_user_logged_in()): ?>
            <p><input type="email" name="user_email" id="user_email" class="input" placeholder="E-mail" value="<?php echo (isset($mepr_current_user->user_email))?esc_attr(stripslashes($mepr_current_user->user_email)):''; ?>" required readonly /></p>
          <?php else: ?>
            <p><input type="email" name="user_email" id="user_email" class="input" placeholder="E-mail" value="<?php echo (isset($user_email))?esc_attr(stripslashes($user_email)):''; ?>" required /></p>
          <?php endif; ?>
          

          <?php MeprHooks::do_action('mepr-after-email-field'); //Deprecated ?>
          <?php MeprHooks::do_action('mepr-checkout-after-email-field', $product->ID); ?>
          <?php
            if(MeprUtils::is_user_logged_in() && $mepr_options->show_fields_logged_in_purchases) {
              MeprUsersHelper::render_custom_fields($product);
            }
            elseif(!MeprUtils::is_user_logged_in()) { // We only pass the 'signup' flag on initial Signup
              MeprUsersHelper::render_custom_fields($product, true);
            }
          ?>
          <?php if(MeprUtils::is_user_logged_in()): ?>
            <p><input type="password" name="mepr_user_password" id="mepr_user_password" placeholder="Password" class="input mepr-password" value="<?php echo (isset($mepr_user_password))?esc_attr(stripslashes($mepr_user_password)):''; ?>" required readonly/></p>
          <?php else: ?>
            <p><input type="password" name="mepr_user_password" id="mepr_user_password" placeholder="Password" class="input mepr-password" value="<?php echo (isset($mepr_user_password))?esc_attr(stripslashes($mepr_user_password)):''; ?>" required /></p>
          <?php endif; ?>
          
          <input type="hidden" name="mepr_user_password_confirm" id="mepr_user_password_confirm" class="input mepr-password-confirm" value="" required />

          <?php MeprHooks::do_action('mepr-after-password-fields'); //Deprecated ?>
          <?php MeprHooks::do_action('mepr-checkout-after-password-fields', $product->ID); ?>

          <?php MeprHooks::do_action('mepr-user-signup-fields'); //Deprecated ?>
          <?php MeprHooks::do_action('mepr-checkout-before-submit', $product->ID); ?>
          <p class="pipeline-register-description">
            By signing up you agree to our <br />terms and services
          </p>
          <p class="other-field"></p>
          <button class="btn dfbtn-red-pipeline-register pull-right" data-orientation="next" id="modal-next-btn"  type="button">Next</button>
        </div>
        <!-- end step1 carousel-item -->
        <!-- start step2 carousel-item -->
        <div class="carousel-item <?php echo (MeprUtils::is_user_logged_in())? 'active': '';?>">
            <div class=" pipeline-price-group">

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
                      <button class="btn dfbtn-white-pipeline-price  btn-lg" type="submit" id="reg_pipeline_light">Get Started</button>
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
                      <button class="btn dfbtn-red-pipeline-price btn-lg" type="submit" id="reg_pipeline_pro">Get Started</button>
                    </div>
                  </div>
                </div>

            </div>

            <?php $active_pms = $product->payment_methods(); ?>
            <?php $pms = $product->payment_methods(); ?>
            <div class="mp-form-row mepr_payment_method">
              <?php echo MeprOptionsHelper::payment_methods_dropdown('mepr_payment_method', $active_pms); ?>
            </div>
            <!--
            <div class="stripe-column">
              <button type="submit" class="mepr-submit button buy-membership" id="buyMembership" >
                <span class="original-price">Buy Membership - $49/year</span>
              </button>
              <?php //MeprView::render('/shared/has_errors', get_defined_vars()); ?>
              <h3><i aria-hidden="true" class="fa fa-lock"></i> No Questions Asked, 100% Money-Back Guarantee</h3><img alt="" src="/wp-content/themes/digital-freelancer/dist/images/powered_by_stripe.png">
            </div>
            -->

        </div>
        <!-- end step2 carousel-item -->
      </div>
    </div>
    <!-- end carousel --> 
  </form>
<?php
        break;
      case 'style4':
        # code...
  ?>
  <?php
        break;
      case 'style5':
        # code...
  ?>
  <form class="mepr-signup-form mepr-form justify-content-center" method="post" action="<?php echo $_SERVER['REQUEST_URI'].'#mepr_errors'; ?>" novalidate>
    <input type="hidden" id="mepr_process_signup_form" name="mepr_process_signup_form" value="Y" />
    <input type="hidden" id="mepr_product_id" name="mepr_product_id" value="<?php echo $product->ID; ?>" />
    <input type="hidden" id="df_redirct_url" name="df_redirct_url" value="free" />
    <?php if(MeprUtils::is_user_logged_in()): ?>
      <input type="hidden" name="logged_in_purchase" value="1" />
    <?php endif; ?>

    <?php if( ($product->register_price_action != 'hidden') && MeprHooks::apply_filters('mepr_checkout_show_terms',true) ): ?>
      <!-- dorin's change
      <div class="mp-form-row mepr_bold mepr_price">
        <?php $price_label = ($product->is_one_time_payment() ? _x('Price:', 'ui', 'memberpress') : _x('Terms:', 'ui', 'memberpress')); ?>
        <label><?php echo $price_label; ?></label>
        <div class="mepr_price_cell">
          <?php MeprProductsHelper::display_invoice( $product, $mepr_coupon_code ); ?>
        </div>
      </div>
      -->
    <?php endif; ?>

    <?php MeprHooks::do_action('mepr-checkout-before-name', $product->ID); ?>
    <?php
    if((!MeprUtils::is_user_logged_in() ||
              (MeprUtils::is_user_logged_in() && $mepr_options->show_fields_logged_in_purchases)) &&
             $mepr_options->show_fname_lname): ?>
      <div class="mp-form-row mepr_first_name">
        <div class="mp-form-label">
          <label><?php _ex('First Name:', 'ui', 'memberpress'); echo ($mepr_options->require_fname_lname)?'*':''; ?></label>
          <span class="cc-error"><?php _ex('First Name Required', 'ui', 'memberpress'); ?></span>
        </div>
        <input type="text" name="user_first_name" id="user_first_name" class="mepr-form-input" value="<?php echo $first_name_value; ?>" <?php echo ($mepr_options->require_fname_lname)?'required':''; ?> />
      </div>
      <div class="mp-form-row mepr_last_name">
        <div class="mp-form-label">
          <label><?php _ex('Last Name:', 'ui', 'memberpress'); echo ($mepr_options->require_fname_lname)?'*':''; ?></label>
          <span class="cc-error"><?php _ex('Last Name Required', 'ui', 'memberpress'); ?></span>
        </div>
        <input type="text" name="user_last_name" id="user_last_name" class="mepr-form-input" value="<?php echo $last_name_value; ?>" <?php echo ($mepr_options->require_fname_lname)?'required':''; ?> />
      </div>
    <?php else: /* this is here to avoid validation issues */ ?>
      <input type="hidden" name="user_first_name" id="user_first_name" value="<?php echo $first_name_value; ?>" />
      <input type="hidden" name="user_last_name" id="user_last_name" value="<?php echo $last_name_value; ?>" />
    <?php endif; ?>

    <?php
      if(MeprUtils::is_user_logged_in() && $mepr_options->show_fields_logged_in_purchases) {
        MeprUsersHelper::render_custom_fields($product);
      }
      elseif(!MeprUtils::is_user_logged_in()) { // We only pass the 'signup' flag on initial Signup
        MeprUsersHelper::render_custom_fields($product, true);
      }
    ?>

    <?php if(MeprUtils::is_user_logged_in()): ?>
      <input type="hidden" name="user_email" id="user_email" value="<?php echo stripslashes($mepr_current_user->user_email); ?>" />
    <?php else: ?>
      <input type="hidden" class="mepr-geo-country" name="mepr-geo-country" value="" />

      <?php if(!$mepr_options->username_is_email): ?>
        <div class="mp-form-row mepr_username">
          <div class="mp-form-label">
            <label><?php _ex('Username:*', 'ui', 'memberpress'); ?></label>
            <span class="cc-error"><?php _ex('Invalid Username', 'ui', 'memberpress'); ?></span>
          </div>
          <input type="text" name="user_login" id="user_login" class="mepr-form-input" value="<?php echo (isset($user_login))?esc_attr(stripslashes($user_login)):''; ?>" required />
        </div>
      <?php endif; ?>
      <!-- dorin's change remove label -->

          <input type="email" name="user_email" id="user_email" class="cc-error form-control" value="<?php echo (isset($user_email))?esc_attr(stripslashes($user_email)):''; ?>" required placeholder="Email Address"/>
       
      <?php MeprHooks::do_action('mepr-after-email-field'); //Deprecated ?>
      <?php MeprHooks::do_action('mepr-checkout-after-email-field', $product->ID); ?>

        <!-- dorin's change remove label -->

          <input type="password" name="mepr_user_password" id="mepr_user_password" class="mepr-form-input mepr-password form-control" value="<?php echo (isset($mepr_user_password))?esc_attr(stripslashes($mepr_user_password)):''; ?>" required placeholder="Password"/>


        <!-- dorin's change remove label -->
        <input type="hidden" name="mepr_user_password_confirm" id="mepr_user_password_confirm" class="mepr-form-input mepr-password-confirm btn btn-primary btn-lg col-lg-2 col-sm-8" value="<?php echo (isset($mepr_user_password_confirm))?esc_attr(stripslashes($mepr_user_password_confirm)):''; ?>" required />

      <?php MeprHooks::do_action('mepr-after-password-fields'); //Deprecated ?>
      <?php MeprHooks::do_action('mepr-checkout-after-password-fields', $product->ID); ?>
    <?php endif; ?>

    <?php MeprHooks::do_action('mepr-before-coupon-field'); //Deprecated ?>
    <?php MeprHooks::do_action('mepr-checkout-before-coupon-field', $product->ID); ?>

    <?php MeprHooks::do_action('mepr-user-signup-fields'); //Deprecated ?>
    <?php MeprHooks::do_action('mepr-checkout-before-submit', $product->ID); ?>

    <?php if($product->adjusted_price() > 0.00): ?>
      <?php if($mepr_options->coupon_field_enabled): ?>
        <div class="mp-form-row mepr_coupon">
          <div class="mp-form-label">
            <label><?php _ex('Coupon Code:', 'ui', 'memberpress'); ?></label>
            <span class="mepr-coupon-loader mepr-hidden">
              <img src="<?php echo includes_url('js/thickbox/loadingAnimation.gif'); ?>" width="100" height="10" />
            </span>
            <span class="cc-error"><?php _ex('Invalid Coupon', 'ui', 'memberpress'); ?></span>
          </div>
          <input type="text" id="mepr_coupon_code-<?php echo $product->ID; ?>" class="mepr-form-input mepr-coupon-code" name="mepr_coupon_code" value="<?php echo (isset($mepr_coupon_code))?esc_attr(stripslashes($mepr_coupon_code)):''; ?>" data-prd-id="<?php echo $product->ID; ?>" />
        </div>
      <?php else: ?>
        <input type="hidden" id="mepr_coupon_code-<?php echo $product->ID; ?>" name="mepr_coupon_code" value="<?php echo (isset($mepr_coupon_code))?esc_attr(stripslashes($mepr_coupon_code)):''; ?>" />
      <?php endif; ?>
      <?php $active_pms = $product->payment_methods(); ?>
      <?php $pms = $product->payment_methods(); ?>
      <div class="mp-form-row mepr_payment_method">
        <?php echo MeprOptionsHelper::payment_methods_dropdown('mepr_payment_method', $active_pms); ?>
      </div>
    <?php endif; ?>

        <input type="submit" class="mepr-submit  btn btn-primary col-md-12" value="<?php echo stripslashes($product->signup_button_text); ?>" />

    <img src="<?php echo admin_url('images/loading.gif'); ?>" style="display: none;" class="mepr-loading-gif" />
    <?php //MeprView::render('/shared/has_errors', get_defined_vars()); ?>
  </form>
  <?php
        break;
      default:
        # code...
  ?>
  <?php if((!MeprUtils::is_user_logged_in() ||
              (MeprUtils::is_user_logged_in() && $mepr_options->show_fields_logged_in_purchases)) &&
             $mepr_options->show_fname_lname): ?>
      <div class="mp-form-row mepr_first_name">
        <div class="mp-form-label">
          <label><?php _ex('First Name:', 'ui', 'memberpress'); echo ($mepr_options->require_fname_lname)?'*':''; ?></label>
          <span class="cc-error"><?php _ex('First Name Required', 'ui', 'memberpress'); ?></span>
        </div>
        <input type="text" name="user_first_name" id="user_first_name" class="mepr-form-input" value="<?php echo $first_name_value; ?>" <?php echo ($mepr_options->require_fname_lname)?'required':''; ?> />
      </div>
      <div class="mp-form-row mepr_last_name">
        <div class="mp-form-label">
          <label><?php _ex('Last Name:', 'ui', 'memberpress'); echo ($mepr_options->require_fname_lname)?'*':''; ?></label>
          <span class="cc-error"><?php _ex('Last Name Required', 'ui', 'memberpress'); ?></span>
        </div>
        <input type="text" name="user_last_name" id="user_last_name" class="mepr-form-input" value="<?php echo $last_name_value; ?>" <?php echo ($mepr_options->require_fname_lname)?'required':''; ?> />
      </div>
    <?php else: /* this is here to avoid validation issues */ ?>
      <input type="hidden" name="user_first_name" id="user_first_name" value="<?php echo $first_name_value; ?>" />
      <input type="hidden" name="user_last_name" id="user_last_name" value="<?php echo $last_name_value; ?>" />
    <?php endif; ?>

    <?php
      if(MeprUtils::is_user_logged_in() && $mepr_options->show_fields_logged_in_purchases) {
        MeprUsersHelper::render_custom_fields($product);
      }
      elseif(!MeprUtils::is_user_logged_in()) { // We only pass the 'signup' flag on initial Signup
        MeprUsersHelper::render_custom_fields($product, true);
      }
    ?>

    <?php if(MeprUtils::is_user_logged_in()): ?>
      <input type="hidden" name="user_email" id="user_email" value="<?php echo stripslashes($mepr_current_user->user_email); ?>" />
    <?php else: ?>
      <input type="hidden" class="mepr-geo-country" name="mepr-geo-country" value="" />

      <?php if(!$mepr_options->username_is_email): ?>
        <div class="mp-form-row mepr_username">
          <div class="mp-form-label">
            <label><?php _ex('Username:*', 'ui', 'memberpress'); ?></label>
            <span class="cc-error"><?php _ex('Invalid Username', 'ui', 'memberpress'); ?></span>
          </div>
          <input type="text" name="user_login" id="user_login" class="mepr-form-input" value="<?php echo (isset($user_login))?esc_attr(stripslashes($user_login)):''; ?>" required />
        </div>
      <?php endif; ?>
      <div class="mp-form-row mepr_email">
        <div class="mp-form-label">
          <label><?php _ex('Email:*', 'ui', 'memberpress'); ?></label>
          <span class="cc-error"><?php _ex('Invalid Email', 'ui', 'memberpress'); ?></span>
        </div>
        <input type="email" name="user_email" id="user_email" class="mepr-form-input" value="<?php echo (isset($user_email))?esc_attr(stripslashes($user_email)):''; ?>" required />
      </div>
      <?php MeprHooks::do_action('mepr-after-email-field'); //Deprecated ?>
      <?php MeprHooks::do_action('mepr-checkout-after-email-field', $product->ID); ?>
      <div class="mp-form-row mepr_password">
        <div class="mp-form-label">
          <label><?php _ex('Password:*', 'ui', 'memberpress'); ?></label>
          <span class="cc-error"><?php _ex('Invalid Password', 'ui', 'memberpress'); ?></span>
        </div>
        <input type="password" name="mepr_user_password" id="mepr_user_password" class="mepr-form-input mepr-password" value="<?php echo (isset($mepr_user_password))?esc_attr(stripslashes($mepr_user_password)):''; ?>" required />
      </div>
      <div class="mp-form-row mepr_password_confirm">
        <div class="mp-form-label">
          <label><?php _ex('Password Confirmation:*', 'ui', 'memberpress'); ?></label>
          <span class="cc-error"><?php _ex('Password Confirmation Doesn\'t Match', 'ui', 'memberpress'); ?></span>
        </div>
        <input type="password" name="mepr_user_password_confirm" id="mepr_user_password_confirm" class="mepr-form-input mepr-password-confirm" value="<?php echo (isset($mepr_user_password_confirm))?esc_attr(stripslashes($mepr_user_password_confirm)):''; ?>" required />
      </div>
      <?php MeprHooks::do_action('mepr-after-password-fields'); //Deprecated ?>
      <?php MeprHooks::do_action('mepr-checkout-after-password-fields', $product->ID); ?>
    <?php endif; ?>

    <?php MeprHooks::do_action('mepr-before-coupon-field'); //Deprecated ?>
    <?php MeprHooks::do_action('mepr-checkout-before-coupon-field', $product->ID); ?>

    <?php if($product->adjusted_price() > 0.00): ?>
      <?php if($mepr_options->coupon_field_enabled): ?>
        <div class="mp-form-row mepr_coupon">
          <div class="mp-form-label">
            <label><?php _ex('Coupon Code:', 'ui', 'memberpress'); ?></label>
            <span class="mepr-coupon-loader mepr-hidden">
              <img src="<?php echo includes_url('js/thickbox/loadingAnimation.gif'); ?>" width="100" height="10" />
            </span>
            <span class="cc-error"><?php _ex('Invalid Coupon', 'ui', 'memberpress'); ?></span>
          </div>
          <input type="text" id="mepr_coupon_code-<?php echo $product->ID; ?>" class="mepr-form-input mepr-coupon-code" name="mepr_coupon_code" value="<?php echo (isset($mepr_coupon_code))?esc_attr(stripslashes($mepr_coupon_code)):''; ?>" data-prd-id="<?php echo $product->ID; ?>" />
        </div>
      <?php else: ?>
        <input type="hidden" id="mepr_coupon_code-<?php echo $product->ID; ?>" name="mepr_coupon_code" value="<?php echo (isset($mepr_coupon_code))?esc_attr(stripslashes($mepr_coupon_code)):''; ?>" />
      <?php endif; ?>
      <?php $active_pms = $product->payment_methods(); ?>
      <?php $pms = $product->payment_methods(); ?>
      <div class="mp-form-row mepr_payment_method">
        <?php echo MeprOptionsHelper::payment_methods_dropdown('mepr_payment_method', $active_pms); ?>
      </div>
    <?php endif; ?>

    <?php if(!MeprUtils::is_user_logged_in()): ?>
      <?php if($mepr_options->require_tos): ?>
        <div class="mp-form-row mepr_tos">
          <label for="mepr_agree_to_tos" class="mepr-checkbox-field mepr-form-input" required>
            <input type="checkbox" name="mepr_agree_to_tos" id="mepr_agree_to_tos" <?php checked(isset($mepr_agree_to_tos)); ?> />
            <a href="<?php echo stripslashes($mepr_options->tos_url); ?>" target="_blank"><?php echo stripslashes($mepr_options->tos_title); ?></a>*
          </label>
        </div>
      <?php endif; ?>

      <?php // This thing needs to be hidden in order for this to work so we do it explicitly as a style ?>
      <input type="text" id="mepr_no_val" name="mepr_no_val" class="mepr-form-input" autocomplete="off" />
    <?php endif; ?>

    <?php MeprHooks::do_action('mepr-user-signup-fields'); //Deprecated ?>
    <?php MeprHooks::do_action('mepr-checkout-before-submit', $product->ID); ?>

    <div class="mepr_spacer">&nbsp;</div>

    <input type="submit" class="mepr-submit" value="<?php echo stripslashes($product->signup_button_text); ?>" />
    <img src="<?php echo admin_url('images/loading.gif'); ?>" style="display: none;" class="mepr-loading-gif" />
    <?php MeprView::render('/shared/has_errors', get_defined_vars()); ?>
  <?php
        break;
    }
  ?>
    

    



