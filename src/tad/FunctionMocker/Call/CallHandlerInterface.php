<?php

namespace tad\FunctionMocker\Call;


use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use tad\FunctionMocker\ReplacementRequest;

interface CallHandlerInterface
{

    /**
     * @param \PHPUnit_Framework_MockObject_Matcher_InvokedRecorder|InvocationOrder $invokedRecorder
     *
     * @return mixed
     */
    public function setInvokedRecorder($invokedRecorder);

    /**
     * @param ReplacementRequest $request
     *
     * @return mixed
     */
    public function setRequest(ReplacementRequest $request);
}
