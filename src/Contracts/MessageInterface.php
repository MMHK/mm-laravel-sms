<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2017/12/5
 * Time: 10:13
 */

namespace MMHK\SMS\Contracts;



interface MessageInterface
{

    /**
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @return array|null
     */
    public function getData();

    /**
     * @return string
     */
    public function getTo();

}