<?php

namespace App\Enums;

enum ArticleAction: string
{
    case Create = 'create';
    case Update = 'update';
    case ChangeState = 'changeState';
    case ChangeOwner = 'changeOwner';
    case Delete = 'delete';
    case ReassignArticles = 'reassignArticles';

    public function label(): string
    {
        return match ($this) {
            ArticleAction::Create => __('Created'),
            ArticleAction::Update => __('Updated'),
            ArticleAction::ChangeState => __('State changed'),
            ArticleAction::ChangeOwner => __('Owner changed'),
            ArticleAction::Delete => __('Deleted'),
            ArticleAction::ReassignArticles => __('Reassigned'),
        };
    }
}

