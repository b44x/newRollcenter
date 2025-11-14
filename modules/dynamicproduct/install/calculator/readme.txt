You can create php files in the calculator folder to calculate each dynamic product price.
Naming files should follow this simple convention:
product{id_product}.php
Example for the product with ID #7
product7.php

The variables which will be available to the code inside the calculator file are

$id_product: holds the current product ID
$id_attribute: holds the combination ID selected by the user

other variables include the field names
Example: if you have a product with fields such as width, height, unit_price
There will be variables with the same name and holding the values entered by the user
$width, $height, $unit_price

The return variable should be named $result

Examples of use:

Example 1:
	$result = $width * $height * $unit_price;

Example 2:
	if ($width > 10)
	{
		//apply a 20% reduction if width is bigger than 10
		$result = ($width * $height * $unit_price) * 0.8;
	}
	else
	{
		$result = $width * $height * $unit_price;
	}

--------------
You can also create a global calculator file named "products.php" and it will
be applied for all products. This is useful if you want to affect the pricing of all customization or
just want to include custom functions that will be used in other calculator files
--------------


--------------
Additionally, you can create weight calculator files named weight{id_product}.php which work similar to price calculators but use
the $weight variable instead of $return
The global weight calcultor should be named weights.php
--------------