<?php

namespace App\Admin\Web\Nav;

use App\Admin\Data\Nav\NavAdminRepositoryInterface;
use App\Data\Nav\NavPosition;
use App\Messages\App;
use Yiisoft\FormModel\Attribute\Safe;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;

final class NavForm extends FormModel
{
    public function __construct(
        private readonly NavAdminRepositoryInterface $navRepository,
        private readonly TranslatorInterface         $translator)
    {
    }

    #[Safe]
    public ?int $id = null;

    #[Trim]
    #[Required]
    #[Regex(pattern: '/^\w+(.)*/u')]
    #[Length(max: 255)]
    #[Callback(method: 'validateName')]
    public string $name = '';

    #[Safe]
    public array $nav_items = [];

    #[Required]
    public string $add_nav_item_type = AddNavItemType::Page->value;

    #[Safe]
    public array $found_entities = [];

    #[Required]
    public string $position = NavPosition::Top->value;

    #[Trim]
    #[Required]
    #[Integer(min: 1, max: 65535)]
    public int $order = 1;

    private function validateName(): Result
    {
        $result = new Result();
        $nav = $this->navRepository->findByName($this->name);
        if ($nav !== null &&
            $nav->getId() !== $this->id &&
            $nav->getName() === $this->name) {
            $result->addError($this->translator->translate(
                App::THE_RECORD_WITH_THE_FIELD_NAME_ALREADY_EXISTS,
                ['field_name' => strtolower($this->translator->translate(App::NAME))]));
        }
        return $result;
    }

    public function getFormName(): string
    {
        return '';
    }
}
