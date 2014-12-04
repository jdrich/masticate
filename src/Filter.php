<?php

namespace Masticate;

class Filter
{
    private $supers = [];

    /**
     * The great destroyer.
     *
     * Eliminates necessary superglobals, returns a Filter instance.
     */
    public static function masticate() {
        $classname = get_called_class();

        $filter = new $classname([
            'get' => $_GET,
            'post' => $_POST,
            'files' => $_FILES,
            'server' => $_SERVER
        ]);

        unset($_GET);
        unset($_POST);
        unset($_FILES);
        unset($_SERVER);

        return $filter;
    }

    /**
     * Exuberant object instantiation.
     */
    public function __construct($registrations = []) {
        foreach($registrations as $super => $values) {
            $this->register($super, $values);
        }
    }

    /**
     * Convenience method for prettier calls:
     *
     * $filter->get('article_id');
     *
     * vs
     *
     * $filter->supers('get', 'article_id');
     */
    public function __call($name, $arguments) {
        if(in_array($name, array_keys($this->supers))) {
            // Heinous, but a bit faster than call_user_func_array.
            switch(count($arguments)) {
                case 1:
                    return $this->supers($name, $arguments[0]);
                case 2:
                    return $this->supers($name, $arguments[0], $arguments[1]);
                default:
                    return $this->supers($name, $arguments[0], $arguments[1], $arguments[2]);
            }
        }

        throw new \InvalidArgumentException('Undefined filter key requested: ' . $name);
    }

    /**
     * Register an array internally.
     */
    public function register($super, $values) {
        $this->supers[$super] = $values;
    }

    /**
     * Returns whether or not the specified superglobal has a key with the
     * specified value.
     */
    public function has( $super, $value ) {
        if( !isset( $this->supers[$super]) ) {
            return false;
        }

        $super = $this->supers[$super];

        return isset( $super[$value] );
    }

    /**
     * Retreive the requested value. Defaults to FILTER_SANITIZE_STRING but
     * should wrap all filter_var functionality.
     */
    public function supers( $super, $value, $filter = \FILTER_SANITIZE_STRING, array $options = array() ) {
        $super = strtolower( $super );

        if( !isset( $this->supers[$super]) ) {
            return false;
        }

        $super = $this->supers[$super];

        if( !isset( $super[$value] ) ) {
            return null;
        }

        $filter = $this->filterFilter( $filter );

        if(is_array($super[$value])) {
            return filter_var_array($super[$value], $filter);
        } else {
            return filter_var($super[$value], $filter, $options);
        }
    }

    /**
     * Parses the passed in parameter for the build in PHP filter, and validates
     * it (sometimes).
     */
    private function filterFilter( $filter )
    {
        if( is_int($filter) ) {
            return $filter;
        }

        $filter = strtoupper( $filter );

        if(strpos($filter, 'FILTER') !== 0) {
            $filter = 'FILTER_' . $filter;
        }

        if(!defined($filter)) {
            throw new \UnexpectedValueException( 'Undefined validation filter: ' . $filter );
        }

        return constant( $filter );
    }
}
