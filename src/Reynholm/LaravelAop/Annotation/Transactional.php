<?php

namespace Reynholm\LaravelAop\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
* @Annotation
* @Target("METHOD")
*
* @property string $databaseConnection
* @property boolean $throwExceptions
*/
final class Transactional extends Annotation
{
    public $databaseConnection;
    public $throwExceptions = true;
}