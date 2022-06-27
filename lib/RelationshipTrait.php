<?php

namespace LuckyConsultation;

Trait RelationshipTrait
{
    private function getRelLink($slug)
    {
        return \PluginEngine::getLink('LuckyConsultation/api/' . $slug);
    }
}
