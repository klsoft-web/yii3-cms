<?php

namespace App\Admin\Web\Role;

use App\Admin\Data\Role\RoleRepositoryInterface;
use App\Messages\App;
use Yiisoft\FormModel\Attribute\Safe;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;

final class RoleForm extends FormModel
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly TranslatorInterface     $translator)
    {
    }

    #[Safe]
    public ?string $id = null;

    #[Trim]
    #[Required]
    #[Regex(pattern: '/^\w+(.)*/u')]
    #[Length(max: 126)]
    #[Callback(method: 'validateName')]
    public string $name = '';

    #[Safe]
    public array $permissions = [];

    private function validateName(): Result
    {
        $result = new Result();
        $roleWithPermissions = $this->roleRepository->find($this->name);
        if ($roleWithPermissions->id !== null &&
            $roleWithPermissions->id !== $this->id &&
            $roleWithPermissions->name === $this->name) {
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
