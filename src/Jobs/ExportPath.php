<?php

namespace Spatie\Export\Jobs;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use RuntimeException;
use Spatie\Export\Destination;
use Spatie\Export\Traits\NormalizedPath;

class ExportPath
{
    use NormalizedPath;

    /** @var string */
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function handle(Kernel $kernel, Destination $destination, UrlGenerator $urlGenerator)
    {
        $response = $kernel->handle(
            Request::create($urlGenerator->to($this->path))
        );

        // Fix to prevent crash on 301

        if($response->status() == 200){

            $destination->write($this->normalizePath($this->path), $response->content());

        }else if($response->status() !== 200){

            throw new RuntimeException("Path [{$this->path}] returned status code [{$response->status()}]");
            
        }

    }
}
