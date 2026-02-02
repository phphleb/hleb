<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

/** @internal */
class FlashSessionHelper
{
    /**
     * Preparing sessions for flash messages.
     *
     * Подготовка сессий для работы флеш-сообщений.
     *
     * @internal
     */
   public static function update(array &$session, string $id): void
   {
      if (\array_key_exists($id, $session) && \is_array($session[$id])) {
           foreach ($session[$id] as $key => &$data) {
               $data['reps_left']--;
               if ($data['reps_left'] < 0) {
                   unset($session[$id][$key]);
                   continue;
               }
               if (isset($data['new'])) {
                   $data['old'] = $data['new'];
                   $data['new'] = null;
               }
               if (\is_null($data['old'])) {
                   unset($session[$id][$key]);
               }
           }
       }
   }
}