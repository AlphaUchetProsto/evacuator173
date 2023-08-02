<?php

namespace app\models\bitrix\traits;

use Tightenco\Collect\Support\Collection;

trait Collector
{
    public static function collect(&$model, array $config = [])
    {
        $config = collect($config);
        $fields = collect($model::mapFields());

        if($config->isNotEmpty())
        {
            foreach ($fields as $fieldID => $attribute)
            {
                if ($model->hasProperty($attribute) && !empty($config->get($fields->search($attribute))))
                {
                    $model->$attribute = $config->get($fields->search($attribute));
                }
            }

            return $model->validate();
        }

        return false;
    }

    public static function getParamsField($model)
    {
        $params = new Collection();

        foreach ($model::mapFields() as $fieldID => $attribute)
        {
            if($model->canGetProperty($attribute) && !empty($model->$attribute))
            {
                $params->put($fieldID, $model->$attribute);
            }
        }

        return $params->toArray();
    }

    public static function multipleCollect($class, array $data)
    {
        if(!empty($data))
        {
            foreach ($data as $key => &$item)
            {
                $itemObj = new $class;

                if($itemObj::collect($itemObj, $item))
                {
                    $item = $itemObj;
                }
            }
        }

        return $data;
    }
}
