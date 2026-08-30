<?php
// auth.php for session handling and the small helper functions every page uses.

// Start the session, but only if one is not already running.
// Calling session_start() twice throws a warning, so we check first.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// only true when a staff member is signed in.
// We check staff_id because that is what login_process.php puts in the session.
function is_logged_in(): bool
{
    return isset($_SESSION['staff_id']);
}

// Same thing under the old camelCase name, kept so nothing that already
// calls isLoggedIn() breaks. Prefer is_logged_in() in new code.
function isLoggedIn(): bool
{
    return is_logged_in();
}

// Escape helper. Everything that comes out of the database goes through this
// before it is printed, so a hero named <script> shows up as plain text.
// ENT_QUOTES also escapes single quotes, which matters inside HTML attributes.
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Bounce anyone who is not signed in back to the login page.
// Call this as the first line of any staff-only page.
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// The name shown in the "Signed in as ..." slot in the header.
function current_staff_name(): string
{
    return $_SESSION['username'] ?? 'staff';
}

// Store a one-time message that the next page will display and then forget.
// Store it raw here; the page that prints it is responsible for escaping.
function set_flash(string $message): void
{
    $_SESSION['flash'] = $message;
}

// Read the one-time message and immediately clear it, so a refresh does not repeat it.
function take_flash(): ?string
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// Builds the correct src for a hero photo.
// image_url is stored relative to the project root, e.g. images/heroes/storm.jpg
// Pages in the root pass '' and pages inside /PHP pass '../'.
function hero_image_src(?string $image_url, string $base = ''): string
{
    return $base . ltrim((string)$image_url, '/');
}

// First letter of the hero name, used as a fallback tile when there is no photo.
// mb_substr instead of substr so accented names do not get cut mid-character.
function hero_initial(string $hero_name): string
{
    return strtoupper(mb_substr($hero_name, 0, 1));
}

// Turns a status value into the CSS class that colours its stamp.
function status_class(string $status): string
{
    return 'stamp-' . strtolower($status);
}