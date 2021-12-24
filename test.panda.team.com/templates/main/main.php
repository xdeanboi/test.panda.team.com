<?php include __DIR__ . '/../header.php'?>
<div style="text-align: center">
    <h1>Авторизация</h1>
    <? if (!empty($error)): ?>
        <div class="error">
            <p><strong><?= $error ?></strong></p>
        </div>
    <? endif; ?>
    <form action="/" method="post">
        <label for="nickname">Nickname:
            <input type="text" name="nickname" value="<?= $_POST['nickname'] ?? '' ?>" placeholder="Введите Nickname">
        </label>
        <br><br>
        <label for="password">Password:
            <input type="password" name="password" value="<?= $_POST['password'] ?? '' ?>"
                   placeholder="Введите password">
        </label>
        <br><br>
        <input type="submit" value="Войти">
    </form>
</div>
<?php include __DIR__ . '/../footer.php'?>