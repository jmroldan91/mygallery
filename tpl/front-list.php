<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>My gallery - List</title>

    <!-- Bootstrap Core CSS -->
    <link href="tpl/gallery1/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="tpl/gallery1/css/grayscale.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="tpl/gallery1/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">

    <!-- Navigation -->
    <nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand page-scroll" href="#page-top">
                    <i class="fa fa-play-circle"></i>  <span class="light">My </span> GALLERY
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-right navbar-main-collapse">
                <ul class="nav navbar-nav">
                    <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#galleries">Galleries</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#about">About</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#contact">Contact</a>
                    </li>
                    {singin}
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Intro Header -->
    <header class="intro">
        <div class="intro-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <h1 class="brand-heading">My Gallery</h1>
                        <p class="intro-text">A free, responsive, gallery provider.</p>
                        <p class="intro-text">CREATE</p>
                        <p class="intro-text">CUSTOMIZE</p>
                        <p class="intro-text">SHARE</p>
                        <a title="Start" href="?op=view&view=singin" class="btn btn-circle page-scroll">
                            <i class="fa fa-angle-double-down animated"></i><br>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Galleries Section -->
    <section id="galleries" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <h2>All galleries</h2>
                {galleries}
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <h2>About My Gallery</h2>
                <p>MY Gallery is a free Bootstrap 3 theme created by Start Bootstrap. It can be yours right now, simply download the template on. 
                The theme is open source, and you can use it for any purpose, personal or commercial.</p>
                <p>Grayscale includes full HTML, CSS, and custom JavaScript files along with LESS files for easy customization.</p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <br><br><br><br><br><br><br>
    <section id="contact" class="container content-section text-center">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <h2>Contact MY Gallery</h2>
                <p>Feel free to email us to provide some feedback on our templates, give us suggestions for new templates and themes, or to just say hello!</p>
                <p><a href="mailto:feedback@startbootstrap.com">feedback@mygallery.com</a>
                </p>
                <ul class="list-inline banner-social-buttons">
                    <li>
                        <a href="https://twitter.com/SBootstrap" class="btn btn-default btn-lg"><i class="fa fa-twitter fa-fw"></i> <span class="network-name">Twitter</span></a>
                    </li>
                    <li>
                        <a href="https://github.com/IronSummitMedia/startbootstrap" class="btn btn-default btn-lg"><i class="fa fa-github fa-fw"></i> <span class="network-name">Github</span></a>
                    </li>
                    <li>
                        <a href="https://plus.google.com/+Startbootstrap/posts" class="btn btn-default btn-lg"><i class="fa fa-google-plus fa-fw"></i> <span class="network-name">Google+</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p>Copyright &copy; My gallery 2016</p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="tpl/gallery1/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="tpl/gallery1/js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="tpl/gallery1/js/jquery.easing.min.js"></script>
    
    <!-- Controller API -->
    <script src="/js/controller.js"></script>

</body>

</html>
