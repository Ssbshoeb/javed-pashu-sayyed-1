<?php
/*
| Shared form pieces, ported from src/components/forms/FormParts.tsx.
|
| Functions rather than templates, because they are called inline from the
| middle of a <div class="field"> and returning a string keeps the markup in
| the form template readable.
|
| Loaded by each form partial that needs it — require_once, so a page carrying
| two forms does not redeclare them.
*/

defined('APP_RUNNING') or exit('No direct access.');

/**
 * partials/form-errors.blade.php
 *
 * Shown only for the form the failure actually came from, so /contact does not
 * print the home page enquiry's errors above its own form.
 */
function form_error_box(string $form): string
{
    $errors = form_errors($form);

    if ($errors === []) {
        return '';
    }

    $items = '';
    foreach ($errors as $error) {
        $items .= '<li>' . e($error) . '</li>';
    }

    return '<div class="alert" role="alert" style="margin-bottom:1.2rem;">'
        . '<strong>This form could not be submitted.</strong>'
        . '<ul>' . $items . '</ul>'
        . '</div>';
}

/**
 * Honeypot. A bot that fills every input it finds trips the `max:0` rule in
 * validation.php and is told nothing useful. Hidden from sight by `.hp` in the
 * stylesheet and from assistive technology by aria-hidden.
 */
function honeypot(string $id): string
{
    return '<div class="hp" aria-hidden="true">'
        . '<label for="' . e($id) . '">Leave this blank</label>'
        . '<input type="text" id="' . e($id) . '" name="website" tabindex="-1" autocomplete="off">'
        . '</div>';
}

/** The red asterisk beside a required field's label. */
function required_mark(): string
{
    return '<span class="req">*</span>';
}

/**
 * Marks a field that failed validation.
 *
 * The message itself is not repeated beside the input: the error box above the
 * form already lists every one of them, and printing each twice made a form
 * with four empty required fields read as eight failures. What this adds is the
 * red border and ring site.css has always carried rules for — the React port
 * computed `fieldErrors` but never put them on the markup, so those rules were
 * dead. The box gives the wording, this gives the visitor somewhere to look.
 */
function field_invalid_attrs(string $field, string $form): string
{
    return field_error($field, $form) === '' ? '' : ' aria-invalid="true"';
}

/** The links every consent box opens with. */
function disclaimer_and_privacy_links(): string
{
    return 'I have read the <a href="' . e(route('legal', 'disclaimer')) . '">disclaimer</a> and '
        . '<a href="' . e(route('legal', 'privacy')) . '">privacy policy</a>';
}

/**
 * The consent box, whose wording differs slightly per form.
 * `$rest` is trusted markup written in the form templates, never visitor input.
 */
function consent_field(string $rest, string $form): string
{
    return '<div class="field field--wide">'
        . '<label class="checkbox">'
        . '<input type="checkbox" name="consent" value="1" required'
        . field_invalid_attrs('consent', $form) . '>'
        . '<span>' . disclaimer_and_privacy_links() . $rest . '</span>'
        . '</label>'
        . '</div>';
}

/** The area <select>, offered by three of the four forms. */
function area_select(string $id, string $form, bool $required = false): string
{
    $selected = old('area', $form);
    $areas = practice('areas');

    $options = '<option value="">' . ($required ? 'Select an area' : 'Not sure / other') . '</option>';

    foreach (area_slugs() as $slug) {
        $options .= '<option value="' . e($slug) . '"' . ($selected === $slug ? ' selected' : '') . '>'
            . e($areas[$slug]['title'])
            . '</option>';
    }

    return '<select id="' . e($id) . '" name="area"' . ($required ? ' required' : '')
        . field_invalid_attrs('area', $form) . '>' . $options . '</select>';
}

/**
 * A <select> built from a list of [value, label] pairs.
 *
 * @param array<int, array{0: string, 1: string}> $options
 */
function select_field(string $id, string $name, array $options, string $form, string $placeholder = 'Select', bool $required = true): string
{
    $selected = old($name, $form);

    $markup = '<option value="">' . e($placeholder) . '</option>';

    foreach ($options as [$value, $label]) {
        $markup .= '<option value="' . e($value) . '"' . ($selected === $value ? ' selected' : '') . '>'
            . e($label)
            . '</option>';
    }

    return '<select id="' . e($id) . '" name="' . e($name) . '"' . ($required ? ' required' : '')
        . field_invalid_attrs($name, $form) . '>' . $markup . '</select>';
}

/** A <select> built from a flat list, where the value is the label. */
function slot_select(string $id, string $name, array $values, string $form, string $placeholder): string
{
    $pairs = array_map(static fn (string $value): array => [$value, $value], $values);

    return select_field($id, $name, $pairs, $form, $placeholder);
}
