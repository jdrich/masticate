Masticate <https://jdrich@github.com/jdrich/masticate.git>

## About

Masticate is an MIT licensed PHP input filtering library that wraps the
dangerous PHP superglobals ($_SERVER, $_GET, $_POST, $_FILES) to enforce some
level of data sanitation.

## Uses

Masticate (destroys superglobals)

    $filter = Filter::masticate();

Register (preserves superglobals)

    $filter = new Filter(['get' => $_GET]); $filter->register(['post' =>
    $_POST]);

Retreiving

    if($filter->has('get','param')) {
        $foo = $filter->get('param');
    }

Filtering

    $bar = $filter->get('param', 'SANITIZE_EMAIL');

## Purpose

Masticate wraps the superglobals in a very simple layer of OO which implements
the build-in PHP filter extension.

## Author

Jonathan Rich <jdrich@gmail.com>

## Feedback

Submit bugs to https://github.com/jdrich/masticate/issues.

## License

Please see the file called LICENSE.
