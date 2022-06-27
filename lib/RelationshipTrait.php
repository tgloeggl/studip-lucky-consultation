<?php

namespace LuckyConsultationsultation;

Trait RelationshipTrait
{
    private function getRelLink($slug)
    {
        return \PluginEngine::getLink('LuckyConsultationsultation/api/' . $slug);
    }
}
