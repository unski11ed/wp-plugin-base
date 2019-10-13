<div class="admin-todos">
    <h1>Todo Items</h1>
    
    <ul>
        <?php foreach($VIEWBAG['todos'] as $todo): ?>
            <li class="admin-todo <?= $todo['complete'] === '1' ? 'admin-todo--complete' : '' ?>">
                <?= $todo['content'] ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
