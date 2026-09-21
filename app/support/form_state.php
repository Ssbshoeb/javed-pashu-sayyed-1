<?php
/*
|--------------------------------------------------------------------------
| Form state
|--------------------------------------------------------------------------
| Validation errors and old input, carried across the redirect that follows a
| failed POST.
|
| The Next.js port kept this in React state, because a Server Action could
| return a value straight to the component that called it. A plain PHP form
| cannot: it POSTs, and the response is a new page. So this is the classic
| post/redirect/get flash bag — which is exactly what Laravel's `old()` and
| `$errors` were, and what the wording in the views was written against:
|
|   - `errors`      is the flat list partials/form-errors.blade.php printed
|   - `fieldErrors` lets a field mark itself invalid
|   - `values`      is Laravel's `old()`, repopulating the form after a failure
|
| Redirecting rather than re-rendering the POST response is deliberate: a
| visitor who refreshes the page after a failed submit gets the page again, not
| a browser prompt to re-send the form.
*/

defined('APP_RUNNING') or exit('No direct access.');

const FLASH_KEY = '_flash';

/**
 * Stores errors and old input for the next request only.
 *
 * @param string[]              $errors      Flat list, in the order the rules ran.
 * @param array<string, string> $fieldErrors First error per field.
 * @param array<string, string> $values      Everything the visitor typed.
 */
function flash_errors(array $errors, array $fieldErrors, array $values): void
{
    $_SESSION[FLASH_KEY] = [
        'errors' => array_values($errors),
        'fieldErrors' => $fieldErrors,
        'values' => $values,
        // Which form on the page the failure belongs to, so a page carrying two
        // forms does not show one form's errors above the other.
        'form' => null,
    ];
}

/** Tags the flashed failure with the form it came from. */
function flash_form(string $form): void
{
    if (isset($_SESSION[FLASH_KEY])) {
        $_SESSION[FLASH_KEY]['form'] = $form;
    }
}

/**
 * Reads the flash bag and clears it.
 *
 * Read once per request and memoised, because both the error box and every
 * field's `value` attribute need it, and the first read is what removes it
 * from the session.
 */
function form_state(): array
{
    static $state = null;

    if ($state !== null) {
        return $state;
    }

    $flash = $_SESSION[FLASH_KEY] ?? null;
    unset($_SESSION[FLASH_KEY]);

    if (!is_array($flash)) {
        return $state = ['errors' => [], 'fieldErrors' => [], 'values' => [], 'form' => null];
    }

    return $state = [
        'errors' => $flash['errors'] ?? [],
        'fieldErrors' => $flash['fieldErrors'] ?? [],
        'values' => $flash['values'] ?? [],
        'form' => $flash['form'] ?? null,
    ];
}

/**
 * What the visitor typed, for repopulating a field. Laravel's `old()`.
 *
 * Scoped to the form the failure came from: the home page carries an enquiry
 * form and nothing else, but /contact carries one too, and a shared `name`
 * value must not bleed from one page's failure into another's.
 */
function old(string $field, string $form, string $default = ''): string
{
    $state = form_state();

    if ($state['form'] !== null && $state['form'] !== $form) {
        return $default;
    }

    $value = $state['values'][$field] ?? $default;

    return is_string($value) ? $value : $default;
}

/** The flat error list for one form, or an empty array. */
function form_errors(string $form): array
{
    $state = form_state();

    if ($state['form'] !== null && $state['form'] !== $form) {
        return [];
    }

    return $state['errors'];
}

/** The first error recorded against one field, or an empty string. */
function field_error(string $field, string $form): string
{
    $state = form_state();

    if ($state['form'] !== null && $state['form'] !== $form) {
        return '';
    }

    return (string) ($state['fieldErrors'][$field] ?? '');
}

/**
 * Everything the visitor typed, so a failed submit does not empty the form.
 * The honeypot, the CSRF token and the file are deliberately not echoed back.
 */
function collect_values(): array
{
    $values = [];

    foreach ($_POST as $key => $value) {
        if ($key === 'website' || $key === '_token' || !is_string($value)) {
            continue;
        }

        $values[$key] = $value;
    }

    return $values;
}
