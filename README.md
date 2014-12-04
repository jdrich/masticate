Masticate <https://jdrich@github.com/jdrich/masticate.git>

## About

Masticate is an MIT licensed PHP input filtering library that wraps the dangerous PHP superglobals ($_SERVER, $_GET, $_POST, $_FILES) to enforce some level of data sanitation.

## Uses

Polling

    // The URL we want to poll
    var polled_location = 'poll_location.php';

    // The JSONP callback
    var poll_callback = function (data) {
        console.log(data);
    }

    // How often to poll the resource
    var interval = 500;

    Vixen.init(polled_location, poll_callback, interval);

Clearing

    Vixen.destroy(polled_location);

Signalling

    // The URL we want to signal
    var signalled_location = 'signal_location.php';

    // The message we want to send
    var message = JSON.stringify('example');

    Vixen.signal(signalled_location, message);

## Purpose

The goal of Masticate is to be a small library

## Author

Jonathan Rich <jdrich@gmail.com>

## Feedback

Submit bugs to https://github.com/jdrich/masticate/issues.

## License

Please see the file called LICENSE.
