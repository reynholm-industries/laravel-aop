<?php

namespace Reynholm\LaravelAop\Aspect;

use Reynholm\LaravelAop\Annotation\Transactional;

use Go\Aop\Aspect;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation\Around;

class TransactionalAspect implements Aspect
{

    /**
     * @param MethodInvocation $invocation Invocation
     * @Around("@annotation(Reynholm\Aop\Annotation\Transactional)")
     *
     * @return mixed
     */
    public function aroundTransactional(MethodInvocation $invocation)
    {
        /** @var Transactional $transactionalAnnotation */
        $transactionalAnnotation = $invocation->getMethod()->getAnnotation('Reynholm\LaravelAop\Annotation\Transactional');

        if (empty($transactionalAnnotation->databaseConnection)) {
            $transactionalAnnotation->databaseConnection = \Config::get('database.default');
        }

        /** @var \PDO $pdoConnection */
        $pdoConnection = \DB::connection($transactionalAnnotation->databaseConnection)->getPdo();

        try {
            $pdoConnection->beginTransaction();
            $result = $invocation->proceed();
            $pdoConnection->commit();

            return $result;
        } catch (\Exception $e) {
            $pdoConnection->rollBack();

            if ($transactionalAnnotation->throwExceptions === true) {
                throw $e;
            }
        }
    }

}