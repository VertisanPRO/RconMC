{include file='header.tpl'}

<body id="page-top">

	<!-- Wrapper -->
	<div id="wrapper">

		<!-- Sidebar -->
		{include file='sidebar.tpl'}

		<!-- Content Wrapper -->
		<div id="content-wrapper" class="d-flex flex-column">

			<!-- Main content -->
			<div id="content">

				<!-- Topbar -->
				{include file='navbar.tpl'}

				<!-- Begin Page Content -->
				<div class="container-fluid">

					<!-- Page Heading -->
					<div class="d-sm-flex align-items-center justify-content-between mb-4">


						<div class="row mb-2">
							<div class="col-sm-6">
								<h1 class="m-0 text-dark">{$TITLE}</h1>
							</div>
						</div>
					</div>

					<!-- Main content -->
					<section class="content">
						<div class="container-fluid">
							<div class="card">
								<div class="card-body">
									<h5 style="display:inline">{$EDIT_SERVER} {$EDIT_NAME}</h5>
									<div class="float-md-right">
										<a href="{$BACK_LINK}" class="btn btn-warning">{$BACK}</a>
									</div>
									<hr />

									{if isset($ERRORS) && count($ERRORS)}
										<div class="alert alert-danger alert-dismissible">
											<button type="button" class="close" data-dismiss="alert" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
											<h5><i class="icon fas fa-exclamation-triangle"></i> {$ERRORS_TITLE}</h5>
											<ul>
												{foreach from=$ERRORS item=error}
													<li>{$error}</li>
												{/foreach}
											</ul>
										</div>
									{/if}

									<form action="" method="post">
										<div class="form-group">
											<label for="ServerName">{$SERVER_NAME}</label>
											<input type="text" id="ServerName" name="server_name" class="form-control" value="{$EDIT_NAME}">
										</div>
										<div class="form-group">
											<label for="ServerIP">{$SERVER_IP}</label>
											<input type="text" id="ServerIP" name="server_ip" class="form-control" value="{$EDIT_IP}">
										</div>
										<div class="form-group">
											<label for="ServerPort">{$SERVER_PORT}</label>
											<input type="text" id="ServerPort" name="server_port" class="form-control" value="{$EDIT_PORT}">
										</div>
										<div class="form-group">
											<label for="ServerPass">{$SERVER_PASS}</label>
											<input type="password" id="ServerPass" name="server_pass" class="form-control"
												value="{$EDIT_PASS}">
										</div>
										<div class="form-group">
											<input type="hidden" name="token" value="{$TOKEN}">
											<input type="submit" class="btn btn-primary" value="{$SUBMIT}">
										</div>
									</form>
								</div>
							</div>

						</div>
					</section>
				</div>

			</div>

			{include file='footer.tpl'}


		</div>
	</div>
	<!-- ./wrapper -->

	{include file='scripts.tpl'}

</body>

</html>