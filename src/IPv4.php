<?php namespace MuVO\Subnet;

/**
 * Class IPv4
 * @package MuVO\Subnet
 */
class IPv4 implements SubnetInterface
{
    /**
     * @var int
     */
    protected $address;

    /**
     * @var int
     */
    protected $netmask = -1;

    /**
     * Subnet constructor.
     * @param string $subnet
     * @throws \ErrorException
     */
    public function __construct(string $subnet)
    {
        list($address, $mask) = preg_split('/[,;\/\s+]/', $subnet, 2);
        if (!preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $address)) {
            throw new \ErrorException("Can't parse IP address");
        }
        $this->address = ip2long($address);

        /**
         * Parsing a netmask/prefixlen
         */
        if (!is_null($mask)) {
            if (is_numeric($mask) && $mask >= 0 && $mask <= 32) {
                $this->netmask = ip2long(self::getNetmaskByPrefix($mask));
            } elseif (preg_match('/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/', $mask)) {
                $this->netmask = ip2long($mask);
            } else {
                throw new \ErrorException(sprintf("Can't parse netmask/prefix: %s",
                    serialize($mask)));
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s/%d', $this->getAddress(false),
            self::getPrefixByNetmask($this->getNetmask(false)));
    }

    /**
     * @param bool $asInteger
     * @return int|string
     */
    public function getAddress(bool $asInteger = false)
    {
        return $asInteger
            ? $this->address
            : long2ip($this->address);
    }

    /**
     * @return int[]
     */
    public function getOctets()
    {
        return array_map(function (string $octet) {
            return intval($octet);
        }, explode('.', $this->getAddress(false)));
    }

    /**
     * @param bool $asInteger
     * @return int|string
     */
    public function getNetwork(bool $asInteger = false)
    {
        return $asInteger
            ? $this->address & $this->netmask
            : long2ip($this->address & $this->netmask);
    }

    /**
     * @param bool $asInteger
     * @return int|string
     */
    public function getBroadcast(bool $asInteger = false)
    {
        return $asInteger
            ? $this->address | (~$this->netmask & 0xffffff)
            : long2ip($this->address | (~$this->netmask & 0xffffff));
    }

    /**
     * @param bool $asInteger
     * @return int|string
     */
    public function getNetmask(bool $asInteger = false)
    {
        return $asInteger
            ? $this->netmask
            : long2ip($this->netmask);
    }

    /**
     * @param bool $asInteger
     * @return int|string
     */
    public function getWildcard(bool $asInteger = false)
    {
        return $asInteger
            ? ~$this->netmask
            : long2ip(~$this->netmask);
    }

    /**
     * @return string
     */
    public function getBitmask()
    {
        return sprintf('%b', $this->netmask);
    }

    /**
     * @param int $length
     * @return string
     */
    public static function getNetmaskByPrefix(int $length = 32)
    {
        return long2ip(~0 & 0xffffffff << (32 - $length));
    }

    /**
     * @param string $netmask
     * @return int
     * @throws \ErrorException
     */
    public static function getPrefixByNetmask(string $netmask)
    {
        $bitmask = sprintf('%b', ~ip2long($netmask) & 0xffffffff);
        if ($bitmask !== "0" && !preg_match('/^1+$/', $bitmask)) {
            throw new \ErrorException(sprintf('"%s" is not valid netmask value', $netmask));
        }

        return 32 - strlen(rtrim($bitmask, '0'));
    }
}
