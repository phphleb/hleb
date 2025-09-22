<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Data;

use Hleb\Constructor\Attributes\Accessible;

#[Accessible]
final readonly class View implements \Stringable
{
    /** @internal  */
    public function __construct(
        public string $template,
        public array  $params = [],
        public ?int $status = null,
    )
    {
    }

    /** @internal  */
    public function toArray(): array
    {
        return [
            'template' => $this->template,
            'params' => $this->params,
            'status' => $this->status,
        ];
    }

    /**
     * Converting the template to a string with the addition of parameters.
     * Does not take into account the transmitted HTTP status,
     * it must be specified separately.
     * This method allows you to use code similar to the following in controllers:
     *
     * Преобразование шаблона в строку с добавлением параметров.
     * Не учитывает переданный HTTP-статус, его нужно указывать отдельно.
     * Этот метод позволяет использовать в контроллерах подобие следующего кода:
     *
     * ```php
     * class AdminController extends Controller
     * {
     *    public function profile(): void
     *    {
     *       echo view('admin/header');
     *       echo view('user/profile', ['id' => 1]);
     *       echo view('admin/footer');
     *    }
     * }
     *
     * //or
     *
     * class AdminController extends Controller
     * {
     *    public function profile(): void
     *    {
     *       $this->response()->setBody(
     *           view('admin/header') .
     *           view('user/profile', ['id' => 1]) .
     *           view('admin/footer')
     *       );
     *    }
     * }
     * ```
     *
     * @return string
     */
    #[\Override]
    public function __toString(): string
    {
        return \template($this->template, $this->params);
    }
}
