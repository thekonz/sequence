<?php
declare(strict_types=1);

namespace Shampine\Sequence\Payload;

use RuntimeException;
use Shampine\Sequence\Support\Str;

abstract class AbstractRequestPayload
{
    /**
     * @var array<string>
     */
    protected array $whitelist = [];

    /**
     * @var array<string>
     */
    protected array $overrides = [];

    /**
     * @param array<string> $overrides
     * @param array<string> $whitelist
     */
    public function __construct(array $whitelist = [], array $overrides = [])
    {
        $this->whitelist = $whitelist;
        $this->overrides = $overrides;
    }

    /**
     * @param array<mixed> $post
     * @return $this
     */
    final public function hydratePost(array $post = []): self
    {
        return $this->hydrate($post);
    }

    /**
     * @param array<mixed> $patch
     * @return $this
     */
    final public function hydratePatch(array $patch = []): self
    {
        if (!$this instanceof PatchInterface) {
            throw new RuntimeException('Payload must implement PatchInterface');
        }

        /** @var $this AbstractRequestPayload */
        return $this->hydrate($patch, true);
    }

    /**
     * @param array<mixed> $values
     * @param bool $isPatch
     * @return $this
     */
    protected function hydrate(array $values = [], bool $isPatch = false): self
    {
        $patchKeys = [];

        foreach ($values as $key => $value) {
            if (array_key_exists($key, $this->overrides)) {
                $key = $this->overrides[$key];
            }

            if (!in_array($key, $this->whitelist)) {
                continue;
            }

            $setter = Str::setter($key);

            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
                $patchKeys[] = $key;
            }
        }

        if ($this instanceof PatchInterface && $isPatch === true) {
            $this->setPatch($patchKeys);
        }

        return $this;
    }
}
