Fund Performance Visualisation (Ver. 2.0)
=======

![alt tag](https://raw.github.com/ywng/fundvis/master/scr_shot.png)

Design Characteristics:

1. Allow quick selection of the funds, and add it into the plot (click the color rectangles, mouse over to get the name)

2. Auto adjust of y-axis according to the selected funds

3. Allow traversal of time line quickly, quick selection of time interval (select over the yellow slider bar at the bottom)

4. Has a tracing dot to view the detailed price (next to the color rectangles) and date (top right corner). You may also click onto the dots/lines to know more details about the funds

5. Two modes for showing fund performance: 1. actual price--only one fund will be shown  2. percent mode, which allow you to add as many funds onto the chart as possible

6. The reference point (base price) of the percentage mode can be set to any date within the data range. That means you can easily find out one month, one year, 5 year return. (you can get percentage change of the fund price of any interval length: just move the refernce pt)





for server side code & web page

<h3>Framework that we are using</h3>
<ol>
<li><a href="http://ellislab.com/codeigniter/user-guide/">Codeigniter 2.1.4 - PHP Framework</a></li>
<li><a href="https://github.com/philsturgeon/codeigniter-restserver">RESTful API for Codeigniter</a></li>
</ol>

<h3>Files structure</h3>
<p>
Please refer to the document in Codeigniter to have a detail understanding. Please basically it can be separated into two parts, <b>application</b> and <b>system</b>
</p>

<h4>Application</h4>
<p>
It is the place where our main application will be put, all later codes commit should be into this file.
</p>

<h5>config</h5>
<p>
All the static fields like database location (in database.php) will be stored in this folder. You will need to modify the error_message.php in that folder later on.
</p>

<h5>controllers</h5>
<p>
The main place where all request is first processed, please take a look on user.php for example.
</p>

<h5>helpers</h5>
<p>
Our custom helper for doing our routine jobs will be put in here. It exists as a form of function but not class, you can think it is a Utility class that has static methods. Currently I do not see a need for adding any helper file in there, using the one provided by Codeigniter is good enough.
</p>

<h5>libraries</h5>
<p>
Our custom library for doing our routine jobs will be put in here. And it always exists as a form of class. Our two main libraries are REST_Controller.php (for handling RESTful request) and CORE_Controller.php (for handling session checking and validation as well as formulate the error and generate error message). Normally, you do not need to change the file in there, it will be covered by me.
</p>

<h5>models</h5>
<p>
The main place where all communications to database will happen in here. Please take user_model.php as the example.
</p>

<h5>views</h5>
<p>
The main place to return some views to user, since we don't need that (we only return JSON), you can skip this folder.
</p>

<h4>System</h4>
<p>
The core part of the framework, normally we will not modify any files in there. And it shares the same structure as the Application folder.
</p>


<h3>Note on creating new file</h3>
<h4>Common</h4>
<p>
Please start with this line
</p>
```
  <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
  // Please ignore the "?>" at the end.
```
<p>
And end with this
</p>
```PHP
/* End of file __filename__.php */
/* Location: ./application/__folder__/__filename__.php */
```

<h4>In controllers</h4>
<p>
For all RESTful controller,
<br>
please incl. this statement before defining the class
</p>
```PHP
require_once (APPPATH. 'libraries/REST_Controller.php');
```
<p>
And define the class like this
<br>
For example, if you have a class called `trip`
</p>
```PHP
class Trip extends REST_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->library('CORE_Controller');
		$this->core_controller->set_response_helper($this);
	}

	// other functions go below...
}
```
<p>
And name the file as trip.php
<br>
Example can be found in user.php
</p>

<h4>In models</h4>
<p>
Define your class like this
</p>
```PHP
class Trip_model extends CI_Model {
	// define the KEY and table name in here

	// methods go below...
}
```
<p>
And name the file as trip_model.php
<br>
</p>

<p>That's all you need to know when creating a new class.</p>

<h3>Special note on classes in controllers folder</h3>
<p>
Currently I have not yet finished writing all checking strategy on the CORE_Controller.php, but you still need to use that class, just initialize the class like the example above will be good enough.
</p>
<p>
- Please use core_controller as your responsing method
<br>
e.g. If you need to response failure message back to the app, please do this
</p>
```PHP
/**
* @param $error_code INT This will be referenced back to the error_message.php in the config folder using the error code
* @param $override_custom_message STRING Once it is not null, it will replace the default message referenced in the error_message.php, case like validation error would be a good place to use $override_custom_message
*/
$this->core_controller->fail_response($error_code, $override_custom_message);
```

<p>
Example: controllers/user.php -> register_post() method
<br>
If you need to response success message back to the app, please do this
</p>
```PHP
  $this->core_controller->successfully_processed();
```
<p>
After calling the above two methods, the server will <b>STOP</b> processing remaining codes.
<br>
More often enough, you need to send data back to client not only success message, you can do this
<br>
</p>
```PHP
/**
* @param $key STRING The key used in the return JSON data pack, `Exception` will be thrown if the key is already used before, reserved keys are `session_token` and `expire_time`
* @param $value ANY The value that will matched with the KEY, $value should not be null
*/
$this->core_controller->add_return_data($key, $value);

// Note that this method can be chained, i.e.
$this->core_controller->add_return_data($key1, $value1)->add_return_data($key2, $value2)->add_return_data($key3, $value3);

// Please remember to call successfully_processed() in the end of the chain if no more processing is needed.
```
<p>
On the call of fail_response(), all added return data <b>will be dropped and will not be included</b> in the return message.
</p>


<p>
<li><a href="https://github.com/ywng/mpf_vis">An older & simpler version (Ver. 1.0) -- MPF Vis</a></li>
</p>
