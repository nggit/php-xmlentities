<!-- <?php /* -->
# PHP xmlentities
**Tip:** Rename this `md` file to `php` to test it on your web server.
<!-- */ ?> -->

    <?php

    require 'src/NCR.php';

    $str     = '<p> &copy; © with ❤ </p>';
    $encoded = Nggit\PHPXMLEntities\NCR::encode($str);

### These are portable ASCII characters
    echo $encoded;
    // Source view:
    // <p> &#169; &#169; with &#10084; </p>
    exit;

    ?>

That's it! This library doesn't use the `mbstring` or `iconv` extension for better compatibility.
