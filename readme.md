# wp-plugin-base
Wordpress Plugin Development project utilising the MVC pattern. Development environment based on the official `Docker` wordpress image with `gulp` as a build tool.

## Usage
This project was created and tested on Unix based systems. There is no support for Windows, though it may be achieved by modifying the `gulpfile`.

A `sudo` account is needed for lauching and managing the docker instance. The `sudo` account needs to have access to `node`, which is also required to run this.

To start the development environment run `sudo npm run dev`, navigate to `localhost:8080` and initialize a Wordpress instance.

To build the plugin run `npm run build`

## Before you start...
Initially you should fill the `plugin-configuration.json` file which should contain the plugin name, author name, version etc. That data is needed mainly for Wordpress which will display it in the plugin install/management section. These are the fields you will find inside the file:

- `namespace` - the namespace that will be used for the PHP code. In the code it is represented by a \_\_PluginNamespace\_\_ tag. Use this namespace as a base for all of your code.
- `name` - name of the plugin which will be visible within Wordpress plugin management
- `executableName` - name of the output directory of the plugin; should be in snake case
- `description` - description showed by Wordpress
- `version` - version number showed by Wordpress
- `author` - author name showed by Wordpress

## Backend
The backend utilises an MVC pattern, so you will need to create Controllers for the application logic, Models, to manage the database via `wpdb` and Views which will contain the presentation. Also registration of Wordpress Shortcodes is possible.

Controllers may also contain AJAX actions which can be used for async requests from the frontend app, which at the moment uses `AngularJs`.

### Controllers
To create a controller add a file to `src/backend/app` directory. It should contain a Controller class definition extending the `Base\Controller` abstract class.

#### Creating an Action
You can create pages that might be attached to the Wordpress dashboard sidebar. Typically those kinds of pages are used for Admin management. To create such an action add a method to the Controller class like this:

````php
public function action__action_name(){
    $postContent = $this->view_model['content'];
    $urlContent = $this->url_params['content'];

    /*
        action logic should go here...
    */

    return $this->view(
        "example_controller/action_name",
        array(
            'foo' => 'bar',
        )
    );
}
````

The `action__` prefix tells the plugin base that this is an action that will return a View, which will have access to the data provided as the second argument of the `view` method. You can access POST data through the `view_model` array, and GET params via `url_params` array.

#### Creating an Ajax Action
Controllers may also have Ajax actions defined which may be called from the Administration dashboard, or from within Shortcodes available to all users. You need to define appropriate access levels to do this. Example:

````php
public function ajax__public__action_name() {
    $content = $this->view_model['content'];
    
    /*
        action logic should go here...
    */

    return json_encode(
        array(
            'foo' => $bar
        )
    );
}
````

This will define a `public` ajax action, meaning that it may be called by authenticated and anonymous users. If you want to create an action which may be accessed only by authenticated users name the action like this - `ajax__private__action_name`.

You can access the request JSON data via the `view_model` array.

Ajax actions should return a JSON encoded array with all the desired data.

### Views
Views are called by Controller Actions and should contain only the presentation layer of the action. Just add a view corresponding to a controller action in such file `src/app/backend/view/example_controller/action_name.php`.

The data from the controller might be accessed by the `$VIEWBAG` associative array.

### Models
Our plugin will probably also need persistent data storage which like the rest of Wordpress is achieved with a `MySql` database. The database itself might be accessed via the `$wpdb` global Wordpress interface.

To create a model add a file like `src/backend/model/example_repository.php` with a class inside deriving from `Base\Model` abstract class.

In this file you should define methods that will execute operations on the database, typically it should have a structure of a Repository pattern. Inside you can perform operations using the `_db` protected object which is just the `$wpdb` global from Wordpress. You can read more about it [here](https://codex.wordpress.org/Class_Reference/wpdb).

There should also be two static methods defined here:

`get_table_name` - should return the SQL table name (with WP prefix) on which the model will be operating.

`initialize` - this method will be called when the pluign is being activated by Wordpress. You can use it to create the table structure if needed.

### Shortcodes
The public part of the plugin should have a form of a Wordpress shortcode which you can embedd wherever you want. To create a Shortcode definition add a `Base\Shortcode` derived class in `src/backend/shortcode/example_shortcode.php`. The most important method which you need to implement is the `execute` method which might look like this:

````php
public function execute() {
    $content = $this->view_model['content'];
    
    /*
        action logic should go here...
    */

    return $this->View(
        "/shortcode/example_shortcode",
        array(
            "foo" => "bar"
        )
    );
}
````

This action will render the View file placed in `src/backend/view/shortcode/example_shortcode.php`. Shortcode parameters might be accessed via the `view_model` assoc array.

## Frontend
By default the frontend of the plugin should be a SPA application created with `AngularJs`. It suppports `SCSS` styling, and `ES6` JS support achieved via Babel. You can add third party modules via `npm` and import them in the app. All of this is managed by Gulp which should be easy to adjust for your specific needs.

### AngularJS App
The plugin is configured to utilise a SPA app written in `AngularJs`.

In the `src/frontend/js` you should have the application defined - controllers, services, factories, directives etc. This codebase has an example TODO app defined there so you can use it as a seed for further development.

### Calling Ajax Actions
To comunicate with the backend you can use the already defined Angular service called `wpPluginCall.service`. Just inject this into your controller and you are good to go.

Using the `call(controller, action, data)` function you can call a specific Ajax action from within a particullar Controller defined earlier. The third argument should be an object containing data which can be accessd by the `view_model` array in the Controller action.

### Styling
The plugin suppports SCSS compilation. You should utilize the two SCSS files placed in the styles directory (`src/frontend/scss`):

`main.scss` - is a style file which will be used in Shortcodes
`admin.scss` - will be loaded for styling the Wordpress Dashboard part of the plugin

## Extensions and more detailed config
The application is built via `Gulp` so you can modify the `gulpfile.js` if you would like to define the build process more precisely.

By modifying the `src/wp-plugin.php` file you can attach specific styles, scripts, wordpress hooks etc.
