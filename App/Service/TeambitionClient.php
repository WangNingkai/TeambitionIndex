<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\Service;


class TeambitionClient
{
    /**
     * @var string
     */
    public $cookie = [];

    /**
     * @var string
     */
    public $user = [];
    /**
     * @var string
     */
    public $orgId = '';
    /**
     * @var string
     */
    public $spaceId = '';
    /**
     * @var string
     */
    public $driveId = '';
    /**
     * @var string
     */
    public $rootId = '';
    /**
     * @var string
     */
    public $memberId = '';

    public function __construct($array = [])
    {
        foreach ($array as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }
}
