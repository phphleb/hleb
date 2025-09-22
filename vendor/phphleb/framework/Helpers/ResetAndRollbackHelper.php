<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Base\ResetInterface;
use Hleb\Reference\LogInterface;
use RuntimeException;
use ReflectionClass;
use Throwable;

/**
 * @internal
 */
final class ResetAndRollbackHelper
{
    /**
     * @internal
     *
     * @param array<string, mixed> $services
     */
   public static function resetServices(array $services, ?LogInterface $logger): void
   {
       foreach($services as $service) {
           if (!\is_object($service)) {
               continue;
           }
           $errors = [];
           if ($service instanceof ResetInterface) {
               if (self::isUninitializedLazyObject($service)) {
                   continue;
               }
               try {
                   $service->reset();
               } catch (Throwable $e) {
                   $errors[] = $e->getMessage() . ' ' . $e->getTraceAsString();
               }
           }
           if ($errors) {
               if ($logger) {
                   foreach ($errors as $error) {
                       $logger->error($error);
                   }
               }
               throw new RuntimeException(\current($errors));
           }
       }
   }

    /**
     * @internal
     *
     * @param string[] $classes
     */
    public static function rollbackClassState(array $classes): void
    {
        $errors = [];
        foreach($classes as $class) {
            try {
                \class_exists($class, false) and $class::rollback();
            } catch (Throwable $e) {
                $errors[] = $e->getMessage() . ' ' . $e->getTraceAsString();
            }
            if ($errors) {
                throw new RuntimeException(\implode(PHP_EOL, $errors));
            }
        }
    }

    private static function isUninitializedLazyObject(object $object): bool
    {
        if (PHP_VERSION_ID < 80400) {
            return false;
        }
        return (new ReflectionClass($object))->isUninitializedLazyObject($object);
    }
}
