<?php

namespace iflow\Helper\Arr;

class Arr extends Collection {

    /**
     * 通过key 获取 value 支持多层级
     * @param string $name key
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get(string $name = '', mixed $default = []): mixed {
        if ($name === '') return $this->items;
        $keys = explode('@', $name);

        if (!$this->offsetExists($keys[0])) return [];
        if (empty($keys[1])) return $this->offsetGet($keys[0]) ?? $default;
        $names = explode('.', $keys[1]);

        $info = [];
        if (count($names) <= 1) {
            foreach ($names as $val) {
                $info = $this->items[$keys[0]][$val] ?? [];
                if ($info) return $info;
            }
        } else {
            $info = $this->getDeepValue($names, $this->offsetGet($keys[0]));
        }

        return !empty($info) ? $info : $default;
    }

    /**
     * 获取深度值
     * @param mixed $names
     * @param array $array
     * @return array|mixed|null
     */
    protected function getDeepValue(mixed $names, array $array = []): mixed {
        // 按.拆分成数组进行判断
        if (count($names) === 1) {
            return $array[array_shift($names)] ?: [];
        }
        $key = array_shift($names);
        return empty($array[$key]) ? null: $this->getDeepValue($names, $array[$key]);
    }

    public function offsetSet(mixed $offset, mixed $value): mixed {
        $offset = is_string($offset) ? explode('.', $offset) : [ $offset ];
        $pKey = array_shift($offset);

        if (count($offset) > 1) {
            $oldValue = $this->offsetGet($pKey);
            $value = $this->setDeepValue($offset, $oldValue, $value);
        }
        return parent::offsetSet($pKey, $value); // TODO: Change the autogenerated stub
    }

    /**
     * 设置子级值
     * @param mixed $offset
     * @param mixed $oldValue
     * @param mixed $newValue
     * @return mixed
     */
    protected function setDeepValue(mixed $offset, mixed $oldValue, mixed $newValue): mixed {

        if (count($offset) <= 1) {
            $oldValue[array_shift($offset)] = $newValue;
            return $oldValue;
        }

        foreach ($offset as $key) {
            if (!isset($oldValue[$key])) continue;

            array_shift($offset);
            if (count($offset) > 0)
                $oldValue[$key] = $this->setDeepValue($offset ?: [], $oldValue[$key], $newValue);
            else $oldValue[$key] = $newValue;
        }

        return $oldValue;
    }

}