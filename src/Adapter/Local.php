<?php
namespace Superbalist\Storage\Adapter;

class Local extends \League\Flysystem\Adapter\Local
{

    /**
     * @var string
     */
    protected $publicUrlBase;

    /**
     * @param string $root
     * @param string $publicUrlBase
     * @param int $writeFlags
     * @param array|int $linkHandling
     * @param array $permissions
     */
    public function __construct(
        $root,
        $publicUrlBase = null,
        $writeFlags = LOCK_EX,
        $linkHandling = self::DISALLOW_LINKS,
        array $permissions = []
    ) {
        $this->publicUrlBase = $publicUrlBase;
        parent::__construct($root, $writeFlags, $linkHandling, $permissions);
    }

    /**
     * @return string
     */
    public function getPublicUrlBase()
    {
        return $this->publicUrlBase;
    }

    /**
     * @param string $publicUrlBase
     */
    public function setPublicUrlBase($publicUrlBase)
    {
        $this->publicUrlBase = $publicUrlBase;
    }
}