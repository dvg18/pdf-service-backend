<?php

/**
 * Use it for implement in classes which would be using
 * in controller for handle actions
 */
interface Actionable
{
    /**
     * @return mixed
     */
    public static function post();

    /**
     * @return mixed
     */
    public static function get();

    /**
     * @return mixed
     */
    public static function put();

    /**
     * @return mixed
     */
    public static function delete();

}
