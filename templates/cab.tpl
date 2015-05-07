	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">SZ panel</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="#spisok" data-toggle="tab">Список сайтов</a></li>
					<li><a href="#add" data-toggle="tab">Добавить сайт</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="#">{$data.username}</a></li>
					<li><a href="/index.php/user/logoff">Выход</a></li>
				</ul>
			</div>
		</div>
    </nav>

    <div class="container">
		<div class="jumbotron">
			<div class="tab-content">
				<div class="tab-pane fade in active" id="spisok">
					{if $data.domains|@count==0}
					<h2>У вас отсутствуют сайты. <a href="#add" onclick="do_add();">Добавить!</a></h2>
					{else}
						<table class="table table-hover">
							<thead>
								<tr>
									<th>№</th>
									<th>Ссылка</th>
									<th>FTP</th>
									<th>Сменить пароль FTP</th>
									<th>Удалить</th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$data.domains key=k item=v}
									<tr>
										<td>{$k+1}</td>
										<td><a href="http://{$v.name}.{$data.head_domain}/">{$v.name}.{$data.head_domain}</a></td>
										<td>ftp://{$data.head_domain}/<br>Login: {$v.name}<br>Password: ********</td>
										<td>
											<form action="/index.php/user#spisok" method="POST">
												<input name="newpass" type="password" class="form-control" placeholder="Новый пароль" required>
												<input name="iddomain" type="hidden" value="{$v.id}">
												<input name="action" type="hidden" value="change">
												<button class="btn btn-sm btn-warning btn-block" type="submit">Сменить</button>
											</form>
										</td>
										<td>
											<form action="/index.php/user#spisok" method="POST">
												<input name="iddomain" type="hidden" value="{$v.id}">
												<input name="action" type="hidden" value="delete">
												<button class="btn btn-sm btn-danger btn-block" type="submit">Удалить</button>
											</form>
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					{/if}
				</div>
				<div class="tab-pane fade" id="add">
					<form class="form-signin" method="POST" action="/index.php/user#add">
						<h2 class="form-signin-heading">Добавить сайт</h2>
						{if isset($data.error)}
						<div class="bs-callout bs-callout-{if $data.error>0}danger{else}info{/if}" id="callout-btn-group-anchor-btn">
							<h4>Ошибка</h4>
							<p>Домен занят</p>
						</div>
						{/if}
						<label for="inputDomain" class="sr-only">Домен</label>
						<input name="domain" type="text" id="inputDomain" class="form-control" placeholder="Домен (English only)" required autofocus pattern="{literal}[A-Za-z0-9]{1,}{/literal}">
						<label for="inputPassword" class="sr-only">Пароль</label>
						<input name="password" type="password" id="inputPassword" class="form-control" placeholder="Пароль" required>
						<input type="hidden" name="action" value="add">
						<button class="btn btn-lg btn-success btn-block" type="submit">Добавить домен</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	
	<script>
		$(function () {
			if (location.hash!=''){
				var activeTab = $('[href=' + location.hash + ']');
				activeTab && activeTab.tab('show');
			}
		});
		
		function do_add() {
			location.hash = '#add';
			activeTab2 = $('[href=#add]');
			activeTab2 && activeTab2.tab('show');
		}
	</script>