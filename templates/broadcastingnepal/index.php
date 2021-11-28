<?php

defined('_JEXEC') or die;
error_reporting(0);

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/js/template.js');

// Add Stylesheets
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/bootstrap.css');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/template.css');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/editor.css');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    
	<!--[if lt IE 9]>
		<script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
	<![endif]-->
</head>

<body>
	<?php  $menu =& JSite::getMenu();    // Load the menu
		$active = $menu->getActive(); // Get the current active menu
		if ($active->home ==1) {  ?>
    <div id="top_wrap">
        <div class="container">
            <div class="content row">
                <div class="col col-lg-6 col-md-6 col-xs-12 col-sm-12">
                    <a href="index.php"><img src="templates/broadcastingnepal/images/logo.png" alt=""></a>
                </div>
                <div class="col col-lg-2 col-md-2 col-xs-12 col-sm-12 search"></div>
                <div class="col col-lg-4 col-md-4 col-xs-12 col-sm-12 search">
                    <jdoc:include type="modules" name="top-search" style="none" />   
                </div>
            </div>
        </div>
    </div>
<!--    end of top_wrap-->
   <div id="mainmenu">
       <div class="container">
           <div class="content row">
            <jdoc:include type="modules" name="mainmenu" style="none" />   
           </div>
       </div>
   </div>
<!--   end of menu-->
   <div id="banner">
       <div class="container">
           <div class="content row">
               <div class="col col-lg-8 col-md-8 col-sm-12 col-xs-12 banner">
                    <jdoc:include type="modules" name="banner" style="none" />   
               </div>
               <div class="col col-lg-4 col-md-4 col-sm-12 col-xs-12 recent">
                    <jdoc:include type="modules" name="recent-news" style="xhtml" />   
               </div>
           </div>
       </div>
   </div>
<!--   end of banner-->
   <div id="mycontent">
       <div class="container">
           <div class="content row">
               <div id="leftcol">
                    <div class="col col-lg-3 col-md-3 col-sm-12 col-xs-12 leftcol">
                        <div class="recentmenu">
                            <jdoc:include type="modules" name="recentmenu" style="none" />    
                        </div> 
                        <div id="contactdetails">
                            <jdoc:include type="modules" name="contactdetails" style="xhtml" />    
                        </div>
                        <div id="policy">
                            <jdoc:include type="modules" name="policy" style="xhtml" />    
                        </div> 
                        <div id="notice">
                            <jdoc:include type="modules" name="notice" style="xhtml" />    
                        </div>
                        <div id="adds">
                            <jdoc:include type="modules" name="adds" style="xhtml" />    
                        </div>
                        <div id="facebook">
                            <jdoc:include type="modules" name="facebook" style="xhtml" />    
                        </div>
                    </div>   
               </div>
               <div class="rightcol">
                    <div class="col col-lg-6 col-md-6 col-sm-12 col-xs-12 myright">
                        <jdoc:include type="modules" name="newsscroll" style="none" />                           
                    </div> 
                    <div class="col col-lg-3 col-md-3 col-sm-12 col-xs-12 mailus">
                        <div class="mail">
                            <jdoc:include type="modules" name="mailus" style="none" />    
                        </div>                           
                    </div>   
               </div>
           </div>
       </div>
   </div>
<!--   end-->
   <div id="footer">
       <div class="container">
           <div class="content row">
                <jdoc:include type="modules" name="footer" style="none" />   
           </div>
       </div>
   </div>
    
    <?php } else { ?>
    <div id="top_wrap">
        <div class="container">
            <div class="content row">
<!--                <a href="index.php"><img src="templates/broadcastingnepal/images/logo.png" alt=""></a>-->
                <div class="col col-lg-6 col-md-6 col-xs-12 col-sm-12">
                    <a href="index.php"><img src="templates/broadcastingnepal/images/logo.png" alt=""></a>
                </div>
                <div class="col col-lg-2 col-md-2 col-xs-12 col-sm-12 search"></div>
                <div class="col col-lg-4 col-md-4 col-xs-12 col-sm-12 search">
                    <jdoc:include type="modules" name="top-search" style="none" />   
                </div>
            </div>
        </div>
    </div>
<!--    end of top_wrap-->
   <div id="mainmenu">
       <div class="container">
           <div class="content row">
            <jdoc:include type="modules" name="mainmenu" style="none" />   
           </div>
       </div>
   </div>
<!--   end of menu-->
   <div id="banner">
       <div class="container">
           <div class="content row">
               <div class="col col-lg-8 col-md-8 col-sm-12 col-xs-12 banner">
                    <jdoc:include type="modules" name="banner" style="none" />   
               </div>
               <div class="col col-lg-4 col-md-4 col-sm-12 col-xs-12 recent">
                    <jdoc:include type="modules" name="recent-news" style="xhtml" />   
               </div>
           </div>
       </div>
   </div>
   
<!--   end-->
  <div id="mycontent">
       <div class="container">
           <div class="content row">
               <div id="leftcol">
                    <div class="col col-lg-3 col-md-3 col-sm-12 col-xs-12 leftcol">
                        <div class="recentmenu">
                            <jdoc:include type="modules" name="recentmenu" style="none" />    
                        </div> 
                        <div id="contactdetails">
                            <jdoc:include type="modules" name="contactdetails" style="xhtml" />    
                        </div>
                        <div id="policy">
                            <jdoc:include type="modules" name="policy" style="xhtml" />    
                        </div> 
                        <div id="notice">
                            <jdoc:include type="modules" name="notice" style="xhtml" />    
                        </div>
                        <div id="adds">
                            <jdoc:include type="modules" name="adds" style="xhtml" />    
                        </div>
                        <div id="facebook">
                            <jdoc:include type="modules" name="facebook" style="xhtml" />    
                        </div>
                    </div>   
               </div>
               <div class="rightcol">
                    <div class="col col-lg-9 col-md-9 col-sm-12 col-xs-12 myright">
                        <div id="innerpage">
                            	<jdoc:include type="message" />
	                            <jdoc:include type="component" />
                        </div>                          
                    </div>   
               </div>
           </div>
       </div>
   </div>
   
   <div id="footer">
       <div class="container">
           <div class="content row">
                <jdoc:include type="modules" name="footer" style="none" />   
           </div>
       </div>
   </div>
   
    <?php } ?>
    
    
    
	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
