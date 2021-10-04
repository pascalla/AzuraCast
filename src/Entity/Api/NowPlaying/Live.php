<?php

declare(strict_types=1);

namespace App\Entity\Api\NowPlaying;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(type="object", schema="Api_NowPlaying_Live")
 */
class Live
{
    /**
     * Whether the stream is known to currently have a live DJ.
     *
     * @OA\Property(example=false)
     * @var bool
     */
    public bool $is_live = false;

    /**
     * The current active streamer/DJ, if one is available.
     *
     * @OA\Property(example="DJ Jazzy Jeff")
     * @var string
     */
    public string $streamer_name = '';

    /**
     * The start timestamp of the current broadcast, if one is available.
     *
     * @OA\Property(example="1591548318")
     * @var int|null
     */
    public ?int $broadcast_start = null;

    /**
     * The ID of the broadcast of the current show, if one is available.
     *
     * @OA\Property(example="123")
     * @var int|null
     */
    public ?int $broadcast_id = null;

    public function __construct(
        bool $is_live = false,
        string $streamer_name = '',
        ?int $broadcast_start = null,
        ?int $broadcast_id = null
    ) {
        $this->is_live = $is_live;
        $this->streamer_name = $streamer_name;
        $this->broadcast_start = $broadcast_start;
        $this->broadcast_id = $broadcast_id;
    }
}
