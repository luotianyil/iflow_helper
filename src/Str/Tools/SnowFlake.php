<?php

namespace iflow\Helper\Str\Tools;

use iflow\Helper\Str\Str;

class SnowFlake {

    // 时间戳
    protected int|string $timeStamp = 0;

    // 上一次的时间戳
    protected int $lastTimeStamp = 0;

    // NS-CODE
    protected string $nsCode = '';

    // 序列号
    protected string $serial = '';

    // Service-Code
    protected string $serviceCode = '';

    // 当前事件生成的总数
    protected int $genCount = 0;

    final protected const max12bit = 4095;
    final protected const max41bit = 1099511627775;

    /**
     * 生成ID
     * @param string $prefix
     * @param string $nsCode 机器id
     * @param string $serviceId 服务id
     * @return string
     */
    public function genSnowFlake(string $prefix = '', string $nsCode = '', string $serviceId = ''): string {

        $nsCode && $this->setNsCode($nsCode);
        $serviceId && $this->setServiceCode($nsCode);


        return $prefix . bindec(
            $this->getTimeStamp()
            | $this->getServiceCode()
            | $this->getNsCode()
            | $this->genSerial()
        );
    }

    /**
     * 获取序列码
     * @return string
     */
    protected function genSerial(): string {
        return $this->serial = decbin(Str::RandomNumberByLength(13) . $this->genCount);
    }


    /**
     * 获取当前时间戳
     * @return int|string
     */
    protected function getTimeStamp(): int|string {
        if ($this->timeStamp === 0 || $this -> genCount > static::max12bit) {
            $time = floor(microtime(true) * 1000);
            if ($this->lastTimeStamp <= $time) $this->lastTimeStamp = $time;

            $this->timeStamp = decbin(self::max41bit + $this->lastTimeStamp);
            $this->genCount = 0;
        }

        $this->genCount += 1;
        return $this->timeStamp;
    }


    /**
     * 设置机器码
     * @param string $nsCode
     */
    public function setNsCode(string $nsCode): void {
        $this->nsCode = $nsCode;
    }

    /**
     * 设置机器id
     * @return string
     */
    public function getNsCode(): string {
        if ($this->nsCode !== '') return $this->nsCode;
        return $this->nsCode = decbin(str_pad(decbin(1), 5, "0", STR_PAD_LEFT));
    }

    /**
     * 获取服务Id
     * @return string
     */
    protected function getServiceCode(): string {
        if ($this->serviceCode !== '') return $this->serviceCode;
        return $this->serviceCode = decbin(str_pad(decbin(1), 5, "0", STR_PAD_LEFT));
    }

    /**
     * @param string $serviceCode
     */
    public function setServiceCode(string $serviceCode): void {
        $this->serviceCode = $serviceCode;
    }

}
