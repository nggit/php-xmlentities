## Usage:
```
<?php

# This is just an example

require 'xmlentities.php';

header('Content-Type: text/plain');
echo YourClass::xmlentities('<p> &copy; © </p>'); // <p> &#169; &#169; </p>
```
