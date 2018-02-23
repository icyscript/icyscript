<?php 
	/* This part of PHP is required for the page to work. */
	$site_sub_title = 'Join';
	include_once('includes/front.inc.php');
	include_once('./g_includes/join.inc.php');
	include_once('./g_includes/update.inc.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

	<head>
		<title>Private Project Prototype</title>
		<meta charset="iso-8859-1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
		<link href="c/main.css" rel="stylesheet" type="text/css" media="all">
		<link href="c/mediaqueries.css" rel="stylesheet" type="text/css" media="all">
		<link href="c/magnific-popup.css" rel="stylesheet" type="text/css" media="all">
		<link href="c/semantic-form.css" rel="stylesheet" type="text/css" media="all">
		<!--[if lt IE 9]>
		<link href="c/ie/ie8.css" rel="stylesheet" type="text/css" media="all">
		<script src="c/ie/css3-mediaqueries.min.js"></script>
		<script src="c/ie/html5shiv.min.js"></script>
		<![endif]-->
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/jquery-ui.min.js"></script>
		<script src="js/jquery.magnific-popup.js"></script>
		<script src="js/responsiveslides.min.js"></script>
	
		<script>
		  $(function() {
		    $(".rslides").responsiveSlides();
		  });
		</script>
		
		<script type="text/javascript">
	      $(function () {
	        // Script for Modal Popup ...
	        $('.popup-modal').magnificPopup({
		        type:'inline',
		        midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
		        });
	      });
	    </script>
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
					<li class="active"><a href="index.php" title="Registration">Registration</a></li>
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
							<div class="boxy4">
								<img src="i/neo-picto3.jpg">
								<h5>The <?php echo htmlentities($fanlisting->settings['site_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
								<p>Welcome to the join page of the blabla fanlisting.</p>
							</div>
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
						</article>
						
						<article class="one_third">
							<div class="boxy">
								<h3>Joining</h3>
								<!-- begins the join php part -->
								<?php if ($join_success !== true) { ?>
								<h2>Some Fanlisting Rules</h2>
								<ul>
									<li><strong>1</strong> : Clearly, you should appreciate or be fan.</li>
									<li><strong>2</strong> : You do not need a website to join.</li>
									<li><strong>3</strong> : If you have a website, please, grab a button and link it to this fanlisting.</li>
									<li><strong>4</strong> : You must complete the form correctly. This includes a valid email address and country, as required by TFL.</li>
									<li><strong>5</strong> : We don't link to websites with offensive content ( such as porn or hate websites ) so, we reserve the right to remove the link from behind your name.</li>
									<li><strong>6</strong> : Keep your membership information up to date !</li>
									<li><strong>7</strong> : Enjoy and have fun :)</li>
								</ul>

							
								<p>Fill out the form below to join the <strong><?php echo htmlentities($fanlisting->settings['site_name'], ENT_QUOTES, 'UTF-8'); ?></strong>. Please, read the rules :) If you have a website, grab a button and add a link back up to <a href="<?php echo htmlentities(handle_site($fanlisting->settings['site_url']), ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlentities($fanlisting->settings['site_name'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlentities(handle_site($fanlisting->settings['site_url']), ENT_QUOTES, 'UTF-8'); ?></a> before applying !</p>
								<p>\o/</p>
								
								<?php if ($message != '') { ?><p class="message"><?php echo $message; ?></p><?php } ?>
								<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
									<div class="ui form segment">
										<div class="field">
											<div class="ui left labeled icon input">
												<input id="name" name="name" type="text" placeholder="Name" size="30"<?php defaultValue('name'); ?> />
												<div class="ui corner label"><?php is_required('<i class="icon asterisk"></i>', 'name'); ?></div>
											</div>
										</div>
										<div class="field">
											<div class="ui left labeled icon input">
												<input id="mail" name="mail" type="text" placeholder="Email" size="30"<?php defaultValue('mail'); ?> />
												<div class="ui corner label"><?php is_required('<i class="icon asterisk"></i>', 'mail'); ?></div>
											</div>
										</div>
	
										<?php if ($fanlisting->settings['show_mail'] == 3) {?>
										<div class="field">
											<div class="ui left labeled icon input" style="text-align: right">
												<input id="showmail" name="showmail" type="checkbox" value="1"<?php defaultValue('showmail', true); ?> />
												<label for="showmail">Show my email</label>
											</div>
										</div>
										<?php } ?>
										
										<?php if ($fanlisting->settings['ask_url']) { ?>
										<div class="field">
											<div class="ui left labeled icon input">
												<input id="url" name="url" type="text" placeholder="Website" size="30"<?php defaultValue('url'); ?> />
												<div class="ui corner label"><?php is_required('<i class="icon asterisk"></i>', 'url'); ?></div>
											</div>
										</div>
										<?php } ?>
	
										<div class="field">
											<div class="ui left labeled icon input" >
												<input id="country" name="country" type="text" placeholder="Your Country" size="30" <?php defaultValue('country'); ?> />
												<div class="ui corner label"><?php is_required('<i class="icon asterisk"></i>', 'country'); ?></div>
											</div>
										</div>
									
									
										<?php  if ($fanlisting->settings['ask_rules']) { ?>
										<div class="field">
											<div class="ui left labeled icon input">
												<input id="rules" name="rules" type="text" size="30" placeholder="<?php echo  htmlentities($fanlisting->settings['rules_question'], ENT_QUOTES, 'UTF-8'); ?>" <?php defaultValue('rules'); ?> />
												<div class="ui corner label"><?php is_required('<i class="icon asterisk"></i>', 'rules'); ?></div>
											</div>
										</div>
										
										<?php } if ($fanlisting->settings['ask_custom']) { ?>
										<div class="field">
											<div class="ui left labeled icon input">
												<input id="custom" name="custom" type="text" size="30" placeholder="<?php echo  htmlentities($fanlisting->settings['custom_field_name'], ENT_QUOTES, 'UTF-8'); ?>" <?php defaultValue('custom'); ?> />
												<div class="ui corner label"><?php is_required('<i class="icon asterisk"></i>', 'custom'); ?></div>
											</div>
										</div>
										<?php } ?>
										
										<div class="lol">
											<input class="lol bg" name="dojoin" type="submit" id="dojoin" value="Join" />
										</div>
										<p>* required / all emails are spam protected :)</p>
									</div> <!-- end  ui form segment -->
								</form>
								<?php } else { ?>
								<p class="message"><?php echo $message; ?></p>
								<br /><br />
								<img src="i/testy-success.png" alt="success"/>
								<ul class="socials">
									<li><a href="registration.php"><i class="user"><img src="i/arrow-down2.png" alt="social media like"/></i></a></li>
								</ul>
								<div class="bigbutton bg3">
									<a href="index.php">Back to Join Page</a>
								</div>
								<?php } ?>
								<!-- ending the join php part -->
							</div> <!-- end div boxy -->
						</article>
        
						<article class="one_third">
							<div class="boxy2">
								<ul class="rslides">
									<li><img src="i/slider-pictos1.jpg" alt=""><br /></li>
									<li><img src="i/slider-pictos2.jpg" alt=""></li>
									<li><img src="i/slider-pictos3.jpg" alt=""></li>
								</ul>
								<h5>A slider</h5>
								<p>To show nice pictures</p>
							</div>
							
							<div class="boxy">
								<h3>Affiliates</h3>
								<ul class="affiliates">
									<?php ShowAffiliates(5); ?>
								</ul>
							</div>
						</article>
					</section>
					
					<section>
						<article class="one_third first">
							<div class="boxy">
								<h3>Quote 1</h3>
								<p class="crayon1">Want a cookie ? Grab it and get a :)</p>
							</div>
							
							<div class="boxy">
								<h3>News</h3>
								<?php showNews(1); ?>
							</div>
							
							<div class="boxy">
								<h3>Quote 2</h3>
								<p class="crayon2">What did say the cookie ? Be happy :)</p>
							</div>
						</article>
						
						<article class="one_third">
							<div class="boxy">
								<h3>Updating</h3>
								<?php if ($update_success !== true) { ?>
								<p>If you are already listed and want to update your data, you can do this below ...</p>
								<p>Fill in only those field that have changed. If you're not sure about what you filled in, you can also fill in all fields. Only the member ID is mandatory.</p>
								<?php if ($message2 != '') { ?>
								<p class="message"><?php echo $message2; ?></p>
								<?php } ?>
								<form method="post" action="<?php $_SERVER['PHP_SELF']; ?>" accept-charset="utf-8">
									<div class="ui form segment">
										<div class="field">
											<div class="ui left labeled icon input">
												<input name="mid" type="text" id="mid" placeholder="Member ID" size="6" maxlength="9"<?php defaultValue('mid'); ?> />
												<div class="ui corner label"><i class="icon asterisk"></i></div>
											</div>
										</div>
										<div class="field">
											<div class="ui left labeled icon input">
												<input id="name" name="name" type="text" placeholder="New name" size="30"<?php defaultValue('name'); ?> />
											</div>
										</div>
										<div class="field">
											<div class="ui left labeled icon input">
												<input id="mail" name="mail" type="text" placeholder="New email" size="30"<?php defaultValue('mail'); ?> />
											</div>
										</div>
										<?php if ($fanlisting->settings['show_mail'] == 3) {?>
										<div class="field">
											<div class="ui left label icon input">												
												<input id="showmail" name="showmail" type="checkbox" value="1" <?php defaultValue('showmail', true); ?> />
												<label for="showmail">Show my new email</label>
											</div>
										</div>
										<?php } ?>
										<?php if ($fanlisting->settings['ask_url']) { ?>
										<div class="field">
											<div class="ui left labeled icon input">
												<input id="url" name="url" type="text" placeholder="New website" size="30"<?php defaultValue('url'); ?> />
											</div>
										</div>
										<?php if (!$fanlisting->CheckRequired('url')) {?>
										<div class="field">
											<div class="ui left labeled icon input">
												<input id="deleteurl" name="deleteurl" type="checkbox" value="1" <?php defaultValue('deleteurl', 'checked'); ?> />
												<label for="deleteurl">Remove my URL</label>
											</div>
										</div>
										<?php } ?>
										<?php } ?>
										<?php if ($fanlisting->settings['allow_memberdelete']) { ?>
										<div class="field">
											<div class="ui left label icon input">
												<input id="delme" name="delme" type="checkbox" value="1"<?php defaultValue('delme', 'checked'); ?> />
												<label for="delme">Please delete me from the list</label>
											</div>
										</div>
										<?php } ?>
										<?php if ($fanlisting->settings['ask_country']) { ?>
										<div class="field">
											<select name="country" id="country"><?php include(PHPFANLIST_INCLUDES . 'countrylist.inc.php'); ?></select>
										</div>
										<?php } ?>
										<?php if ($fanlisting->settings['ask_custom']) { ?>
										<div class="field">
											<div class="ui left labeled icon input">
												<input id="custom" name="custom" type="text" size="30" placeholder="<?php echo $fanlisting->settings['custom_field_name']; ?>"<?php defaultValue('custom'); ?> />
											</div>
										</div>
										<?php } ?>
									</div> <!-- end ui form segment -->
									<div class="lol">
										<input class="lol bg1" name="doupdate" type="submit" id="doupdate" value="Update" />
									</div>
									<p>* required / all emails are spam protected :)</p>
								</form>
								<?php } else { ?>
								<p class="message"><?php echo $message2; ?></p>
								<br /><br />
								<img src="i/testy-update.png" alt="success"/>
								<ul class="socials">
									<li><a href="registration.php"><i class="user"><img src="i/arrow-down2.png" alt="social media like"/></i></a></li>
								</ul>
								<div class="bigbutton bg3">
									<a href="index.php">Back to Update Page</a>
								</div>
								<?php } ?>
							</div> <!-- end boxy -->
						</article>
						
						<article class="one_third">
							<div class="boxy4">
								<img src="i/neo-picto2.jpg">
								<h5>Advice for a Better Web</h5>
								<p>Let's be pragmatic : a fanlisting is not a secret place with "super-important-hot-informations" It's just some pages about a subject where people come and say : "Great ! I'm fan about too, I like your fanlisting and so, I suscribe" - No money transaction, no sensible information.</p>
								<p>So, why would you over protect such a thing ? I know, I know ... The web is not a safe place, and so on ... But, a good webmaster does always his/her duty : he/she saves databases. A php script is ... a php script : always buggy somewhere, enough for any good hacker ...</p>
								<p>As far we know how sometimes the web is unsafe for everyone, get your lesson : NEVER give your prime email. For anything related to fanlisting, forum, facebook, streaming or whatever non important stuff : ALWAYS get a 2nd or 3rd email. Free and Easy :)</p>
							</div>
						</article>
					</section>
					
				</div> <!-- end homepage -->
				<div class="clear"></div>
			</div> <!-- end container -->
		</div>
		<!-- End Content -->
		<!-- Begin Footer -->
		<div class="wrapper row4">
			<div id="copyright" class="clear">
				<p class="fl_left">copyright &copy; 2007 - 2018 - <a href="http://dazhibao.org">Dazhibao</a></p>
				<p class="fl_right">. <a href="#" title="design">. .</a></p>
			</div>
		</div>
		<!-- End Footer -->
		<!-- Scripts -->
		<script src="js/jquery-3.3.1.min.js"></script>
		<script src="js/jquery-mobilemenu.min.js"></script>
	</body>
</html>
