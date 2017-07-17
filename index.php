<?php
ob_start();
session_start();
error_reporting(0);
include "data/conn.php";
include "data/constants.php";
include "data/sqlinjection.php";
include "data/youtubeimagegrabber.php";

include "data/groups.php";
include "data/feedbacks.php";
include "data/listings.php";
include "data/listingfiles.php";
include "data/galleries.php";
include "data/videos.php";
include "data/extendtrip.php";
include "data/adds.php";
include "data/testimonials.php";
include "data/menu.php";
include "data/metahome.php";
include "data/listingdate.php";
include("data/linkexchange.php");
include("data/blog.php");
include ("data/comment.php");
include ("data/cities.php");

$conn = new Dbconn();
$groups	= new Groups();
$feedbacks = new Feedbacks();
$listings = new Listings();
$listingFiles = new ListingFiles();
$galleries = new Galleries();
$videos = new Videos();
$metahome = new metaHome();
$datelisting	= new Datelisting();
$extendTrip = new extendTrip;
$exchange  = new Exchange();
$adds = new Adds();
$testimonials = new Testimonials();
$menu = new menu();
$blog  = new Blog;
$comment = new Comment;
$cities = new Cities;
//print_r($_GET);



if(empty($_SERVER['QUERY_STRING']))
$url = true;
else
$url = false;

if(isset($_GET['title'])){
	$title = $_GET['title'];
	$url = true;
if(file_exists("includes/".$title.".php")){
	$_GET['action'] = $title;
	$url = true;
}
else
{
	$row = $listings -> getByURLName($title);
	if($row)
	{
		$listId = $row['id'];
		$_GET['listId'] = $row['id'];
		$url = true;
	}
	else
	{
		$row = $groups -> getByURLName($title);
		if($row)
		{
			if(isset($_GET['action'])){
				$_GET['id'] = $row['id'];
				$url = true;
			}
			else
			{
				$linkId = $row['id'];
				$_GET['linkId'] = $row['id'];	
				$url = true;	
			}
		}
	}
}
}

if(!$url && !isset($_GET['cate_id']) && !isset($_GET['blogId']) && ($_GET['action']!='blog')){
	header("Location:http://".$_SERVER['HTTP_HOST']);
	exit;	
}


$linkId = cleanQuery($_GET['linkId']);
$listId = cleanQuery($_GET['listId']);
$galleryId  = cleanQuery($_GET['galleryId']);

if(isset($_GET['linkId']))
{
	$title = $groups -> getPageTitle($linkId);
	$keyword = $groups -> getPageKeyword($linkId);
	$description = $groups -> getMetaDescription($linkId);
}
elseif(isset($_GET['listId']))
{
	
	$title = $listings -> getPageTitle($listId);
	
	$keyword = $listings -> getPageKeyword($listId);
	
	$description = $listings -> getMetaDescription($listId);	
	
	
} else if($_GET['action']=='blog' && !isset($_GET['cate_id'])){
	
	$title = $groups -> getPageTitle(173);
	$keyword = $groups -> getPageKeyword(173);
	$description = $groups -> getMetaDescription(173);
		
} else if($_GET['blogId']){
	$row = $blog->getById($_GET['blogId']);
	$title = $row['title'];
	if(!empty($row['pageTitle']))
	$title = $row['pageTitle'];
	$keyword = $row['metaKeyword'];
	$description = $row['metaDescription'];
	
} else if($_GET['cate_id']){
	$row = $cities->getById($_GET['cate_id']);
	$title = "Category .:. ".$row['title'];	
	$keyword = $description = $row['title'];
}
elseif(isset($_GET['listingId']))
{
	$row = $galleries -> getParentDetailsById($galleryId);
	$title = $row['title'];
	$keyword = $row['keyword'];
	$description = $row['metaDescription'];
}else if(isset($_GET['title'])){
	$title = true;
	$title = $groups -> getPageTitle($groups->getWhatByUrlName("id",$_GET["title"]));
	if(empty($title)){
		$title = $groups->getWhatById("name",$groups->getWhatByUrlName("id",$_GET["title"]));
		if(empty($title)){
			$title = false;
		}	
	}
	$keyword = $groups -> getPageKeyword($groups->getWhatByUrlName("id",$_GET["title"]));
	$description = $groups -> getMetaDescription($groups->getWhatByUrlName("id",$_GET["title"]));
	
}else if(isset($_GET['action'])){
	$title = true;
	$title = $groups -> getPageTitle($groups->getWhatByUrlName("id",$_GET["title"]));
	if(empty($title)){
		$title = $groups->getWhatById("name",$groups->getWhatByUrlName("id",$_GET["title"]));
		if(empty($title)){
			$title = false;
		}	
	}
	$keyword = $groups -> getPageKeyword($groups->getWhatByUrlName("id",$_GET["title"]));
	$description = $groups -> getMetaDescription($groups->getWhatByUrlName("id",$_GET["title"]));
	
}else{
	
	$res = $metahome -> getById(1);
	$row = $conn -> fetchArray($res);
	$title = $row['pageTitle'] ;
	$keyword = $row['pageKeyword'];
	$description = $row['metaDescription'];
	
	
}

if(isset($_GET["action"]) && !isset($_GET["page"])){
	if($title)
	$action = " - ".strtoupper(strtolower($_GET["action"]));
	else 
	$action =  strtoupper(strtolower($_GET["action"]));	
}
else if(isset($_GET["page"]) && isset($_GET["action"]) && !isset($_GET["title"])){

	$action = $_GET["action"]." - Page ".strtoupper(strtolower($_GET["page"]));		
}
if(isset($_GET["linkId"]) && isset($_GET["page"])){
	$action = " - Page ".strtoupper(strtolower($_GET["page"]));	
}

function altTag($field,$sql_array,$image_name){
	if(!empty($sql_array[$field])){
		$alt = $sql_array[$field];	
	} else{
		$image_name = str_replace("-"," ",current(explode(".",$image_name)));
		$image_name = str_replace("_"," ",current(explode(".",$image_name)));
		$image_name = preg_replace('/[0-9]+/', '', current(explode(".",$image_name)));
		$alt= $image_name;
	}
	
	return $alt;
}
	
	
	function getLink($resources){
			if($resources['linkType']=="Link"){
				$link=$resources['contents'];	
			}
			else {
				$link=$resources['urlname'].".html";	
			}
			return $link;	
	}
	
	function findOrderlize($interger){
		
	 switch($interger){
						case 1:
						$orderlize = "st";
						break;
						case 2:
						$orderlize = "nd";
						break;
						case 3:
						$orderlize = "rd";
						break;
						default:
						$orderlize = "th";
						break;   
				   }
				   
				   return $orderlize;	
	}
function ratingSet($statement){
		switch($statement){	
			case "Poor":
			$rating = 1;
			break;
			case "Fair":
			$rating = 2;
			break;
			case "Good":
			$rating = 3;
			break;
			case  "Very Good":
			$rating = 4;
			break;
			case "Excellent";
			$rating = 5;
			break;	
		}
		return $rating;
	}
	
include("includes/tripprocess.php");
include("includes/hotelprocess.php");
include("includes/feedbackprocess.php");
include("includes/testimonialprocess.php");
include("includes/bookingprocess.php");
include("includes/referprocess.php");
include("includes/partnerprocess.php");
include("includes/trip_plannerprocess.php");
include("data/mis.func.php");
include("formaturl.php");
include("includes/commentprocessing.php");
include("includes/quickqueryprocess.php");


if(isset($_GET['linkId'])){
	
	$id = $_GET['linkId'];
	$result = $groups->getById($id);
	$rq = $conn->fetchArray($result);
	if($rq['linkType']=='Trips Page')
		$p = false;
	 else		
		$p = true;	
	
} else {
	
	$p = true;
}


if(isset($_GET['blogId']) || $_GET['action']=='blog'){
$p = false;
}

function clearfix($lg,$sm){
	global $x;
	if($x%$lg==0)
	echo "<div class=\"clearfix visible-lg-block\"></div>";	
	if($x%$lg==0)
	echo "<div class=\"clearfix visible-md-block\"></div>";	
	if($x%$sm==0)
	echo "<div class=\"clearfix visible-sm-block\"></div>";	
}

 ?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="keywords" content="<?php echo $keyword.$action; ?>" />
<meta name="description" content="<?php echo $description.$action; ?>" />
<title><?php if(!empty($title)) echo $title.$action; else if(isset($_GET["action"])) echo $action; else if(isset($_GET["page"])) echo $action; else echo $groups->getNameByTitle($_GET['linkId']); ?></title>
<?php include("baselocation.php"); ?>
<link rel="canonical" href="http://<?php echo $_SERVER["SERVER_NAME"].$_SERVER['REQUEST_URI']; ?>" />
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/jquery.bxslider.css" rel="stylesheet" />
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/owl.carousel.min.css" rel="stylesheet">
	<link href="css/owl.theme.default.min.css" rel="stylesheet">
    <link href="css/ihover.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/menu.css" rel="stylesheet">
</head>

<body>
	<header id="header">
    	<div class="header-top">
        	<div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="top-navigation">
                            <ul class="list-inline">
                                <li><a href=""><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
                                <li><a href=""><i class="fa fa-envelope-o" aria-hidden="true"></i> Contact Us</a></li>
                                <li><a href=""><i class="fa fa-paper-plane" aria-hidden="true"></i> Join the explores</a></li>
                                <li><a href=""><i class="fa fa-question-circle" aria-hidden="true"></i> FAQ</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3">
                    	<span class="customer-support">Customer Support: +977-1-4331402</span>
                    </div>
                    <div class="col-md-3">
                    	<div class="social-media-links">
                        	<ul class="list-inline">
                            	<li><a href=""><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                <li><a href=""><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                <li><a href=""><i class="fa fa-youtube-play" aria-hidden="true"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- header top end -->
        
        <div class="header-bottom">
        	<div class="container">
            	<div class="row">
                	<div class="col-md-4">
                    	<div class="header-logo">
                        	<a href="//<?php echo $_SERVER['HTTP_HOST']; ?>"><img src="images/logo.png" alt="Logo"></a>
                        </div>
                    </div>
                    <div class="col-md-8">
                    	<div class="main-menu">
                        	<nav class="navbar" id="main-navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        
        <li><a href="#">Nepal</a></li>
       	<li><a href="#">Tibet</a></li>
        <li><a href="#">Bhutan</a></li>
        <li><a href="#">India</a></li>
        <li><a href="#">Packages</a></li>
        <li><a href="#">Travel Guide</a></li>
        <li><a href="#">Company</a></li>
        <li><a href="#">Blog</a></li>
      </ul>
      
      
    </div><!-- /.navbar-collapse -->
</nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       <!-- header bottom end -->      
    </header>
    <!-- hedaer end -->
    <div class="clearfix"></div>
    
    <section class="slideshow">
    	<ul class="bxslider">
            <li><img src="images/slideshow.png" title="Trekking in Nepal" /></li>
            <li><img src="images/slideshow.png" title="Trekking Everest base camp via Kalapathhar" /></li>
            <li><img src="images/slideshow.png" title="Trekking Everest base camp via Kalapathhar"/></li>
            <li><img src="images/slideshow.png"title="Trekking Everest base camp via Kalapathhar" /></li>
        </ul>
    </section>
   <!-- section slideshow end -->
   
   <section class="container">
   		<div class="recommended-trips-row">
        	<h1>MORE DESTINATIONS RECOMMENDED FOR YOU</h1>
            <p class="recommended-txt">Take the stress out of planning a holiday with one of these best-selling packages, which a holiday with out of planning
</p>
			
            <div class="recommended-trip-inner">
            	<div class="owl-carousel owl-theme">
                	<div class="item">
                    	<div class="ih-item square colored effect13 bottom_to_top"><a href="#">
        <div class="img"><img src="images/pack3.png" alt="img"></div>
        <div class="info">
          <h3>Everest Mini Trek</h3>
          <p class="rating"><img src="images/rating.png" class="img-responsive" alt="Rating"></p>
          <p class="price-from">Starting From</p>
          <p class="price-total">Price <span>$499</span></p>
          <p class="book-btn"><button onClick="location.href='booking.html'">Book Now</button></p>
           <p class="read-btn"><button onClick="location.href='booking.html'">Read More</button></p>
        </div></a></div>
                    </div>
                   
                   
                   <div class="item">
                    	<div class="ih-item square colored effect13 bottom_to_top"><a href="#">
        <div class="img"><img src="images/pack1.png" alt="img"></div>
        <div class="info">
          <h3>Everest Mini Trek</h3>
          <p class="rating"><img src="images/rating.png" class="img-responsive" alt="Rating"></p>
          <p class="price-from">Starting From</p>
          <p class="price-total">Price <span>$499</span></p>
          <p class="book-btn"><button onClick="location.href='booking.html'">Book Now</button></p>
           <p class="read-btn"><button onClick="location.href='booking.html'">Read More</button></p>
        </div></a></div>
                    </div>
                    
                    
                    
                    <div class="item">
                    	<div class="ih-item square colored effect13 bottom_to_top"><a href="#">
        <div class="img"><img src="images/pack2.png" alt="img"></div>
        <div class="info">
          <h3>Everest Mini Trek</h3>
          <p class="rating"><img src="images/rating.png" class="img-responsive" alt="Rating"></p>
          <p class="price-from">Starting From</p>
          <p class="price-total">Price <span>$499</span></p>
          <p class="book-btn"><button onClick="location.href='booking.html'">Book Now</button></p>
           <p class="read-btn"><button onClick="location.href='booking.html'">Read More</button></p>
        </div></a></div>
                    </div>
                    
                    
                    
                    <div class="item">
                    	<div class="ih-item square colored effect13 bottom_to_top"><a href="#">
        <div class="img"><img src="images/pack3.png" alt="img"></div>
        <div class="info">
          <h3>Everest Mini Trek</h3>
          <p class="rating"><img src="images/rating.png" class="img-responsive" alt="Rating"></p>
          <p class="price-from">Starting From</p>
          <p class="price-total">Price <span>$499</span></p>
          <p class="book-btn"><button onClick="location.href='booking.html'">Book Now</button></p>
           <p class="read-btn"><button onClick="location.href='booking.html'">Read More</button></p>
        </div></a></div>
                    </div>
                   
                   
                </div>
            </div>
            
        </div> 
        <!-- recommended trip end -->
        <div class="clearfix"></div>
        
        
        <div class="destination-row">
        	<h2>BEAUTIFUL TRIPS</h2>
             <p class="recommended-txt">Lorem ipsum dolor sit amet, consectetur adipiscing elit
</p>
        	<div class="row">
            	<div class="col-md-3">
                	<div class="destination-item">
                    	<a href=""><img src="images/nepal.png" alt="Nepal">
                        <h3>Nepal</h3></a>
                    </div>
                </div>
                <div class="col-md-3">
                	<div class="destination-item">
                    	<a href=""><img src="images/india.png" alt="India">
                        <h3>India</h3></a>
                    </div>
                </div>
                <div class="col-md-3">
                	<div class="destination-item">
                    	<a href=""><img src="images/tibet.png" alt="Tibet">
                        <h3>Tibet</h3></a>
                    </div>
                </div>
                <div class="col-md-3">
                	<div class="destination-item">
                    	<a href=""><img src="images/bhutan.png" alt="Bhutan">
                        <h3>Bhutan</h3></a>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- destination row end -->
        <div class="clearfix"></div>
        
               
   </section>
   <!-- container end -->
   
   
<section class="find-destination-row">
        		<div class="container"><h2><span>Find Your Perfect</span>
Mountain King Trek and Expedition</h2>

<a href="" class="btn btn-default">GET STARTED</a>


</div>
</section><!-- destination find row end -->
<div class="clearfix"></div>


<section class="recommended-outdoor-packages">
	<div class="container">
	<h2>RECOMMENDED OUTDOOR PACKAGES</h2>
    <p class="recommended-txt">Lorem ipsum dolor sit amet, consectetur adipiscing elit</p>
    
    <div class="recommended-outdoor-packages-list">
    <div class="owl-carousel owl-theme">
    	
        <div class="item">
        	<div class="outddor-packages-det">
            	<a href=""><img src="images/outdoor-pack1.png" class="img-responsive" alt="Outdoor Package"></a>
                <div class="outdoor-pack-inn">
                <h3><a href="">Annapurna Circuit Trek</a></h3>
                <p>
                	Starting From <span>$1500</span> 
                </p>
                <div class="clearfix"></div>
                </div>
            </div>
        </div>
        
        <div class="item">
        	<div class="outddor-packages-det">
            	<a href=""><img src="images/outdoor-pack2.png" class="img-responsive" alt="Outdoor Package"></a>
                <div class="outdoor-pack-inn">
                <h3><a href="">Annapurna Circuit Trek</a></h3>
                <p>
                	Starting From <span>$1500</span> 
                </p>
                <div class="clearfix"></div>
                </div>
            </div>
        </div>
        
        
        <div class="item">
        	<div class="outddor-packages-det">
            	<a href=""><img src="images/outdoor-pack3.png" class="img-responsive" alt="Outdoor Package"></a>
                <div class="outdoor-pack-inn">
                <h3><a href="">Annapurna Circuit Trek</a></h3>
                <p>
                	Starting From <span>$1500</span> 
                </p>
                <div class="clearfix"></div>
                </div>
            </div>
        </div>
        
        
        <div class="item">
        	<div class="outddor-packages-det">
            	<a href=""><img src="images/outdoor-pack4.png" class="img-responsive" alt="Outdoor Package"></a>
                <div class="outdoor-pack-inn">
                <h3><a href="">Annapurna Circuit Trek</a></h3>
                <p>
                	Starting From <span>$1500</span> 
                </p>
                <div class="clearfix"></div>
                </div>
            </div>
        </div>
        
        
        <div class="item">
        	<div class="outddor-packages-det">
            	<a href=""><img src="images/outdoor-pack1.png" class="img-responsive" alt="Outdoor Package"></a>
                <div class="outdoor-pack-inn">
                <h3><a href="">Annapurna Circuit Trek</a></h3>
                <p>
                	Starting From <span>$1500</span> 
                </p>
                <div class="clearfix"></div>
                </div>
            </div>
        </div>
        
    </div>
    </div>
   </div>
</section>
<!-- recommended outdoor packages end -->
 <div class="clearfix"></div>
 
 
 <section class="container">
 	<div class="row">
    	<div class="col-md-8">
        	<div class="quick-links">
            <h2>Quick Links</h2>
            	<div class="row">
                		<div class="col-md-4 col-sm-4">
                        	<ul>
                            	<li><a href="">About us</a></li>
                                <li><a href="">Destinations</a></li>
                                <li><a href="">Terms and Conditions</a></li>
                                <li><a href="">Contact Us</a></li>
                                <li><a href="">Our Special Packages</a></li>
                                <li><a href="">Photo Gallery</a></li>
                                <li><a href="">Testimonials</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4 col-sm-4">
                        	<ul>
                            	<li><a href="">General Information</a></li>
                                <li><a href="">Trekking By Region</a></li>
                                <li><a href="">Adventure Activities</a></li>
                                <li><a href="">Tours in Nepal</a></li>
                                <li><a href="">Services in Nepal</a></li>
                                <li><a href="">Other Services</a></li>
                                <li><a href="">Nepal Expeditions</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4 col-sm-4">
                        	<ul>
                            	<li><a href="">Site Map</a></li>
                                <li><a href="">Extend Trip</a></li>
                                <li><a href="">Affiliated</a></li>
                                <li><a href="">Partner with us</a></li>
                                <li><a href="">Emergency Contact</a></li>
                                <li><a href="">Blog</a></li>
                                <li><a href="">FAQ</a></li>
                            </ul>
                        </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
        	<div class="banner-footertop">
            	<img src="images/about-us.png" class="img-responsive" alt="No. 1 Trekking Agency in Nepal">
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
 </section>
 <!-- container section end -->
   
   
<section class="social-media-row">
	<div class="container">
    
    <div class="row">
    	<div class="col-md-8">
        	<div class="social-media-links">
            	<ul class="list-inline">
                    <li><a href=""><i class="fa fa-facebook" aria-hidden="true"></i> Facebook</a></li>
                    <li><a href=""><i class="fa fa-twitter" aria-hidden="true"></i> Twitter</a></li>
                    <li><a href=""><i class="fa fa-linkedin" aria-hidden="true"></i> Linkedin</a></li>
                    <li><a href=""><i class="fa fa-youtube" aria-hidden="true"></i> Youtube</a></li>
                    <li><a href=""><i class="fa fa-google-plus" aria-hidden="true"></i> Google Plus</a></li>
        		</ul>
            </div>
        </div>
        <div class="col-md-4">
        	<div class="scroll-top text-right">
            	<a href="">TO TOP <i class="fa fa-angle-up" aria-hidden="true"></i></a> 
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    
    	
    </div>
</section>
<!-- social media row end -->

<section class="footer-top">
	<div class="container">
    	<div class="row">
        	<div class="col-md-4">
            	<div class="footer-contact">
                	<h3>Contact Information</h3>
                    <img src="images/footer-logo.png" class="img-responsive" alt="Logo">
                    <p>
                    	<strong>Mountain King Treks and Expedition</strong> <br />
Maitrinagar, Kirtipur-2, Kathmandu<br />
Tel: +977-1-4331402<br />
Cell: +977-9841566449<br /><br />

Email : mountainkingtrek@gmail.com<br />
           &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; govindaprasadsapkota@gmail.com<br />
URL: http://mountainking.com.np
                    </p>
                </div>
            </div>
            <div class="col-md-4">
            	<div class="subscribe-section">
                	<h3>Subsribed Newsletter</h3>
                    <p>Sign up for our mailing list to get latest updates and offers.</p>
                    <div class="subscribe-form">
                    	<form method="post">
                        	<input type="text" name="subscribe-name"><button type="submit" name="subscribe-btn">Subscribe!</button>
                        </form>
                    </div>
                    <p>We respect your privacy</p>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="col-md-4">
            	<div class="traveller-expert">
                	<h3>Traveler Expert?</h3>
                    <p>We would be more than happy to help you. Our team advisor are 24/7 at your service to help you.</p>
                    <div class="expert-conatct">
                    	<p>+977-1-4331402</p>
                        <p>info@mountainking.com.np</p>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="clearfix"></div>
</section>
<!-- footer top end -->

<footer id="footer">
	<div class="container">
    	<div class="row">
        	<div class="col-md-6">
            	<div class="cpright">Copyright © 2017 Mountain King Treks and Expedition. All rights reserved.<br />
<ul class="list-inline">
	<li><a href="">Terms & Conditions </a></li> |
    <li><a href="">Privacy Policy</a></li>	
</ul>
</div>
            </div>
            <div class="col-md-2">
            	<div class="icon-img"><img src="images/associated-with.png" class="img-responsive" alt="We are associated."></div>
            </div>
            
            <div class="col-md-2">
            	<div class="icon-img"><img src="images/we-accept.png" class="img-responsive" alt="We Accept"></div>
            </div>
            
            <div class="col-md-2">
            	<div class="icon-img"><a href=""><img src="images/tripadvisor.png" class="img-responsive" alt="Trip Advisor"></a></div>
            </div>
            
        </div>
    </div>
</footer>
<!-- footer end -->   
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.bxslider.js"></script>
<script>
	$(document).ready(function(){
		$('.bxslider').bxSlider({
			auto: true,
			mode: 'fade',
			captions: true
		});
	});
</script>
<script src="js/owl.carousel.js"></script>
<script>
	$(document).ready(function() {
      $('.recommended-trip-inner .owl-carousel').owlCarousel({
		loop: true,
		margin: 10,
		navText: [ '<img src="images/rec-l.png" alt="Previous">', '<img src="images/rec-r.png" alt="Next">' ],
		responsiveClass: true,
		items: 6,
		dots: false,
		autoplay: true,
		nav: true,
		responsive: {
                  0: {
                    items: 1,
                    nav: false
                  },
                  600: {
                    items: 3,
                    nav: false
                  },
                  1000: {
                    items: 3,
                    nav: true,
                    loop: false,
                    margin: 20
                  }
                }	
	});
	
	$('.recommended-outdoor-packages-list .owl-carousel').owlCarousel({
		loop: true,
		margin: 10,
		navText: [ '<img src="images/rec-l.png" alt="Previous">', '<img src="images/rec-r.png" alt="Next">' ],
		responsiveClass: true,
		items: 6,
		dots: false,
		autoplay: true,
		nav: true,
		responsive: {
                  0: {
                    items: 1,
                    nav: false
                  },
                  600: {
                    items: 3,
                    nav: false
                  },
                  1000: {
                    items: 4,
                    nav: true,
                    loop: false,
                    margin: 20
                  }
                }	
	})  
	 
    });
	
</script>  
</body>
</html>