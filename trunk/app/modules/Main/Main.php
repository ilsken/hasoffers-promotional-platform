<?php
class Main extends Templator {

	public static $route_okay = array(
			'index',
			'banners',
			'email',
			'view_creative',
			'show_creative_code',
			'ajax_text_ad',
			'facebook',
			'linkedin',
			'twitter',
			'twitterpost',
			'link',
			'statistics',
			'refer',
			'contact',
			'signup',
			'login',
			'logout',
		);
		
	private static function getSelectedOfferId() {
		if( Session::exists('offer') ) 
			return Session::read('offer');
		else 
			return Conf::read('ENV.DefaultOfferId');
	}	

	private static function setSelectedOffer( $offer_id ) {
		self::getTracking( $offer_id );
		return Session::write('offer', $offer_id);
	}
	
	private static function getTracking( $offer_id = null, $params = null, $options = null ) {
		if ( !$offer_id ) 
			$offer_id = self::getSelectedOfferId();
			
		if ( !$params && !$options && Session::exists('tracking') ) {
			$tracking = Session::read('tracking');		
			if ( $tracking['offer_id'] == $offer_id )
				return $tracking;
		}
			
		$user = Session::read( 'User' );				
			
		$response = ApiClient_HasOffers_Request::Execute(
					'Offer',
					'generateTrackingLink',
					array(
						'offer_id' => $offer_id,
						'affiliate_id' => $user['affiliate_id'],
						'params' => $params,
						'options' => $options
					)
				);	
		$tracking = $response->getValue();	
		
		/* Array ( 
			'affiliate_id' => 1,
			'offer_id' => 2,
			'click_url' => "http://demo.go2jump.org/aff_c?offer_id=2&aff_id=1",
			'impression_pixel' => "<img src="http://demo.stage-go2jump.org/aff_i?offer_id=2&amp;aff_id=1" width="1" height="1">"
		) */
		
		if ( !$params && !$options )
			Session::write('tracking', $tracking);

		return $tracking;
	}
	
	public function index() {
		$this->hasAuth();
		$user = Session::read( 'User' );	
		$this->requireCss('main');	
		
		$this->title = "Home";
		
		if ( isset($_POST['offer']) )
			self::setSelectedOffer( $_POST['offer'] );

		$response = ApiClient_HasOffers_Request::Execute(
					'Offer',
					'findAll',
					array(
						'Interface' => 'affiliate',
						'InterfaceId' => $user['affiliate_id'],	
						'filters' => array(
							'Offer.status' => 'active',
							'Offer.is_private' => '0'
						),
						'sort' => array(
							'Offer.name' => 'asc',
						),
					)
				);	
		$offers = $response->getValue();
		
		/* echo "<pre>";
		print_r ($offers);
		echo "<pre>";	*/	

		$selected_offer_id = self::getSelectedOfferId();
		
		if ( isset($offers[$selected_offer_id]) )
			$selected_offer = $offers[$selected_offer_id];
		else
			$selected_offer = null;
			
		$tracking = self::getTracking();

		$this->display($this->getTpl('index', array(
			'user' => $user,
			'offers' => $offers,
			'selectedOffer' => $selected_offer,
			'tracking' => $tracking
		)));
	}
	
	public function banners() {
		$this->hasAuth();
		$user = Session::read( 'User' );	
		$this->requireCss('main');	
		
		$this->title = "Banners";
	
		$response = ApiClient_HasOffers_Request::Execute(
					'OfferFile',
					'findAll',
					array(
						'filters' => array(
							'offer_id' => self::getSelectedOfferId(),
							'type' => array (
								'html ad',
								'flash banner',
								'image banner'
							),
							'status' => 'active'
						),
						'contain' => array(
							'CreativeCode' => array()
						),
						'Interface' => 'affiliate',
						'InterfaceId' => $user['affiliate_id']	
					)
				);	
		
		$banners = $response->getValue();

		$this->display($this->getTpl('banners', array(
			'user' => $user,
			'banners' => $banners
		)));
	}
	
	public function view_creative() {
		$this->hasAuth();
		$user = Session::read( 'User' );	

		if( isset($_REQUEST['id']) ) {
			$response = ApiClient_HasOffers_Request::Execute(
					'OfferFile',
					'getCreativeCode',
					array(
						'id' => $_REQUEST['id'],
						'Interface' => 'affiliate',
						'InterfaceId' => $user['affiliate_id']
					)
				);	
			$creative = $response->getValue();
			
			echo $creative['CreativeCode'];			
		}

		die();
	}	
	
	public function show_creative_code() {
		$this->hasAuth();
		$user = Session::read( 'User' );	

		if( isset($_REQUEST['id']) ) {
			$params = array(
					'file_id' => $_REQUEST['id']
				);
			$tracking = self::getTracking( null, $params );
		
			$response = ApiClient_HasOffers_Request::Execute(
					'OfferFile',
					'getCreativeCode',
					array(
						'id' => $_REQUEST['id'],
						'tracking_link' => $tracking['click_url'],
						'impression_pixel' => $tracking['impression_pixel'],
						'Interface' => 'affiliate',
						'InterfaceId' => $user['affiliate_id']
					)
				);	
			$creative = $response->getValue();
			
			$code = $creative['CreativeCode'];			
		} else {
			$code = "";
		}
		
		$this->setTemplate( 'blank' );		

		$this->display($this->getTpl('show_creative_code', array(
			'code' => $code
		)));
	}		

	public function email() {
		$this->hasAuth();
		$user = Session::read( 'User' );	
		$this->requireCss('main');	
		
		$this->title = "Email";

		$response = ApiClient_HasOffers_Request::Execute(
					'OfferFile',
					'findAll',
					array(
						'filters' => array(
							'offer_id' => self::getSelectedOfferId(),
							'type' => 'email creative',
							'status' => 'active'
						),
						'contain' => array(
							'CreativeCode' => array()
						),
						'Interface' => 'affiliate',
						'InterfaceId' => $user['affiliate_id']
					)
				);	
		$emails = $response->getValue();

		$response = ApiClient_HasOffers_Request::Execute(
					'DneList',
					'findByOfferId',
					array(
						'offer_id' => self::getSelectedOfferId(),
						'filters' => array(
							'status' => 'enabled'
						),
					)
				);	
		$dne_list = $response->getValue();

	//echo "<pre>";
	//print_r($dne_list );
	//die();

		$this->display($this->getTpl('email', array(
			'user' => $user,
			'emails' => $emails,
			'dne_list' => $dne_list
		)));
	}
	
	
	public function ajax_text_ad( ) {
		$this->hasAuth();
		$user = Session::read( 'User' );	
		
		$return = array('url' => '', 'code' => '');

		if( isset($_REQUEST['id']) ) {
			$params = array(
					'file_id' => $_REQUEST['id']
				);
			if( isset($_REQUEST['source']) )
				$params['source'] = $_REQUEST['source'];
				
			$tracking = self::getTracking( null, $params );
		
			if ( isset($_REQUEST['code']) ) {
				$code = $_REQUEST['code'];
			} else {
				$response = ApiClient_HasOffers_Request::Execute(
						'OfferFile',
						'getCreativeCode',
						array(
							'id' => $_REQUEST['id'],
							'Interface' => 'affiliate',
							'InterfaceId' => $user['affiliate_id']
						)
					);	
					
				
				$creative = $response->getValue();
				
				$code = $creative['CreativeCode'];
				$code = str_replace('{tracking_link}', $tracking['click_url'], $code); 			
				$code = str_replace('{impression_pixel}', "", $code); 			
			}
			$code = strip_tags( $code );			
	
			$url = $tracking['click_url'];
			
			$return['url'] = $url;
			$return['code'] = $code;
		}
		
		echo json_encode( $return );			
		
		die();
	}
	
	public function facebook() {
		$this->hasAuth();
		$user = Session::read( 'User' );	
		$this->requireCss('main');	
		
		$this->title = "Facebook";

		$response = ApiClient_HasOffers_Request::Execute(
					'OfferFile',
					'findAll',
					array(
						'filters' => array(
							'offer_id' => self::getSelectedOfferId(),
							'type' => 'text ad',
							'status' => 'active',
							'Interface' => 'affiliate',
							'InterfaceId' => $user['affiliate_id']
						),
					)
				);	
		
		$creatives = $response->getValue();

		
		$source = 'facebook';
		
		$params = array( 'source' => $source );
		$tracking = self::getTracking( null, $params );
		
		
		foreach( $creatives as &$creative ) {
			$creative['OfferFile']['code'] = str_replace('{tracking_link}', "", $creative['OfferFile']['code']); 
			$creative['OfferFile']['code'] = str_replace('{impression_pixel}', "", $creative['OfferFile']['code']); 
			
			$creative['OfferFile']['code'] = strip_tags( $creative['OfferFile']['code'] );
		}		

		$this->display($this->getTpl('facebook', array(
			'user' => $user,
			'creatives' => $creatives,
			'source' => $source,
			'tracking' => $tracking
		)));
	}

	public function linkedin() {
		$this->hasAuth();
		$user = Session::read( 'User' );	
		$this->requireCss('main');	
		
		$this->title = "LinkedIn";

		$response = ApiClient_HasOffers_Request::Execute(
					'OfferFile',
					'findAll',
					array(
						'filters' => array(
							'offer_id' => self::getSelectedOfferId(),
							'type' => 'text ad',
							'status' => 'active',
							'Interface' => 'affiliate',
							'InterfaceId' => $user['affiliate_id']
						),
					)
				);	
		
		$creatives = $response->getValue();
		
		$source = 'linkedin';
		
		$params = array( 'source' => $source );
		$tracking = self::getTracking( null, $params );
		
		
		foreach( $creatives as &$creative ) {
			$creative['OfferFile']['code'] = str_replace('{tracking_link}', "", $creative['OfferFile']['code']); 
			$creative['OfferFile']['code'] = str_replace('{impression_pixel}', "", $creative['OfferFile']['code']); 
			
			$creative['OfferFile']['code'] = strip_tags( $creative['OfferFile']['code'] );
		}				

		$this->display($this->getTpl('linkedin', array(
			'user' => $user,
			'creatives' => $creatives,
			'source' => $source,
			'tracking' => $tracking			
		)));
	}

	public function twitter() {
		$this->hasAuth();
		$user = Session::read( 'User' );	
		$this->requireCss('main');	

		$this->title = "Twitter";

		$response = ApiClient_HasOffers_Request::Execute(
					'OfferFile',
					'findAll',
					array(
						'filters' => array(
							'offer_id' => self::getSelectedOfferId(),
							'type' => 'text ad',
							'status' => 'active',
							'Interface' => 'affiliate',
							'InterfaceId' => $user['affiliate_id']
						),
					)
				);	
		
		$creatives = $response->getValue();
		
		$source = 'twitter';
		
		$params = array( 'source' => $source );
		$options = array( 'tiny_url' => 1 );
		$tracking = self::getTracking( null, $params, $options );
		
		foreach( $creatives as &$creative ) {
			$creative['OfferFile']['code'] = str_replace('{tracking_link}', "", $creative['OfferFile']['code']); 
			$creative['OfferFile']['code'] = str_replace('{impression_pixel}', "", $creative['OfferFile']['code']); 
			
			$creative['OfferFile']['code'] = strip_tags( $creative['OfferFile']['code'] );
		}		

		$this->display($this->getTpl('twitter', array(
			'user' => $user,
			'creatives' => $creatives,
			'source' => $source,
			'tracking' => $tracking	
		)));
	}	
	
	public function twitterpost() {
		$this->hasAuth();
		$user = Session::read( 'User' );	
		$this->requireCss('main');	
		$this->requireJs('twitter');	

		$this->title = "Tweet This";

		if( isset($_REQUEST['message']) ) 
			$status = $_REQUEST['message'];
		else if( isset($_REQUEST['status']) ) 
			$status = $_REQUEST['status'];
		else
			$status = "";

		if( isset($_REQUEST['username']) ) 
			$username = $_REQUEST['username'];
		else
			$username= "";
			
		if( isset($_REQUEST['password']) ) 
			$password = $_REQUEST['password'];
		else
			$password= "";			
			
		$sent = null;
		if ( !empty($status) && !empty($username) && !empty($password) ) {	
			$tweetUrl = 'http://www.twitter.com/statuses/update.xml';

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "$tweetUrl");
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, "status=$status");
			curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");

			$result = curl_exec($curl);
			$resultArray = curl_getinfo($curl);

			if ($resultArray['http_code'] == 200) {
				curl_close($curl);
				$sent = true;
			}else{
				curl_close($curl);
				$sent = false;
			}

		}

		$this->display($this->getTpl('twitter-post', array(
			'user' => $user,
			'username' => $username,
			'password' => $password,
			'sent' => $sent,
			'status' => $status
		)));
	}

	public function link() {
		if (isset($_REQUEST['txt'])) :
			$txt = $_REQUEST['txt'];
		else :
			$txt = sprintf( '%s Promotional Platform', Conf::read('ENV.CompanyName') );
		endif;
		
		$this->setTemplate( 'blank' );	

		$this->display($this->getTpl('link', array(
			'url' => $_REQUEST['url'],
			'txt' => $txt
		)));
	}
	
	public function statistics() {
		$this->hasAuth();
		$user = Session::read( 'User' );	
		$this->requireCss('main');	
		
		$this->requireJs('rgraph/RGraph.common.core');
		$this->requireJs('rgraph/RGraph.line');
		
		$this->title = "Earnings Report";
		
		$start_date = date( "Y-m-d", strtotime("-1 MONTH"));
		$end_date = date( "Y-m-d", strtotime("TODAY"));

		$response = ApiClient_HasOffers_Request::Execute(
					'Report',
					'getConversions',
					array(
						'fields' => array(
							'Stat.id',
							'Stat.date',
							'Offer.name',
							'Stat.affiliate_id',
							'Stat.payout',
							'Stat.source',
							'Stat.advertiser_info'
						),
						'filters' => array(
							'Stat.date' => array(
								'conditional' => 'BETWEEN',
								'values' => array( $start_date, $end_date ),
							)							
						),
						'sort' => array(
							'Stat.date' => 'asc',
						),
						'totals' => true,
						'Interface' => 'affiliate',
						'InterfaceId' => $user['affiliate_id']					
					)
				);	
		$conversions = $response->getValue();
		
		$response = ApiClient_HasOffers_Request::Execute(
					'Report',
					'getStats',
					array(
						'fields' => array(
							'Stat.date',
							'Stat.affiliate_id',
							'Stat.conversions',
							'Stat.payout',
						),
						'groups' => array(
							'Stat.date'
						),
						'filters' => array(
							'Stat.date' => array(
								'conditional' => 'BETWEEN',
								'values' => array( $start_date, $end_date ),
							)	
						),
						'sort' => array(
							'Stat.date' => 'asc',
						),
						'Interface' => 'affiliate',
						'InterfaceId' => $user['affiliate_id']	
					)
				);
		$stats = $response->getValue();

		$response = ApiClient_HasOffers_Request::Execute(
					'Report',
					'getAffiliateCommissions',
					array(
						'fields' => array(
							'Stat.amount',
						),
						'groups' => array(
							'Stat.referral_id',
						),
						'filters' => array(
							'Stat.date' => array(
								'conditional' => 'BETWEEN',
								'values' => array( $start_date, $end_date ),
							)	
						),
						'totals' => true,
						'Interface' => 'affiliate',
						'InterfaceId' => $user['affiliate_id']	
					)
				);
		$refer_stats = $response->getValue();
		
		$total_earnings = 0;
		
		if(!empty($conversions['data']) )
			$total_earnings += $conversions['totals']['Stat']['payout'];
			
		if(!empty($refer_stats['data']) )
			$total_earnings += $refer_stats['totals']['Stat']['payout'];

		$this->display($this->getTpl('statistics', array(
			'user' => $user,
			'conversions' => $conversions,
			'stats' => $stats,
			'refer_stats' => $refer_stats,
			'total_earnings' => $total_earnings
		)));
	}

	public function refer() {
		$this->hasAuth();
		$user = Session::read( 'User' );	
		$this->requireCss('main');	
		
		$this->title = "Refer a Friend";

		$response = ApiClient_HasOffers_Request::Execute(
					'Affiliate',
					'getReferralAffiliateIds',
					array(
						'id' => $user['affiliate_id']
					)
				);	
		
		$refer_count = count($response->getValue());

		$response = ApiClient_HasOffers_Request::Execute(
					'Affiliate',
					'getReferralCommission',
					array(
						'id' => $user['affiliate_id']
					)
				);	
		
		$refer_commission = $response->getValue();

		$response = ApiClient_HasOffers_Request::Execute(
					'Report',
					'getAffiliateCommissions',
					array(
						'fields' => array(
							'Stat.date',
							'Stat.amount',
						),
						'groups' => array(
							'Stat.referral_id',
						),
						'filters' => array(	),
						'sort' => array(
							'Stat.date' => 'asc',
						),
						'totals' => true,
						'Interface' => 'affiliate',
						'InterfaceId' => $user['affiliate_id']	
					)
				);	
		
		$refer_stats = $response->getValue();

		$this->display($this->getTpl('refer', array(
			'user' => $user,
			'refer_count' => $refer_count,
			'refer_commission' => $refer_commission,
			'refer_stats' => $refer_stats
		)));
	}

	public function contact() {
		// $this->hasAuth();
		$user = Session::read( 'User' );	
		$this->requireCss('main');	
		$this->requireJs('contact');	

		$this->title = "Contact";
		
		$name = "";
		if ( isset($_POST['name']) )
			$name = $_POST['name'];
		elseif ( isset($user['first_name']) )
			$name = $user['first_name'].' '.$user['last_name'];
			
		$email = "";
		if ( isset($_POST['email']) )
			$email = $_POST['email'];
		elseif ( isset($user['email']) )
			$email = $user['email'];	

		$subject = "";
		if ( isset($_POST['subject']) )
			$subject = $_POST['subject'];

		$message = "";
		if ( isset($_POST['message']) )
			$message = $_POST['message'];	
			
		$sent = null;
		if ( !empty($email) && (!empty($subject) || !empty($message)) ) {
			$response = ApiClient_HasOffers_Request::Execute(
					'Application',
					'getBrand'
				);
			$brand = $response->getValue();
			
			$from = $email;
			$to = $brand['Brand']['email'];
			$subject = "Affiliate Contact: ".$subject;

			$message = str_replace("\r", "<br/>", $message);
			$message = str_replace("\n", "<br/>", $message);
			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= "From: {$from}\r\n";

			$body = "<html><body>".$message."<br/><br/>Affiliate Name: ".$name."<br/>Affiliate Email: ".$email."</body></html>";

			if (mail($to, $subject, $body, $headers)) {
				$sent = true;
			} else {
				$sent = false;
			} 
		}		

		$this->display($this->getTpl('contact', array(
			'name' => $name,
			'email' => $email,
			'subject' => $subject,
			'message' => $message,
			'sent' => $sent
		)));
	}		
	
	public function signup() {
		$this->title = "Affiliate Signup";
		$this->requireCss('main');	
		$this->requireJs('signup');	

		$msg = null;
		$company = "";
		$country = "";
		$address1 = "";
		$city = "";
		$region = "";
		$zipcode = "";
		$phone = "";
		$first_name = "";
		$last_name = "";
		$email = "";
		$password = "";
		$referral_id = "";
		
		if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['company']) && isset($_POST['country']) && isset($_POST['address1']) && isset($_POST['region']) && isset($_POST['city']) && isset($_POST['zipcode']) && isset($_POST['phone']) && isset($_POST['terms']) ) {
			$company = $_POST['company'];
			$country = $_POST['country'];
			$address1 = $_POST['address1'];
			$city = $_POST['city'];
			$region = $_POST['region'];
			$zipcode = $_POST['zipcode'];
			$phone = $_POST['phone'];
			$company = $_POST['company'];
			$referral_id = $_POST['referral_id'];
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$email = $_POST['email'];
			$password = $_POST['password'];

			$response = ApiClient_HasOffers_Request::Execute(
					'Affiliate',
					'signup',
					array(
					'account' => array(
						'company' => $company,
						'country' => $country,
						'address1' => $address1,
						'city' => $city,
						'region' => $region,
						'zipcode' => $zipcode,
						'phone' => $phone,
						'referral_id' => $referral_id
					),
					'user' => array(
						'first_name' => $first_name,
						'last_name' => $last_name,
						'email' => $email,
						'password' => $password
					)
					)
				);	

			if ( $response->isSuccess() ) {
				$signup = $response->getValue();

				if($signup['Affiliate']['status'] == "active") {
				
					Session::write( 'User', $signup['AffiliateUser'] );
					Session::write( 'hasAuth', true );
					
					//echo "<pre>";
					//print_r($_SESSION['User']);
					//echo "here".$_SESSION['hasAuth'];
					//die();
					
					header( 'location: /index');
					die();

				} else {
					header( 'location: /login?msg=success');
					die();
				}
			} else {
				$msg = "There was an error processing your application. Please correct the following:<ul>";

				foreach(  $response->getValidationErrors() as $error ) 
					$msg .= "<li>".$error[0]['err_msg']."</li>";
			
				$msg .= "</ul>";
			}
		} 
		
		if ( isset($_GET['r']) ) {
			$referral_id = $_GET['r'];
			setcookie("referral_id", $referral_id, time()+2592000);
		} else if ( isset($_COOKIE['referral_id']) ) {
			$referral_id = $_COOKIE['referral_id'];
		}		

		$this->display($this->getTpl('signup', array(
			'msg' => $msg,
			'company' => $company,
			'country' => $country,
			'address1' => $address1,
			'city' => $city,
			'region' => $region,
			'zipcode' => $zipcode,
			'phone' => $phone,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'email' => $email,
			'password' => $password,
			'referral_id' => $referral_id
		)));
	}
	
	public function login() {
		$this->title = "Login";
		$this->requireCss('main');	

		$msg = null;
		
		if ( isset($_POST['email']) && $_POST['password'] ) {
			$response = ApiClient_HasOffers_Request::Execute(
					'Authentication',
					'findUserByCredentials',
					array(
						'type' => 'affiliate_user',
						'email' => $_POST['email'],
						'password' => $_POST['password']
					)
				);	

			if ( $response->isSuccess() ) {
				$auth = $response->getValue();
				
				$response = ApiClient_HasOffers_Request::Execute(
						'AffiliateUser',
						'findById',
						array(
							'id' => $auth['user_id']
						)
					);		
				$user = $response->getValue();
				
				Session::write( 'User', $user['AffiliateUser'] );
				Session::write( 'hasAuth', true );
				
				header( 'location: /index');
				die();
			} else {
				$msg = "Invalid email / password.";
			}
		}

		$this->display($this->getTpl('login', array(
			'msg' => $msg
		)));
	}	
	
	public function logout() {
		$this->hasAuth();
		$user = Session::read( 'User' );
		session_destroy();
		header("location: /login");
	}
	
	private function hasAuth() {
		$user = Session::read( 'User' );
		
		if ( !Session::read( 'hasAuth' ) || empty($user) ) {
			header( 'location: /login');
			die();
		}
	}
}
