<?php

declare(strict_types=1);

namespace App\Enums;

enum ChoiceType: string {
    case NPS = 'nps';
    case RadioButton = 'radio-button';
    case CheckBox = 'checkbox';
    case Input = 'input';
    case TextArea = 'textarea';
    case Rating1To5Star = 'rating_0_5_star';
    case Rating1To5Number = 'rating_0_5_number';
    case Score0To10Number = 'rating_0_10_number';
    case Rating0To10Star = 'rating_0_10_star';
    case DropDown = 'dropdown';
    case Combo = 'combo';
    case RatingYesNo = 'rating_0_1_no-yes';
    case RatingYesNoNeutral = 'rating_0_2_no-neutral-yes';
    case RatingNoYes = 'rating_0_1_yes-no';
}
