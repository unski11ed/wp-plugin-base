<div id="todo-app-container" ng-app="todo">
    <div class="todos" ng-controller="todo.todoController as todoCtrl">
        <div class="todos__header todos-header">
            <h3 class="todos-header__title"><?= $VIEWBAG['list_name'] ?></h3>

            <button class="todos-header__add" type="button">
                Add
            </button>
        </div>

        <ul class="todos__list todos-list">
            <li ng-repeat="item in todos.list" class="todo-item">
                <input
                    type="checkbox"
                    ng-model="item.isComplete"
                    class="todo-item__check"
                />
                
                <span class="todo-item__content" ng-class="{'todo-item__content--complete': item.isComplete}">
                    {{ item.content }}
                </span>

                <button type="button" class="todo-item__delete">
                    Delete
                </button>
            </li>
        </ul>

        <div class="todos__empty">
            There are no TODOs added yet, you can add one by clicking <a href="javascript:;">here</a>.
        </div>
    </div>
</div>
