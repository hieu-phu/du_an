<?php

namespace App\Enums;

enum ApprovalDecisionDeliveryStatus: string
{
    case QUEUED = 'queued';
    case SENT = 'sent';
    case FAILED = 'failed';
    case SKIPPED = 'skipped';
}
