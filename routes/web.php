<?php

use Illuminate\Support\Facades\Route;

// ── Auth controllers ──────────────────────────────────────────────
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// ── User controllers ──────────────────────────────────────────────
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PushController;

// ── Admin controllers ─────────────────────────────────────────────
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\LandingController;

// ══════════════════════════════════════════════════════════════════
// GUEST ROUTES
// ══════════════════════════════════════════════════════════════════
// Public landing page (redirects to dashboard if already logged in)
Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login',   [LoginController::class, 'showForm'])->name('login');
    Route::post('/login',  [LoginController::class, 'login']);

    Route::get('/register',  [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password',  [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}',  [ForgotPasswordController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password',         [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// ══════════════════════════════════════════════════════════════════
// AUTHENTICATED USER ROUTES
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'approved'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Feed & Posts
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::prefix('posts')->name('posts.')->group(function () {
        Route::post('/',                          [PostController::class, 'store'])->name('store');
        Route::get('/my',                         [PostController::class, 'myPosts'])->name('my');
        Route::get('/{post}',                     [PostController::class, 'show'])->name('show');
        Route::patch('/{post}',                    [PostController::class, 'update'])->name('update');
        Route::delete('/{post}',                  [PostController::class, 'destroy'])->name('destroy');
        Route::post('/{post}/react',              [PostController::class, 'react'])->name('react');
        Route::post('/{post}/comments',             [PostController::class, 'storeComment'])->name('comments.store');
        Route::delete('/{post}/comments/{comment}', [PostController::class, 'destroyComment'])->name('comments.destroy');
    });

    // Profile
    Route::get('/profile/{user}',         [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/me/edit',        [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',              [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/picture',       [ProfileController::class, 'updatePicture'])->name('profile.picture');
    Route::patch('/profile/password',     [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Connections
    Route::prefix('connections')->name('connections.')->group(function () {
        Route::get('/',                              [ConnectionController::class, 'index'])->name('index');
        Route::post('/',                             [ConnectionController::class, 'store'])->name('store');
        Route::patch('/{connection}/accept',         [ConnectionController::class, 'accept'])->name('accept');
        Route::delete('/{connection}',               [ConnectionController::class, 'destroy'])->name('destroy');
    });

    // Events (user-side)
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/',         [EventController::class, 'index'])->name('index');
        Route::get('/{event}',  [EventController::class, 'show'])->name('show');
        Route::post('/{event}/like',                 [EventController::class, 'toggleLike'])->name('like');
        Route::post('/{event}/rsvp',                 [EventController::class, 'rsvp'])->name('rsvp');
        Route::post('/{event}/comments',             [EventController::class, 'storeComment'])->name('comments.store');
        Route::delete('/{event}/comments/{comment}', [EventController::class, 'destroyComment'])->name('comments.destroy');
    });

    // News (user-side)
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/',        [NewsController::class, 'index'])->name('index');
        Route::get('/{news}',  [NewsController::class, 'show'])->name('show');
        Route::post('/{news}/like',                  [NewsController::class, 'toggleLike'])->name('like');
        Route::post('/{news}/comments',              [NewsController::class, 'storeComment'])->name('comments.store');
        Route::delete('/{news}/comments/{comment}',  [NewsController::class, 'destroyComment'])->name('comments.destroy');
    });

    // Messages
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/',                   [MessageController::class, 'index'])->name('index');
        Route::post('/new',               [MessageController::class, 'startConversation'])->name('new');
        Route::get('/search',             [MessageController::class, 'searchUsers'])->name('search');
        Route::get('/{conversation}',     [MessageController::class, 'show'])->name('show');
        Route::post('/{conversation}',    [MessageController::class, 'send'])->name('send');
        Route::get('/{conversation}/poll',[MessageController::class, 'poll'])->name('poll');
        Route::delete('/{conversation}',  [MessageController::class, 'deleteConversation'])->name('delete');
        Route::post('/react/{message}',   [MessageController::class, 'react'])->name('react');
        Route::post('/attachment',        [MessageController::class, 'uploadAttachment'])->name('attachment');
        Route::get('/group/{group}',      [MessageController::class, 'showGroup'])->name('group.show');
        Route::post('/group/{group}',     [MessageController::class, 'sendGroup'])->name('group.send');
        Route::get('/group/{group}/poll', [MessageController::class, 'pollGroup'])->name('group.poll');
    });

    // Community Groups
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/',         [GroupController::class, 'index'])->name('index');
        Route::get('/create',   [GroupController::class, 'create'])->name('create');
        Route::post('/',        [GroupController::class, 'store'])->name('store');
        Route::get('/{group}',  [GroupController::class, 'show'])->name('show');
        Route::get('/{group}/members',             [GroupController::class, 'members'])->name('members');
        Route::post('/{group}/members',            [GroupController::class, 'addMember'])->name('members.add');
        Route::delete('/{group}/members/{user}',   [GroupController::class, 'removeMember'])->name('members.remove');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',          [NotificationController::class, 'index'])->name('index');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read.all');
        Route::post('/{id}/read',[NotificationController::class, 'markRead'])->name('read');
        Route::get('/check',     [NotificationController::class, 'check'])->name('check');
    });

    // Web Push subscriptions
    Route::post('/push/subscribe',   [PushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushController::class, 'unsubscribe'])->name('push.unsubscribe');
});

// ══════════════════════════════════════════════════════════════════
// ADMIN ROUTES
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // User management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',                  [AdminUserController::class, 'index'])->name('index');
        Route::post('/bulk-action',      [AdminUserController::class, 'bulkAction'])->name('bulk');
        Route::get('/{user}',            [AdminUserController::class, 'show'])->name('show');
        Route::patch('/{user}/approve',  [AdminUserController::class, 'approve'])->name('approve');
        Route::patch('/{user}/reject',   [AdminUserController::class, 'reject'])->name('reject');
        Route::delete('/{user}',         [AdminUserController::class, 'destroy'])->name('destroy');
    });

    // News management
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/',             [AdminNewsController::class, 'index'])->name('index');
        Route::get('/create',       [AdminNewsController::class, 'create'])->name('create');
        Route::post('/',            [AdminNewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit',  [AdminNewsController::class, 'edit'])->name('edit');
        Route::put('/{news}',       [AdminNewsController::class, 'update'])->name('update');
        Route::delete('/{news}',    [AdminNewsController::class, 'destroy'])->name('destroy');
    });

    // Events management
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/',              [AdminEventController::class, 'index'])->name('index');
        Route::get('/create',        [AdminEventController::class, 'create'])->name('create');
        Route::post('/',             [AdminEventController::class, 'store'])->name('store');
        Route::get('/{event}/edit',  [AdminEventController::class, 'edit'])->name('edit');
        Route::put('/{event}',       [AdminEventController::class, 'update'])->name('update');
        Route::delete('/{event}',    [AdminEventController::class, 'destroy'])->name('destroy');
    });

    // Content moderation
    Route::prefix('moderation')->name('moderation.')->group(function () {
        Route::delete('/posts/{post}',              [ModerationController::class, 'deletePost'])->name('posts.destroy');
        Route::delete('/post-comments/{comment}',   [ModerationController::class, 'deleteComment'])->name('comments.destroy');
        Route::delete('/event-comments/{comment}',  [ModerationController::class, 'deleteEventComment'])->name('event-comments.destroy');
        Route::delete('/news-comments/{comment}',   [ModerationController::class, 'deleteNewsComment'])->name('news-comments.destroy');
    });
});

// Logout
Route::post('/logout', [LogoutController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
