<?php

namespace iflow\Helper\Arr\CollectionProxy\interfaces;

interface WatchInterface {

    /**
     * @param mixed $newValue 更改的值
     * @param mixed $oldValue 旧属性
     * @param mixed $changeKey 所变更的key
     * @param mixed $allValue 全部值开启
     * @return mixed
     */
    public function handle(mixed $newValue, mixed $oldValue, mixed $changeKey, mixed $allValue): mixed;

}
