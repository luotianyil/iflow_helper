<?php

namespace iflow\Helper\Arr\CollectionProxy;

use iflow\Helper\Arr\Arr;
use iflow\Helper\Arr\CollectionProxy\Exceptions\WatchHandleNonException;
use iflow\Helper\Arr\CollectionProxy\interfaces\WatchInterface;

class CollectionProxy extends Arr {

    /**
     * 监听事件
     * @var array
     */
    protected array $watch = [];

    /**
     * 设置监听
     * @param string $key
     * @param array|\Closure|WatchInterface $watch
     * @return $this
     */
    public function setWatch(string $key, array|\Closure|WatchInterface $watch): CollectionProxy {
        $this->watch[$key] = $watch instanceof \Closure || $watch instanceof WatchInterface ? [
            'handle' => $watch
        ] : $watch;
        return $this;
    }

    /**
     * 设置拦截
     * @param mixed $offset key.[...deepKey]
     * @param mixed $value
     * @return mixed
     */
    public function offsetSet(mixed $offset, mixed $value): mixed {
        $oldValue = $this->offsetGet($offset);

        $saved = parent::offsetSet($offset, $value); // TODO: Change the autogenerated stub

        // 触发监听
        if ($oldValue !== $value) $this->trigger($offset, $oldValue, $value, $saved);
        return $saved;
    }

    /**
     * 触发通知
     * @param mixed $offset
     * @param mixed $oldValue
     * @param mixed $newValue
     * @param mixed $allValue
     * @return mixed
     */
    protected function trigger(mixed $offset, mixed $oldValue, mixed $newValue, mixed $allValue): mixed {

        $offsetArr = explode('.', $offset);

        if (!isset($this->watch[$offset]) && !isset($this->watch[$offsetArr[0]])) return false;
        $watch = $this->watch[$offset] ?? $this->watch[$offsetArr[0]];

        $handle = $watch['handle'] ?? throw new WatchHandleNonException('WatchHandle is Non');

        if ($handle instanceof \Closure) return $handle($newValue, $oldValue, $offset, $allValue);

        $object = is_object($handle) ? $handle : app($handle);
        if (!$object instanceof WatchInterface) throw new WatchHandleNonException('WatchHandle give WatchInterfaceType ?');
        return $object -> handle($newValue, $oldValue, $offset, $allValue);
    }
}
