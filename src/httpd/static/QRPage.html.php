<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
	<title>PhQAgent二维码登录</title>
	<link href="./base.min.css" rel="stylesheet">
	<link href="./project.min.css" rel="stylesheet">
</head>
<body class="page-brand">
	<header class="header header-brand ui-header">
		<span class="header-logo">PhQAgent :: 扫描二维码登录</span>
	</header>
	<main class="content">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 col-lg-push-4 col-sm-6 col-sm-push-3">
					<section class="content-inner">
						<div class="card">
							<div class="card-main">
								<div class="card-header">
									<div class="card-inner">
										<h1 class="card-heading">二维码
										</h1>
									</div>
								</div>
								<div class="card-inner">
									<img style="width: 100%;" src="data:image/jpeg;base64,$BASE64QRCODE">
								</div>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</main>
	<footer class="ui-footer">
		<div class="container">
			<p>Powered By PhQAgent</p>
		</div>
	</footer>
</body>
</html>
