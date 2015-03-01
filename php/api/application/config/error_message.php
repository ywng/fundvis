<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// Please add your custom error here
// format: $config['error'][__number__] = '__Your Error Message__';

$config['error'][0] = 'UNKNOWN ERROR.'; // do not use this except CORE_Controller
$config['error'][1] = 'Failed credential check.';
$config['error'][2] = 'Standard validation error, you should use validation_error() function to generate exact message back to the app.';

// Login related error message
$config['error'][3] = 'User does not exist！';
$config['error'][4] = 'Incorrect password！';

// FB login related error message
$config['error'][5] = 'Facebook login failed！';


// Register_related error message
$config['error'][10] = 'User already exists';
$config['error'][11] = 'DB insert fails';



//extrater
$config['error'][100] = 'Extrater failed!';
$config['error'][101] = 'No such stock!';

//transactions
$config['error'][200] = 'Insert transaction failed!';
$config['error'][201] = 'You have not bought any of such stock before!';
$config['error'][202] = 'The quantity is larger than what you have!';

//alerts
$config['error'][300] = 'Update alert failed!';


//Transactions
$config['error'][400] = 'Update transaction failed!';





/* End of file error_message.php */
/* Location: ./application/config/error_message.php */