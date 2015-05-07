<div class="container">

	<form class="form-signin" method="POST">
		<h2 class="form-signin-heading">Регистрация</h2>
		{if isset($data.error)}
		<div class="bs-callout bs-callout-{if $data.error>0}danger{else}info{/if}" id="callout-btn-group-anchor-btn">
			<h4>{if $data.error>0}Ошибка{else}Поздравляем{/if}</h4>
			<p>{$data.message}</p>
		</div>
		{/if}
		<label for="inputEmail" class="sr-only">Email</label>
		<input name="email" type="email" id="inputEmail" class="form-control" placeholder="Email" required autofocus>
		<label for="inputPassword" class="sr-only">Пароль</label>
		<input name="password" type="password" id="inputPassword" class="form-control" placeholder="Пароль" required>
		<button class="btn btn-lg btn-success btn-block" type="submit">Зарегистрироваться</button>
		<a href="/" class="btn btn-lg btn-primary btn-block" type="submit">Назад</a>
	</form>

</div>