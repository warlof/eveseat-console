<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 19/03/2017
 * Time: 13:23
 */

namespace Seat\Console\Readers;


use Seat\Console\Models\Sde\DgmTypeAttribute;
use Symfony\Component\Yaml\Yaml;

class TypeAttributeReader extends AbstractReader
{
    public function parse()
    {
        $content = Yaml::parse(file_get_contents($this->file));

        if (!is_array($content)) {
            throw new \Exception('invFlags should be a collection !');
        }

        foreach ($content as $typeAttribute) {
            if ($this->checkItemStructure($typeAttribute)) {

                $model = DgmTypeAttribute::firstOrNew([
                    'typeID' => $typeAttribute['typeID'],
                    'attributeID' => $typeAttribute['attributeID']
                ]);
                $model->valueInt = array_key_exists('valueInt', $typeAttribute) ? $typeAttribute['valueInt'] : null;
                $model->valueFloat = array_key_exists('valueFloat', $typeAttribute) ? $typeAttribute['valueFloat'] : null;
                $model->save();

                $this->success++;
            } else {
                $this->error++;
            }
        }
    }

    /**
     * Allow to check the structure of an item from the collection
     *
     * @param array $item The item from which the structure must be checked
     * @return bool True if the structure has been successfully checked
     */
    protected function checkItemStructure($item) : bool
    {
        $fields = ['typeID', 'attributeID'];

        if (!is_array($item)) {
            return false;
        }

        foreach ($fields as $field) {
            if (!array_key_exists($field, $item)) {
                return false;
            }
        }

        return true;
    }
}