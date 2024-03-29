<?php

namespace Webdevvie\Nestis;

/**
 * @author John Bakker <me@johnbakker.name>
 * built using information from http://unicode.org/emoji/charts/full-emoji-list.html
 *
 *
 *Copyright 2016 John Bakker <me@johnbakker.name>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 */
class Nestis
{
    /**
     * Gets a nested value from an object
     *
     * @param string $pattern
     * @param mixed  $object
     * @param mixed  $default
     * @return mixed|null
     */
    public function getNestedItem($pattern, $object, $default = null)
    {
        return self::getNestedValue($pattern, $object, $default);
    }

    /**
     * Gets a nested value from an object
     *
     * @param string  $pattern
     * @param mixed   $object
     * @param mixed   $value
     * @param boolean $clear
     * @return mixed|null
     */
    public function setNestedItem($pattern, $object, $value = null, $clear = false)
    {
        return self::setNestedValue($pattern, $object, $value, $clear = false);
    }

    /**
     * Gets a nested value from an object
     *
     * @param string $pattern
     * @param mixed  $object
     * @param mixed  $default
     * @return mixed|null
     */
    public static function getNestedValue($pattern, $object, $default = null)
    {
        $parts = explode("/", $pattern);
        $lastObject = $object;
        $nr = 0;
        $totalParts = count($parts);
        $item = null;
        foreach ($parts as $part) {
            $item = null;
            $nr++;
            $found = false;
            if ($part == '') {
                return $default;
            }
            try {
                if (is_object($lastObject)) {
                    $mthd = 'get' . ucfirst($part);
                    $mthdIs = 'is' . ucfirst($part);
                    if (substr($part, 0, 2) == '::') {
                        $reflect = new \ReflectionObject($lastObject);
                        $props = $reflect->getStaticProperties();
                        $nm = substr($part, 2);
                        if (isset($props[$nm])) {
                            $prop = $reflect->getProperty($nm);
                            if ($prop->isPublic()) {
                                $item = $lastObject::$$nm;
                            }
                        }
                    } elseif (is_callable([$lastObject, $mthd])) {
                        $item = $lastObject->$mthd();
                    } elseif (is_callable([$lastObject, $mthdIs])) {
                        $item = $lastObject->$mthdIs();
                    } elseif (is_callable([$lastObject, $part])) {
                        $item = $lastObject->$part();
                    } else {
                        $reflect = new \ReflectionObject($lastObject);
                        $prop = $reflect->getProperty($part);
                        if ($prop->isPublic()) {
                            $item = $lastObject->$part;
                        }
                    }
                    $found = true;
                } elseif (is_array($lastObject)) {
                    if (array_key_exists($part, $lastObject)) {
                        $item = $lastObject[$part];
                        $found = true;
                    }
                }
            } catch (\Exception $e) {
                return $default;
            }
            if ((!is_null($item) && !is_object($item) && !is_array($item) && $nr < $totalParts) || !$found) {
                //its a string,boolean or integer , but we're not at the end of the list so we can't find the end
                return $default;
            }
            $lastObject = $item;
        }
        return $item;
    }

    /**
     * @param string       $path
     * @param object|array $object
     * @param mixed        $value
     * @param boolean      $clear
     */
    public static function setNestedValue($path, &$object, $value, $clear = false)
    {
        if (!strstr($path, "/")) {
            //now we set it
            try {
                if (is_object($object)) {
                    //try a setter first
                    $cmd = 'set' . ucfirst($path);
                    if (is_callable([$object, $cmd])) {
                        $object->$cmd($value);
                        return true;
                    }
                    $reflect = new \ReflectionObject($object);
                    $prop = $reflect->getProperty($path);
                    if ($prop->isPublic()) {
                        $object->$path = $value;
                        return true;
                    }
                } elseif (is_array($object)) {
                    if ($clear) {
                        unset($object[$path]);
                        return true;
                    }
                    $object[$path] = $value;
                    return true;
                }
            } catch (\Exception $exception) {
                //don't bother ?
            }
            return false;
        }
        $parts = explode("/", $path);
        $lastObject = &$object;
        $part = array_shift($parts);
        $item = null;
        if (is_object($lastObject)) {
            $mthd = 'get' . ucfirst($part);
            $mthdIs = 'is' . ucfirst($part);
            if (substr($part, 0, 2) == '::') {
                $reflect = new \ReflectionObject($lastObject);
                $props = $reflect->getStaticProperties();
                $nm = substr($part, 2);
                if (isset($props[$nm])) {
                    $prop = $reflect->getProperty($nm);
                    if ($prop->isPublic()) {
                        $item = $lastObject::$$nm;
                    }
                }
            } elseif (is_callable([$lastObject, $mthd])) {
                $item = $lastObject->$mthd();
            } elseif (is_callable([$lastObject, $mthdIs])) {
                $item = $lastObject->$mthdIs();
            } elseif (is_callable([$lastObject, $part])) {
                $item = $lastObject->$part();
            } else {
                $reflect = new \ReflectionObject($lastObject);
                $prop = $reflect->getProperty($part);
                if ($prop->isPublic()) {
                    $item = &$lastObject->$part;
                }
            }
        } elseif (is_array($lastObject)) {
            if (array_key_exists($part, $lastObject)) {
                $item = &$lastObject[$part];
            }
        }
        if (is_array($item) || is_object($item)) {

            return self::setNestedValue(implode("/", $parts), $item, $value, $clear);
        }
        if (is_null($item) && is_array($lastObject)) {
            $lastObject[$part] = [];
            return self::setNestedValue(implode("/", $parts), $lastObject[$part], $value, $clear);
        }
        if (is_null($item) && is_object($lastObject)) {
            $lastObject->$part = new \stdClass();
            return self::setNestedValue(implode("/", $parts), $lastObject->$part, $value, $clear);
        }
        return false;
    }
}
