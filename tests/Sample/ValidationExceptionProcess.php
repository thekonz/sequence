<?php
declare(strict_types=1);

namespace Sample;

use Shampine\Sequence\Exceptions\ValidationException;
use Shampine\Sequence\Process\AbstractProcess;
use Shampine\Sequence\Support\StatusCode;

class ValidationExceptionProcess extends AbstractProcess
{
    /**
     * @var bool
     */
    protected bool $fail = false;

    /**
     * @var SampleUseService|null
     */
    protected ?SampleUseService $sampleUseService = null;

    /**
     * @param bool $fail
     * @param SampleUseService|null $sampleUseService
     */
    public function __construct(bool $fail = false, ?SampleUseService $sampleUseService = null)
    {
        $this->fail = $fail;
        $this->sampleUseService = $sampleUseService;
    }

    /**
     * @param SampleRequestPayload $payload
     * @return SampleRequestPayload
     * @throws ValidationException
     */
    public function process($payload): SampleRequestPayload
    {
        if ($this->fail) {
            throw new ValidationException(
                9999,
                [
                    'error' => 'validation failed',
                    'fields' => [
                        'name' => 'undefined',
                    ]
                ],
                StatusCode::BAD_REQUEST
            );
        }

        return $payload;
    }
}
