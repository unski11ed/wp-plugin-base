<div id="todo-app-container" ng-app="todo">
    <div class="todos" ng-controller="todo.todoController as todoCtrl">
        <!-- Header + Add Button -->
        <div class="todos__header todos-header">
            <h3 class="todos-header__title"><?= $VIEWBAG['list_name'] ?></h3>

            <button
                ng-click="actions.toggleModal()"
                class="todos-header__add button"
                type="button"
            >
                Add
            </button>
        </div>

        <!-- List -->
        <ul class="todos__list todos-list">
            <li
                ng-repeat="item in todos.list"
                ng-class="{ 'todo-item--complete': item.isComplete }"
                class="todo-item"
            >
                <input
                    type="checkbox"
                    ng-model="item.isComplete"
                    class="todo-item__check"
                    ng-change="actions.toggleCompleteTodo(item.id)"
                />
                
                <span class="todo-item__content" ng-class="{'todo-item__content--complete': item.isComplete}">
                    {{ item.content }}
                </span>

                <button
                    type="button"
                    class="todo-item__delete button"
                    ng-click="actions.deleteTodo(item.id)"
                >
                    Delete
                </button>
            </li>
        </ul>

        <!-- Empty Placeholder -->
        <div class="todos__empty" ng-if="todos.list.length === 0">
            There are no TODOs added yet, you can add one by clicking <a href="javascript:;">here</a>.
        </div>

        <!-- Add Modal -->
        <div class="modal" ng-class="{ 'modal--visible': modalVisible }">
            <div class="add-todo modal__content">
                <h2 class="add-todo__header">Add Todo</h2>
                <form class="add-todo__form form" ng-submit="actions.addTodo()">
                    <div class="form__field">
                        <label for="todo-content">Content</label>
                        <input type="text" id="todo-content" name="content" ng-model="newTodo.content" />
                    </div>
                    <div class="form__actions">
                        <button type="submit" class="button">
                            Add
                        </button>
                        <button
                            type="button"
                            class="button button--outline"
                            ng-click="actions.toggleModal()"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
