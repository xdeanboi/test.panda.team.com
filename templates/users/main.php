<?php include __DIR__ . '/../header.php'?>
<div class="userMenu">
    <h3>Вы вошли как - <?= $user->getNickname() ?> | <a href="/users/exit">Выйти</a></h3>
</div>
<div style="text-align: center">
    <h2>Список задач:</h2>
    <table align="center" cellpadding="30px"  border="2px solid black">
        <? foreach ($tasks as $task):?>
        <tr>
            <? if ($task->getDone()):?>
            <td style="color: blue"><strong>Задача: <?= $task->getName()?></strong></td>
            <? else:?>
            <td><strong>Задача: <?= $task->getName()?></strong></td>
            <? endif;?>
            <td><button><a href="/task/done/<?= $task->getId()?>">Сделано</a></button></td>
            <td>
                <? if ($task->getDone()):?>
                <button><a href="/task/delete/<?= $task->getId()?>">Удалить</a></button>
                <? endif;?>
            </td>
        </tr>
        <? endforeach;?>
    </table>
    <br><br><br>
    <form action="/task/add" method="post">
        <label for="newTask">
            Новая задача <input type="text" name="name" value="<?= $_POST['name'] ?? ''?>" placeholder="Введите задачу">
            <br><br>
            <input type="submit" value="Добавить задачу">
        </label>
    </form>
</div>
<?php include __DIR__ . '/../footer.php'?>