<?php
	/* This part of PHP is required for the page to work. */

	$site_sub_title = 'Members';
	include_once('includes/front.inc.php');
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
<title>Private Project Prototype</title>
<meta charset="iso-8859-1">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="c/main.css" rel="stylesheet" type="text/css" media="all">
<link href="c/mediaqueries.css" rel="stylesheet" type="text/css" media="all">
<link href="c/bxslider.css" rel="stylesheet" type="text/css" media="all">
<!--[if lt IE 9]>
<link href="c/ie/ie8.css" rel="stylesheet" type="text/css" media="all">
<script src="c/ie/css3-mediaqueries.min.js"></script>
<script src="c/ie/html5shiv.min.js"></script>
<![endif]-->
</head>
<body class="">
<div class="wrapper row1">
  <header id="header" class="full_width clear">
    <hgroup>
      <h1><a href="index.html">PHPFANLIST REVIVAL</a></h1>
      <h2>free & responsive fanlisting script</h2>
    </hgroup>
  </header>
</div>
<!-- End Header -->
<!-- Begin Navigation -->
<div class="wrapper row2">
  <nav id="topnav">
    <ul class="clear">
      <li class="active"><a href="registration.php" title="Registration">Registration</a></li>
      <li><a class="drop" href="membership.php" title="Membership">Membership</a></li>
      <li><a class="drop" href="index.php" title="Back 2 the Fanlisting">Home</a></li>
      <li><a class="drop" href="#" title="Collective">Collective</a></li>
      <li><a href="http://www.jquery.com/" title="jQuery Website">jQuery</a></li>
      <li><a href="http://www.thefanlistings.org/" title="TFL">TFL</a></li>
      <li class="last-child"><a href="#" title="A Very Long Link Text Or Whatever">A Very Long Link Text Or Whatever You Need :)</a></li>
    </ul>
  </nav>
</div>
<!-- End Navigation -->
<!-- Begin Content -->
<div class="wrapper row3">
  <div id="container">
    <div id="homepage" class="clear">
      <section>
        <article class="one_third first">
        
          <div class="boxy">
          	<h3>Some Stats</h3>
          	<p>Members : <strong><?php echo PHPFANLIST_MEMBERCOUNT;?></strong></p>
			<p>Pending : <strong><?php echo PHPFANLIST_NUMJOIN; ?></strong></p>
			<p>Last Update : <strong><?php echo PHPFANLIST_LASTUPDATE; ?></strong></p>
          </div>
          
          <div class="boxy">
          	<h3>Social Media Like</h3>
          	<ul class="socials">
          		<li><a href="#"><i class="user"><img src="i/i-twitter3.png" alt="social media like"/></i>twitter</a></li>
		  		<li><a href="#"><i class="user"><img src="i/i-tfl3.png" alt="social media like"/></i>TFL</a></li>	  		
          	</ul>
          </div>
          
          <div class="boxy">
          <h3>Lorem Ipsum</h3>
          <p>Lorem Ipsum is not only a dummy text used during website creation, it has a long history since 1505.<br />
          Back to Renaissance and Lorem Ipsum forever !</p>
          <ul class="socials">
          <li><a href="#"><i class="user"><img src="i/arrow-down3.png" alt="social media like"/></i></a></li>
          </ul>
          	<div class="bigbutton bg2"><a href="#">Back to Fanlisting</a></div>
          </div>
       
        </article>
        <article class="two_third">
        
          	<!-- begins the join php part -->
          	<?php include(PHPFANLIST_INCLUDES . 'members.inc.php'); ?>
          	<!-- ending the join php part -->
          
        </article>                          
       </section>

    </div>
    <div class="clear"></div>
  </div>
</div>
<!-- End Content -->
<!-- Begin Footer -->
<div class="wrapper row4">
  <div id="copyright" class="clear">
    <p class="fl_left">copyright &copy; 2007 - 2014 - <a href="#">occlumens</a></p>
    <p class="fl_right">. <a href="#" title="design">. .</a></p>
  </div>
</div>
<!-- End Footer -->
<!-- Scripts -->
<script src="js/jquery-latest.min.js"></script>
<script src="js/jquery-mobilemenu.min.js"></script>
</body>
</html>
