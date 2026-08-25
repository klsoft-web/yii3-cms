<?php

namespace App\Admin\Web\Category;

use App\Admin\Data\Slug\SlugAdminRepositoryInterface;
use App\Data\Entities\Post;
use App\Data\Entities\Slug;
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

final class CategoryForm extends FormModel
{
    public function __construct(
        private readonly SlugAdminRepositoryInterface     $slugRepository,
        private readonly TranslatorInterface              $translator)
    {
    }

    #[Safe]
    public ?string $id = null;

    #[Trim]
    #[Required]
    #[Regex(pattern: '/^\w+(.)*/u')]
    #[Length(max: Slug::ID_LENGTH)]
    public string $name = '';

    #[Trim]
    #[Required]
    #[Regex(pattern: '/^[a-z0-9]+(-?[a-z0-9]+)*$/')]
    #[Length(max: Slug::ID_LENGTH)]
    #[Callback(method: 'validateSlug')]
    public string $slug = '';

    #[Safe]
    public ?string $summary_img_path = null;

    #[Trim]
    #[Regex(pattern: '/^\w+(.)*$/u', skipOnEmpty: true)]
    #[Length(max: Post::SUMMARY_LENGTH)]
    public string $summary = '';

    #[Trim]
    #[Regex(pattern: '/^\w+(.)*$/u', skipOnEmpty: true)]
    #[Length(max: 160)]
    public string $description = '';

    #[Trim]
    #[Required]
    #[Integer(min: 1, max: 65535)]
    public int $order = 1;

    private function validateSlug(): Result
    {
        $result = new Result();
        if ($this->id !== $this->slug) {
            if ($this->slugRepository->find($this->slug) !== null) {
                $result->addError($this->translator->translate(
                    App::THE_RECORD_WITH_THE_FIELD_NAME_ALREADY_EXISTS,
                    ['field_name' => strtolower($this->translator->translate(App::SLUG))]));
            }
        }
        return $result;
    }

    public function getFormName(): string
    {
        return '';
    }
}
