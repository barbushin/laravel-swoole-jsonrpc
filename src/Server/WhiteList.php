<?php

namespace HuangYi\JsonRpc\Server;

use Illuminate\Contracts\Container\Container;

class WhiteList
{
    /**
     * @var bool
     */
    protected $isOpen;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var array
     */
    protected $whiteList = [];

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * WhiteList constructor.
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->setFilePath();
        $this->loadWhiteList();
    }

    /**
     * Set file path.
     */
    protected function setFilePath()
    {
        $this->filePath = $this->container['config']['jsonrpc.white_list'];
    }

    /**
     * Load white list.
     */
    protected function loadWhiteList()
    {
        if (! $this->isOpen()) {
            return;
        }

        $this->ensureFileIsReadable();

        $lines = $this->readLinesFromFile();

        foreach ($lines as $ip) {
            $ip = trim($ip);

            if ($this->isComment($ip)) {
                continue;
            }

            if ($this->isInvalidIp($ip)) {
                continue;
            }

            $this->whiteList[] = $ip;
        }
    }

    /**
     * Check if the ip is in the white list.
     *
     * @param string $ip
     * @return bool
     */
    public function check($ip)
    {
        if (! $this->isOpen()) {
            return true;
        }

        if (in_array('0.0.0.0', $this->whiteList, true)) {
            return true;
        }

        return in_array($ip, $this->whiteList, true);
    }

    /**
     * @return bool
     */
    protected function isOpen()
    {
        if (is_null($this->isOpen)) {
            $this->isOpen = file_exists($this->filePath);
        }

        return $this->isOpen;
    }

    /**
     * Ensure the ".whitelist" file is readable.
     *
     * @return bool
     * @throws \Exception
     */
    protected function ensureFileIsReadable()
    {
        if (! is_readable($this->filePath)) {
            throw new \Exception('Please make sure the ".whitelist" file is readable.');
        }
    }

    /**
     * @return array
     */
    protected function readLinesFromFile()
    {
        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        ini_set('auto_detect_line_endings', $autodetect);

        return $lines;
    }

    /**
     * @param string $line
     * @return bool
     */
    protected function isComment($line)
    {
        return strpos($line, '#') === 0;
    }

    /**
     * @param string $ip
     * @return bool
     */
    protected function isInvalidIp($ip)
    {
        return false === filter_var($ip, FILTER_VALIDATE_IP);
    }
}
