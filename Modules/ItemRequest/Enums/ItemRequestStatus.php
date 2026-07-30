<?php

namespace Modules\ItemRequest\Enums;

enum ItemRequestStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Validated = 'validated';
    case Accounted = 'accounted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Revised = 'revised';
    case Cancelled = 'cancelled';
}
