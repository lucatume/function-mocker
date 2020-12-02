<?php

namespace tad\FunctionMocker\Call;


use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use tad\FunctionMocker\ReplacementRequest;

interface CallHandlerInterface
{

    /**
     * @param InvocationOrder $invokedRecorder
     *
     * @return mixed
     */
    public function setInvokedRecorder(InvocationOrder $invokedRecorder);

    /**
     * @param ReplacementRequest $request
     *
     * @return mixed
     */
    public function setRequest(ReplacementRequest $request);
}
