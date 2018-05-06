<?php namespace MuVO\Subnet;

interface SubnetInterface
{
    /**
     * @param bool $asInteger
     * @return mixed
     */
    public function getAddress(bool $asInteger = false);

    /**
     * @param bool $asInteger
     * @return mixed
     */
    public function getNetmask(bool $asInteger = false);

    /**
     * @param bool $asInteger
     * @return mixed
     */
    public function getWildcard(bool $asInteger = false);

    /**
     * @param bool $asInteger
     * @return mixed
     */
    public function getNetwork(bool $asInteger = false);

    /**
     * @param bool $asInteger
     * @return mixed
     */
    public function getBroadcast(bool $asInteger = false);

    /**
     * @return mixed
     */
    public function getBitmask();

    /**
     * @param string $netmask
     * @return mixed
     */
    public static function getPrefixByNetmask(string $netmask);
}
