<?php

namespace App\Sharp\ConcreteSessions\Commands;

use App\Models\ConcreteSession;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Illuminate\Filesystem\FilesystemManager;

class DownloadPdfCommand extends InstanceCommand
{
    /**
     * @var FilesystemManager
     */
    protected $filesystemManager;

    /**
     * @param FilesystemManager $filesystemManager
     */
    public function __construct(FilesystemManager $filesystemManager)
    {
        $this->filesystemManager = $filesystemManager;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return "Télécharger le PDF";
    }

    /**
     * @param string $instanceId
     * @param array $data
     * @return array
     */
    public function execute($instanceId, array $data = []): array
    {
        $concreteSession = ConcreteSession::findOrFail($instanceId);

        return $this->download($concreteSession->file_path, $concreteSession->file_name, "imports");
    }

    /**
     * @param $instanceId
     * @return bool
     */
    public function authorizeFor($instanceId): bool
    {
        return true;
    }

}
