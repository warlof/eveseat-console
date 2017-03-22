<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 19/03/2017
 * Time: 13:23
 */

namespace Seat\Console\Readers;


use Seat\Console\Models\Sde\CategoryID;
use Symfony\Component\Yaml\Yaml;

class CategoryReader extends AbstractReader
{
    public function parse()
    {
        $content = Yaml::parse(file_get_contents($this->file));

        if (!is_array($content)) {
            throw new \Exception('invFlags should be a collection !');
        }

        foreach ($content as $categoryID => $category) {
            if ($this->checkItemStructure($category)) {

                $model = CategoryID::firstOrNew([
                    'categoryID' => $categoryID,
                ]);

                $model->categoryName = $category['name']['en'];
                $model->iconID = array_key_exists('iconID', $category) ? $category['iconID'] : null;
                $model->published = $category['published'];
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
        $fields = ['name', 'published'];

        if (!is_array($item)) {
            return false;
        }

        foreach ($fields as $field) {
            if (!array_key_exists($field, $item)) {
                return false;
            }
        }

        if (!is_array($item['name'])) {
            return false;
        }

        if (!array_key_exists('en', $item['name'])) {
            return false;
        }

        return true;
    }
}