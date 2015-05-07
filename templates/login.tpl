<div class="container">

	<form class="form-signin" method="POST">
		<h2 class="form-signin-heading">Войдите</h2>
		{if isset($data.error)}
		<div class="bs-callout bs-callout-danger" id="callout-btn-group-anchor-btn">
			<h4>Ошибка</h4>
			<p>Неверная связка логин-пароль</p>
		</div>
		{/if}
		<label for="inputEmail" class="sr-only">Email</label>
		<input name="email" type="email" id="inputEmail" class="form-control" placeholder="Email" required autofocus>
		<label for="inputPassword" class="sr-only">Пароль</label>
		<input name="password" type="password" id="inputPassword" class="form-control" placeholder="Пароль" required>
		<button class="btn btn-lg btn-success btn-block" type="submit">Войти</button>
		<a href="/index.php/user/reg" class="btn btn-lg btn-primary btn-block" type="submit">Зарегистрироваться</a>
	</form>

</div>