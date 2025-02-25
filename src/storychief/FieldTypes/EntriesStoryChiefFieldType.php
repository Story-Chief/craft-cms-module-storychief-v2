<?php namespace storychief\storychiefv3\storychief\FieldTypes;

use craft\base\Field;
use craft\elements\Entry;
use craft\helpers\Db;

class EntriesStoryChiefFieldType implements StoryChiefFieldTypeInterface
{
    public function supportedStorychiefFieldTypes(): array
    {
        return [
            'select',
        ];
    }

    public function prepFieldData(Field $field, $fieldData)
    {
        $preppedData = [];

        if (empty($fieldData)) {
            return $preppedData;
        }
        if (! is_array($fieldData)) {
            $fieldData = [$fieldData];
        }

        // Find existing
        foreach ($fieldData as $entry) {
            $criteria = Entry::find();
            $criteria->status = null;
            $criteria->sectionId = '*';
            $criteria->limit = $field->limit;
            $criteria->id = Db::escapeParam($entry);
            $elements = $criteria->ids();

            $preppedData = array_merge($preppedData, $elements);
        }

        // Check for field limit - only return the specified amount
        if ($preppedData) {
            if ($field->limit) {
                $preppedData = array_chunk($preppedData, $field->limit);
                $preppedData = $preppedData[0];
            }
        }

        // Check if we've got any data for the fields in this element
        if (isset($fieldData['fields'])) {
            $this->_populateElementFields($preppedData, $fieldData['fields']);
        }

        return $preppedData;
    }
}
