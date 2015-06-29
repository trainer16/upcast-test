# Description
This command line app outputs a CSV file listing events for each month from today or a supplied date.

The app will try to write to the document root so the directory must be writable.

## Usage
To run the app type the following from the document root:
	php app.php

Example with options:
    php app.php -n30 -d"1/1/2016"
    php app.php -d"next year"

Available options:
-n      Specify number of months to print. Default is 6
-d      A PHP DateTime format string specifying the date from which to start outputting months.  Defaults to today's date