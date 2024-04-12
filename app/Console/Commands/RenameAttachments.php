<?php

namespace App\Console\Commands;

use App\Enum\StorageFilePath;
use App\Models\Operation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Monolog\Logger;

class RenameAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rename-attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $operations = Operation::whereNotNull('attachment')->get();
        foreach ($operations as $operation) {
            if (Storage::exists(StorageFilePath::OperationAttachments->value . '/' . $operation->attachment)) {
                Storage::move(
                    StorageFilePath::OperationAttachments->value . '/' . $operation->attachment,
                    StorageFilePath::OperationAttachments->value . '/' . md5($operation->id . $operation->attachment)
                );
                logger()->info('Renamed attachment file', [
                    'from' => StorageFilePath::OperationAttachments->value . '/' . $operation->attachment,
                    'to' => StorageFilePath::OperationAttachments->value . '/' . md5($operation->id . $operation->attachment),
                ]);
            }
        }
    }
}
