<?php

namespace App\Enum;

enum StorageFilePath: string
{
    case Attachments = 'attachments';
    case OperationAttachments = self::Attachments->value . '/operations';
}
