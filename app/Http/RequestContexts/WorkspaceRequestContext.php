<?php

namespace App\Http\RequestContexts;

use App\Data\WorkspaceUserContextData;
use App\Models\Workspace;
use Illuminate\Http\Request;
use RuntimeException;

final class WorkspaceRequestContext
{
    public function __construct(
        public Workspace $workspace,
        public WorkspaceUserContextData $workspaceUserContext,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $ctx = $request->attributes->get(self::class);

        if (! $ctx instanceof self) {
            throw new RuntimeException('Workspace request context is not set.');
        }

        return $ctx;
    }

    public static function tryFromRequest(Request $request): ?self
    {
        $ctx = $request->attributes->get(self::class);

        return $ctx instanceof self ? $ctx : null;
    }
}
