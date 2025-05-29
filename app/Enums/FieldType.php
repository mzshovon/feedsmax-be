<?php

declare(strict_types=1);

namespace App\Enums;

enum FieldType: string {
    case RadioButton = 'radio-button';
    case CheckBox = 'checkbox';
    case Input = 'input';
    case TextArea = 'textarea';
    case DropDown = 'dropdown';
    case MultiSelect = 'multiselect';
    case VoiceRecorder = 'voice_recorder';
    case VideoRecorder = 'video_recorder';
    case Recorder = 'recorder';
    case FileUpload = 'file_upload';
    case Combo = 'combo';
    case Range = 'range';
    case NpsRatingNumber = 'rating_number';
    case NpsRatingFeedback = 'rating_feedback';
    case NpsRatingStar = 'rating_star';
    case NpsRatingEmoji = 'rating_emoji';
    case NpsRatingCustom = 'rating_custom';
}
