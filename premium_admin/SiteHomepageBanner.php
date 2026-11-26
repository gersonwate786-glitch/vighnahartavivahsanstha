<?php
    include_once '../databaseConn.php';
    include_once '../class/Config.class.php';
    $configObj = new Config();
    include_once '../lib/requestHandler.php';
    $DatabaseCo = new DatabaseConn();

	/**
	 * Handle banner upload with case-insensitive extension support, size validation,
	 * and cache-busting unique filename generation.
	 */
	function handleBannerUpload($DatabaseCo, $fieldName, $dbColumn, $label) {
		if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
			return;
		}
		if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
			echo "<script>alert('{$label} upload failed (error code: " . (int)$_FILES[$fieldName]['error'] . ").');window.location='SiteHomepageBanner.php';</script>";
			exit;
		}

		$targetDir = '../img/banners/';
		$imageName = $_FILES[$fieldName]['name'];
		$imageFileType = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
		$allowedTypes = array('jpg', 'jpeg', 'png', 'webp');

		if (!in_array($imageFileType, $allowedTypes, true)) {
			echo "<script>alert('Sorry, only JPG, JPEG, PNG & WEBP files are allowed.');window.location='SiteHomepageBanner.php';</script>";
			exit;
		}
		if ($_FILES[$fieldName]['size'] > 2097152) {
			echo "<script>alert('Your file size is more than 2MB.');window.location='SiteHomepageBanner.php';</script>";
			exit;
		}
		if (@getimagesize($_FILES[$fieldName]['tmp_name']) === false) {
			echo "<script>alert('{$label} is not a valid image file.');window.location='SiteHomepageBanner.php';</script>";
			exit;
		}

		$imgConvertedName = $fieldName . '-' . time() . '.' . $imageFileType;
		$targetFile = $targetDir . $imgConvertedName;

		if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $targetFile)) {
			$safeName = mysqli_real_escape_string($DatabaseCo->dbLink, $imgConvertedName);
			$DatabaseCo->dbLink->query("UPDATE site_config SET {$dbColumn}='{$safeName}' WHERE id='1'");
			echo "<script>alert('{$label} Uploaded Successfully');window.location='SiteHomepageBanner.php';</script>";
			exit;
		}

		echo "<script>alert('{$label} size is too large or could not be saved.');window.location='SiteHomepageBanner.php';</script>";
		exit;
	}

	$bannerConfigs = array(
		array('field' => 'banner-1', 'column' => 'banner1', 'label' => 'Banner 1', 'formId' => 'bannerForm1'),
		array('field' => 'banner-2', 'column' => 'banner2', 'label' => 'Banner 2', 'formId' => 'bannerForm2'),
		array('field' => 'banner-3', 'column' => 'banner3', 'label' => 'Banner 3', 'formId' => 'bannerForm3'),
	);

	foreach ($bannerConfigs as $cfg) {
		handleBannerUpload($DatabaseCo, $cfg['field'], $cfg['column'], $cfg['label']);
	}

$SQL_SELECT_BANNER=$DatabaseCo->dbLink->query("SELECT banner1,banner2,banner3 FROM site_config WHERE id='1'");
$row=mysqli_fetch_object($SQL_SELECT_BANNER);
?>

<!DOCTYPE html>
<html>
	<head>
    	<meta charset="UTF-8">
    	<title>Admin | Update Homepage Banner</title>
    	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		
   		<!-- Bootstrap & custom css -->
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="css/custom.css" rel="stylesheet" type="text/css" />
    
    	<!-- Font Awsome -->
		<script src="https://kit.fontawesome.com/48403ccd1a.js" crossorigin="anonymous"></script>
		
    	<!-- Ionicons -->
    	<link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
   		
		<!-- Theme css -->
    	<link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    	<link href="dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
		
   		<!-- Checkbox css -->
		<link href="plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />
		
		<!-- Post Validation CSS -->
    	<link href="css/postvalidationcss.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="../css/validate.css">
	</head>	
    <body class="skin-blue">
    	<!-- Icon Loader -->
        <div class="preloader-wrapper text-center">
        	<div class="spinner"></div>
        </div>
        <!-- /. Icon Loader-->
   		<div class="wrapper" style="display:none" id="body">
			<!-- Header & Menu -->
			<?php include "page-part/header.php"; ?> 
			<?php include "page-part/left_panel.php"; ?>
			<!-- /. Header & Menu -->
      		<div class="content-wrapper">
		  		<section class="content-header">
					<h1>Home Page Banner</h1>
					<ol class="breadcrumb">
						<li><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
					  	<li class="active">Add / Edit Homepage Banner</li>
					</ol>
		  		</section>
			  	<section class="content">
					<div class="row">
						<div class="box-body">
							<div class="row">
								<?php foreach ($bannerConfigs as $cfg) {
									$col = $cfg['column'];
									$bannerFile = isset($row->$col) ? $row->$col : '';
									$bannerPath = '../img/banners/' . $bannerFile;
									$hasBanner = ($bannerFile !== '' && file_exists($bannerPath));
									$previewSrc = $hasBanner
										? $bannerPath . '?v=' . (@filemtime($bannerPath) ?: time())
										: '../img/no-banner.jpg';
								?>
								<div class="col-lg-4">
									<form method="post" enctype="multipart/form-data" id="<?php echo $cfg['formId']; ?>" action="">
										<div class="box box-success siteLogo">
											<div class="box-body">
												<h4>Add / Edit <?php echo $cfg['label']; ?></h4>
												<div class="row">
													<div class="col-xs-12">
														<img src="<?php echo htmlspecialchars($previewSrc, ENT_QUOTES, 'UTF-8'); ?>" class="img-responsive img-thumbnail">
													</div>
													<div class="col-xs-12">
														<p class="mt-5 fontS12"><b>Note</b>:- Banner size - <b class="text-danger">1351px(Width) X 591px(Height).</b></p>
													</div>
													<div class="col-xs-12">
														<div class="form-group mt-10">
															<input type="file" class="form-control" name="<?php echo $cfg['field']; ?>" accept=".jpg,.jpeg,.png,.webp,image/*" data-validetta="required">
														</div>
														<div class="col-xs-12">
															<p class="fontS12"><b>Note:-</b>Only <b>jpg, jpeg, png &amp; webp</b> files supported. Max Size <b>2MB</b>.</p>
														</div>
														<div class="col-xs-12">
															<p class="fontS12"><b>Note:-</b>Press <b>Control + f5</b> if banner not reflecting after upload.</p>
														</div>
														<div class="form-group text-center">
															<input type="submit" class="btn btnThemeG3" name="sub_add_logo" value="Submit">
														</div>
													</div>
												</div>
												<div class="clearfix"></div>
											</div>
										</div>
									</form>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
			</section>
			</div>
     		<?php include "page-part/footer.php"; ?>
		</div>
		<!-- jQuery -->
		<script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
    
		<!-- Bootstrap JS -->
		<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<script>
			$(document).ready(function() {
		   		$('#body').show();
		   		$('.preloader-wrapper').hide();
		   	});
	   	</script>	
   
  		<!-- Validation -->
		<script src="../js/validetta.js" type="text/javascript"></script>
		<script type="text/javascript">
			$(function(){
				$('#bannerForm1').validetta({
					errorClose : false,
					realTime : true
				});
				$('#bannerForm2').validetta({
					errorClose : false,
					realTime : true
				});
				$('#bannerForm3').validetta({
					errorClose : false,
					realTime : true
				});
			});
		</script>
		
		<!--jquery for left menu active class-->
		<script type="text/javascript" src="dist/js/general.js"></script>
		<script type="text/javascript" src="dist/js/cookieapi.js"></script>
		<script type="text/javascript">
			setPageContext("site-settings","homepagebanner");
		</script>	
  
		<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
		<script>
			$.widget.bridge('uibutton', $.ui.button);
		</script>
    	<!-- Theme Js -->
		<script src="dist/js/app.min.js" type="text/javascript"></script>
	</body>
</html>