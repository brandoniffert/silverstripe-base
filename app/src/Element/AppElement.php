<?php

namespace App\Element;

trait AppElement
{
    public function getType()
    {
        return self::$singular_name;
    }

    protected function provideBlockSchema()
    {
        $blockSchema = parent::provideBlockSchema();

        $blockSchema['content'] = $this->getBlockSummary();

        if ($this->IsHidden) {
            $blockSchema['content'] = "[Hidden] {$blockSchema['content']}";
        }

        return $blockSchema;
    }

    public function getHideIf()
    {
        $hidden = parent::getHideIf();

        if ($this->hasMethod('shouldHide')) {
            return $hidden || $this->shouldHide();
        }

        return $hidden;
    }
}
