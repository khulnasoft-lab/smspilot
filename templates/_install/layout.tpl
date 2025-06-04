<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link rel="icon" href="./templates/_assets/images/favicon.png">

    <title>Installation &middot; Smspilot</title>

    <link rel="stylesheet" href="//fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="templates/_assets/css/libs/line-awesome.min.css">
    <link rel="stylesheet" href="./templates/_assets/css/libs/flag-icon.min.css">
    <link rel="stylesheet" href="./templates/dashboard/assets/css/libs/bootstrap.min.css">
    <link rel="stylesheet" href="./templates/dashboard/assets/css/style.min.css">

    <style>
    	.card {
    		width: 50rem;
    	}
    </style>
</head>

<body>

    <div smspilot-preloader>
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div smspilot-wrapper>
	    <div class="container">
	    	<div class="w-100 mb-5 text-center">
	    		<img src="./templates/_install/assets/images/logo.png" class="w-25">
	    	</div>

            <div class="card mx-auto" smspilot-install>
                <div class="card-header mx-auto">
                    <h2>
                        <i class="la la-magic la-lg"></i> 
                        Install Wizard
                    </h2>
                </div>

                <form smspilot-form>
	                <div class="card-body">
	        			<div class="form-row">
	                    	<div class="form-group mb-0 col-12">
			                    <h2 class="text-uppercase">System</h2>
			                </div>

			                <div class="form-group col-6">
			                    <label>Site Name</label>
			                    <input type="text" name="site_name" class="form-control" placeholder="eg. Smspilot">
			                </div>

			                <div class="form-group col-6">
			                    <label>Protocol</label>
			                    <select name="protocol" class="form-control">
			                    	<option value="1" selected>HTTP</option>
			                    	<option value="2">HTTPS</option>
			                    </select>
			                </div>
			                
			                <div class="form-group col-12">
			                    <label>Site Description</label>
			                    <input type="text" name="site_desc" class="form-control" placeholder="eg. This is my awesome site!">
			                </div>
			            </div>

			            <div class="form-row">
	                    	<div class="form-group mb-0 col-12">
			                    <h2 class="text-uppercase">Database</h2>
			                </div>

			                <div class="form-group col-6">
			                    <label>Database Host</label>
			                    <input type="text" name="dbhost" class="form-control" placeholder="eg. localhost:port" value="localhost:3306">
			                </div>

			                <div class="form-group col-6">
			                    <label>Database Name</label>
			                    <input type="text" name="dbname" class="form-control" placeholder="eg. smspilot_db">
			                </div>
			                
			                <div class="form-group col-6">
			                    <label>Database Username</label>
			                    <input type="text" name="dbuser" class="form-control" placeholder="eg. root">
			                </div>

			                <div class="form-group col-6">
			                    <label>Database Password</label>
			                    <input type="password" name="dbpass" class="form-control" placeholder="eg. password">
			                </div>
			            </div>

			            <div class="form-row">
	                    	<div class="form-group mb-0 col-12">
			                    <h2 class="text-uppercase">Account</h2>
			                </div>

			                <div class="form-group col-4">
			                    <label>Full Name</label>
			                    <input type="text" name="name" class="form-control" placeholder="eg. John Doe">
			                </div>

			                <div class="form-group col-4">
			                    <label>Email Address</label>
			                    <input type="text" name="email" class="form-control" placeholder="eg. user@mail.com">
			                </div>
			                
			                <div class="form-group col-4">
			                    <label>Password</label>
			                    <input type="password" name="password" class="form-control" placeholder="eg. Enter Password">
			                </div>
			            </div>
	                </div>

	                <div class="card-footer text-center">
					    <button type="submit" class="btn btn-lg btn-primary pt-3">
					    	<h4 class="text-white">
					    		<i class="la la-cog la-lg"></i> Install Smspilot
					    	</h4>
					    </button>
					</div>
				</form>
            </div>

            <div class="card mx-auto" style="display: none" smspilot-installed>
                <div class="card-header mx-auto">
                    <h2>
                        <i class="la la-check-circle la-lg"></i> 
                        Install Successful!
                    </h2>
                </div>

                <div class="card-body text-center">
                	<h4 class="text-uppercase">Smspilot has been successfully installed!</h4>
                	<div class="alert alert-warning">
                		<h6 class="text-uppercase">Please delete the following files/folders:</h6>
                		<ul class="mb-3">
                			<li>system/controllers/install.php</li>
                			<li>templates/_install/</li>
                			<li>populate.sql</li>
                			<li>install.sql</li>
                		</ul>

                		<h6 class="text-uppercase">Change permissions of the following files to 644:</h6>
                		<ul>
                			<li>system/configurations/cc_env.inc</li>
                			<li>system/configurations/cc_ver.inc</li>
                		</ul>
                	</div>
                </div>

				<div class="card-footer text-center">
				    <a href="./" class="btn btn-lg btn-primary pt-3">
				    	<h4 class="text-white">
				    		<i class="la la-eye la-lg"></i> View Site
				    	</h4>
				    </a>
				</div>
            </div>
	    </div>
	</div>

	<div class="footer">
	    <div class="container">
	        <div class="row align-items-center">
	            <div class="col-12">
	                <div class="copyright text-center">
	                    <p>All Rights Reserved &copy; {date(Y)}</p>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

    <script src="./templates/_assets/js/libs/fetch.min.js"></script>
    <script>
    	var lang_response_went_wrong = "Something went wrong!",
            lang_validate_cannotemp = "cannot be empty!",
            lang_alert_attention = "Attention";

        fetchInject([
            "./templates/_install/assets/js/install.js"
        ], fetchInject([
            "./templates/_assets/js/functions.js",
        ], fetchInject([
            "./templates/_assets/js/libs/pjax.min.js",
            "./templates/_assets/js/libs/waves.min.js",
            "./templates/_assets/js/libs/topbar.min.js",
            "./templates/_assets/js/libs/izitoast.min.js",
            "./templates/_assets/js/libs/bootstrap-select.min.js"
        ], fetchInject([
            "./templates/_assets/js/libs/bootstrap.min.js"
        ], fetchInject([
            "./templates/_assets/js/libs/jquery.min.js",
            "./templates/_assets/css/libs/waves.min.css",
            "./templates/_assets/css/libs/izitoast.min.css",
            "./templates/_assets/css/libs/bootstrap-select.min.css"
        ])))));
    </script>
</body>

</html>