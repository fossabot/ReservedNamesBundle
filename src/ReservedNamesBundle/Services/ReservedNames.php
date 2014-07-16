<?php
namespace Alister\ReservedNamesBundle\Services;

use Alister\ReservedNamesBundle\Services\CleanUserNamesInterface;

class ReservedNames implements ReservedNamesInterface
{
    /** @var array of usernames to chack against */
    private $reservedNames;

    /** @var CleanUserNamesInterface class to return a 'clean' username */
    private $cleanUsername;

    public function __construct(array $reservedNames, CleanUserNamesInterface $cleanUsername)
    {
        if (! $this->reservedNames) {
            $this->reservedNames = $reservedNames;
        }
        $this->cleanUsername = $cleanUsername;
    }

    /**
     * Allow the reserved username list to be extracted.
     *
     * The inbuild list is written all in lower-case, but any new ones are 
     * down-cased as part of the bundle configuration.
     * 
     * @return array list of usernames ['username' => 1, ...]
     */
    public function getReservedNames()
    {
        return $this->reservedNames;
    }

    /**
     * A ReservedName is one based on a list - we also strip numbers and '-_' chars
     *
     * @param string  $username [description]
     *
     * @return boolean Is the username reserved?
     */
    public function isReserved($username)
    {
        if (array_key_exists($username, $this->reservedNames)) {
            return true;
        }

        $cleanName = $this->cleanUsername->clean($username);
        if ($this->isTest($cleanName, true)) {
            return true;
        }
        if ($cleanName === $username) {
            return false;
        }

        return array_key_exists($cleanName, $this->reservedNames);
    }

    /**
     * Convenience function - is the post-cleaned username start with 'test' ?
     * 
     * @param string username to clean and check
     * 
     * @return boolean true if the first 4 'real' chars are 'test'
     */
    public function isTest($username, $isClean = false)
    {
        if (! $isClean) {
            $username = $this->cleanUsername->clean($username);
        }

        /**
         * This direct-check-return is good for code complexity,
         * but doesn't obviously check the true and false cases.
         * With an explicit return true/false, we would prove that 
         * both possibilities were covered.
         */
        return 'test' == substr($username, 0, 4);
    }
}
