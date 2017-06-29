<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MeprStripeGateway extends MeprBaseRealGateway {
  public static $stripe_plan_id_str = '_mepr_stripe_plan_id';
  public static $country_list = array(
                    'AD'=>array('name'=>'ANDORRA','code'=>'376'),
                    'AE'=>array('name'=>'UNITED ARAB EMIRATES','code'=>'971'),
                    'AF'=>array('name'=>'AFGHANISTAN','code'=>'93'),
                    'AG'=>array('name'=>'ANTIGUA AND BARBUDA','code'=>'1268'),
                    'AI'=>array('name'=>'ANGUILLA','code'=>'1264'),
                    'AL'=>array('name'=>'ALBANIA','code'=>'355'),
                    'AM'=>array('name'=>'ARMENIA','code'=>'374'),
                    'AN'=>array('name'=>'NETHERLANDS ANTILLES','code'=>'599'),
                    'AO'=>array('name'=>'ANGOLA','code'=>'244'),
                    'AQ'=>array('name'=>'ANTARCTICA','code'=>'672'),
                    'AR'=>array('name'=>'ARGENTINA','code'=>'54'),
                    'AS'=>array('name'=>'AMERICAN SAMOA','code'=>'1684'),
                    'AT'=>array('name'=>'AUSTRIA','code'=>'43'),
                    'AU'=>array('name'=>'AUSTRALIA','code'=>'61'),
                    'AW'=>array('name'=>'ARUBA','code'=>'297'),
                    'AZ'=>array('name'=>'AZERBAIJAN','code'=>'994'),
                    'BA'=>array('name'=>'BOSNIA AND HERZEGOVINA','code'=>'387'),
                    'BB'=>array('name'=>'BARBADOS','code'=>'1246'),
                    'BD'=>array('name'=>'BANGLADESH','code'=>'880'),
                    'BE'=>array('name'=>'BELGIUM','code'=>'32'),
                    'BF'=>array('name'=>'BURKINA FASO','code'=>'226'),
                    'BG'=>array('name'=>'BULGARIA','code'=>'359'),
                    'BH'=>array('name'=>'BAHRAIN','code'=>'973'),
                    'BI'=>array('name'=>'BURUNDI','code'=>'257'),
                    'BJ'=>array('name'=>'BENIN','code'=>'229'),
                    'BL'=>array('name'=>'SAINT BARTHELEMY','code'=>'590'),
                    'BM'=>array('name'=>'BERMUDA','code'=>'1441'),
                    'BN'=>array('name'=>'BRUNEI DARUSSALAM','code'=>'673'),
                    'BO'=>array('name'=>'BOLIVIA','code'=>'591'),
                    'BR'=>array('name'=>'BRAZIL','code'=>'55'),
                    'BS'=>array('name'=>'BAHAMAS','code'=>'1242'),
                    'BT'=>array('name'=>'BHUTAN','code'=>'975'),
                    'BW'=>array('name'=>'BOTSWANA','code'=>'267'),
                    'BY'=>array('name'=>'BELARUS','code'=>'375'),
                    'BZ'=>array('name'=>'BELIZE','code'=>'501'),
                    'CA'=>array('name'=>'CANADA','code'=>'1'),
                    'CC'=>array('name'=>'COCOS (KEELING) ISLANDS','code'=>'61'),
                    'CD'=>array('name'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE','code'=>'243'),
                    'CF'=>array('name'=>'CENTRAL AFRICAN REPUBLIC','code'=>'236'),
                    'CG'=>array('name'=>'CONGO','code'=>'242'),
                    'CH'=>array('name'=>'SWITZERLAND','code'=>'41'),
                    'CI'=>array('name'=>'COTE D IVOIRE','code'=>'225'),
                    'CK'=>array('name'=>'COOK ISLANDS','code'=>'682'),
                    'CL'=>array('name'=>'CHILE','code'=>'56'),
                    'CM'=>array('name'=>'CAMEROON','code'=>'237'),
                    'CN'=>array('name'=>'CHINA','code'=>'86'),
                    'CO'=>array('name'=>'COLOMBIA','code'=>'57'),
                    'CR'=>array('name'=>'COSTA RICA','code'=>'506'),
                    'CU'=>array('name'=>'CUBA','code'=>'53'),
                    'CV'=>array('name'=>'CAPE VERDE','code'=>'238'),
                    'CX'=>array('name'=>'CHRISTMAS ISLAND','code'=>'61'),
                    'CY'=>array('name'=>'CYPRUS','code'=>'357'),
                    'CZ'=>array('name'=>'CZECH REPUBLIC','code'=>'420'),
                    'DE'=>array('name'=>'GERMANY','code'=>'49'),
                    'DJ'=>array('name'=>'DJIBOUTI','code'=>'253'),
                    'DK'=>array('name'=>'DENMARK','code'=>'45'),
                    'DM'=>array('name'=>'DOMINICA','code'=>'1767'),
                    'DO'=>array('name'=>'DOMINICAN REPUBLIC','code'=>'1809'),
                    'DZ'=>array('name'=>'ALGERIA','code'=>'213'),
                    'EC'=>array('name'=>'ECUADOR','code'=>'593'),
                    'EE'=>array('name'=>'ESTONIA','code'=>'372'),
                    'EG'=>array('name'=>'EGYPT','code'=>'20'),
                    'ER'=>array('name'=>'ERITREA','code'=>'291'),
                    'ES'=>array('name'=>'SPAIN','code'=>'34'),
                    'ET'=>array('name'=>'ETHIOPIA','code'=>'251'),
                    'FI'=>array('name'=>'FINLAND','code'=>'358'),
                    'FJ'=>array('name'=>'FIJI','code'=>'679'),
                    'FK'=>array('name'=>'FALKLAND ISLANDS (MALVINAS)','code'=>'500'),
                    'FM'=>array('name'=>'MICRONESIA, FEDERATED STATES OF','code'=>'691'),
                    'FO'=>array('name'=>'FAROE ISLANDS','code'=>'298'),
                    'FR'=>array('name'=>'FRANCE','code'=>'33'),
                    'GA'=>array('name'=>'GABON','code'=>'241'),
                    'GB'=>array('name'=>'UNITED KINGDOM','code'=>'44'),
                    'GD'=>array('name'=>'GRENADA','code'=>'1473'),
                    'GE'=>array('name'=>'GEORGIA','code'=>'995'),
                    'GH'=>array('name'=>'GHANA','code'=>'233'),
                    'GI'=>array('name'=>'GIBRALTAR','code'=>'350'),
                    'GL'=>array('name'=>'GREENLAND','code'=>'299'),
                    'GM'=>array('name'=>'GAMBIA','code'=>'220'),
                    'GN'=>array('name'=>'GUINEA','code'=>'224'),
                    'GQ'=>array('name'=>'EQUATORIAL GUINEA','code'=>'240'),
                    'GR'=>array('name'=>'GREECE','code'=>'30'),
                    'GT'=>array('name'=>'GUATEMALA','code'=>'502'),
                    'GU'=>array('name'=>'GUAM','code'=>'1671'),
                    'GW'=>array('name'=>'GUINEA-BISSAU','code'=>'245'),
                    'GY'=>array('name'=>'GUYANA','code'=>'592'),
                    'HK'=>array('name'=>'HONG KONG','code'=>'852'),
                    'HN'=>array('name'=>'HONDURAS','code'=>'504'),
                    'HR'=>array('name'=>'CROATIA','code'=>'385'),
                    'HT'=>array('name'=>'HAITI','code'=>'509'),
                    'HU'=>array('name'=>'HUNGARY','code'=>'36'),
                    'ID'=>array('name'=>'INDONESIA','code'=>'62'),
                    'IE'=>array('name'=>'IRELAND','code'=>'353'),
                    'IL'=>array('name'=>'ISRAEL','code'=>'972'),
                    'IM'=>array('name'=>'ISLE OF MAN','code'=>'44'),
                    'IN'=>array('name'=>'INDIA','code'=>'91'),
                    'IQ'=>array('name'=>'IRAQ','code'=>'964'),
                    'IR'=>array('name'=>'IRAN, ISLAMIC REPUBLIC OF','code'=>'98'),
                    'IS'=>array('name'=>'ICELAND','code'=>'354'),
                    'IT'=>array('name'=>'ITALY','code'=>'39'),
                    'JM'=>array('name'=>'JAMAICA','code'=>'1876'),
                    'JO'=>array('name'=>'JORDAN','code'=>'962'),
                    'JP'=>array('name'=>'JAPAN','code'=>'81'),
                    'KE'=>array('name'=>'KENYA','code'=>'254'),
                    'KG'=>array('name'=>'KYRGYZSTAN','code'=>'996'),
                    'KH'=>array('name'=>'CAMBODIA','code'=>'855'),
                    'KI'=>array('name'=>'KIRIBATI','code'=>'686'),
                    'KM'=>array('name'=>'COMOROS','code'=>'269'),
                    'KN'=>array('name'=>'SAINT KITTS AND NEVIS','code'=>'1869'),
                    'KP'=>array('name'=>'KOREA DEMOCRATIC PEOPLES REPUBLIC OF','code'=>'850'),
                    'KR'=>array('name'=>'KOREA REPUBLIC OF','code'=>'82'),
                    'KW'=>array('name'=>'KUWAIT','code'=>'965'),
                    'KY'=>array('name'=>'CAYMAN ISLANDS','code'=>'1345'),
                    'KZ'=>array('name'=>'KAZAKSTAN','code'=>'7'),
                    'LA'=>array('name'=>'LAO PEOPLES DEMOCRATIC REPUBLIC','code'=>'856'),
                    'LB'=>array('name'=>'LEBANON','code'=>'961'),
                    'LC'=>array('name'=>'SAINT LUCIA','code'=>'1758'),
                    'LI'=>array('name'=>'LIECHTENSTEIN','code'=>'423'),
                    'LK'=>array('name'=>'SRI LANKA','code'=>'94'),
                    'LR'=>array('name'=>'LIBERIA','code'=>'231'),
                    'LS'=>array('name'=>'LESOTHO','code'=>'266'),
                    'LT'=>array('name'=>'LITHUANIA','code'=>'370'),
                    'LU'=>array('name'=>'LUXEMBOURG','code'=>'352'),
                    'LV'=>array('name'=>'LATVIA','code'=>'371'),
                    'LY'=>array('name'=>'LIBYAN ARAB JAMAHIRIYA','code'=>'218'),
                    'MA'=>array('name'=>'MOROCCO','code'=>'212'),
                    'MC'=>array('name'=>'MONACO','code'=>'377'),
                    'MD'=>array('name'=>'MOLDOVA, REPUBLIC OF','code'=>'373'),
                    'ME'=>array('name'=>'MONTENEGRO','code'=>'382'),
                    'MF'=>array('name'=>'SAINT MARTIN','code'=>'1599'),
                    'MG'=>array('name'=>'MADAGASCAR','code'=>'261'),
                    'MH'=>array('name'=>'MARSHALL ISLANDS','code'=>'692'),
                    'MK'=>array('name'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','code'=>'389'),
                    'ML'=>array('name'=>'MALI','code'=>'223'),
                    'MM'=>array('name'=>'MYANMAR','code'=>'95'),
                    'MN'=>array('name'=>'MONGOLIA','code'=>'976'),
                    'MO'=>array('name'=>'MACAU','code'=>'853'),
                    'MP'=>array('name'=>'NORTHERN MARIANA ISLANDS','code'=>'1670'),
                    'MR'=>array('name'=>'MAURITANIA','code'=>'222'),
                    'MS'=>array('name'=>'MONTSERRAT','code'=>'1664'),
                    'MT'=>array('name'=>'MALTA','code'=>'356'),
                    'MU'=>array('name'=>'MAURITIUS','code'=>'230'),
                    'MV'=>array('name'=>'MALDIVES','code'=>'960'),
                    'MW'=>array('name'=>'MALAWI','code'=>'265'),
                    'MX'=>array('name'=>'MEXICO','code'=>'52'),
                    'MY'=>array('name'=>'MALAYSIA','code'=>'60'),
                    'MZ'=>array('name'=>'MOZAMBIQUE','code'=>'258'),
                    'NA'=>array('name'=>'NAMIBIA','code'=>'264'),
                    'NC'=>array('name'=>'NEW CALEDONIA','code'=>'687'),
                    'NE'=>array('name'=>'NIGER','code'=>'227'),
                    'NG'=>array('name'=>'NIGERIA','code'=>'234'),
                    'NI'=>array('name'=>'NICARAGUA','code'=>'505'),
                    'NL'=>array('name'=>'NETHERLANDS','code'=>'31'),
                    'NO'=>array('name'=>'NORWAY','code'=>'47'),
                    'NP'=>array('name'=>'NEPAL','code'=>'977'),
                    'NR'=>array('name'=>'NAURU','code'=>'674'),
                    'NU'=>array('name'=>'NIUE','code'=>'683'),
                    'NZ'=>array('name'=>'NEW ZEALAND','code'=>'64'),
                    'OM'=>array('name'=>'OMAN','code'=>'968'),
                    'PA'=>array('name'=>'PANAMA','code'=>'507'),
                    'PE'=>array('name'=>'PERU','code'=>'51'),
                    'PF'=>array('name'=>'FRENCH POLYNESIA','code'=>'689'),
                    'PG'=>array('name'=>'PAPUA NEW GUINEA','code'=>'675'),
                    'PH'=>array('name'=>'PHILIPPINES','code'=>'63'),
                    'PK'=>array('name'=>'PAKISTAN','code'=>'92'),
                    'PL'=>array('name'=>'POLAND','code'=>'48'),
                    'PM'=>array('name'=>'SAINT PIERRE AND MIQUELON','code'=>'508'),
                    'PN'=>array('name'=>'PITCAIRN','code'=>'870'),
                    'PR'=>array('name'=>'PUERTO RICO','code'=>'1'),
                    'PT'=>array('name'=>'PORTUGAL','code'=>'351'),
                    'PW'=>array('name'=>'PALAU','code'=>'680'),
                    'PY'=>array('name'=>'PARAGUAY','code'=>'595'),
                    'QA'=>array('name'=>'QATAR','code'=>'974'),
                    'RO'=>array('name'=>'ROMANIA','code'=>'40'),
                    'RS'=>array('name'=>'SERBIA','code'=>'381'),
                    'RU'=>array('name'=>'RUSSIAN FEDERATION','code'=>'7'),
                    'RW'=>array('name'=>'RWANDA','code'=>'250'),
                    'SA'=>array('name'=>'SAUDI ARABIA','code'=>'966'),
                    'SB'=>array('name'=>'SOLOMON ISLANDS','code'=>'677'),
                    'SC'=>array('name'=>'SEYCHELLES','code'=>'248'),
                    'SD'=>array('name'=>'SUDAN','code'=>'249'),
                    'SE'=>array('name'=>'SWEDEN','code'=>'46'),
                    'SG'=>array('name'=>'SINGAPORE','code'=>'65'),
                    'SH'=>array('name'=>'SAINT HELENA','code'=>'290'),
                    'SI'=>array('name'=>'SLOVENIA','code'=>'386'),
                    'SK'=>array('name'=>'SLOVAKIA','code'=>'421'),
                    'SL'=>array('name'=>'SIERRA LEONE','code'=>'232'),
                    'SM'=>array('name'=>'SAN MARINO','code'=>'378'),
                    'SN'=>array('name'=>'SENEGAL','code'=>'221'),
                    'SO'=>array('name'=>'SOMALIA','code'=>'252'),
                    'SR'=>array('name'=>'SURINAME','code'=>'597'),
                    'ST'=>array('name'=>'SAO TOME AND PRINCIPE','code'=>'239'),
                    'SV'=>array('name'=>'EL SALVADOR','code'=>'503'),
                    'SY'=>array('name'=>'SYRIAN ARAB REPUBLIC','code'=>'963'),
                    'SZ'=>array('name'=>'SWAZILAND','code'=>'268'),
                    'TC'=>array('name'=>'TURKS AND CAICOS ISLANDS','code'=>'1649'),
                    'TD'=>array('name'=>'CHAD','code'=>'235'),
                    'TG'=>array('name'=>'TOGO','code'=>'228'),
                    'TH'=>array('name'=>'THAILAND','code'=>'66'),
                    'TJ'=>array('name'=>'TAJIKISTAN','code'=>'992'),
                    'TK'=>array('name'=>'TOKELAU','code'=>'690'),
                    'TL'=>array('name'=>'TIMOR-LESTE','code'=>'670'),
                    'TM'=>array('name'=>'TURKMENISTAN','code'=>'993'),
                    'TN'=>array('name'=>'TUNISIA','code'=>'216'),
                    'TO'=>array('name'=>'TONGA','code'=>'676'),
                    'TR'=>array('name'=>'TURKEY','code'=>'90'),
                    'TT'=>array('name'=>'TRINIDAD AND TOBAGO','code'=>'1868'),
                    'TV'=>array('name'=>'TUVALU','code'=>'688'),
                    'TW'=>array('name'=>'TAIWAN, PROVINCE OF CHINA','code'=>'886'),
                    'TZ'=>array('name'=>'TANZANIA, UNITED REPUBLIC OF','code'=>'255'),
                    'UA'=>array('name'=>'UKRAINE','code'=>'380'),
                    'UG'=>array('name'=>'UGANDA','code'=>'256'),
                    'US'=>array('name'=>'UNITED STATES','code'=>'1'),
                    'UY'=>array('name'=>'URUGUAY','code'=>'598'),
                    'UZ'=>array('name'=>'UZBEKISTAN','code'=>'998'),
                    'VA'=>array('name'=>'HOLY SEE (VATICAN CITY STATE)','code'=>'39'),
                    'VC'=>array('name'=>'SAINT VINCENT AND THE GRENADINES','code'=>'1784'),
                    'VE'=>array('name'=>'VENEZUELA','code'=>'58'),
                    'VG'=>array('name'=>'VIRGIN ISLANDS, BRITISH','code'=>'1284'),
                    'VI'=>array('name'=>'VIRGIN ISLANDS, U.S.','code'=>'1340'),
                    'VN'=>array('name'=>'VIET NAM','code'=>'84'),
                    'VU'=>array('name'=>'VANUATU','code'=>'678'),
                    'WF'=>array('name'=>'WALLIS AND FUTUNA','code'=>'681'),
                    'WS'=>array('name'=>'SAMOA','code'=>'685'),
                    'XK'=>array('name'=>'KOSOVO','code'=>'381'),
                    'YE'=>array('name'=>'YEMEN','code'=>'967'),
                    'YT'=>array('name'=>'MAYOTTE','code'=>'262'),
                    'ZA'=>array('name'=>'SOUTH AFRICA','code'=>'27'),
                    'ZM'=>array('name'=>'ZAMBIA','code'=>'260'),
                    'ZW'=>array('name'=>'ZIMBABWE','code'=>'263')
                  );

  /** Used in the view to identify the gateway */
  public function __construct() {
    $this->name = __("Stripe", 'memberpress');
    $this->icon = MEPR_IMAGES_URL . '/checkout/cards.png';
    $this->desc = __('Pay with your credit card via Stripe', 'memberpress');
    $this->set_defaults();

    $this->capabilities = array(
      'process-credit-cards',
      'process-payments',
      'process-refunds',
      'create-subscriptions',
      'cancel-subscriptions',
      'update-subscriptions',
      'suspend-subscriptions',
      'resume-subscriptions',
      'send-cc-expirations'
    );

    // Setup the notification actions for this gateway
    $this->notifiers = array( 'whk' => 'listener' );
  }

  public function load($settings) {
    $this->settings = (object)$settings;
    $this->set_defaults();
  }

  protected function set_defaults() {
    if(!isset($this->settings)) {
      $this->settings = array();
    }

    $this->settings = (object)array_merge(
      array(
        'gateway' => 'MeprStripeGateway',
        'id' => $this->generate_id(),
        'label' => '',
        'use_label' => true,
        'use_icon' => true,
        'use_desc' => true,
        'email' => '',
        'sandbox' => false,
        'force_ssl' => false,
        'debug' => false,
        'test_mode' => false,
        'api_keys' => array(
          'test' => array(
            'public' => '',
            'secret' => ''
          ),
          'live' => array(
            'public' => '',
            'secret' => ''
          )
        )
      ),
      (array)$this->settings
    );

    $this->id = $this->settings->id;
    $this->label = $this->settings->label;
    $this->use_label = $this->settings->use_label;
    $this->use_icon = $this->settings->use_icon;
    $this->use_desc = $this->settings->use_desc;
    //$this->recurrence_type = $this->settings->recurrence_type;

    if($this->is_test_mode()) {
      $this->settings->public_key = $this->settings->api_keys['test']['public'];
      $this->settings->secret_key = $this->settings->api_keys['test']['secret'];
    }
    else {
      $this->settings->public_key = $this->settings->api_keys['live']['public'];
      $this->settings->secret_key = $this->settings->api_keys['live']['secret'];
    }

    // An attempt to correct people who paste in spaces along with their credentials
    $this->settings->api_keys['test']['secret'] = trim($this->settings->api_keys['test']['secret']);
    $this->settings->api_keys['test']['public'] = trim($this->settings->api_keys['test']['public']);
    $this->settings->api_keys['live']['secret'] = trim($this->settings->api_keys['live']['secret']);
    $this->settings->api_keys['live']['public'] = trim($this->settings->api_keys['live']['public']);
  }

  /** Used to send data to a given payment gateway. In gateways which redirect
    * before this step is necessary this method should just be left blank.
    */
  public function process_payment($txn) {
    if(isset($txn) and $txn instanceof MeprTransaction) {
      $usr = $txn->user();
      $prd = $txn->product();
    }
    else {
      throw new MeprGatewayException( __('Payment was unsuccessful, please check your payment details and try again.', 'memberpress') );
    }

    $mepr_options = MeprOptions::fetch();

    //Handle zero decimal currencies in Stripe
    $amount = (MeprUtils::is_zero_decimal_currency())?MeprUtils::format_float(($txn->total), 0):MeprUtils::format_float(($txn->total * 100), 0);

    // dorin's change - set payment meta
    $user_id = MeprUtils::get_current_user_id();//dorin
    $mepr_billing_address = get_user_meta($user_id, 'mepr_billing_address', true);
    $mepr_city = get_user_meta($user_id, 'mepr_city', true);
    $mepr_province = get_user_meta($user_id, 'mepr_province', true);
    $mepr_zip = get_user_meta($user_id, 'mepr_zip', true);
    $mepr_country = get_user_meta($user_id, 'mepr_country', true);

    // create the charge on Stripe's servers - this will charge the user's card
    $args = MeprHooks::apply_filters('mepr_stripe_payment_args', array(
      'amount' => $amount,
      'currency' => $mepr_options->currency_code,
      'description' => sprintf(__('%s (transaction: %s)', 'memberpress'), $prd->post_title, $txn->id ),
      'receipt_email' => $usr->user_email,
      'metadata' => array(
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'address' => $mepr_billing_address, //dorin's add
        'city' => $mepr_city, //dorin's add
        'province' => $mepr_province, //dorin's add
        'country' => $mepr_country //dorin's add
      )
    ), $txn);

    // get the credit card details submitted by the form
    $user_id = MeprUtils::get_current_user_id();
    if(isset($_REQUEST['stripe_token'])) {
      $args['card'] = $_REQUEST['stripe_token'];
    }
    else if(isset($_REQUEST['stripe_customer'])) {
      $args['customer'] = $_REQUEST['stripe_customer'];
    }
    else if(get_user_meta($user_id, 'mepr-df-stripe-customer-id', true) != '') { //dorin's add
      $args['customer'] = $this->stripe_customer(get_user_meta($user_id, 'mepr-df-stripe-customer-id', true));
    }
    else if(isset($_REQUEST['mepr_cc_num'])) {
      $args['card'] = array(
        'number'    => $_REQUEST['mepr_cc_num'],
        'exp_month' => $_REQUEST['mepr_cc_exp_month'],
        'exp_year'  => $_REQUEST['mepr_cc_exp_year'],
        'cvc'       => $_REQUEST['mepr_cvv_code']
      );
    }
    else {
      ob_start();
      $err = ob_get_clean();
      throw new MeprGatewayException( __('There was a problem sending your credit card details to the processor. Please try again later.' , 'memberpress') . ' 1 ' . $err );
    }

    $this->email_status('Stripe Charge Happening Now ... ' . MeprUtils::object_to_string($args), $this->settings->debug);

    $charge = (object)$this->send_stripe_request( 'charges', $args, 'post' );
    $this->email_status('Stripe Charge: ' . MeprUtils::object_to_string($charge), $this->settings->debug);

    $txn->trans_num = $charge->id;
    $txn->response = json_encode($charge);
    $txn->store();

    $this->email_status('Stripe Charge Happening Now ... 2', $this->settings->debug);

    $_REQUEST['data'] = $charge;

    return $this->record_payment();
  }

  /** Used to record a successful recurring payment by the given gateway. It
    * should have the ability to record a successful payment or a failure. It is
    * this method that should be used when receiving an IPN from PayPal or a
    * Silent Post from Authorize.net.
    */
  public function record_subscription_payment() {
    if(isset($_REQUEST['data'])) {
      $charge = (object)$_REQUEST['data'];

      // Make sure there's a valid subscription for this request and this payment hasn't already been recorded
      if( !isset($charge) or !isset($charge->customer) or
          !($sub = MeprSubscription::get_one_by_subscr_id($charge->customer)) or
          ( isset($charge->id) and MeprTransaction::txn_exists($charge->id) ) ) {
        return false;
      }

      $sub->subscr_id = $charge->customer; //Needs to be here to get around some funky GoDaddy caching issue
      $first_txn = $txn = $sub->first_txn();

      $this->email_status( "record_subscription_payment:" .
                           "\nSubscription: " . MeprUtils::object_to_string($sub, true) .
                           "\nTransaction: " . MeprUtils::object_to_string($txn, true),
                           $this->settings->debug);

      $txn = new MeprTransaction();
      $txn->user_id    = $sub->user_id;
      $txn->product_id = $sub->product_id;
      $txn->status     = MeprTransaction::$complete_str;
      $txn->coupon_id  = $first_txn->coupon_id;
      $txn->response   = json_encode($charge);
      $txn->trans_num  = $charge->id;
      $txn->gateway    = $this->id;
      $txn->subscription_id = $sub->id;

      if(MeprUtils::is_zero_decimal_currency()) {
        $txn->set_gross((float)$charge->amount);
      }
      else {
        $txn->set_gross((float)$charge->amount / 100);
      }

      $sdata = $this->send_stripe_request("customers/{$sub->subscr_id}", array(), 'get');

      // 'subscription' attribute went away in 2014-01-31
      //$txn->expires_at = MeprUtils::ts_to_mysql_date($sdata['subscription']['current_period_end'], 'Y-m-d 23:59:59');

      $this->email_status( "/customers/{$sub->subscr_id}\n" .
                           MeprUtils::object_to_string($sdata, true) .
                           MeprUtils::object_to_string($txn, true),
                           $this->settings->debug );

      $txn->store();

      $sub->status = MeprSubscription::$active_str;

      if($card = $this->get_card($charge)) {
        $sub->cc_exp_month = $card['exp_month'];
        $sub->cc_exp_year  = $card['exp_year'];
        $sub->cc_last4     = $card['last4'];
      }

      $sub->gateway = $this->id;
      $sub->store();
      // If a limit was set on the recurring cycles we need
      // to cancel the subscr if the txn_count >= limit_cycles_num
      // This is not possible natively with Stripe so we
      // just cancel the subscr when limit_cycles_num is hit
      $sub->limit_payment_cycles();

      $this->email_status( "Subscription Transaction\n" .
                           MeprUtils::object_to_string($txn->rec, true),
                           $this->settings->debug );

      MeprUtils::send_transaction_receipt_notices( $txn );
      MeprUtils::send_cc_expiration_notices( $txn );

      return $txn;
    }

    return false;
  }

  /** Used to record a declined payment. */
  public function record_payment_failure() {
    if(isset($_REQUEST['data'])) {
      $charge = (object)$_REQUEST['data'];
      $txn_res = MeprTransaction::get_one_by_trans_num($charge->id);

      if(is_object($txn_res) and isset($txn_res->id)) {
        $txn = new MeprTransaction($txn_res->id);
        $txn->status = MeprTransaction::$failed_str;
        $txn->store();
      }
      elseif(isset($charge) && isset($charge->customer) && ($sub = MeprSubscription::get_one_by_subscr_id($charge->customer))) {
        $first_txn = $sub->first_txn();
        $latest_txn = $sub->latest_txn();

        $txn = new MeprTransaction();
        $txn->user_id = $sub->user_id;
        $txn->product_id = $sub->product_id;
        $txn->coupon_id = $first_txn->coupon_id;
        $txn->txn_type = MeprTransaction::$payment_str;
        $txn->status = MeprTransaction::$failed_str;
        $txn->subscription_id = $sub->id;
        $txn->response = json_encode($_REQUEST);
        $txn->trans_num = $charge->id;
        $txn->gateway = $this->id;

        if(MeprUtils::is_zero_decimal_currency()) {
          $txn->set_gross((float)$charge->amount);
        }
        else {
          $txn->set_gross((float)$charge->amount / 100);
        }

        $txn->store();

        $sub->status = MeprSubscription::$active_str;
        $sub->gateway = $this->id;
        $sub->expire_txns(); //Expire associated transactions for the old subscription
        $sub->store();
      }
      else {
        return false; // Nothing we can do here ... so we outta here
      }

      MeprUtils::send_failed_txn_notices($txn);

      return $txn;
    }

    return false;
  }

  /** Used to record a successful payment by the given gateway. It should have
    * the ability to record a successful payment or a failure. It is this method
    * that should be used when receiving an IPN from PayPal or a Silent Post
    * from Authorize.net.
    */
  public function record_payment() {
    $this->email_status( "Starting record_payment: " . MeprUtils::object_to_string($_REQUEST), $this->settings->debug );
    if(isset($_REQUEST['data'])) {
      $charge = (object)$_REQUEST['data'];
      $this->email_status("record_payment: \n" . MeprUtils::object_to_string($charge, true) . "\n", $this->settings->debug);
      $obj = MeprTransaction::get_one_by_trans_num($charge->id);

      if(is_object($obj) and isset($obj->id)) {
        $txn = new MeprTransaction();
        $txn->load_data($obj);
        $usr = $txn->user();

        // Just short circuit if the txn has already completed
        if($txn->status == MeprTransaction::$complete_str)
          return;

        $txn->status    = MeprTransaction::$complete_str;
        $txn->response  = json_encode($charge);

        // This will only work before maybe_cancel_old_sub is run
        $upgrade = $txn->is_upgrade();
        $downgrade = $txn->is_downgrade();

        $txn->maybe_cancel_old_sub();
        $txn->store();

        $this->email_status("Standard Transaction\n" . MeprUtils::object_to_string($txn->rec, true) . "\n", $this->settings->debug);

        $prd = $txn->product();

        if( $prd->period_type=='lifetime' ) {
          if( $upgrade ) {
            $this->upgraded_sub($txn);
            MeprUtils::send_upgraded_txn_notices( $txn );
          }
          else if( $downgrade ) {
            $this->downgraded_sub($txn);
            MeprUtils::send_downgraded_txn_notices( $txn );
          }
          else {
            $this->new_sub($txn);
          }

          MeprUtils::send_signup_notices( $txn );
        }

        MeprUtils::send_transaction_receipt_notices( $txn );
        MeprUtils::send_cc_expiration_notices( $txn );
      }
    }

    return false;
  }

  /** This method should be used by the class to record a successful refund from
    * the gateway. This method should also be used by any IPN requests or Silent Posts.
    */
  public function process_refund(MeprTransaction $txn) {
    $args = MeprHooks::apply_filters('mepr_stripe_refund_args', array(), $txn);
    $refund = (object)$this->send_stripe_request( "charges/{$txn->trans_num}/refund", $args );
    $this->email_status( "Stripe Refund: " . MeprUtils::object_to_string($refund), $this->settings->debug );
    $_REQUEST['data'] = $refund;
    return $this->record_refund();
  }

  /** This method should be used by the class to record a successful refund from
    * the gateway. This method should also be used by any IPN requests or Silent Posts.
    */
  public function record_refund() {
    if(isset($_REQUEST['data']))
    {
      $charge = (object)$_REQUEST['data'];
      $obj = MeprTransaction::get_one_by_trans_num($charge->id);

      if(!is_null($obj) && (int)$obj->id > 0) {
        $txn = new MeprTransaction($obj->id);

        // Seriously ... if txn was already refunded what are we doing here?
        if($txn->status == MeprTransaction::$refunded_str) { return $txn->id; }

        $txn->status = MeprTransaction::$refunded_str;
        $txn->store();

        MeprUtils::send_refunded_txn_notices($txn);

        return $txn->id;
      }
    }

    return false;
  }

  public function process_trial_payment($txn) {
    $mepr_options = MeprOptions::fetch();
    $sub = $txn->subscription();

    // get the credit card details submitted by the form
    if(isset($_REQUEST['stripe_token']))
      $card = $_REQUEST['stripe_token'];
    elseif(isset($_REQUEST['mepr_cc_num'])) {
      $card = array( 'number'    => $_REQUEST['mepr_cc_num'],
                     'exp_month' => $_REQUEST['mepr_cc_exp_month'],
                     'exp_year'  => $_REQUEST['mepr_cc_exp_year'],
                     'cvc'       => $_REQUEST['mepr_cvv_code'] );
    }
    else {
      throw new MeprGatewayException( __('There was a problem sending your credit card details to the processor. Please try again later.', 'memberpress') );
    }

    $customer = $this->stripe_customer($txn->subscription_id, $card);

    //Prepare the $txn for the process_payment method
    $txn->set_subtotal($sub->trial_amount);
    $txn->status = MeprTransaction::$pending_str;

    unset($_REQUEST['stripe_token']);
    $_REQUEST['stripe_customer'] = $customer->id;

    //Attempt processing the payment here - the send_aim_request will throw the exceptions for us
    $this->process_payment($txn);

    return $this->record_trial_payment($txn);
  }

  public function record_trial_payment($txn) {
    $sub = $txn->subscription();

    //Update the txn member vars and store
    $txn->txn_type = MeprTransaction::$payment_str;
    $txn->status = MeprTransaction::$complete_str;
    $txn->expires_at = MeprUtils::ts_to_mysql_date(time() + MeprUtils::days($sub->trial_days), 'Y-m-d 23:59:59');
    $txn->store();

    return true;
  }

  /** Used to send subscription data to a given payment gateway. In gateways
    * which redirect before this step is necessary this method should just be
    * left blank.
    */
  public function process_create_subscription($txn) {
    if(isset($txn) and $txn instanceof MeprTransaction) {
      $usr = $txn->user();
      $prd = $txn->product();
    }
    else {
      throw new MeprGatewayException( __('Payment was unsuccessful, please check your payment details and try again.', 'memberpress') );
    }

    $mepr_options = MeprOptions::fetch();
    $sub = $txn->subscription();
    //error_log("********** MeprStripeGateway::process_create_subscription Subscription:\n" . MeprUtils::object_to_string($sub));

    //Get the customer -- if the $sub had a paid trial, then the customer was already setup

    $user_id = MeprUtils::get_current_user_id();//dorin
    if($sub->trial && $sub->trial_amount > 0.00) {
      $customer = $this->stripe_customer($txn->subscription_id);
    }
    elseif (get_user_meta($user_id, 'mepr-df-stripe-customer-id', true) != '') {
      // get the credit card details submitted by the form
      if(isset($_REQUEST['stripe_token'])) {
        $card = $_REQUEST['stripe_token'];
        $customer = $this->stripe_customer($txn->subscription_id, $card);
        $this->send_stripe_request("customers/{$customer->id}/sources", array('source' => $card));
      }
      else {
        //if ($_REQUEST['df_checkout_flag'] == 0){
          $customer = $this->stripe_customer(get_user_meta($user_id, 'mepr-df-stripe-customer-id', true));
          //$this->send_stripe_request("customers/{$customer->id}/sources", array('source' => $card));
          $this->send_stripe_request("customers/{$customer->id}", array('default_source' => $_REQUEST['current_card']));
        // } elseif ($_REQUEST['df_checkout_flag'] == 1) {
        //   $customer = $this->stripe_customer(get_user_meta($user_id, 'mepr-df-stripe-customer-id', true));
        //   $this->send_stripe_request("customers/{$customer->id}/sources", array('source' => $card));
        //  // $this->send_stripe_request("customers/{$customer->id}", array('default_source' => $_REQUEST['current_card']));
        // }
      }
      
    }
    else {
      // get the credit card details submitted by the form
      if(isset($_REQUEST['stripe_token'])) {
        $card = $_REQUEST['stripe_token'];
      }
      elseif(isset($_REQUEST['mepr_cc_num'])) {
        $card = array( 'number'    => $_REQUEST['mepr_cc_num'],
                       'exp_month' => $_REQUEST['mepr_cc_exp_month'],
                       'exp_year'  => $_REQUEST['mepr_cc_exp_year'],
                       'cvc'       => $_REQUEST['mepr_cvv_code'] );
      }
      else {
        throw new MeprGatewayException( __('There was a problem sending your credit card details to the processor. Please try again later.', 'memberpress') );
      }

      $customer = $this->stripe_customer($txn->subscription_id, $card);
    }

    $plan = $this->stripe_plan($txn->subscription(), true);

    // dorin's change - set payment meta
    //$user_id = MeprUtils::get_current_user_id();//dorin
    $mepr_billing_address = get_user_meta($user_id, 'mepr_billing_address', true);
    $mepr_city = get_user_meta($user_id, 'mepr_city', true);
    $mepr_province = get_user_meta($user_id, 'mepr_province', true);
    $mepr_zip = get_user_meta($user_id, 'mepr_zip', true);
    $mepr_country = get_user_meta($user_id, 'mepr_country', true);

    $args = MeprHooks::apply_filters('mepr_stripe_subscription_args', array(
      'plan' => $plan->id,
      'metadata' => array(
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'address' => $mepr_billing_address, //dorin's add
        'city' => $mepr_city, //dorin's add
        'province' => $mepr_province, //dorin's add
        'country' => $mepr_country //dorin's add
      ),
      'tax_percent' => MeprUtils::format_float($txn->tax_rate) //Can't do 3 decimal places here for some reason
    ), $txn, $sub);

    $this->email_status("process_create_subscription: \n" . MeprUtils::object_to_string($txn, true) . "\n", $this->settings->debug);
    $subscr = $this->send_stripe_request("customers/{$customer->id}/subscriptions", $args);

    $sub->subscr_id = $customer->id;
    $sub->store();

    //error_log("********** MeprStripeGateway::process_create_subscription altered Subscription:\n" . MeprUtils::object_to_string($sub));

    $_REQUEST['data'] = $customer;

    return $this->record_create_subscription();
  }

  /** Used to record a successful subscription by the given gateway. It should have
    * the ability to record a successful subscription or a failure. It is this method
    * that should be used when receiving an IPN from PayPal or a Silent Post
    * from Authorize.net.
    */
  public function record_create_subscription() {
    $mepr_options = MeprOptions::fetch();

    if(isset($_REQUEST['data'])) {
      $sdata = (object)$_REQUEST['data'];
      //error_log("********** MeprStripeGateway::record_create_subscription sData: \n" . MeprUtils::object_to_string($sdata));
      $sub = MeprSubscription::get_one_by_subscr_id($sdata->id);
      //error_log("********** MeprStripeGateway::record_create_subscription Subscription: \n" . MeprUtils::object_to_string($sub));
      $sub->response=$sdata;
      $sub->status=MeprSubscription::$active_str;

      if($card = $this->get_default_card($sdata)) {
        $sub->cc_last4 = $card['last4'];
        $sub->cc_exp_month = $card['exp_month'];
        $sub->cc_exp_year = $card['exp_year'];
      }

      $sub->created_at = gmdate('c');
      $sub->store();

      // This will only work before maybe_cancel_old_sub is run
      $upgrade = $sub->is_upgrade();
      $downgrade = $sub->is_downgrade();

      $sub->maybe_cancel_old_sub();

      $txn = $sub->first_txn();
      $old_total = $txn->total;

      // If no trial or trial amount is zero then we've got to make
      // sure the confirmation txn lasts through the trial
      if(!$sub->trial || ($sub->trial and $sub->trial_amount <= 0.00)) {
        $trial_days = ($sub->trial)?$sub->trial_days:$mepr_options->grace_init_days;

        $txn->trans_num  = $sub->subscr_id;
        $txn->status     = MeprTransaction::$confirmed_str;
        $txn->txn_type   = MeprTransaction::$subscription_confirmation_str;
        $txn->response   = (string)$sub;
        $txn->expires_at = MeprUtils::ts_to_mysql_date(time() + MeprUtils::days($trial_days), 'Y-m-d 23:59:59');
        $txn->set_subtotal(0.00); // Just a confirmation txn
        $txn->store();
      }

      // $txn->set_gross($old_total); // Artificially set the subscription amount

      if($upgrade) {
        $this->upgraded_sub($sub);
        MeprUtils::send_upgraded_sub_notices($sub);
      }
      else if($downgrade) {
        $this->downgraded_sub($sub);
        MeprUtils::send_downgraded_sub_notices($sub);
      }
      else {
        $this->new_sub($sub);
        MeprUtils::send_new_sub_notices($sub);
      }

      MeprUtils::send_signup_notices( $txn );

      return array('subscription' => $sub, 'transaction' => $txn);
    }

    return false;
  }

  public function process_update_subscription($sub_id) {
    $mepr_options = MeprOptions::fetch();
    $sub = new MeprSubscription($sub_id);

    //Fix for duplicate token errors when the_content is somehow run more than once despite our best efforts to avoid it
    static $subscr;
    if(!is_null($subscr)) { return $subscr; }

    if(!isset($_REQUEST['stripe_token'])) {
      ob_start();
      print_r($_REQUEST);
      $err = ob_get_clean();
      throw new MeprGatewayException(__('There was a problem sending your credit card details to the processor. Please try again later.' , 'memberpress') . ' 3 ' . $err);
    }

    // get the credit card details submitted by the form
    $token    = $_REQUEST['stripe_token'];
    $customer = $this->stripe_customer($sub_id, $token);

    //TODO check for $customer === false - do better error handling here

    $usr = $sub->user();

    $args = MeprHooks::apply_filters('mepr_stripe_update_subscription_args', array("card" => $token), $sub);

    $subscr = (object)$this->send_stripe_request("customers/{$customer->id}", $args, 'post');
    $sub->subscr_id = $subscr->id;

    if($card = $this->get_default_card($subscr)) {
      $sub->cc_last4 = $card['last4'];
      $sub->cc_exp_month = $card['exp_month'];
      $sub->cc_exp_year = $card['exp_year'];
    }

    $sub->response = $subscr;
    $sub->store();

    return $subscr;
  }

  /** This method should be used by the class to record a successful cancellation
    * from the gateway. This method should also be used by any IPN requests or
    * Silent Posts.
    */
  public function record_update_subscription() {
    // No need for this one with stripe
  }

  /** Used to suspend a subscription by the given gateway.
    */
  public function process_suspend_subscription($sub_id) {
    $mepr_options = MeprOptions::fetch();
    $sub = new MeprSubscription($sub_id);

    // If there's not already a customer then we're done here
    if(!($customer = $this->stripe_customer($sub_id))) { return false; }

    $args = MeprHooks::apply_filters('mepr_stripe_suspend_subscription_args', array(), $sub);

    // Yeah ... we're cancelling here bro ... with stripe we should be able to restart again
    $res = $this->send_stripe_request( "customers/{$customer->id}/subscription", $args, 'delete' );
    $_REQUEST['data'] = $res;

    return $this->record_suspend_subscription();
  }

  /** This method should be used by the class to record a successful suspension
    * from the gateway.
    */
  public function record_suspend_subscription() {
    if(isset($_REQUEST['data']))
    {
      $sdata = (object)$_REQUEST['data'];
      if( $sub = MeprSubscription::get_one_by_subscr_id($sdata->customer) ) {
        // Seriously ... if sub was already cancelled what are we doing here?
        if($sub->status == MeprSubscription::$suspended_str) { return $sub; }

        $sub->status = MeprSubscription::$suspended_str;
        $sub->store();

        MeprUtils::send_suspended_sub_notices($sub);
      }
    }

    return false;
  }

  /** Used to suspend a subscription by the given gateway.
    */
  public function process_resume_subscription($sub_id) {
    $mepr_options = MeprOptions::fetch();
    $sub = new MeprSubscription($sub_id);

    $customer = $this->stripe_customer($sub_id);

    //Set enough of the $customer data here to get this resumed
    if(empty($customer)) { $customer = (object)array('id' => $sub->subscr_id); }

    $orig_trial        = $sub->trial;
    $orig_trial_days   = $sub->trial_days;
    $orig_trial_amount = $sub->trial_amount;

    if( $sub->is_expired() and !$sub->is_lifetime()) {
      $exptxn = $sub->expiring_txn();

      // if it's already expired with a real transaction
      // then we want to resume immediately
      if($exptxn->status!=MeprTransaction::$confirmed_str) {
        $sub->trial = false;
        $sub->trial_days = 0;
        $sub->trial_amount = 0.00;
        $sub->store();
      }
    }
    else {
      $sub->trial = true;
      $sub->trial_days = MeprUtils::tsdays(strtotime($sub->expires_at) - time());
      $sub->trial_amount = 0.00;
      $sub->store();
    }

    // Create new plan with optional trial in place ...
    $plan = $this->stripe_plan($sub,true);

    $sub->trial        = $orig_trial;
    $sub->trial_days   = $orig_trial_days;
    $sub->trial_amount = $orig_trial_amount;
    $sub->store();

    $args = MeprHooks::apply_filters('mepr_stripe_resume_subscription_args', array( 'plan' => $plan->id ), $sub);

    $this->email_status( "process_resume_subscription: \n" .
                         MeprUtils::object_to_string($sub, true) . "\n",
                         $this->settings->debug );

    $subscr = $this->send_stripe_request( "customers/{$sub->subscr_id}/subscription", $args, 'post' );

    $_REQUEST['data'] = $customer;
    return $this->record_resume_subscription();
  }

  /** This method should be used by the class to record a successful resuming of
    * as subscription from the gateway.
    */
  public function record_resume_subscription() {
    if(isset($_REQUEST['data'])) {
      $mepr_options = MeprOptions::fetch();

      $sdata = (object)$_REQUEST['data'];
      $sub = MeprSubscription::get_one_by_subscr_id($sdata->id);
      $sub->response=$sdata;
      $sub->status=MeprSubscription::$active_str;

      if( $card = $this->get_default_card($sdata) ) {
        $sub->cc_last4 = $card['last4'];
        $sub->cc_exp_month = $card['exp_month'];
        $sub->cc_exp_year = $card['exp_year'];
      }

      $sub->store();

      //Check if prior txn is expired yet or not, if so create a temporary txn so the user can access the content immediately
      $prior_txn = $sub->latest_txn();
      if(strtotime($prior_txn->expires_at) < time()) {
        $txn = new MeprTransaction();
        $txn->subscription_id = $sub->id;
        $txn->trans_num  = $sub->subscr_id . '-' . uniqid();
        $txn->status     = MeprTransaction::$confirmed_str;
        $txn->txn_type   = MeprTransaction::$subscription_confirmation_str;
        $txn->response   = (string)$sub;
        $txn->expires_at = MeprUtils::ts_to_mysql_date(time()+MeprUtils::days(0), 'Y-m-d 23:59:59');
        $txn->set_subtotal(0.00); // Just a confirmation txn
        $txn->store();
      }

      MeprUtils::send_resumed_sub_notices($sub);

      return array('subscription' => $sub, 'transaction' => (isset($txn))?$txn:$prior_txn);
    }

    return false;
  }

  /** Used to cancel a subscription by the given gateway. This method should be used
    * by the class to record a successful cancellation from the gateway. This method
    * should also be used by any IPN requests or Silent Posts.
    */
  public function process_cancel_subscription($sub_id) {
    $mepr_options = MeprOptions::fetch();
    $sub = new MeprSubscription($sub_id);

    // If there's not already a customer then we're done here
    if(!($customer = $this->stripe_customer($sub_id))) { return false; }

    $args = MeprHooks::apply_filters('mepr_stripe_cancel_subscription_args', array(), $sub);

    $res = $this->send_stripe_request( "customers/{$customer->id}/subscription", $args, 'delete' );
    $_REQUEST['data'] = $res;

    return $this->record_cancel_subscription();
  }

  /** This method should be used by the class to record a successful cancellation
    * from the gateway. This method should also be used by any IPN requests or
    * Silent Posts.
    */
  public function record_cancel_subscription() {
    if(isset($_REQUEST['data']))
    {
      $sdata = (object)$_REQUEST['data'];
      if( $sub = MeprSubscription::get_one_by_subscr_id($sdata->customer) ) {
        // Seriously ... if sub was already cancelled what are we doing here?
        // Also, for stripe, since a suspension is only slightly different
        // than a cancellation, we kick it into high gear and check for that too
        if($sub->status == MeprSubscription::$cancelled_str or
           $sub->status == MeprSubscription::$suspended_str) { return $sub; }

        $sub->status = MeprSubscription::$cancelled_str;
        $sub->store();

        if(isset($_REQUEST['expire']))
          $sub->limit_reached_actions();

        if(!isset($_REQUEST['silent']) || ($_REQUEST['silent']==false))
          MeprUtils::send_cancelled_sub_notices($sub);
      }
    }

    return false;
  }

  /** This gets called on the 'init' hook when the signup form is processed ...
    * this is in place so that payment solutions like paypal can redirect
    * before any content is rendered.
  */
  public function process_signup_form($txn) {
    //if($txn->amount <= 0.00) {
    //  MeprTransaction::create_free_transaction($txn);
    //  return;
    //}
  }

  public function display_payment_page($txn) {
    // Nothing to do here ...
  }

  /** This gets called on wp_enqueue_script and enqueues a set of
    * scripts for use on the page containing the payment form
    */
  public function enqueue_payment_form_scripts() {
    wp_enqueue_script('stripe-js', 'https://js.stripe.com/v1/', array(), MEPR_VERSION);
    wp_enqueue_script('stripe-create-token', MEPR_GATEWAYS_URL . '/stripe/create_token.js', array('stripe-js', 'mepr-checkout-js', 'jquery.payment'), MEPR_VERSION);
    wp_localize_script('stripe-create-token', 'MeprStripeGateway', array( 'public_key' => $this->settings->public_key ));
  }

  /** This gets called on the_content and just renders the payment form
    */
  public function display_payment_form($amount, $user, $product_id, $txn_id) {
    $mepr_options = MeprOptions::fetch();
    $prd = new MeprProduct($product_id);
    $coupon = false;

    $txn = new MeprTransaction($txn_id);

    //Artifically set the price of the $prd in case a coupon was used
    if($prd->price != $amount)
    {
      $coupon = true;
      $prd->price = $amount;
    }

    $invoice = MeprTransactionsHelper::get_invoice($txn);
    //dorin's change: cost format
    if( $sub = $txn->subscription() ) {
        $sub_price_str = MeprSubscriptionsHelper::format_currency($sub);
    }else{
      $sub_price_str = MeprAppHelper::format_currency($prd->price, true, false);
    }
    ?>
    <div class="col-md-8 content-area" id="primary">
      <main class="site-main" id="main">
        <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
          <div class="entry-content">
            <div class="mp_wrapper mp_payment_form_wrapper df-checkout-wrapper">
              <form action="<?php //echo $prd->url('',true); //COMMENTING THIS OUT FOR https://github.com/caseproof/memberpress/issues/790  ?>" method="post" id="payment-form" class="mepr-checkout-form mepr-form mepr-card-form" novalidate>
                <?php 
                  $current_customer_id = get_user_meta($user->ID, 'mepr-df-stripe-customer-id', true); 
                  $customer = $this->stripe_customer($current_customer_id);
                ?>
                <input type="hidden" name="mepr_process_payment_form" value="Y" />
                <input type="hidden" name="mepr_transaction_id" value="<?php echo $txn_id; ?>" />
                <input type="hidden" class="card-name" value="<?php echo $user->get_full_name(); ?>" />
                <input type="hidden" id="card-info" name="card-info" value="<?php echo $current_customer_id; ?>" />
                <input type="hidden" name="df_checkout_flag" id="df_checkout_flag" value="<?php echo ($current_customer_id == '')? '1':'0';?>" />

                <?php if($mepr_options->show_address_fields && $mepr_options->require_address_fields): ?>
                  <input type="hidden" class="card-address-1" value="<?php echo get_user_meta($user->ID, 'mepr-address-one', true); ?>" />
                  <input type="hidden" class="card-address-2" value="<?php echo get_user_meta($user->ID, 'mepr-address-two', true); ?>" />
                  <input type="hidden" class="card-city" value="<?php echo get_user_meta($user->ID, 'mepr-address-city', true); ?>" />
                  <input type="hidden" class="card-state" value="<?php echo get_user_meta($user->ID, 'mepr-address-state', true); ?>" />
                  <input type="hidden" class="card-zip" value="<?php echo get_user_meta($user->ID, 'mepr-address-zip', true); ?>" />
                  <input type="hidden" class="card-country" value="<?php echo get_user_meta($user->ID, 'mepr-address-country', true); ?>" />
                <?php endif; ?>

                <div class="mepr-stripe-errors"></div>
                <?php MeprView::render('/shared/errors', get_defined_vars()); ?>
                <!-- custom checkout form -->
                <div class="checkout-section">
                  <h1>Step1: Account Info</h1>
                  <div class="checkout-section-body">
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">First Name:</label>
                      <div class="col-sm-10 col-12">
                        <input type="text" name="user_first_name" id="user_first_name" class="mepr-form-input form-control" value="<?php echo $user->first_name; ?>"  placeholder="Please enter your first name" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">Last Name:</label>
                      <div class="col-sm-10 col-12">
                        <input type="text" name="user_last_name" id="user_last_name" class="mepr-form-input form-control" value="<?php echo $user->last_name; ?>" placeholder="Please enter your last name" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">Email:</label>
                      <div class="col-sm-10 col-12">
                        <input type="email" name="user_email" id="user_email" class="form-control" value="<?php echo (isset($user->user_email))?esc_attr(stripslashes($user->user_email)):''; ?>" required placeholder="Please enter your email address"/>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="checkout-section">
                  <h1>Step2: Billing Info</h1>
                  <div class="checkout-section-body">
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">Address:</label>
                      <div class="col-sm-10 col-12">
                        <input class="form-control card-address-1" type="text" name="mepr_billing_address" id="mepr_billing_address" value="<?php echo get_user_meta($user->ID, 'mepr_billing_address', true); ?>"  placeholder="Please enter your address" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">City:</label>
                      <div class="col-sm-10 col-12">
                        <input class="form-control card-city" type="text" id="mepr_city" name="mepr_city" value="<?php echo get_user_meta($user->ID, 'mepr_city', true); ?>" placeholder="Please enter city name" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">State:</label>
                      <div class="col-sm-10 col-12">
                        <input class="form-control card-state" type="text" id="mepr_province" name="mepr_province" value="<?php echo get_user_meta($user->ID, 'mepr_province', true); ?>" placeholder="Please enter state" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">Zip:</label>
                      <div class="col-sm-10 col-12">
                        <input class="form-control card-zip" type="text" id="mepr_zip" name="mepr_zip" value="<?php echo get_user_meta($user->ID, 'mepr_zip', true); ?>" placeholder="Enter zip/postal code" />
                      </div>
                    </div>
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">Country:</label>
                      <div class="col-sm-10 col-12">

                        <select class="select grayColor mepr-form-input mepr-select-field" id="mepr_country" name="mepr_country">
                        <option value="" disabled selected>Country</option>
                        <?php
                          foreach(self::$country_list as $key=>$country){
                        ?>
                        <option value="<?php echo strtolower($key);?>" <?php echo (strtolower($key)==get_user_meta($user->ID, 'mepr_country', true))?'selected':'';?>><?php echo $country['name']?></option>
                        <?php    
                          }
                        ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="checkout-section">
                  <h1>Step3: Payment Info</h1>
                  <?php
                    if ($current_customer_id != ''){
                      $current_cus_obj = $this->get_customer_object($customer->id);
                      $current_cards = $this->get_all_cards_by_cus($current_cus_obj);
                      $default_card = $this->get_default_card_by_cus($current_cards);
                    }
                    
                  ?>
                  <?php

                        $customer = $this->stripe_customer($current_customer_id);
                        $card = $this->get_default_card($customer);
                    ?>
                  <?php if ($current_customer_id == ''){?>
                  <div class="checkout-section-body">
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">Cards:</label>
                      <div class="col-sm-10 col-12">
                        <select class="select grayColor mepr-form-input mepr-select-field " id="current_card" name="current_card">
                          <?php foreach($current_cards as $ccard){?>
                          <option value="<?php echo $ccard['id']?>" <?php echo ($ccard['id'] == $default_card)?'selected':''?>><?php echo $ccard['brand'].' ending in '.$ccard['last4'];?></option>
                          <?php }?>
                          <option value="add-new-card">Add New Card</option>
                        </select>
                      </div>
                      <!--
                      <div class="col-sm-4 col-4">
                        <a class="new-card btn dfbtn-green-default" href="javascript:void(0);">Use a new card</a>
                      </div>
                      -->
                    </div>
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">Card Number:</label>
                      <div class="col-sm-10 col-12">
                        <input type="tel" class="mepr-form-input card-number cc-number validation form-control" pattern="\d*" autocomplete="cc-number" required>
                      </div>
                    </div>
                    <input type="hidden" name="mepr-cc-type" class="cc-type" value="" />
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2  col-form-label">Expiration Date:</label>
                      <div class="col-sm-4 col-12">
                        <input type="tel" class="mepr-form-input cc-exp validation form-control" pattern="\d*" autocomplete="cc-exp" placeholder="<?php _ex('mm/yy', 'ui', 'memberpress'); ?>" required>
                      </div>
                      <label for="example-text-input" class="col-sm-2 col-form-label">CVC Code:</label>
                      <div class="col-sm-4 col-12">
                        <input type="tel" class="mepr-form-input card-cvc cc-cvc validation form-control" pattern="\d*" autocomplete="off" required>
                        <span class="cvc-tooltip" data-toggle="tooltip" data-placement="top" title="3 or 4-digit code on the back of your card, next to your account number">
                          What's this?
                        </span>
                      </div>
                    </div>
                    
                  </div>
                  <?php } else {?>
                  <div class="checkout-section-body">
                    <div class="form-group row">
                      <label for="example-text-input" class="col-sm-2 col-form-label">Cards:</label>
                      <div class="col-sm-10 col-12">
                        <select class="select grayColor mepr-form-input mepr-select-field " id="current_card" name="current_card">
                          <?php foreach($current_cards as $ccard){?>
                          <option value="<?php echo $ccard['id']?>" <?php echo ($ccard['id'] == $default_card)?'selected':''?>><?php echo $ccard['brand'].' ending in '.$ccard['last4'];?></option>
                          <?php }?>
                          <option value="add-new-card">Add New Card</option>
                        </select>
                      </div>
                      <!--
                      <div class="col-sm-4 col-4">
                        <a class="new-card btn dfbtn-green-default" href="javascript:void(0);">Use a new card</a>
                      </div>
                      -->
                    </div>
                    <div class="form-group row add-new-card-section">
                      <label for="example-text-input" class="col-sm-2 col-form-label">Card Number:</label>
                      <div class="col-sm-10 col-12">
                        <input type="tel" class="mepr-form-input card-number cc-number validation form-control" pattern="\d*" autocomplete="cc-number" required value="<?php echo MeprUtils::cc_num($card['last4']);?>" disabled="disabled">
                      </div>
                    </div>
                    <input type="hidden" name="mepr-cc-type" class="cc-type" value="" />
                    <div class="form-group row add-new-card-section">
                      <label for="example-text-input" class="col-sm-2  col-form-label">Expiration Date:</label>
                      <div class="col-sm-4 col-12">
                        <input type="tel" class="mepr-form-input cc-exp validation form-control" pattern="\d*" autocomplete="cc-exp" placeholder="<?php _ex('mm/yy', 'ui', 'memberpress'); ?>" required  value="12/30" disabled="disabled">
                      </div>
                      <label for="example-text-input" class="col-sm-2 col-form-label">CVC Code:</label>
                      <div class="col-sm-4 col-12">
                        <input type="tel" class="mepr-form-input card-cvc cc-cvc validation form-control" pattern="\d*" autocomplete="off" required  placeholder="123" disabled="disabled">
                        <span class="cvc-tooltip" data-toggle="tooltip" data-placement="top" title="3 or 4-digit code on the back of your card, next to your account number">
                          What's this?
                        </span>
                      </div>
                    </div>
                  </div>
                  <?php }?>
                </div>



                <!-- end custom checkout form -->

                <?php
                  //MeprUsersHelper::render_custom_fields();
                  MeprHooks::do_action('mepr-account-home-fields', $user);
                ?>
                <!--
                <div class="mp-form-row">
                  <div class="mp-form-label">
                    <label><?php _ex('Credit Card Number', 'ui', 'memberpress'); ?></label>
                    <span class="cc-error"><?php _ex('Invalid Credit Card Number', 'ui', 'memberpress'); ?></span>
                  </div>
                  <input type="tel" class="mepr-form-input card-number cc-number validation" pattern="\d*" autocomplete="cc-number" required>
                </div>

                <input type="hidden" name="mepr-cc-type" class="cc-type" value="" />

                <div class="mp-form-row">
                  <div class="mp-form-label">
                    <label><?php _ex('Expiration', 'ui', 'memberpress'); ?></label>
                    <span class="cc-error"><?php _ex('Invalid Expiration', 'ui', 'memberpress'); ?></span>
                  </div>
                  <input type="tel" class="mepr-form-input cc-exp validation" pattern="\d*" autocomplete="cc-exp" placeholder="<?php _ex('mm/yy', 'ui', 'memberpress'); ?>" required>
                  <?php //$this->months_dropdown('','card-expiry-month',isset($_REQUEST['card-expiry-month'])?$_REQUEST['card-expiry-month']:'',true); ?>
                  <?php //$this->years_dropdown('','card-expiry-year',isset($_REQUEST['card-expiry-year'])?$_REQUEST['card-expiry-year']:''); ?>
                </div>

                <div class="mp-form-row">
                  <div class="mp-form-label">
                    <label><?php _ex('CVC', 'ui', 'memberpress'); ?></label>
                    <span class="cc-error"><?php _ex('Invalid CVC Code', 'ui', 'memberpress'); ?></span>
                  </div>
                  <input type="tel" class="mepr-form-input card-cvc cc-cvc validation" pattern="\d*" autocomplete="off" required>
                </div>
                -->
                <?php MeprHooks::do_action('mepr-stripe-payment-form', $txn); ?>

                <?php
                  if( $sub = $txn->subscription() ) {
                ?>
                <div class="checkout-subscription">
                  <h1><?php echo $prd->post_title."Monthly Subscription";?></h1>
                  <ul><li><span><?php echo "Just ".$sub_price_str;?></span></li></ul>
                  <div class="subtotal">
                    <div class="row">
                      <div class="col-6 price-txt">Price:</div>
                      <div class="col-6 price-value"><?php echo MeprAppHelper::format_currency($prd->price, true, true); ?></div>
                    </div>
                  </div>
                </div>
                <?php
                  }else{
                    echo $invoice;
                  }
                ?>
                <input type="submit" class="mepr-submit dfbtn-order btn" value="<?php _ex('SUBMIT ORDER', 'ui', 'memberpress'); ?>" />
                <img src="<?php echo admin_url('images/loading.gif'); ?>" style="display: none;" class="mepr-loading-gif" />
                <?php //MeprView::render('/shared/has_errors', get_defined_vars()); ?>

                <noscript><p class="mepr_nojs"><?php _e('Javascript is disabled in your browser. You will not be able to complete your purchase until you either enable JavaScript in your browser, or switch to a browser that supports it.', 'memberpress'); ?></p></noscript>
              </form>
              <div class="checkout-identity">
                  <div class="row">
                    <div class="col-3 flex-div"><img class="d-flex align-self-center" src="/wp-content/themes/digital-freelancer/dist/images/identity1.png" /></div>
                    <div class="col-3 flex-div"><img class="d-flex align-self-center" src="/wp-content/themes/digital-freelancer/dist/images/identity2.png" /></div>
                    <div class="col-3 flex-div"><img class="d-flex align-self-center" src="/wp-content/themes/digital-freelancer/dist/images/identity3.png" /></div>
                    <div class="col-3 flex-div"><img class="d-flex align-self-center" src="/wp-content/themes/digital-freelancer/dist/images/identity4.png" /></div>
                  </div>
                </div>
            </div>
          </div><!-- .entry-content -->
        </article><!-- #post-## -->
      </main><!-- #main -->
    </div><!-- #primary -->
    <!-- Do the right sidebar check -->
    <div class="col-md-4 widget-area df-checkout-right-sidebar" id="checkout-right-sidebar" role="complementary">
      <aside id="checkout-img-section" class="widget widget_checkout_img_section">
        <img src="/wp-content/themes/digital-freelancer/dist/images/pipeline.png" />
      </aside>
      <aside id="checkout-product-detail-section" class="widget widget_product_detail_section">
        <h3 class="widget-title">Product Details</h3>
        <p><span>Cost:</span> <?php echo $sub_price_str;?> <br />
        <span>Level:</span> <?php echo $prd->post_title;?></p>
      </aside>
      <aside id="checkout-guarantee" class="widget widget_guarantee">
        <h3 class="widget-title">Money Back Guarantee</h3>
        <p>All of DigitalFreelancers’s products are backed by our 30 day money back guarantee.</p>
      </aside>
      <?php get_sidebar( 'checkout' ); ?>
      <!--
      <aside id="checkout-get" class="widget widget_get">
        <h3 class="widget-title">What You’ll Get</h3>
        <ul>
          <li>Feature 1</li>
          <li>Feature 2</li>
          <li>Feature 3</li>
          <li>Feature 4</li>
          <li>Feature 5</li>
        </ul>
      </aside>
      -->
    </div>
    <?php

  }

  /** Validates the payment form before a payment is processed */
  public function validate_payment_form($errors) {
    // This is done in the javascript with Stripe
  }

  /** Displays the form for the given payment gateway on the MemberPress Options page */
  public function display_options_form() {
    $mepr_options = MeprOptions::fetch();

    $test_secret_key = trim($this->settings->api_keys['test']['secret']);
    $test_public_key = trim($this->settings->api_keys['test']['public']);
    $live_secret_key = trim($this->settings->api_keys['live']['secret']);
    $live_public_key = trim($this->settings->api_keys['live']['public']);
    $force_ssl       = ($this->settings->force_ssl == 'on' or $this->settings->force_ssl == true);
    $debug           = ($this->settings->debug == 'on' or $this->settings->debug == true);
    $test_mode       = ($this->settings->test_mode == 'on' or $this->settings->test_mode == true);

    ?>
    <table id="mepr-stripe-test-keys-<?php echo $this->id; ?>" class="mepr-stripe-test-keys mepr-hidden">
      <tr>
        <td><?php _e('Test Secret Key*:', 'memberpress'); ?></td>
        <td><input type="text" class="mepr-auto-trim" name="<?php echo $mepr_options->integrations_str; ?>[<?php echo $this->id;?>][api_keys][test][secret]" value="<?php echo $test_secret_key; ?>" /></td>
      </tr>
      <tr>
        <td><?php _e('Test Publishable Key*:', 'memberpress'); ?></td>
        <td><input type="text" class="mepr-auto-trim" name="<?php echo $mepr_options->integrations_str; ?>[<?php echo $this->id;?>][api_keys][test][public]" value="<?php echo $test_public_key; ?>" /></td>
      </tr>
    </table>
    <table id="mepr-stripe-live-keys-<?php echo $this->id; ?>" class="mepr-stripe-live-keys mepr-hidden">
      <tr>
        <td><?php _e('Live Secret Key*:', 'memberpress'); ?></td>
        <td><input type="text" class="mepr-auto-trim" name="<?php echo $mepr_options->integrations_str; ?>[<?php echo $this->id;?>][api_keys][live][secret]" value="<?php echo $live_secret_key; ?>" /></td>
      </tr>
      <tr>
        <td><?php _e('Live Publishable Key*:', 'memberpress'); ?></td>
        <td><input type="text" class="mepr-auto-trim" name="<?php echo $mepr_options->integrations_str; ?>[<?php echo $this->id;?>][api_keys][live][public]" value="<?php echo $live_public_key; ?>" /></td>
      </tr>
    </table>
    <table>
      <tr>
        <td colspan="2"><input class="mepr-stripe-testmode" data-integration="<?php echo $this->id; ?>" type="checkbox" name="<?php echo $mepr_options->integrations_str; ?>[<?php echo $this->id;?>][test_mode]"<?php echo checked($test_mode); ?> />&nbsp;<?php _e('Test Mode', 'memberpress'); ?></td>
      </tr>
      <tr>
        <td colspan="2"><input type="checkbox" name="<?php echo $mepr_options->integrations_str; ?>[<?php echo $this->id;?>][force_ssl]"<?php echo checked($force_ssl); ?> />&nbsp;<?php _e('Force SSL', 'memberpress'); ?></td>
      </tr>
      <tr>
        <td colspan="2"><input type="checkbox" name="<?php echo $mepr_options->integrations_str; ?>[<?php echo $this->id;?>][debug]"<?php echo checked($debug); ?> />&nbsp;<?php _e('Send Debug Emails', 'memberpress'); ?></td>
      </tr>
      <tr>
        <td><?php _e('Stripe Webhook URL:', 'memberpress'); ?></td>
        <td><input type="text" onfocus="this.select();" onclick="this.select();" readonly="true" class="clippy_input" value="<?php echo $this->notify_url('whk'); ?>" /><span class="clippy"><?php echo $this->notify_url('whk'); ?></span></td>
      </tr>
    </table>
    <?php
  }

  /** Validates the form for the given payment gateway on the MemberPress Options page */
  public function validate_options_form($errors) {
    $mepr_options = MeprOptions::fetch();

    $testmode = isset($_REQUEST[$mepr_options->integrations_str][$this->id]['test_mode']);
    $testmodestr  = $testmode ? 'test' : 'live';

    if( !isset($_REQUEST[$mepr_options->integrations_str][$this->id]['api_keys'][$testmodestr]['secret']) or
         empty($_REQUEST[$mepr_options->integrations_str][$this->id]['api_keys'][$testmodestr]['secret']) or
        !isset($_REQUEST[$mepr_options->integrations_str][$this->id]['api_keys'][$testmodestr]['public']) or
         empty($_REQUEST[$mepr_options->integrations_str][$this->id]['api_keys'][$testmodestr]['public']) ) {
      $errors[] = __("All Stripe keys must be filled in.", 'memberpress');
    }

    return $errors;
  }

  /** This gets called on wp_enqueue_script and enqueues a set of
    * scripts for use on the front end user account page.
    */
  public function enqueue_user_account_scripts() {
    $sub = (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['sub'])) ? new MeprSubscription((int)$_GET['sub']) : false;
    if($sub !== false && $sub->gateway == $this->id) {
      wp_enqueue_script('stripe-js', 'https://js.stripe.com/v1/', array(), MEPR_VERSION);
      wp_enqueue_script('stripe-create-token', MEPR_GATEWAYS_URL . '/stripe/create_token.js', array('stripe-js', 'mepr-checkout-js', 'jquery.payment'), MEPR_VERSION);
      wp_localize_script('stripe-create-token', 'MeprStripeGateway', array( 'public_key' => $this->settings->public_key ));
    }
  }

  /** Displays the update account form on the subscription account page **/
  public function display_update_account_form($sub_id, $errors=array(), $message='') {
    $mepr_options = MeprOptions::fetch();
    $customer = $this->stripe_customer($sub_id);
    $sub = new MeprSubscription($sub_id);
    $usr = $sub->user();

    $cc_exp_month = isset($_REQUEST['card-expiry-month'])?$_REQUEST['card-expiry-month']:$sub->cc_exp_month;
    $cc_exp_year = isset($_REQUEST['card-expiry-year'])?$_REQUEST['card-expiry-year']:$sub->cc_exp_year;

    if( $card = $this->get_default_card($customer) ) {
      $card_num = MeprUtils::cc_num($card['last4']);
      $card_name = ( isset($card['name']) and $card['name']!='undefined' ) ? $card['name'] : $usr->get_full_name();
    }
    else {
      $card_num = $sub->cc_num();
      $card_name = $usr->get_full_name();
    }

    ?>
    <div class="mp_wrapper">
      <form action="" method="post" id="payment-form" class="mepr-checkout-form mepr-form" novalidate>
        <input type="hidden" name="_mepr_nonce" value="<?php echo wp_create_nonce('mepr_process_update_account_form'); ?>" />
        <input type="hidden" class="card-name" value="<?php echo $card_name; ?>" />

        <?php if($mepr_options->show_address_fields && $mepr_options->require_address_fields): ?>
          <input type="hidden" class="card-address-1" value="<?php echo get_user_meta($usr->ID, 'mepr-address-one', true); ?>" />
          <input type="hidden" class="card-address-2" value="<?php echo get_user_meta($usr->ID, 'mepr-address-two', true); ?>" />
          <input type="hidden" class="card-city" value="<?php echo get_user_meta($usr->ID, 'mepr-address-city', true); ?>" />
          <input type="hidden" class="card-state" value="<?php echo get_user_meta($usr->ID, 'mepr-address-state', true); ?>" />
          <input type="hidden" class="card-zip" value="<?php echo get_user_meta($usr->ID, 'mepr-address-zip', true); ?>" />
          <input type="hidden" class="card-country" value="<?php echo get_user_meta($usr->ID, 'mepr-address-country', true); ?>" />
        <?php endif; ?>

        <div class="mepr_update_account_table">
          <div><strong><?php _e('Update your Credit Card information below', 'memberpress'); ?></strong></div><br/>

          <div class="mepr-stripe-errors"></div>
          <?php MeprView::render('/shared/errors', get_defined_vars()); ?>

          <div class="mp-form-row">
            <div class="mp-form-label">
              <label><?php _e('Credit Card Number', 'memberpress'); ?></label>
              <span class="cc-error"><?php _e('Invalid Credit Card Number', 'memberpress'); ?></span>
            </div>
            <input type="text" class="mepr-form-input card-number cc-number validation" pattern="\d*" autocomplete="cc-number" placeholder="<?php echo $card_num; ?>" required>
          </div>

          <input type="hidden" name="mepr-cc-type" class="cc-type" value="" />

          <div class="mp-form-row">
            <div class="mp-form-label">
              <label><?php _e('Expiration', 'memberpress'); ?></label>
              <span class="cc-error"><?php _e('Invalid Expiration', 'memberpress'); ?></span>
            </div>
            <input type="text" class="mepr-form-input cc-exp validation" pattern="\d*" autocomplete="cc-exp" placeholder="mm/yy" required>
          </div>

          <div class="mp-form-row">
            <div class="mp-form-label">
              <label><?php _e('CVC', 'memberpress'); ?></label>
              <span class="cc-error"><?php _e('Invalid CVC Code', 'memberpress'); ?></span>
            </div>
            <input type="text" class="mepr-form-input card-cvc cc-cvc validation" pattern="\d*" autocomplete="off" required>
          </div>

          <div class="mepr_spacer">&nbsp;</div>

          <input type="submit" class="mepr-submit" value="<?php _e('Update Credit Card', 'memberpress'); ?>" />
          <img src="<?php echo admin_url('images/loading.gif'); ?>" style="display: none;" class="mepr-loading-gif" />
          <?php MeprView::render('/shared/has_errors', get_defined_vars()); ?>
        </div>
      </form>
    </div>
    <?php
  }

  /** Validates the payment form before a payment is processed */
  public function validate_update_account_form($errors=array()) {
    return $errors;
  }

  /** Used to update the credit card information on a subscription by the given gateway.
    * This method should be used by the class to record a successful cancellation from
    * the gateway. This method should also be used by any IPN requests or Silent Posts.
    */
  public function process_update_account_form($sub_id) {
    $this->process_update_subscription($sub_id);
  }

  /** Returns boolean ... whether or not we should be sending in test mode or not */
  public function is_test_mode() {
    return (isset($this->settings->test_mode) and $this->settings->test_mode);
  }

  public function force_ssl() {
    return (isset($this->settings->force_ssl) and ($this->settings->force_ssl == 'on' or $this->settings->force_ssl == true));
  }

  /** STRIPE SPECIFIC METHODS **/

  public function listener() {
    // retrieve the request's body and parse it as JSON
    $body = @file_get_contents('php://input');
    $event_json = (object)json_decode($body,true);

    if(!isset($event_json->id)) return;

    // Use the id to pull the event directly from the API (purely a security measure)
    try {
      $event = (object)$this->send_stripe_request( "events/{$event_json->id}", array(), 'get' );
    }
    catch( Exception $e ) {
      http_response_code(500); //Throw a 500 here so Stripe will try to resend Webhook again
      die($e->getMessage()); // Do nothing
    }
    //$event = $event_json;

    $_REQUEST['data'] = $obj = (object)$event->data['object'];

    if($event->type=='charge.succeeded') {
      $this->email_status("###Event: {$event->type}\n" . MeprUtils::object_to_string($event, true)."\n", $this->settings->debug);

      // Description only gets set with the txn id in a standard charge
      if(isset($obj->description)) {
        //$this->record_payment(); // done on page
      }
      elseif(isset($obj->customer))
        $this->record_subscription_payment();
    }
    else if($event->type=='charge.failed') {
      $this->record_payment_failure();
    }
    else if($event->type=='charge.refunded') {
      $this->record_refund();
    }
    else if($event->type=='charge.disputed') {
      // Not worried about this right now
    }
    else if($event->type=='customer.subscription.created') {
      //$this->record_create_subscription(); // done on page
    }
    else if($event->type=='customer.subscription.updated') {
      //$this->record_update_subscription(); // done on page
    }
    else if($event->type=='customer.subscription.deleted') {
      $this->record_cancel_subscription();
    }
    else if($event->type=='customer.subscription.trial_will_end') {
      // We may want to implement this feature at some point
    }
  }

  // Originally I thought these should be associated with
  // our membership objects but now I realize they should be
  // associated with our subscription objects
  public function stripe_plan($sub, $is_new = false) {
    $mepr_options = MeprOptions::fetch();
    $prd = $sub->product();

    try {
      if($is_new)
        $plan_id = $this->create_new_plan_id($sub);
      else
        $plan_id = $this->get_plan_id($sub);

      $stripe_plan = $this->send_stripe_request( "plans/{$plan_id}", array(), 'get' );
    }
    catch( Exception $e ) {
      // The call resulted in an error ... meaning that
      // there's no plan like that so let's create one
      if( $sub->period_type == 'months' )
        $interval = 'month';
      else if( $sub->period_type == 'years' )
        $interval = 'year';
      else if( $sub->period_type == 'weeks' )
        $interval = 'week';

      //Setup a new plan ID and store the meta with this subscription
      $new_plan_id = $this->create_new_plan_id($sub);

      //Handle zero decimal currencies in Stripe
      $amount = (MeprUtils::is_zero_decimal_currency())?MeprUtils::format_float(($sub->price), 0):MeprUtils::format_float(($sub->price * 100), 0);

      $args = MeprHooks::apply_filters('mepr_stripe_create_plan_args', array(
        'amount' => $amount,
        'interval' => $interval,
        'interval_count' => $sub->period,
        'name' => $prd->post_title,
        'currency' => $mepr_options->currency_code,
        'id' => $new_plan_id,
        'statement_descriptor' => substr(str_replace(array("'", '"', '<', '>', '$', '®'), '', get_option('blogname')), 0, 21) //Get rid of invalid chars
      ), $sub);

      if($sub->trial) {
        $args = array_merge(array("trial_period_days"=>$sub->trial_days), $args);
      }

      // Don't enclose this in try/catch ... we want any errors to bubble up
      $stripe_plan = $this->send_stripe_request( 'plans', $args );
    }

    return (object)$stripe_plan;
  }

  public function get_plan_id($sub) {
    $meta_plan_id = get_post_meta($sub->id, self::$stripe_plan_id_str, true);

    if($meta_plan_id == '')
      return $sub->id;
    else
      return $meta_plan_id;
  }

  public function create_new_plan_id($sub) {
    $parse = parse_url(home_url());
    $new_plan_id = $sub->id . '-' . $parse['host'] . '-' . uniqid();
    update_post_meta($sub->id, self::$stripe_plan_id_str, $new_plan_id);
    return $new_plan_id;
  }

  public function stripe_customer($sub_id, $cc_token = null) {
    $mepr_options     = MeprOptions::fetch();
    $user_id = MeprUtils::get_current_user_id();//dorin
    $stripe_customer_id = get_user_meta($user_id, 'mepr-df-stripe-customer-id', true);//dorin
    // dorin's change set meta data
    if ($stripe_customer_id != ''){
      $sub_id = $stripe_customer_id;
    } else {
      update_user_meta($user_id, 'mepr-df-stripe-customer-id', $sub_id);
    }
    $mepr_billing_address = get_user_meta($user_id, 'mepr_billing_address', true);
    $mepr_city = get_user_meta($user_id, 'mepr_city', true);
    $mepr_province = get_user_meta($user_id, 'mepr_province', true);
    $mepr_zip = get_user_meta($user_id, 'mepr_zip', true);
    $mepr_country = get_user_meta($user_id, 'mepr_country', true);

    $sub              = new MeprSubscription($sub_id);
    $user             = $sub->user();
    $stripe_customer  = (object)$sub->response;
    $uid              = uniqid();

    $this->email_status("###{$uid} Stripe Customer (should be blank at this point): \n" . MeprUtils::object_to_string($stripe_customer, true) . "\n", $this->settings->debug);

    if(!$stripe_customer || empty($stripe_customer) || !isset($stripe_customer->id) || empty($stripe_customer->id)) {
      if(strpos($sub->subscr_id, 'cus_') === 0) {
        $stripe_customer = (object)$this->send_stripe_request( 'customers/' . $sub->subscr_id );

        if(!isset($stripe_customer->error)) {
          $sub->response = $stripe_customer;
          $sub->store();
        }
        else {
          return false;
        }
      }
      elseif(!empty($cc_token)) {
        $stripe_args = MeprHooks::apply_filters('mepr_stripe_customer_args', array(
          'card' => $cc_token,
          'email' => $user->user_email,
          'description' => $user->get_full_name()
        ), $sub);
        $stripe_customer = (object)$this->send_stripe_request( 'customers', $stripe_args );
        $sub->subscr_id = $stripe_customer->id;
        //update_user_meta($user_id, 'mepr-df-stripe-customer-id', $stripe_customer->id);
        $sub->response  = $stripe_customer;
        $sub->store();
      }
      else {
        return false;
      }
    }

    $this->email_status("###{$uid} Stripe Customer (should not be blank at this point): \n" . MeprUtils::object_to_string($stripe_customer, true) . "\n", $this->settings->debug);

    return (object)$stripe_customer;
  }

  public function send_stripe_request( $endpoint,
                                       $args=array(),
                                       $method='post',
                                       $domain='https://api.stripe.com/v1/',
                                       $blocking=true,
                                       $idempotency_key=false ) {
    $uri = "{$domain}{$endpoint}";

    $args = MeprHooks::apply_filters('mepr_stripe_request_args', $args);

    $arg_array = array(
      'method'    => strtoupper($method),
      'body'      => $args,
      'timeout'   => 15,
      'blocking'  => $blocking,
      'sslverify' => false, // We assume the cert on stripe is trusted
      'headers'   => array(
        'Authorization' => 'Basic ' . base64_encode("{$this->settings->secret_key}:")
      )
    );

    if(false !== $idempotency_key) {
      $arg_array['headers']['Idempotency-Key'] = $idempotency_key;
    }

    $arg_array = MeprHooks::apply_filters('mepr_stripe_request', $arg_array);

    $uid = uniqid();
    // $this->email_status("###{$uid} Stripe Call to {$uri} API Key: {$this->settings->secret_key}\n" . MeprUtils::object_to_string($arg_array, true) . "\n", $this->settings->debug);

    $resp = wp_remote_request( $uri, $arg_array );

    // If we're not blocking then the response is irrelevant
    // So we'll just return true.
    if( $blocking==false )
      return true;

    if( is_wp_error( $resp ) ) {
      throw new MeprHttpException( sprintf( __( 'You had an HTTP error connecting to %s' , 'memberpress'), $this->name ) );
    }
    else {
      if( null !== ( $json_res = json_decode( $resp['body'], true ) ) ) {
        //$this->email_status("###{$uid} Stripe Response from {$uri}\n" . MeprUtils::object_to_string($json_res, true) . "\n", $this->settings->debug);
        if( isset($json_res['error']) )
          throw new MeprRemoteException( "{$json_res['error']['message']} ({$json_res['error']['type']})" );
        else
          return $json_res;
      }
      else // Un-decipherable message
        throw new MeprRemoteException( sprintf( __( 'There was an issue with the credit card processor. Try again later.', 'memberpress'), $this->name ) );
    }

    return false;
  }

  /** Get the default card object from a subscription creation response */
  public function get_default_card($data) {
    $data = (object)$data; // ensure we're dealing with a stdClass object

    if(isset($data->default_source)) { // Added in version 2015-02-15 of stripe's API
      foreach($data->sources['data'] as $source) {
        if($source['id']==$data->default_source) { return $source; }
      }
    }
    else if(isset($data->default_card)) { // Added in version 2013-07-05 of stripe's API
      foreach($data->cards['data'] as $card) {
        if($card['id']==$data->default_card) { return $card; }
      }
    }
    else if(isset($data->active_card)) { // Removed in version 2013-07-05 of stripe's API
      return $data->active_card;
    }

    return false;
  }

  /** Get card object from a charge response */
  public function get_card($data) {
    // the card object is no longer returned as of 2015-02-18 ... instead it returns 'source'
    if(isset($data->source) && $data->source['object']=='card') {
      return $data->source;
    }
    elseif(isset($data->card)) {
      return $data->card;
    }
  }

  /** Test customized stripe api **/
  public function get_stripe_data($endpoint,
                                 $args=array(),
                                 $method='post',
                                 $domain='https://api.stripe.com/v1/',
                                 $blocking=true,
                                 $idempotency_key=false) 
  {
    $uri = "{$domain}{$endpoint}";
    $args = MeprHooks::apply_filters('mepr_stripe_request_args', $args);

    $arg_array = array(
      'method'    => strtoupper($method),
      'body'      => $args,
      'timeout'   => 15,
      'blocking'  => $blocking,
      'sslverify' => false, // We assume the cert on stripe is trusted
      'headers'   => array(
        'Authorization' => 'Basic ' . base64_encode("{$this->settings->secret_key}:")
      )
    );

    if(false !== $idempotency_key) {
      $arg_array['headers']['Idempotency-Key'] = $idempotency_key;
    }

    $arg_array = MeprHooks::apply_filters('mepr_stripe_request', $arg_array);

    $uid = uniqid();
    // $this->email_status("###{$uid} Stripe Call to {$uri} API Key: {$this->settings->secret_key}\n" . MeprUtils::object_to_string($arg_array, true) . "\n", $this->settings->debug);

    $resp = wp_remote_request( $uri, $arg_array );
    // If we're not blocking then the response is irrelevant
    // So we'll just return true.
    if( $blocking==false )
      return true;

    if( is_wp_error( $resp ) ) {
      throw new MeprHttpException( sprintf( __( 'You had an HTTP error connecting to %s' , 'memberpress'), $this->name ) );
    }
    else {
      if( null !== ( $json_res = json_decode( $resp['body'], true ) ) ) {
        //$this->email_status("###{$uid} Stripe Response from {$uri}\n" . MeprUtils::object_to_string($json_res, true) . "\n", $this->settings->debug);
        if( isset($json_res['error']) )
          throw new MeprRemoteException( "{$json_res['error']['message']} ({$json_res['error']['type']})" );
        else
          return $json_res;
      }
      else // Un-decipherable message
        throw new MeprRemoteException( sprintf( __( 'There was an issue with the credit card processor. Try again later.', 'memberpress'), $this->name ) );
    }

    return false;
  }
  /* get customer object*/
  public function get_customer_object($customer_name) {
    return (object)$this->get_stripe_data( 'customers/'.$customer_name);
  }
  /* get all cards */
  public function get_all_cards_by_cus($customer_obj) {
    $cards = $customer_obj->sources['data'];
    return $cards;
  }

  /* get default card */
  public function get_default_card_by_cus($customer_obj) {
    return $customer_obj->default_source;
  }
}

