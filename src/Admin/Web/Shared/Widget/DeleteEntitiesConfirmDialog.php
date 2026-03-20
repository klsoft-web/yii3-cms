<?php

declare(strict_types=1);

namespace App\Admin\Web\Shared\Widget;

use App\Messages\App;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Widget\Widget;

final class DeleteEntitiesConfirmDialog extends Widget
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    )
    {
    }

    public function render(): string
    {
        $title = $this->translator->translate(App::WOULD_YOU_LIKE_TO_DELETE_THE_SELECTED_RECORDS);
        $forbiddenError =  $this->translator->translate(App::THE_ACTION_FAILED_DUE_TO_INSUFFICIENT_RIGHTS);
        $commonError =  $this->translator->translate(App::SOMETHING_GOT_WRONG);
        $closeBtnText =  $this->translator->translate(App::CLOSE);
        $deleteBtnText =  $this->translator->translate(App::DELETE);
        return <<<HTML
            <div id="delete-entities-confirm-dialog" class="modal" tabindex="-1">
                <div class="modal-dialog">
                     <div class="modal-content">
                         <div class="modal-body">
                             <p>$title</p>
                             <div id="delete-entities-forbidden-alert" class="alert alert-danger d-none" role="alert">
                                 $forbiddenError
                             </div>
                             <div id="delete-entities-error-alert" class="alert alert-danger d-none" role="alert">
                             </div>
                             <div id="delete-entities-common-error-alert" class="alert alert-danger d-none" role="alert">
                                 $commonError
                             </div>
                         </div>
                         <div class="modal-footer">
                             <button type="button" class="btn" data-bs-dismiss="modal">$closeBtnText</button>
                             <button id="delete-entities-confirm-btn" type="button" class="btn btn-primary">
                                 <span id="delete-entities-confirm-btn-spinner" class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                                 <span id="delete-entities-confirm-btn-span-delete">$deleteBtnText</span>
                             </button>
                         </div>
                     </div>
                </div>
            </div>
            HTML;
    }
}
