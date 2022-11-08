<?php

namespace Blackdoor\Util;

/**
 * Tells you what to sync, receives two lists and tells you exactly what to do, you lazy son of a bitch!
 */
class Comparinator
{
    /**
     * @var leading package when data is different in two packages
     */
    private $leading = 'from';

    /**
     * @array from list sync
     */
    private $from = [];

    /**
     * @array to list sync
     */
    private $to = [];

    /**
     * @var callback  to get to other data
     */
    private $toCallback;

    /**
     * @var callback to get from data
     */
    private $fromCallback;

    /**
     * @param array $list
     */
    public function setFrom(array $list)
    {
        $this->from = $list;
    }

    /**
     * @param array $list
     */
    public function setTo(array $list)
    {
        $this->to = $list;
    }

    /**
     * Callback to convert and it is also used for the check if a update is needed
     * If in both packages the data exists, from is always leading..
     * @param $callback
     */
    public function setFromCallback($callback)
    {
        $this->fromCallback = $callback;
    }

    /**
     * Callback to convert and it is also used for the check if a update is needed
     * @param $callback
     */
    public function setToCallback($callback)
    {
        $this->toCallback = $callback;
    }

    /**
     * Set what package is leading
     */
    public function setLeading($type)
    {
        $this->leading = in_array($type, ['from', 'to']) ? $type : 'from';
    }

    /**
     * Calculate the differences and put them to an array
     */
    public function calculate()
    {
        if (!$this->fromCallback || !$this->toCallback) {
            throw new \Exception('Missing a callback to calculate!');
        }

        // first we calculate what we should send to the $from package..
        $packageFrom = array_keys($this->from);
        $packageTo = array_keys($this->to);

        foreach ($packageFrom as $id) {
            if (strlen($id) == 0) {
                throw new \Exception('A empty key was found in your from list!');
            }
        }
        foreach ($packageTo as $id) {
            if (strlen($id) == 0) {
                throw new \Exception('A empty key was found in your to list!');
            }
        }

        // data we return always the same format..
        $return = $delta = [
            'from' => ['create' => [], 'update' => [], 'delete' => []],
            'to' => ['create' => [], 'update' => [], 'delete' => []],
        ];

        // what do we need to create in from?
        $return['from']['create'] = array_diff($packageTo, $packageFrom);
        // what do we need to create in to?
        $return['to']['create'] = array_diff($packageFrom, $packageTo);

        // what do we need to update?
        $return['from']['update'] = $return['to']['update'] = array_intersect($packageFrom, $packageTo);

        // create and update are easy.. map them to their counterparts
        foreach (['from', 'to'] as $type) {
            foreach ($return[$type]['create'] as $index => $object) {
                $data = $type == 'to' ? $this->from[$object]['data'] : $this->to[$object]['data'];
                $newData = [];
                // from?
                if ($type == 'from') {
                    $function = $this->toCallback;
                    $newData = $function($data);
                }
                if ($type == 'to') {
                    $function = $this->fromCallback;
                    $newData = $function($data);
                }
                if ($newData) {
                    $delta[$type]['create'][$index] = $newData;
                }
            }
        }

        // update is a little harder, we need to use the data coming from the origin package, overwrite it with the data we have in the leading package
        foreach ($return[$this->leading == 'from' ? 'to' : 'from']['update'] as $entity) {
            // so first we need to get the source..
            $fromFunction = $this->fromCallback;
            $toFunction = $this->toCallback;

            if ($this->leading == 'from') {
                $source = $fromFunction($this->from[$entity]['data']);
                $destination = $toFunction($this->to[$entity]['data']);
            } else {
                $source = $toFunction($this->to[$entity]['data']);
                $destination = $fromFunction($this->from[$entity]['data']);
            }

            // now we calculate the new value based on the new package that is leading
            if ($destination != $source) {
                $delta[$this->leading == 'from' ? 'to' : 'from']['update'][$entity] = $this->leading == 'from' ? $source : $destination;

                // Just check out the diffrence:
                //echo json_encode($source, JSON_PRETTY_PRINT);
                //echo json_encode($destination, JSON_PRETTY_PRINT);
            }
        }

        // delete depends on what is leading..
        $deleteFrom = $this->leading == 'from' ? 'to' : 'from';
        $return[$deleteFrom]['delete'] = $this->leading == 'from' ? array_diff($packageTo, $packageFrom) : array_diff($packageFrom, $packageTo);

        foreach ($return[$deleteFrom]['delete'] as $delete) {
            if ($this->leading == 'from') {
                $destination = $this->to[$delete]['data'];
            } else {
                $destination = $this->from[$delete]['data'];
            }
            $delta[$deleteFrom]['delete'][$delete] = $destination;
        }

        foreach ($delta[$deleteFrom]['delete'] as $deleteKey => $data) {
            // dont recreate it in package from
            unset($delta[$this->leading == 'from' ? 'to' : 'from']['create'][$deleteKey]);
        }

        return $delta;
    }
}
