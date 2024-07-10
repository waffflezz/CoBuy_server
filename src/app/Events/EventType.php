<?php

namespace App\Events;

enum EventType
{
    case Create;
    case Update;
    case Delete;
}
